<?php
session_start();
require_once "config/database.php";
require_once "includes/functions.php";

if (isset($_GET['logout'])) {
    unset($_SESSION['delivery_id'], $_SESSION['delivery_name']);
    header('Location: delivery.php'); exit;
}
if (isset($_SESSION['delivery_id'])) {
    $staffId = (int)$_SESSION['delivery_id'];
    $stmt = $conn->prepare("SELECT id,name,email,phone,status FROM delivery_staff WHERE id=? LIMIT 1");
    $stmt->bind_param('i',$staffId); $stmt->execute(); $staff=$stmt->get_result()->fetch_assoc();
    if (!$staff || $staff['status'] !== 'Active') { unset($_SESSION['delivery_id'],$_SESSION['delivery_name']); header('Location: delivery.php'); exit; }

    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $orderId=(int)($_POST['order_id']??0); $status=$_POST['status']??'';
        $allowed=['Processing','Out for Delivery','Delivered'];
        if ($orderId>0 && in_array($status,$allowed,true)) {
            $u=$conn->prepare("UPDATE orders SET status=? WHERE id=? AND delivery_staff_id=?");
            $u->bind_param('sii',$status,$orderId,$staffId); $u->execute();
            flash('success','Order status updated.'); header('Location: delivery.php'); exit;
        }
    }
    $orders=$conn->prepare("SELECT id,customer_name,phone,address,total_amount,payment_method,status,created_at FROM orders WHERE delivery_staff_id=? AND status <> 'Cancelled' ORDER BY created_at DESC");
    $orders->bind_param('i',$staffId); $orders->execute(); $rows=$orders->get_result();
    $counts=['all'=>0,'pending'=>0,'out'=>0,'delivered'=>0];
    $list=[]; while($o=$rows->fetch_assoc()){ $list[]=$o; $counts['all']++; if($o['status']==='Pending')$counts['pending']++; if($o['status']==='Out for Delivery')$counts['out']++; if($o['status']==='Delivered')$counts['delivered']++; }
    ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Delivery Staff | Ice Cream Delights</title><link rel="stylesheet" href="css/style.css"></head><body class="delivery-body">
    <header class="delivery-top"><div class="brand"><span class="brand-cone">🍦</span><span><b>ICE CREAM</b><b>DELIGHTS</b></span></div><div><a class="btn btn-light" href="delivery_profile.php">My Account</a><a class="btn btn-light" href="index.php">Website</a><a class="btn" href="delivery.php?logout=1">Logout</a></div></header>
    <main class="delivery-main"><section class="delivery-welcome"><div><p class="eyebrow">Delivery Staff Panel</p><h1>Welcome, <?=e($staff['name'])?>!</h1><p>Manage your assigned customer deliveries from one place.</p></div><div class="delivery-badge">🚴‍♂️<span>Active Staff</span></div></section>
    <section class="delivery-cards"><div><span>Assigned Orders</span><strong><?=$counts['all']?></strong></div><div><span>Pending</span><strong><?=$counts['pending']?></strong></div><div><span>Out for Delivery</span><strong><?=$counts['out']?></strong></div><div><span>Delivered</span><strong><?=$counts['delivered']?></strong></div></section>
    <?php if($msg=getFlash('success')):?><div class="flash success"><?=e($msg)?></div><?php endif; ?>
    <section class="delivery-panel"><div class="panel-heading"><h2>Assigned Orders</h2><span><?=e($staff['phone'])?></span></div><div class="delivery-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Address</th><th>Total</th><th>Status</th><th>Update</th></tr></thead><tbody>
    <?php if(!$list):?><tr><td colspan="6">No orders have been assigned to you yet.</td></tr><?php else: foreach($list as $o):?><tr><td>#<?=intval($o['id'])?><br><small><?=e($o['created_at'])?></small></td><td><b><?=e($o['customer_name'])?></b><br><?=e($o['phone'])?></td><td><?=e($o['address'])?></td><td>৳<?=number_format($o['total_amount'],2)?><br><small><?=e($o['payment_method'])?></small></td><td><span class="delivery-status <?=strtolower(str_replace(' ','-',e($o['status'])))?>"><?=e($o['status'])?></span></td><td><?php if($o['status']!=='Delivered'):?><form method="post"><input type="hidden" name="order_id" value="<?=intval($o['id'])?>"><select name="status"><option value="Processing">Processing</option><option value="Out for Delivery" <?= $o['status']==='Out for Delivery'?'selected':'' ?>>Out for Delivery</option><option value="Delivered">Delivered</option></select><button class="small-btn" type="submit">Update</button></form><?php else:?><b>✓ Complete</b><?php endif;?></td></tr><?php endforeach; endif;?></tbody></table></div></section></main></body></html><?php exit;
}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email=trim($_POST['email']??''); $password=$_POST['password']??'';
    $stmt=$conn->prepare("SELECT id,name,email,password,status FROM delivery_staff WHERE email=? LIMIT 1"); $stmt->bind_param('s',$email); $stmt->execute(); $staff=$stmt->get_result()->fetch_assoc();
    if($staff && $staff['status']==='Active' && password_verify($password,$staff['password'])){session_regenerate_id(true);$_SESSION['delivery_id']=$staff['id'];$_SESSION['delivery_name']=$staff['name'];header('Location: delivery.php');exit;}
    $error='Invalid delivery staff login.';
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Delivery Staff Login</title><link rel="stylesheet" href="css/style.css"></head><body class="admin-login-page"><div class="form-card auth-card"><div class="auth-icon">🚴‍♂️</div><h1>Delivery Staff Login</h1><p>Ice Cream Delights Delivery Management</p><?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?><form method="post"><label>Email</label><input type="email" name="email" value="delivery@icecream.com" required><label>Password</label><input type="password" name="password" value="delivery123" required><button class="btn full" type="submit">Login</button></form><p class="form-foot">Demo: delivery@icecream.com / delivery123</p><a class="form-foot" href="index.php">← Back to website</a></div></body></html>
