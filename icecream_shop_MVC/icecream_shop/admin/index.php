<?php
session_start();
require_once "../config/database.php";
require_once "../includes/functions.php";

if (isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

// Always repair the demo account if someone imported an older SQL file with a bad hash.
$demoEmail = 'admin@icecream.com';
$demoPassword = 'admin123';
$check = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ? LIMIT 1");
$check->bind_param('s', $demoEmail);
$check->execute();
$demo = $check->get_result()->fetch_assoc();
if (!$demo) {
    $hash = password_hash($demoPassword, PASSWORD_DEFAULT);
    $add = $conn->prepare("INSERT INTO admins (name,email,password) VALUES ('Administrator',?,?)");
    $add->bind_param('ss', $demoEmail, $hash);
    $add->execute();
}
// If the existing hash is invalid for admin123, repair it automatically.
if ($demo && !password_verify($demoPassword, $demo['password'])) {
    $hash = password_hash($demoPassword, PASSWORD_DEFAULT);
    $fix = $conn->prepare("UPDATE admins SET password=? WHERE id=?");
    $fix->bind_param('si', $hash, $demo['id']);
    $fix->execute();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid admin login. Use admin@icecream.com / admin123";
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Admin Login</title><link rel="stylesheet" href="../css/style.css"></head>
<body class="admin-login-page">
<div class="form-card auth-card">
    <div class="auth-icon">🍨</div><h1>Admin Login</h1><p>Ice Cream Delights Management</p>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="index.php">
        <label>Email</label><input type="email" name="email" value="admin@icecream.com" required>
        <label>Password</label><input type="password" name="password" value="admin123" required>
        <button class="btn full" type="submit">Login</button>
    </form>
    <p class="form-foot">Demo: admin@icecream.com / admin123</p>
    <a class="form-foot" href="../index.php">← Back to website</a>
</div>
</body></html>
