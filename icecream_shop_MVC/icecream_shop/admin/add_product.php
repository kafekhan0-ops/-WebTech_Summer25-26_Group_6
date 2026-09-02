<?php
require_once "header.php";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? 'vanilla.svg');

    if ($name && $category && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $category, $price, $description, $image);
        $stmt->execute();
        flash('success', 'Product added.');
        header("Location: products.php");
        exit;
    }
    $error = "Please enter product name, category and a valid price.";
}
?>
<section class="admin-content"><div class="form-card admin-form"><h2>Add Product</h2>
<?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<label>Name</label><input name="name" required>
<label>Category</label><input name="category" placeholder="Classic / Fruit / Chocolate" required>
<label>Price</label><input type="number" step="0.01" name="price" required>
<label>Description</label><textarea name="description" rows="5"></textarea>
<label>Image filename</label><input name="image" value="vanilla.svg">
<p class="hint">Put the image file inside the project's images folder.</p>
<button class="btn" type="submit">Save Product</button> <a class="btn btn-light" href="products.php">Cancel</a>
</form></div></section>
<?php require_once "footer.php"; ?>
