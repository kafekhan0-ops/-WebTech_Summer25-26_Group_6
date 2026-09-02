<?php
require_once "includes/header.php";
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    flash('error', 'Product not found.');
    header("Location: shop.php");
    exit;
}
$pageTitle = $product['name'];
?>
<section class="section product-detail">
    <div class="detail-image">
        <img src="images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div class="detail-copy">
        <span class="tag"><?= e($product['category']) ?></span>
        <h1><?= e($product['name']) ?></h1>
        <div class="stars">★★★★★</div>
        <div class="detail-price">৳<?= number_format($product['price'], 2) ?></div>
        <p><?= e($product['description']) ?></p>
        <p>Freshly prepared and ready to make your day a little sweeter.</p>
        <form action="cart.php" method="get" class="quantity-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
            <label>Quantity</label>
            <input type="number" name="qty" value="1" min="1" max="20">
            <button class="btn" type="submit">Add To Cart</button>
        </form>
    </div>
</section>
<?php require_once "includes/footer.php"; ?>
