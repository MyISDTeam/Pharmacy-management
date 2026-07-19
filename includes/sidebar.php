<div class="wrapper">
    <div class="sidebar d-flex flex-column p-3">
        <a href="/Pharmacy-management/dashboard.php" class="text-white text-decoration-none mb-4 fs-5 fw-bold">
            <i class="fas fa-pills"></i> Pharmacy MS
        </a>
        <hr class="text-white opacity-25">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="/Pharmacy-management/dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'medicines') !== false ? 'active' : '' ?>" href="/Pharmacy-management/pages/medicines.php">
                    <i class="fas fa-fw fa-pills"></i> Medicines
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'inventory') !== false ? 'active' : '' ?>" href="/Pharmacy-management/pages/inventory.php">
                    <i class="fas fa-fw fa-warehouse"></i> Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : '' ?>" href="/Pharmacy-management/pages/reports.php">
                    <i class="fas fa-fw fa-chart-bar"></i> Reports
                </a>
            </li>
            <hr class="text-white opacity-25">
            <li class="nav-item">
                <a class="nav-link" href="/Pharmacy-management/logout.php">
                    <i class="fas fa-fw fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    <div class="content-wrapper">
