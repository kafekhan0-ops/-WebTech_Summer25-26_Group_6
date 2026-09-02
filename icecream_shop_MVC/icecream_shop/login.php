<?php
require_once "includes/header.php";
if (isLoggedIn()) { header("Location: index.php"); exit; }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $redirect = ($_GET['redirect'] ?? '') === 'checkout' ? 'checkout.php' : 'index.php';
        header("Location: $redirect");
        exit;
    }
    $error = "Invalid email or password.";
}
?>
<section class="auth-section">
<div class="form-card auth-card">
    <div class="auth-icon">🍨</div>
    <h1>Welcome Back</h1>
    <p>Sign in to continue your sweet journey.</p>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn full" type="submit">Login</button>
    </form>
    <p class="form-foot"><a href="reset_password.php">Forgot password?</a></p><p class="form-foot">Don't have an account? <a href="register.php">Create one</a></p>
</div>
</section>
<?php require_once "includes/footer.php"; ?>
