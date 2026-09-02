<?php
$pageTitle = "Contact Us";
require_once "includes/header.php";
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['message'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $body) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $body);
        $stmt->execute();
        $message = "Thank you! Your message has been sent.";
    } else {
        $message = "Please complete the required fields with a valid email.";
    }
}
?>
<section class="page-banner">
    <p class="eyebrow">We'd Love To Hear From You</p>
    <h1>Contact Us</h1>
    <p>Questions, feedback or simply craving a scoop? Send us a message.</p>
</section>
<section class="section contact-grid">
    <div class="contact-info">
        <h2>Let's Talk Ice Cream</h2>
        <p>Our team is happy to help with orders, flavors and general questions.</p>
        <div class="contact-item"><b>📍 Address</b><span>Dhaka, Bangladesh</span></div>
        <div class="contact-item"><b>☎ Phone</b><span>+880 1234-567890</span></div>
        <div class="contact-item"><b>✉ Email</b><span>hello@icecreamdelights.com</span></div>
    </div>
    <form class="form-card" method="post">
        <h2>Send A Message</h2>
        <?php if ($message): ?><div class="form-success"><?= e($message) ?></div><?php endif; ?>
        <label>Name *</label><input name="name" required>
        <label>Email *</label><input type="email" name="email" required>
        <label>Subject</label><input name="subject">
        <label>Message *</label><textarea name="message" rows="6" required></textarea>
        <button class="btn" type="submit">Send Message</button>
    </form>
</section>
<?php require_once "includes/footer.php"; ?>
