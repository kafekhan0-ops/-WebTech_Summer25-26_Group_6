<?php
require_once "header.php";
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<section class="admin-content">
<div class="panel-heading"><h2>Products</h2><a class="btn" href="add_product.php">+ Add Product</a></div>
<div class="admin-panel">
<table class="admin-table"><thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead><tbody>
<?php while ($p = $result->fetch_assoc()): ?>
<tr>
<td><img class="admin-thumb" src="../images/<?= e($p['image']) ?>"></td>
<td><?= e($p['name']) ?></td><td><?= e($p['category']) ?></td><td>৳<?= number_format($p['price'],2) ?></td>
<td><a class="action-link" href="edit_product.php?id=<?= (int)$p['id'] ?>">Edit</a> <a class="danger-link" onclick="return confirm('Delete this product?')" href="delete_product.php?id=<?= (int)$p['id'] ?>">Delete</a></td>
</tr>
<?php endwhile; ?>
</tbody></table>
</div></section>
<?php require_once "footer.php"; ?>
