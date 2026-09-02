<?php
require_once "includes/header.php";
if (isLoggedIn()) { header("Location: index.php"); exit; }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = "Enter a valid name/email and a password of at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows) {
            $error = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $address, $hash);
            if ($stmt->execute()) {
                flash('success', 'Registration successful. Please login.');
                header("Location: login.php");
                exit;
            }
            $error = "Registration failed.";
        }
    }
}
?>
<section class="auth-section">
<div class="form-card auth-card wide">
    <div class="auth-icon">🍦</div>
    <h1>Create Account</h1>
    <p>Join Ice Cream Delights today.</p>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Full Name</label><input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
        <label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        <label>Phone</label><input type="text" name="phone" value="<?= e($_POST['phone'] ?? '') ?>">
        <label>Address</label><textarea name="address" rows="3"><?= e($_POST['address'] ?? '') ?></textarea>
        <label>Password</label><input type="password" name="password" required minlength="6">
        <button class="btn full" type="submit">Register</button>
    </form>
    <p class="form-foot">Already registered? <a href="login.php">Login</a></p>
</div>
</section>
<?php require_once "includes/footer.php"; ?>
