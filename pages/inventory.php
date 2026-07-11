<?php
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$search = $_GET['search'] ?? '';
$message = '';
$messageType = '';

// Update stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $medicineId = (int)$_POST['medicine_id'];
    $newQuantity = (int)$_POST['stock_quantity'];
    if ($newQuantity >= 0) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE medicines SET quantity = ? WHERE medicine_id = ?")->execute([$newQuantity, $medicineId]);
            $pdo->prepare("UPDATE inventory SET stock_quantity = ? WHERE medicine_id = ?")->execute([$newQuantity, $medicineId]);
            $pdo->commit();
            $message = 'Stock updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = 'Error updating stock.';
            $messageType = 'danger';
        }
    }
}

$inventory = [];
if ($search) {
    $stmt = $pdo->prepare("SELECT i.*, m.name, m.category, m.manufacturer, m.unit_price, m.expiry_date FROM inventory i JOIN medicines m ON i.medicine_id = m.medicine_id WHERE m.name LIKE ? ORDER BY m.name");
    $stmt->execute(["%$search%"]);
    $inventory = $stmt->fetchAll();
} else {
    $inventory = $pdo->query("SELECT i.*, m.name, m.category, m.manufacturer, m.unit_price, m.expiry_date FROM inventory i JOIN medicines m ON i.medicine_id = m.medicine_id ORDER BY m.name")->fetchAll();
}

$lowStockItems = $pdo->query("SELECT * FROM medicines WHERE quantity > 0 AND quantity <= 10 ORDER BY quantity")->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<h1 class="h3 mb-4 text-gray-800">Inventory Management</h1>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Stock</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="low-stock-tab" data-bs-toggle="tab" data-bs-target="#low-stock" type="button" role="tab">Low Stock Items</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="all" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Current Stock</span>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" placeholder="Search by medicine name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
                    </div>
                    <?php if ($search): ?>
                    <div class="col-md-2">
                        <a href="inventory.php" class="btn btn-secondary w-100">Clear</a>
                    </div>
                    <?php endif; ?>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Stock Quantity</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['category']) ?></td>
                                <td><?= htmlspecialchars($item['manufacturer']) ?></td>
                                <td><?= $item['stock_quantity'] ?></td>
                                <td>
                                    <?php if ($item['stock_quantity'] == 0): ?>
                                        <span class="badge bg-secondary">Out of Stock</span>
                                    <?php elseif ($item['stock_quantity'] <= 10): ?>
                                        <span class="badge badge-low">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-ok">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $item['last_updated'] ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?= $item['inventory_id'] ?>"><i class="fas fa-edit"></i> Update</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($inventory)): ?>
                            <tr><td colspan="7" class="text-center text-muted">No inventory records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="low-stock" role="tabpanel">
        <div class="card">
            <div class="card-header"><span>Low Stock Medicines (Quantity &le; 10)</span></div>
            <div class="card-body">
                <?php if (empty($lowStockItems)): ?>
                    <p class="text-success"><i class="fas fa-check-circle"></i> No low stock items.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockItems as $item): ?>
                            <tr class="table-warning">
                                <td><?= $item['medicine_id'] ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['category']) ?></td>
                                <td><strong><?= $item['quantity'] ?></strong></td>
                                <td>
                                    <a href="edit_medicine.php?id=<?= $item['medicine_id'] ?>" class="btn btn-warning btn-sm">Restock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Update Modals -->
<?php foreach ($inventory as $item): ?>
<div class="modal fade" id="updateModal<?= $item['inventory_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stock: <?= htmlspecialchars($item['name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="medicine_id" value="<?= $item['medicine_id'] ?>">
                    <label class="form-label">Current Stock: <strong><?= $item['stock_quantity'] ?></strong></label>
                    <input type="number" name="stock_quantity" class="form-control mt-2" min="0" value="<?= $item['stock_quantity'] ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_stock" class="btn btn-success">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
