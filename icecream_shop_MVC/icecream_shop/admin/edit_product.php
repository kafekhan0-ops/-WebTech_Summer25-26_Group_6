<?php
require_once "header.php";
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id); $stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) { header("Location: products.php"); exit; }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? 'vanilla.svg');

    if ($name && $category && $price > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("ssdssi", $name, $category, $price, $description, $image, $id);
        $stmt->execute();
        flash('success', 'Product updated.');
        header("Location: products.php"); exit;
    }
    $error = "Please fill the required fields.";
}
?>
<section class="admin-content"><div class="form-card admin-form"><h2>Edit Product</h2>
<?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<label>Name</label><input name="name" value="<?= e($product['name']) ?>" required>
<label>Category</label><input name="category" value="<?= e($product['category']) ?>" required>
<label>Price</label><input type="number" step="0.01" name="price" value="<?= e($product['price']) ?>" required>
<label>Description</label><textarea name="description" rows="5"><?= e($product['description']) ?></textarea>
<label>Image filename</label><input name="image" value="<?= e($product['image']) ?>">
<button class="btn">Update Product</button> <a class="btn btn-light" href="products.php">Cancel</a>
</form></div></section>
<?php require_once "footer.php"; ?>
