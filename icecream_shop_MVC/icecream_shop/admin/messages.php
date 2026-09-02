<?php
require_once "header.php";
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<section class="admin-content"><div class="panel-heading"><h2>Contact Messages</h2></div>
<div class="admin-panel"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th></tr></thead><tbody>
<?php while ($m = $result->fetch_assoc()): ?>
<tr><td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= e($m['subject']) ?></td><td><?= e($m['message']) ?></td><td><?= e($m['created_at']) ?></td></tr>
<?php endwhile; ?>
</tbody></table></div></section>
<?php require_once "footer.php"; ?>
