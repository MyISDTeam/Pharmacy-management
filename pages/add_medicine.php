<?php
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $unit_price = trim($_POST['unit_price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $manufacturing_date = trim($_POST['manufacturing_date'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$name || !$category || !$manufacturer || !$unit_price || !$quantity || !$manufacturing_date || !$expiry_date) {
        $error = 'Please fill all required fields.';
    } elseif (!is_numeric($unit_price) || $unit_price < 0) {
        $error = 'Please enter a valid unit price.';
    } elseif (!is_numeric($quantity) || $quantity < 0) {
        $error = 'Please enter a valid quantity.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO medicines (name, category, manufacturer, unit_price, quantity, manufacturing_date, expiry_date, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $manufacturer, $unit_price, $quantity, $manufacturing_date, $expiry_date, $description]);
            $medicineId = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO inventory (medicine_id, stock_quantity) VALUES (?, ?)")->execute([$medicineId, $quantity]);
            $pdo->commit();
            $success = 'Medicine added successfully!';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Error adding medicine: ' . $e->getMessage();
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<h1 class="h3 mb-4 text-gray-800">Add Medicine</h1>
<div class="card">
    <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success alert-permanent"><?= $success ?> <a href="medicines.php">View all medicines</a></div><?php endif; ?>
        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                        <option>Analgesic</option>
                        <option>Antibiotic</option>
                        <option>Antihistamine</option>
                        <option>Antiviral</option>
                        <option>Antifungal</option>
                        <option>Antidepressant</option>
                        <option>Antidiabetic</option>
                        <option>Antihypertensive</option>
                        <option>Antipyretic</option>
                        <option>Supplement</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="manufacturer" class="form-label">Manufacturer <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="manufacturer" name="manufacturer" required>
                </div>
                <div class="col-md-3">
                    <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="unit_price" name="unit_price" required>
                </div>
                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control" id="quantity" name="quantity" required>
                </div>
                <div class="col-md-3">
                    <label for="manufacturing_date" class="form-label">Manufacturing Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="manufacturing_date" name="manufacturing_date" required>
                </div>
                <div class="col-md-3">
                    <label for="expiry_date" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Medicine</button>
                    <a href="medicines.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
