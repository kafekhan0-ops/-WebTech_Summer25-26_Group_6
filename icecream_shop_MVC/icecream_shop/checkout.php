<?php
require_once "includes/header.php";
requireLogin();

if (empty($_SESSION['cart'])) {
    flash('error', 'Your cart is empty.');
    header("Location: shop.php");
    exit;
}

$userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->bind_param("i", $_SESSION['user_id']);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$total = cartTotal($conn);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment = $_POST['payment'] ?? 'Cash on Delivery';

    if ($name === '' || $phone === '' || $address === '') {
        $error = "Please complete all required fields.";
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, phone, address, payment_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("issssd", $_SESSION['user_id'], $name, $phone, $address, $payment, $total);
            $stmt->execute();
            $orderId = $conn->insert_id;

            $ids = array_map('intval', array_keys($_SESSION['cart']));
            $idList = implode(',', $ids);
            $result = $conn->query("SELECT id, price FROM products WHERE id IN ($idList)");
            $prices = [];
            while ($row = $result->fetch_assoc()) $prices[$row['id']] = $row['price'];

            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $price = (float)$prices[$pid];
                $pid = (int)$pid;
                $qty = (int)$qty;
                $itemStmt->bind_param("iiid", $orderId, $pid, $qty, $price);
                $itemStmt->execute();
            }

            $conn->commit();
            $_SESSION['cart'] = [];
            flash('success', "Order #$orderId placed successfully.");
            header("Location: order.php");
            exit;
        } catch (Exception $ex) {
            $conn->rollback();
            $error = "Could not place the order. Please try again.";
        }
    }
}
?>
<section class="page-banner small-banner">
    <p class="eyebrow">Almost There</p>
    <h1>Checkout</h1>
</section>
<section class="section checkout-grid">
    <form method="post" class="form-card">
        <h2>Delivery Details</h2>
        <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
        <label>Full Name *</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? $user['name']) ?>" required>
        <label>Phone *</label>
        <input type="text" name="phone" value="<?= e($_POST['phone'] ?? $user['phone']) ?>" required>
        <label>Delivery Address *</label>
        <textarea name="address" rows="5" required><?= e($_POST['address'] ?? $user['address']) ?></textarea>
        <label>Payment Method</label>
        <select name="payment">
            <option>Cash on Delivery</option>
            <option>Mobile Banking</option>
        </select>
        <button class="btn full" type="submit">Place Order</button>
    </form>
    <div class="summary-card">
        <h2>Order Summary</h2>
        <?php
        foreach ($_SESSION['cart'] as $pid => $qty):
            $pid = (int)$pid;
            $s = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
            $s->bind_param("i", $pid);
            $s->execute();
            $p = $s->get_result()->fetch_assoc();
            if (!$p) continue;
        ?>
        <div class="summary-row"><span><?= e($p['name']) ?> × <?= (int)$qty ?></span><b>৳<?= number_format($p['price']*$qty, 2) ?></b></div>
        <?php endforeach; ?>
        <hr>
        <div class="summary-total"><span>Total</span><strong>৳<?= number_format($total, 2) ?></strong></div>
    </div>
</section>
<?php require_once "includes/footer.php"; ?>
