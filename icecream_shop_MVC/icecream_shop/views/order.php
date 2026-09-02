<?php
$pageTitle = "My Orders";
require_once "includes/header.php";
requireLogin();

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result();
?>
<section class="page-banner small-banner">
    <p class="eyebrow">Your History</p>
    <h1>My Orders</h1>
</section>
<section class="section">
<?php if ($orders->num_rows === 0): ?>
<div class="empty-state"><div class="empty-icon">🍧</div><h2>No orders yet</h2><p>Your delicious first order is waiting.</p><a class="btn" href="shop.php">Shop Now</a></div>
<?php else: ?>
<div class="orders-list">
<?php while ($order = $orders->fetch_assoc()): ?>
<div class="order-card">
    <div><b>Order #<?= (int)$order['id'] ?></b><span><?= e($order['created_at']) ?></span></div>
    <div><strong>৳<?= number_format($order['total_amount'],2) ?></strong><span class="status"><?= e($order['status']) ?></span></div>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>
</section>
<?php require_once "includes/footer.php"; ?>
