<?php
require_once "../config/database.php";
require_once "../includes/functions.php";
requireAdmin();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Admin | Ice Cream Delights</title><link rel="stylesheet" href="../css/style.css"></head>
<body class="admin-body">
<aside class="admin-sidebar">
    <div class="admin-logo">🍦<span>ICE CREAM<br>ADMIN</span></div>
    <a href="dashboard.php">▦ Dashboard</a>
    <a href="products.php">🍨 Products</a>
    <a href="orders.php">🧾 Orders</a>
    <a href="delivery_staff.php">🚴 Delivery Staff</a>
    <a href="users.php">♙ Users</a>
    <a href="messages.php">✉ Messages</a>
    <a href="profile.php">👤 My Account</a>
    <a href="../index.php">↗ Website</a>
    <a href="logout.php">↪ Logout</a>
</aside>
<div class="admin-main">
<header class="admin-top"><h1>Admin Panel</h1><span>Hi, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span></header>
<?php if ($msg = getFlash('success')): ?><div class="flash success"><?= e($msg) ?></div><?php endif; ?>
