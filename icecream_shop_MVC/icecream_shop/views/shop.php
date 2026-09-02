<?php
$pageTitle = "Shop";
require_once "includes/header.php";

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $like = "%$search%";
    $params[] = $like; $params[] = $like;
    $types .= "ss";
}
if ($category !== "") {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$cats = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
?>
<section class="page-banner">
    <p class="eyebrow">Fresh Every Day</p>
    <h1>Our Ice Cream Shop</h1>
    <p>Choose your favorite flavor and add it to your cart.</p>
</section>

<section class="section">
    <div class="filter-row">
        <a href="shop.php" class="<?= $category === '' ? 'selected' : '' ?>">All</a>
        <?php while ($cat = $cats->fetch_assoc()): ?>
            <a href="shop.php?category=<?= urlencode($cat['category']) ?>" class="<?= $category === $cat['category'] ? 'selected' : '' ?>">
                <?= e($cat['category']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <?php if ($search !== ""): ?>
        <p class="result-note">Search results for <b><?= e($search) ?></b></p>
    <?php endif; ?>

    <div class="product-grid">
        <?php if ($result->num_rows === 0): ?>
            <div class="empty-state"><h2>No products found</h2><p>Try another search or category.</p></div>
        <?php endif; ?>

        <?php while ($product = $result->fetch_assoc()): ?>
        <article class="product-card">
            <a href="product.php?id=<?= (int)$product['id'] ?>">
                <img src="images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            </a>
            <div class="product-info">
                <span class="tag"><?= e($product['category']) ?></span>
                <h3><?= e($product['name']) ?></h3>
                <p><?= e($product['description']) ?></p>
                <div class="product-bottom">
                    <strong>৳<?= number_format($product['price'], 2) ?></strong>
                    <a class="small-btn" href="cart.php?action=add&id=<?= (int)$product['id'] ?>">Add to Cart</a>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</section>
<?php require_once "includes/footer.php"; ?>
