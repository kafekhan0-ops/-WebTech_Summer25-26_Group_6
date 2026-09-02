<?php
require_once "includes/header.php";

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'add') {
    $id = (int)($_GET['id'] ?? 0);
    $qty = max(1, min(20, (int)($_GET['qty'] ?? 1)));
    $check = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    if ($check->get_result()->num_rows) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
        flash('success', 'Product added to your cart.');
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    unset($_SESSION['cart'][$id]);
    flash('success', 'Item removed from cart.');
    header("Location: cart.php");
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    flash('success', 'Cart cleared.');
    header("Location: cart.php");
    exit;
}

if ($action === 'update' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = min(20, $qty);
    }
    flash('success', 'Cart updated.');
    header("Location: cart.php");
    exit;
}

$items = [];
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $idList = implode(',', $ids);
    $result = $conn->query("SELECT * FROM products WHERE id IN ($idList)");
    while ($row = $result->fetch_assoc()) $items[] = $row;
}
$total = cartTotal($conn);
?>
<section class="page-banner small-banner">
    <p class="eyebrow">Your Selection</p>
    <h1>Shopping Cart</h1>
</section>

<section class="section">
<?php if (!$items): ?>
    <div class="empty-state">
        <div class="empty-icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Add a few scoops and come back here.</p>
        <a class="btn" href="shop.php">Browse Shop</a>
    </div>
<?php else: ?>
<form method="post" action="cart.php">
<input type="hidden" name="action" value="update">
<div class="cart-table-wrap">
<table class="cart-table">
<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
<tbody>
<?php foreach ($items as $item): ?>
<tr>
<td class="cart-product">
<img src="images/<?= e($item['image']) ?>" alt="">
<div><b><?= e($item['name']) ?></b><small><?= e($item['category']) ?></small></div>
</td>
<td>৳<?= number_format($item['price'], 2) ?></td>
<td><input class="qty" type="number" name="qty[<?= (int)$item['id'] ?>]" value="<?= (int)$_SESSION['cart'][$item['id']] ?>" min="0" max="20"></td>
<td>৳<?= number_format($item['price'] * $_SESSION['cart'][$item['id']], 2) ?></td>
<td><a class="remove" href="cart.php?action=remove&id=<?= (int)$item['id'] ?>">×</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="cart-actions">
    <a href="shop.php" class="btn btn-light">Continue Shopping</a>
    <div>
        <button class="btn" type="submit">Update Cart</button>
        <a href="cart.php?action=clear" class="text-link">Clear</a>
    </div>
</div>
<div class="checkout-box">
    <h2>Cart Total</h2>
    <div><span>Total</span><strong>৳<?= number_format($total, 2) ?></strong></div>
    <a class="btn full" href="checkout.php">Proceed To Checkout</a>
</div>
</form>
<?php endif; ?>
</section>
<?php require_once "includes/footer.php"; ?>
