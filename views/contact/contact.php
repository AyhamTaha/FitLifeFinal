<?php
// views/contact/contact.php

require_once __DIR__ . '/../auth/dbconn.php';
require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();

// We’ll store actual text messages here
$successMessage = '';
$errorMessage   = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

    // Simple values (you can add more validation if you want)
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || strlen($name) > 100
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150
        || $message === '' || strlen($message) > 5000) {
        $errorMessage = "Please enter your name, a valid email address, and a message.";
    } else {
      try {
        $stmt = $conn->prepare(
            "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        $stmt->close();
        $successMessage = "Your message has been sent successfully!";
      } catch (mysqli_sql_exception $exception) {
        error_log('FitLife contact form failed: ' . $exception->getMessage());
        $errorMessage = "Something went wrong while sending your message. Please try again.";
      }
    }
}
include __DIR__ . '/../templates/header.php';
?>
 <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/style.css">
<section class="container contact-page">
  <nav class="breadcrumbs">
    <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a> ›
    <span>Contact Us</span>
  </nav>

  <header class="contact-header">
    <h2>Contact Us</h2>
    <p>If you have any questions or need help, feel free to message us below.</p>

    <?php if (!empty($successMessage)): ?>
      <p class="success-msg">
        <?php echo htmlspecialchars($successMessage); ?>
      </p>
    <?php elseif (!empty($errorMessage)): ?>
      <p class="error-msg">
        <?php echo htmlspecialchars($errorMessage); ?>
      </p>
    <?php endif; ?>
  </header>

  <form action="" method="post" class="contact-form">
    <?= fitlife_csrf_input() ?>
    <div class="form-group">
      <label for="name">Your Name</label>
      <input
        type="text"
        id="name"
        name="name"
        maxlength="100"
        required
        placeholder="Enter your name"
      >
    </div>

    <div class="form-group">
      <label for="email">Your Email</label>
      <input
        type="email"
        id="email"
        name="email"
        maxlength="150"
        required
        placeholder="you@example.com"
      >
    </div>

    <div class="form-group">
      <label for="message">Your Message</label>
      <textarea
        id="message"
        name="message"
        rows="5"
        maxlength="5000"
        required
        placeholder="Write your message..."
      ></textarea>
    </div>

    <button type="submit" class="btn">Send Message</button>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
