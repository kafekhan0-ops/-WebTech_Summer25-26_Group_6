<?php
require_once "header.php";
$products = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];
$users = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$orders = $conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$sales = $conn->query("SELECT COALESCE(SUM(total_amount),0) total FROM orders WHERE status <> 'Cancelled'")->fetch_assoc()['total'];
?>
<section class="admin-content">
<div class="admin-cards">
    <div><span>Products</span><strong><?= $products ?></strong></div>
    <div><span>Customers</span><strong><?= $users ?></strong></div>
    <div><span>Orders</span><strong><?= $orders ?></strong></div>
    <div><span>Total Sales</span><strong>৳<?= number_format($sales,2) ?></strong></div>
</div>
<div class="admin-panel">
    <div class="panel-heading"><h2>Quick Actions</h2></div>
    <div class="quick-links">
        <a href="add_product.php">＋ Add Product</a>
        <a href="products.php">🍨 Manage Products</a>
        <a href="orders.php">🧾 Manage Orders</a>
    </div>
</div>
</section>
<?php require_once "footer.php"; ?>
