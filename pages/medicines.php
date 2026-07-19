<?php

require_once __DIR__ . '/../includes/header.php';
requireLogin();

$search = $_GET['search'] ?? '';
$medicines = [];

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE name LIKE ? OR category LIKE ? OR manufacturer LIKE ? ORDER BY name");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    $medicines = $stmt->fetchAll();
} else {
    $medicines = $pdo->query("SELECT * FROM medicines ORDER BY name")->fetchAll();
}

// Delete medicine
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM medicines WHERE medicine_id = ?")->execute([$id]);
        echo '<script>window.location.href = "medicines.php";</script>';
    } catch (PDOException $e) {
        $error = "Error deleting medicine.";
    }
}
?>
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<h1 class="h3 mb-4 text-gray-800">Medicines</h1>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Medicines</span>
        <a href="add_medicine.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Medicine</a>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by name, category or manufacturer..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
            </div>
            <?php if ($search): ?>
            <div class="col-md-2">
                <a href="medicines.php" class="btn btn-secondary w-100">Clear</a>
            </div>
            <?php endif; ?>
        </form>

        <?php if (empty($medicines)): ?>
            <p class="text-muted">No medicines found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicines as $m): ?>
                    <tr>
                        <td><?= $m['medicine_id'] ?></td>
                        <td><?= htmlspecialchars($m['name']) ?></td>
                        <td><?= htmlspecialchars($m['category']) ?></td>
                        <td><?= htmlspecialchars($m['manufacturer']) ?></td>
                        <td><?= formatCurrency($m['unit_price']) ?></td>
                        <td>
                            <?php if ($m['quantity'] <= 10 && $m['quantity'] > 0): ?>
                                <span class="badge badge-low"><?= $m['quantity'] ?></span>
                            <?php elseif ($m['quantity'] == 0): ?>
                                <span class="badge bg-secondary">Out of Stock</span>
                            <?php else: ?>
                                <span class="badge badge-ok"><?= $m['quantity'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($m['expiry_date']) ?></td>
                        <td>
                            <a href="edit_medicine.php?id=<?= $m['medicine_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="medicines.php?delete=<?= $m['medicine_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this medicine?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
