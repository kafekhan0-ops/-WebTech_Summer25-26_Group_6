<?php
require_once "header.php";
$result = $conn->query("SELECT id,name,email,phone,address,created_at FROM users ORDER BY id DESC");
?>
<section class="admin-content"><div class="panel-heading"><h2>Customers</h2></div>
<div class="admin-panel"><table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Joined</th></tr></thead><tbody>
<?php while ($u = $result->fetch_assoc()): ?>
<tr><td><?= (int)$u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['phone']) ?></td><td><?= e($u['address']) ?></td><td><?= e($u['created_at']) ?></td></tr>
<?php endwhile; ?>
</tbody></table></div></section>
<?php require_once "footer.php"; ?>
