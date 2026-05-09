<?php
require_once("../db.php");

$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($group_id <= 0) { header("Location: index.php"); exit; }

// Fetch group
$stmt = $pdo->prepare("SELECT * FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch();

if (!$group) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Group • " . $group['name'];

require_once("../includes/header.php");

// Handle add participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['participant_name'])) {

    $name = trim($_POST['participant_name']);

    if ($name !== "") {

        $stmt = $pdo->prepare("
            INSERT INTO participants(group_id, name)
            VALUES(?, ?)
        ");

        $stmt->execute([$group_id, $name]);

        header("Location: group.php?id=" . $group_id);
        exit;
    }
}

// Handle add expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['amount']) &&
    isset($_POST['payer_id'])) {

    $amount = floatval($_POST['amount']);
    $payer  = intval($_POST['payer_id']);
    $desc   = trim($_POST['description'] ?? "");

    if ($amount > 0 && $payer > 0) {

        $stmt = $pdo->prepare("
            INSERT INTO expenses(group_id, payer_id, amount, description)
            VALUES(?, ?, ?, ?)
        ");

        $stmt->execute([
            $group_id,
            $payer,
            $amount,
            $desc
        ]);

        header("Location: group.php?id=" . $group_id);
        exit;
    }
}

// Fetch members
$members = $pdo->prepare("
    SELECT *
    FROM participants
    WHERE group_id = ?
    ORDER BY id ASC
");

$members->execute([$group_id]);

$members = $members->fetchAll();

// Fetch expenses
$expenses = $pdo->prepare("
    SELECT
        e.*,
        p.name AS payer_name
    FROM expenses e
    JOIN participants p
        ON p.id = e.payer_id
    WHERE e.group_id = ?
    ORDER BY e.id DESC
");

$expenses->execute([$group_id]);

$expenses = $expenses->fetchAll();
?>

<div class="row g-4">

    <!-- TOP BAR -->
    <div class="col-12" data-aos="fade-up">

        <div class="card p-4 border-0 shadow-sm rounded-4">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                <div>
                    <h2 class="h4 fw-bold mb-1">
                        <?= h($group['name']) ?>
                    </h2>

                    <p class="text-muted mb-0">
                        Manage group expenses easily
                    </p>
                </div>

                <a href="index.php"
                   class="btn btn-outline-dark rounded-pill px-4">
                    ← Back
                </a>

            </div>

        </div>

    </div>

    <!-- PARTICIPANTS -->
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">

        <div class="card p-4 border-0 shadow-sm rounded-4 h-100">

            <h3 class="h5 fw-bold mb-3">
                Add Participants
            </h3>

            <form method="post" class="d-flex gap-2">

                <input
                    name="participant_name"
                    class="form-control rounded-pill"
                    placeholder="Enter participant name"
                    required
                >

                <button class="btn btn-success rounded-pill px-4">
                    Add
                </button>

            </form>

            <hr>

            <div class="d-flex flex-wrap gap-2">

                <?php if(!$members): ?>

                    <span class="text-muted small">
                        No members added yet.
                    </span>

                <?php else: ?>

                    <?php foreach($members as $m): ?>

                        <?php
                            $colors = [
                                '#ff6b6b',
                                '#6c5ce7',
                                '#00b894',
                                '#fdcb6e',
                                '#0984e3',
                                '#e84393'
                            ];

                            $color =
                                $colors[$m['id'] % count($colors)];
                        ?>

                        <span
                            class="badge rounded-pill px-3 py-2"
                            style="
                                background: <?= $color ?>20;
                                color: <?= $color ?>;
                                font-size: 14px;
                            "
                        >
                            <?= h($m['name']) ?>
                        </span>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- EXPENSES -->
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="150">

        <div class="card p-4 border-0 shadow-sm rounded-4">

            <h3 class="h5 fw-bold mb-3">
                Add Expense
            </h3>

            <form method="post" class="row g-2">

                <div class="col-md-3">

                    <select
                        name="payer_id"
                        class="form-select rounded-pill"
                        required
                    >

                        <option value="">
                            Who paid?
                        </option>

                        <?php foreach($members as $m): ?>

                            <option value="<?= intval($m['id']) ?>">
                                <?= h($m['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-3">

                    <input
                        type="number"
                        step="0.01"
                        name="amount"
                        class="form-control rounded-pill"
                        placeholder="Amount"
                        required
                    >

                </div>

                <div class="col-md-4">

                    <input
                        name="description"
                        class="form-control rounded-pill"
                        placeholder="Description"
                    >

                </div>

                <div class="col-md-2">

                    <button class="btn btn-warning w-100 rounded-pill fw-bold">
                        Add
                    </button>

                </div>

            </form>

            <div class="table-responsive mt-4">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Payer</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if(!$expenses): ?>

                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">
                                No expenses added yet.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach($expenses as $e): ?>

                            <tr>

                                <td class="fw-semibold">
                                    <?= h($e['payer_name']) ?>
                                </td>

                                <td class="text-success fw-bold">
                                    ₹<?= number_format($e['amount'],2) ?>
                                </td>

                                <td>
                                    <?= h($e['description']) ?>
                                </td>

                                <td class="small text-muted">
                                    <?= h($e['created_at']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- SETTLEMENTS -->
    <div class="col-12" data-aos="fade-up" data-aos-delay="200">

        <div class="card p-4 border-0 shadow-sm rounded-4">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                <div>

                    <h3 class="h5 fw-bold mb-1">
                        Settlement Results
                    </h3>

                    <p class="text-muted mb-0">
                        Calculate who pays whom
                    </p>

                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <button
                        class="btn btn-primary rounded-pill px-4"
                        id="calcBtn"
                    >
                        Calculate
                    </button>

                    <button
                        class="btn btn-success rounded-pill px-4"
                        onclick="exportPDF()"
                    >
                        Export PDF
                    </button>

                    <button
                        class="btn btn-info text-white rounded-pill px-4"
                        onclick="shareWhatsApp()"
                    >
                        WhatsApp
                    </button>

                </div>

            </div>

            <div
                id="loading"
                class="text-center py-4"
                style="display:none;"
            >

                <div class="spinner-border text-primary"></div>

                <p class="text-muted mt-2">
                    Calculating settlements...
                </p>

            </div>

            <ul
                id="resultList"
                class="list-group mt-4"
            ></ul>

        </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>

async function getTransactions() {

    const res = await fetch('calculate.php', {

        method: 'POST',

        headers: {
            'Content-Type':'application/json'
        },

        body: JSON.stringify({
            group_id: <?= $group_id ?>
        })
    });

    return await res.json();
}

// CALCULATE
document.getElementById('calcBtn')
.addEventListener('click', async () => {

    const loading =
        document.getElementById('loading');

    const list =
        document.getElementById('resultList');

    list.innerHTML = '';

    loading.style.display = 'block';

    const data = await getTransactions();

    loading.style.display = 'none';

    if(data.success){

        if(data.transactions.length === 0){

            list.innerHTML = `
                <li class="list-group-item rounded-4">
                    No transactions needed.
                </li>
            `;

        } else {

            data.transactions.forEach((t, index) => {

                const li =
                    document.createElement('li');

                li.className =
                    'list-group-item border-0 shadow-sm rounded-4 mb-3 py-3';

                li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <div>
                            <strong>${t.from_name}</strong>
                            pays
                            <strong>${t.to_name}</strong>
                        </div>

                        <span class="badge bg-success fs-6">
                            ₹${t.amount.toFixed(2)}
                        </span>

                    </div>
                `;

                list.appendChild(li);
            });
        }

        if(typeof fireConfetti === "function"){
            fireConfetti();
        }

    } else {

        list.innerHTML = `
            <li class="list-group-item text-danger">
                ${data.message || "Something went wrong"}
            </li>
        `;
    }
});

```javascript
// PRETTY PDF
async function exportPDF() {

    const data = await getTransactions();

    if (!data.success) {
        alert("Unable to generate PDF");
        return;
    }

    const { jsPDF } = window.jspdf;

    const doc = new jsPDF();

    // COLORS
    const primary = [79, 70, 229];
    const secondary = [15, 23, 42];
    const success = [34, 197, 94];
    const gray = [100, 116, 139];
    const light = [248, 250, 252];

    // PAGE BACKGROUND
    doc.setFillColor(...light);
    doc.rect(0, 0, 210, 297, "F");

    // HEADER
    doc.setFillColor(...primary);
    doc.rect(0, 0, 210, 45, "F");

    // TITLE
    doc.setTextColor(255,255,255);
    doc.setFont("helvetica", "bold");
    doc.setFontSize(24);

    doc.text(
        "Expense Splitter",
        20,
        22
    );

    doc.setFontSize(12);
    doc.setFont("helvetica", "normal");

    doc.text(
        "Smart Group Expense Settlement Report",
        20,
        32
    );

    // GROUP CARD
    doc.setFillColor(255,255,255);

    doc.roundedRect(
        15,
        55,
        180,
        38,
        6,
        6,
        "F"
    );

    // GROUP TITLE
    doc.setTextColor(...secondary);

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);

    doc.text(
        "Group Information",
        25,
        70
    );

    // GROUP DETAILS
    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);

    doc.text(
        "Group Name: <?= h($group['name']) ?>",
        25,
        82
    );

    const today = new Date().toLocaleString();

    doc.text(
        "Generated: " + today,
        110,
        82
    );

    // SECTION TITLE
    let y = 115;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(18);

    doc.text(
        "Settlement Summary",
        20,
        y
    );

    y += 15;

    // NO TRANSACTIONS
    if(data.transactions.length === 0){

        doc.setFont("helvetica", "normal");
        doc.setFontSize(13);

        doc.text(
            "No transactions needed.",
            20,
            y
        );

    } else {

        data.transactions.forEach((t, index) => {

            // CARD BACKGROUND
            doc.setFillColor(255,255,255);

            doc.roundedRect(
                15,
                y - 8,
                180,
                28,
                5,
                5,
                "F"
            );

            // LEFT COLOR STRIP
            doc.setFillColor(...primary);

            doc.rect(
                15,
                y - 8,
                5,
                28,
                "F"
            );

            // NUMBER
            doc.setTextColor(...gray);

            doc.setFontSize(11);

            doc.text(
                "#" + (index + 1),
                28,
                y
            );

            // PAYMENT TEXT
            doc.setTextColor(...secondary);

            doc.setFont("helvetica", "bold");
            doc.setFontSize(13);

            doc.text(
                t.from_name,
                28,
                y + 10
            );

            doc.setFont("helvetica", "normal");

            doc.text(
                "pays",
                75,
                y + 10
            );

            doc.setFont("helvetica", "bold");

            doc.text(
                t.to_name,
                95,
                y + 10
            );

            // AMOUNT BOX
            doc.setFillColor(...success);

            doc.roundedRect(
                145,
                y,
                38,
                12,
                4,
                4,
                "F"
            );

            doc.setTextColor(255,255,255);

            doc.setFontSize(12);

            doc.text(
                "Rs. " + t.amount.toFixed(2),
                152,
                y + 8
            );

            y += 38;

            // NEW PAGE
            if(y > 250){

                doc.addPage();

                // BACKGROUND FOR NEW PAGE
                doc.setFillColor(...light);
                doc.rect(0, 0, 210, 297, "F");

                y = 30;
            }
        });
    }

    // FOOTER LINE
    doc.setDrawColor(220,220,220);

    doc.line(
        20,
        280,
        190,
        280
    );

    // FOOTER TEXT
    doc.setTextColor(...gray);

    doc.setFontSize(10);

    doc.text(
        "Generated by Expense Splitter",
        20,
        287
    );

    doc.text(
        "Thank you for using our app",
        135,
        287
    );

    // SAVE PDF
    doc.save(
        "expense-settlement-report.pdf"
    );
}
```


// WHATSAPP SHARE
async function shareWhatsApp() {

    const data = await getTransactions();

    if (!data.success) {
        alert("Unable to share");
        return;
    }

    let message =
`Expense Split Results

Group: <?= h($group['name']) ?>


`;

    if(data.transactions.length === 0){

        message +=
            "No transactions needed.";

    } else {

        data.transactions.forEach((t, index) => {

            message +=
`${index + 1}. ${t.from_name} gives Rs.${t.amount.toFixed(2)} to ${t.to_name}

`;
        });
    }

    const url =
        "https://wa.me/?text=" +
        encodeURIComponent(message);

    window.open(url, "_blank");
}

</script>

<?php require_once("../includes/footer.php"); ?>