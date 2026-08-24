<?php
/*
 * register.php  -  CUSTOMER REGISTRATION
 * ----------------------------------------------------------
 * Creates a new customer account.
 * - Validates the input in PHP (JavaScript also checks in the browser).
 * - Stores the password securely with password_hash().
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// If the visitor is already logged in, there is no need to register.
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$name   = '';
$email  = '';

// Only run this block when the form was submitted (POST request).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim the form values.
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // --- Server-side validation ---
    if (strlen($name) < 2) {
        $errors[] = 'Please enter your full name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check the email is not already registered.
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'That email is already registered. Please log in instead.';
        }
    }

    // --- If everything is valid, create the account ---
    if (empty($errors)) {
        // Hash the password. Never store the plain password!
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        $stmt->execute();

        // Send them to the login page with a success message.
        redirect('login.php?registered=1');
    }
}

$page_title = 'Register';
include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <h1>Create an Account</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- id="registerForm" is used by JavaScript for extra browser-side checks -->
        <form method="post" action="register.php" id="registerForm" novalidate>
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo e($name); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn btn-block">Register</button>
        </form>

        <p class="form-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
