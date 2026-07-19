<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $passwordIsValid = $user && password_verify($password, $user['password']);
        $usesLegacyPassword = $user
            && password_get_info($user['password'])['algo'] === null
            && hash_equals($user['password'], $password);

        if ($passwordIsValid || $usesLegacyPassword) {
            if ($usesLegacyPassword || password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
                $updateStmt->execute([$newHash, $user['user_id']]);
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Pharmacy Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/Pharmacy-management/assets/css/style.css" />
</head>
<body class="login-page d-flex align-items-center justify-content-center">
    <div class="login-card">
        <div class="card shadow-lg p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-pills login-logo"></i>
                    <h3 class="mt-2">Pharmacy MS</h3>
                    <p class="text-muted">Sign in to your account</p>
                </div>
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success" role="alert">
                        Account created successfully. You can now sign in.
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" autocomplete="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-login w-100">Sign In</button>
                </form>
                <p class="text-center text-muted mt-3 mb-0">
                    Don't have an account? <a href="signup.php">Sign up</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
