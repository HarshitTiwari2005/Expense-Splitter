<?php
require_once("../db.php");

$input = json_decode(file_get_contents('php://input'), true);
$group_id = intval($input['group_id'] ?? 0);

if ($group_id <= 0) {
    echo json_encode(['success'=>false,'message'=>'Invalid group id']); exit;
}

// Fetch members
$stmt = $pdo->prepare("SELECT id, name FROM participants WHERE group_id = ? ORDER BY id ASC");
$stmt->execute([$group_id]);
$members = $stmt->fetchAll();
if (!$members) {
    echo json_encode(['success'=>true,'transactions'=>[]]); exit;
}
$memberIds = array_column($members, 'id');
$memberNames = [];
foreach($members as $m){ $memberNames[$m['id']] = $m['name']; }

// Fetch expenses
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE group_id = ?");
$stmt->execute([$group_id]);
$expenses = $stmt->fetchAll();

if (!$expenses) {
    echo json_encode(['success'=>true,'transactions'=>[]]); exit;
}

// Initialize balances
$balance = array_fill_keys($memberIds, 0.0);

// Compute balance: each expense split equally among all members
$N = count($memberIds);
foreach ($expenses as $e) {
    $share = floatval($e['amount']) / $N;
    foreach ($memberIds as $pid) {
        if ($pid == $e['payer_id']) {
            $balance[$pid] += (floatval($e['amount']) - $share);
        } else {
            $balance[$pid] -= $share;
        }
    }
}

// Build debtors and creditors
$debtors = []; $creditors = [];
foreach ($balance as $pid => $bal) {
    if ($bal < -0.005) $debtors[$pid] = $bal; // owes money
    if ($bal >  0.005) $creditors[$pid] = $bal; // to receive
}

// Greedy settlement
$transactions = [];
foreach ($debtors as $did => $dbal) {
    foreach ($creditors as $cid => $cbal) {
        if ($dbal >= -0.005) break;
        if ($cbal <=  0.005) continue;
        $amt = min($cbal, -$dbal);
        if ($amt > 0.005) {
            $transactions[] = [
                'from_id' => $did,
                'to_id' => $cid,
                'from_name' => $memberNames[$did],
                'to_name' => $memberNames[$cid],
                'amount' => round($amt, 2)
            ];
            $dbal += $amt;
            $cbal -= $amt;
            $debtors[$did] = $dbal;
            $creditors[$cid] = $cbal;
        }
    }
}

echo json_encode(['success'=>true,'transactions'=>$transactions]);
