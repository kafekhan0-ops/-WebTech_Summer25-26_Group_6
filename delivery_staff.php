<?php
require_once "header.php";
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action']??''; $id=(int)($_POST['id']??0);
    if($action==='add') {
        $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $password=$_POST['password']??'';
        if($name===''||$email===''||$password==='') $error='Name, email and password are required.';
        else { $hash=password_hash($password,PASSWORD_DEFAULT); $st=$conn->prepare("INSERT INTO delivery_staff(name,email,phone,password,status) VALUES(?,?,?,?, 'Active')"); $st->bind_param('ssss',$name,$email,$phone,$hash); if(!$st->execute()) $error='Could not add staff. Email may already exist.'; else {flash('success','Delivery staff added.'); header('Location: delivery_staff.php');exit;} }
    } elseif($action==='toggle' && $id>0) { $st=$conn->prepare("UPDATE delivery_staff SET status=IF(status='Active','Inactive','Active') WHERE id=?"); $st->bind_param('i',$id); $st->execute(); flash('success','Delivery staff status changed.'); header('Location: delivery_staff.php');exit; }
}
$rows=$conn->query("SELECT id,name,email,phone,status,created_at FROM delivery_staff ORDER BY id DESC");
?>
<section class="admin-content"><div class="panel-heading"><div><h2>Delivery Staff</h2><p class="hint">Create and manage the third actor of the system.</p></div></div>
<?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?>
<div class="delivery-admin-grid"><form method="post" class="form-card admin-form"><input type="hidden" name="action" value="add"><h3>Add Delivery Staff</h3><label>Name</label><input name="name" required><label>Email</label><input type="email" name="email" required><label>Phone</label><input name="phone"><label>Password</label><input type="password" name="password" required><button class="btn" type="submit">＋ Add Staff</button></form>
<div class="admin-panel"><table class="admin-table"><thead><tr><th>Name</th><th>Contact</th><th>Status</th><th>Action</th></tr></thead><tbody><?php while($d=$rows->fetch_assoc()):?><tr><td><b><?=e($d['name'])?></b><br><small><?=e($d['email'])?></small></td><td><?=e($d['phone'])?></td><td><span class="delivery-status <?=strtolower(e($d['status']))?>"><?=e($d['status'])?></span></td><td><form method="post"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=intval($d['id'])?>"><button class="small-btn" type="submit">Toggle Status</button></form></td></tr><?php endwhile;?></tbody></table></div></div></section>
<?php require_once "footer.php"; ?>
