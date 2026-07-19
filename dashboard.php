<?php
require_once 'includes/header.php';
requireLogin();

$totalMedicines = $pdo->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
$totalStock = $pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM medicines")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM medicines WHERE quantity > 0 AND quantity <= 10")->fetchColumn();
$expiredCount = $pdo->query("SELECT COUNT(*) FROM medicines WHERE expiry_date < CURDATE()")->fetchColumn();
?>
<?php require_once 'includes/sidebar.php'; ?>
<h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Medicines</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMedicines ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-pills fa-2x text-gray-300 card-icon"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalStock ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-cubes fa-2x text-gray-300 card-icon"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $lowStock ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300 card-icon"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Expired</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $expiredCount ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar-times fa-2x text-gray-300 card-icon"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-pills fa-3x text-primary mb-3"></i>
                <h5>Medicines</h5>
                <p class="text-muted">Manage medicine records</p>
                <a href="/Pharmacy-management/pages/medicines.php" class="btn btn-primary btn-sm">Go to Medicines</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-warehouse fa-3x text-success mb-3"></i>
                <h5>Inventory</h5>
                <p class="text-muted">Monitor and update stock</p>
                <a href="/Pharmacy-management/pages/inventory.php" class="btn btn-success btn-sm">Go to Inventory</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                <h5>Reports</h5>
                <p class="text-muted">View and print reports</p>
                <a href="/Pharmacy-management/pages/reports.php" class="btn btn-warning btn-sm">Go to Reports</a>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
