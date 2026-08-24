<?php
/*
 * admin/login.php  -  ADMIN LOGIN
 * ----------------------------------------------------------
 * Checks the username + password against the "admin" table.
 * On success it stores the admin in the session.
 *
 * This page has its own simple layout (no sidebar) because the
 * admin is not logged in yet.
 * ----------------------------------------------------------
 */

require_once __DIR__ . '/includes/admin-auth.php';   // session + helpers
require_once __DIR__ . '/../config/database.php';

// Already logged in? Go to the dashboard.
if (is_admin_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Find the admin by username (prepared statement).
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    // Verify the password against the stored hash.
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('index.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | ShopEasy</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-login-body">

<div class="admin-login-box">
    <h1>Shop<span>Easy</span> Admin</h1>
    <p class="admin-login-sub">Please log in to manage the store</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-block">Login</button>
    </form>

    <p class="form-hint">Default login: admin / 123456</p>
    <p class="admin-login-back"><a href="../index.php">&larr; Back to store</a></p>
</div>

</body>
</html>
