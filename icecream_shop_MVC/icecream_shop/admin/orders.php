<?php
require_once "header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $delivery = (int)($_POST['delivery_staff_id'] ?? 0);
    $allowed = ['Pending','Processing','Out for Delivery','Delivered','Cancelled'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        if ($delivery > 0) {
            $stmt = $conn->prepare("UPDATE orders SET status=?, delivery_staff_id=? WHERE id=?");
            $stmt->bind_param("sii", $status, $delivery, $id);
        } else {
            $stmt = $conn->prepare("UPDATE orders SET status=?, delivery_staff_id=NULL WHERE id=?");
            $stmt->bind_param("si", $status, $id);
        }
        $stmt->execute();
        flash('success', 'Order assignment/status updated.');
        header("Location: orders.php"); exit;
    }
}
$staffResult = $conn->query("SELECT id,name FROM delivery_staff WHERE status='Active' ORDER BY name");
$staff = []; while ($d = $staffResult->fetch_assoc()) $staff[] = $d;
$result = $conn->query("SELECT o.*, d.name AS delivery_name FROM orders o LEFT JOIN delivery_staff d ON d.id=o.delivery_staff_id ORDER BY o.created_at DESC");
?>
<section class="admin-content"><div class="panel-heading"><div><h2>Orders & Delivery Assignment</h2><p class="hint">Assign an active delivery staff member and update the order workflow.</p></div></div>
<div class="admin-panel"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Contact</th><th>Total</th><th>Delivery Staff</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php while ($o = $result->fetch_assoc()): ?>
<tr><td>#<?= (int)$o['id'] ?></td><td><?= e($o['customer_name']) ?></td><td><?= e($o['phone']) ?><br><small><?= e($o['address']) ?></small></td><td>৳<?= number_format($o['total_amount'],2) ?></td>
<td><form method="post" class="inline-form"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>"><select name="delivery_staff_id"><option value="0">Not Assigned</option><?php foreach($staff as $d): ?><option value="<?= (int)$d['id'] ?>" <?= ((int)$o['delivery_staff_id']===(int)$d['id'])?'selected':'' ?>><?= e($d['name']) ?></option><?php endforeach; ?></select></td>
<td><select name="status"><?php foreach (['Pending','Processing','Out for Delivery','Delivered','Cancelled'] as $st): ?><option value="<?=e($st)?>" <?= $o['status']===$st?'selected':'' ?>><?=e($st)?></option><?php endforeach; ?></select><button class="small-btn" type="submit">Save</button></form><?php if($o['delivery_name']): ?><small>Assigned: <?=e($o['delivery_name'])?></small><?php endif; ?></td><td><?= e($o['created_at']) ?></td></tr>
<?php endwhile; ?>
</tbody></table></div></section>
<?php require_once "footer.php"; ?>
