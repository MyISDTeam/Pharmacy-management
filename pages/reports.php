<?php
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$type = $_GET['type'] ?? 'medicines';

$data = [];
$title = '';
$headers = [];

switch ($type) {
    case 'medicines':
        $title = 'Complete Medicine List';
        $headers = ['ID', 'Name', 'Category', 'Manufacturer', 'Unit Price', 'Quantity', 'Manufacturing Date', 'Expiry Date'];
        $data = $pdo->query("SELECT medicine_id, name, category, manufacturer, unit_price, quantity, manufacturing_date, expiry_date FROM medicines ORDER BY name")->fetchAll();
        break;
    case 'inventory':
        $title = 'Current Inventory Report';
        $headers = ['Medicine', 'Category', 'Stock Quantity', 'Status', 'Last Updated'];
        $data = $pdo->query("SELECT m.name, m.category, i.stock_quantity,
            CASE WHEN i.stock_quantity = 0 THEN 'Out of Stock' WHEN i.stock_quantity <= 10 THEN 'Low Stock' ELSE 'In Stock' END as status,
            i.last_updated FROM inventory i JOIN medicines m ON i.medicine_id = m.medicine_id ORDER BY m.name")->fetchAll();
        break;
    case 'low-stock':
        $title = 'Low Stock Medicines Report';
        $headers = ['ID', 'Name', 'Category', 'Manufacturer', 'Current Stock'];
        $data = $pdo->query("SELECT medicine_id, name, category, manufacturer, quantity FROM medicines WHERE quantity > 0 AND quantity <= 10 ORDER BY quantity")->fetchAll();
        break;
    default:
        header('Location: reports.php?type=medicines');
        exit;
}
?>
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<h1 class="h3 mb-4 text-gray-800">Reports</h1>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= $title ?></span>
        <div>
            <a href="reports.php?type=medicines" class="btn btn-sm <?= $type == 'medicines' ? 'btn-primary' : 'btn-outline-primary' ?>">Medicines</a>
            <a href="reports.php?type=inventory" class="btn btn-sm <?= $type == 'inventory' ? 'btn-primary' : 'btn-outline-primary' ?>">Inventory</a>
            <a href="reports.php?type=low-stock" class="btn btn-sm <?= $type == 'low-stock' ? 'btn-primary' : 'btn-outline-primary' ?>">Low Stock</a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm ms-2 no-print"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <?php foreach ($headers as $h): ?>
                            <th><?= $h ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr><td colspan="<?= count($headers) ?>" class="text-center text-muted">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($row as $col): ?>
                                    <td><?= htmlspecialchars($col) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted mt-2 no-print"><small><i class="fas fa-info-circle"></i> Total records: <?= count($data) ?></small></p>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

//devrakin