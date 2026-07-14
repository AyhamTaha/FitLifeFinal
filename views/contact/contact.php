<?php
// views/contact/contact.php

include __DIR__ . '/../templates/header.php';
require __DIR__ . '/../auth/dbconn.php'; // DB connection

// We’ll store actual text messages here
$successMessage = '';
$errorMessage   = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Simple values (you can add more validation if you want)
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Prepare statement (safer than putting values directly in SQL)
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)"
    );

    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $successMessage = "Your message has been sent successfully!";
        } else {
            $errorMessage = "Something went wrong while sending your message. Please try again.";
        }

        $stmt->close();
    } else {
        $errorMessage = "Could not prepare the database query.";
    }
}
?>
 <link rel="stylesheet" href="/fitness-website/public/css/style.css">
<section class="container contact-page">
  <nav class="breadcrumbs">
    <a href="/fitness-website/views/auth/home.php">Home</a> ›
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
    <div class="form-group">
      <label for="name">Your Name</label>
      <input
        type="text"
        id="name"
        name="name"
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
        required
        placeholder="Write your message..."
      ></textarea>
    </div>

    <button type="submit" class="btn">Send Message</button>
  </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
