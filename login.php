<?php
/*
 * login.php  -  CUSTOMER LOGIN
 * ----------------------------------------------------------
 * Checks the email + password against the database.
 * Uses password_verify() to compare against the stored hash.
 * On success it saves the user in the session.
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Already logged in? Go home.
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        // Look up the user by email (prepared statement).
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // Verify the password against the stored hash.
        if ($user && password_verify($password, $user['password'])) {
            // Success: remember the user in the session.
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('index.php');
        } else {
            // We show the same message whether the email or password
            // was wrong, so attackers cannot tell which one existed.
            $errors[] = 'Invalid email or password.';
        }
    }
}

$page_title = 'Login';
include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h1>Login</h1>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! You can now log in.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php" id="loginForm" novalidate>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-block">Login</button>
        </form>

        <p class="form-footer">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
