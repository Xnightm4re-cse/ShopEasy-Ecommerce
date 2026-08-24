<?php
/*
 * contact.php  -  CONTACT PAGE
 * ----------------------------------------------------------
 * A simple contact form. Because this is a local project with
 * no email server, submitting the form just shows a thank-you
 * message (it does not actually send an email).
 * ----------------------------------------------------------
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please fill in all fields with a valid email.';
    } else {
        // In a real site we would email this. Here we just confirm.
        $sent = true;
    }
}

$page_title = 'Contact';
include 'includes/header.php';
?>

<h1 class="page-title">Contact Us</h1>

<div class="form-page">
    <div class="form-card">
        <?php if ($sent): ?>
            <div class="alert alert-success">Thank you for your message! We will get back to you soon.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="contact.php" id="contactForm" novalidate>
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit" class="btn btn-block">Send Message</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
