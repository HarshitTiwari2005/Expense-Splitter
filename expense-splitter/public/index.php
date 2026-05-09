<?php
require_once("../db.php");
$pageTitle = "Student Expense Splitter - Home";
require_once("../includes/header.php");

// Handle create group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'])) {
    $name = trim($_POST['group_name']);
    if ($name !== "") {
        $stmt = $pdo->prepare("INSERT INTO groups(name) VALUES(?)");
        $stmt->execute([$name]);
        $gid = $pdo->lastInsertId();
        header("Location: group.php?id=" . intval($gid));
        exit;
    }
}

// Fetch groups
$groups = $pdo->query("SELECT * FROM groups ORDER BY created_at DESC")->fetchAll();
?>
<div class="row g-4">
  <div class="col-12" data-aos="fade-up">
    <div class="card p-4">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h2 class="h4 mb-1">Create a New Group</h2>
          <p class="text-muted mb-0">Add members & track expenses together.</p>
        </div>
        <form class="d-flex gap-2" method="post">
          <input name="group_name" class="form-control" placeholder="e.g. Goa Trip, Canteen Squad" required>
          <button class="btn btn-primary">Create Group</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12" id="how" data-aos="fade-up" data-aos-delay="100">
    <div class="card p-4">
      <h2 class="h5 mb-3">How it works</h2>
      <div class="row text-center g-3">
        <div class="col-md-4">
          <span class="badge-soft">1</span>
          <p class="mb-0">Create a group & add all participants.</p>
        </div>
        <div class="col-md-4">
          <span class="badge-soft">2</span>
          <p class="mb-0">Record each expense & who paid.</p>
        </div>
        <div class="col-md-4">
          <span class="badge-soft">3</span>
          <p class="mb-0">Click Calculate to get fair settlements.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12" data-aos="fade-up" data-aos-delay="150">
    <div class="card p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 mb-0">Your Groups</h2>
      </div>
      <?php if(!$groups): ?>
        <p class="text-muted mb-0">No groups yet. Create one above!</p>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach($groups as $g): ?>
            <div class="col-md-6">
              <a class="text-decoration-none" href="group.php?id=<?= intval($g['id']) ?>">
                <div class="card p-3 fade-rise">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <h3 class="h6 mb-1"><?= h($g['name']) ?></h3>
                      <div class="text-muted small"><?= h($g['created_at']) ?></div>
                    </div>
                    <span class="badge-pastel" style="color:#6a11cb;">Open →</span>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once("../includes/footer.php"); ?>
