<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/functions.php";
$pageTitle = $pageTitle ?? "Ice Cream Delights";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Ice Cream Delights</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a href="index.php" class="brand">
        <span class="brand-cone">🍦</span>
        <span><b>ICE CREAM</b><b>DELIGHTS</b></span>
    </a>

    <button class="menu-toggle" id="menuToggle">☰</button>

    <nav class="navbar" id="mainNav">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="shop.php">Shop</a>
        <a href="order.php">Order</a>
        <a href="contact.php">Contact Us</a>
        <a href="admin/" class="admin-link">Admin Login</a>
        <a href="delivery.php" class="delivery-link">Delivery Login</a>
    </nav>

    <form class="search-box" action="shop.php" method="get">
        <input type="text" name="search" placeholder="search product..." value="<?= e($_GET['search'] ?? '') ?>">
        <button type="submit">⌕</button>
    </form>

    <div class="header-icons">
        <a href="shop.php" title="Wishlist" class="head-icon">♡</a>
        <a href="cart.php" title="Cart" class="head-icon cart-link">🛒<span><?= cartCount() ?></span></a>
        <?php if (isLoggedIn()): ?>
            <a href="account.php" title="My Account" class="head-icon">👤</a>
            <a href="logout.php" title="Logout" class="head-icon">↪</a>
        <?php else: ?>
            <a href="login.php" title="Login" class="head-icon">♙</a>
        <?php endif; ?>
    </div>
</header>

<?php if ($msg = getFlash('success')): ?>
    <div class="flash success"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = getFlash('error')): ?>
    <div class="flash error"><?= e($msg) ?></div>
<?php endif; ?>
<main>
