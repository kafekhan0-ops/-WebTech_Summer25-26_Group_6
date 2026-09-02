<?php
$pageTitle = "Home";
require_once "includes/header.php";
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Welcome To The</p>
        <h1>Classic Ice<br><span>Cream Parlor</span></h1>
        <p class="hero-text">Savor our artisanal ice cream scoops served on fresh, crispy homemade waffle cones.</p>
        <div class="hero-buttons">
            <a href="shop.php" class="btn">Order Now</a>
            <a href="about.php" class="btn btn-light">Learn More</a>
        <a class="btn btn-light" href="admin/">Admin Login</a>
        <a class="btn btn-light" href="delivery.php">Delivery Staff</a></div>
    </div>
    <div class="hero-art">
        <div class="plate"></div>
        <div class="scoop scoop-vanilla"></div>
        <div class="scoop scoop-strawberry"></div>
        <div class="scoop scoop-mint"></div>
        <div class="scoop scoop-blue"></div>
        <div class="waffle waffle-1"></div>
        <div class="waffle waffle-2"></div>
        <div class="cherry cherry-1"></div>
        <div class="cherry cherry-2"></div>
        <div class="cherry cherry-3"></div>
        <div class="sprinkle s1"></div><div class="sprinkle s2"></div><div class="sprinkle s3"></div>
    </div>
</section>

<section class="section showcase-section">
    <div class="section-heading"><p class="eyebrow">Complete Management System</p><h2>One Shop, Three Powerful Roles</h2><p>Customer ordering, admin management and delivery tracking work together in one responsive system.</p></div>
    <div class="showcase-card"><img src="images/project-showcase.png" alt="Ice Cream Delights project showcase with customer, admin and delivery panels"></div>
</section>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Our Favorites</p>
        <h2>Made For Sweet Moments</h2>
    </div>
    <div class="product-grid">
        <?php
        $result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
        while ($product = $result->fetch_assoc()):
        ?>
        <article class="product-card">
            <a href="product.php?id=<?= (int)$product['id'] ?>">
                <img src="images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            </a>
            <div class="product-info">
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
