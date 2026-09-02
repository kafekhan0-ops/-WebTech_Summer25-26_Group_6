<?php
require_once "header.php";
$adminId=(int)$_SESSION['admin_id']; $error='';
$s=$conn->prepare("SELECT id,name,email,password FROM admins WHERE id=? LIMIT 1"); $s->bind_param('i',$adminId); $s->execute(); $admin=$s->get_result()->fetch_assoc();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action']??'';
  if($action==='profile'){
    $name=trim($_POST['name']??''); $email=trim($_POST['email']??'');
    $c=$conn->prepare("SELECT id FROM admins WHERE email=? AND id<>? LIMIT 1"); $c->bind_param('si',$email,$adminId); $c->execute();
    if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Enter a valid name and email.';
    elseif($c->get_result()->num_rows) $error='That email is already in use.';
    else { $u=$conn->prepare("UPDATE admins SET name=?,email=? WHERE id=?"); $u->bind_param('ssi',$name,$email,$adminId); $u->execute(); $_SESSION['admin_name']=$name; flash('success','Admin profile updated.'); header('Location: profile.php'); exit; }
  } elseif($action==='password'){
    $cur=$_POST['current_password']??'';$new=$_POST['new_password']??'';$con=$_POST['confirm_password']??'';
    if(!password_verify($cur,$admin['password']))$error='Current password is incorrect.'; elseif(strlen($new)<6||$new!==$con)$error='Check the new password fields.'; else {$h=password_hash($new,PASSWORD_DEFAULT);$u=$conn->prepare("UPDATE admins SET password=? WHERE id=?");$u->bind_param('si',$h,$adminId);$u->execute();flash('success','Admin password changed.');header('Location: profile.php');exit;}
  }
}
?>
<section class="admin-content"><div class="panel-heading"><div><h2>Account Management</h2><p class="hint">Manage administrator profile and password.</p></div></div><?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?><div class="account-grid admin-account-grid"><div class="form-card account-card"><div class="auth-icon">👨‍💼</div><h2>Profile Information</h2><form method="post"><input type="hidden" name="action" value="profile"><label>Name</label><input name="name" value="<?=e($admin['name'])?>" required><label>Email</label><input type="email" name="email" value="<?=e($admin['email'])?>" required><button class="btn" type="submit">Save Profile</button></form></div><div class="form-card account-card"><div class="auth-icon">🔐</div><h2>Change Password</h2><form method="post"><input type="hidden" name="action" value="password"><label>Current Password</label><input type="password" name="current_password" required><label>New Password</label><input type="password" name="new_password" minlength="6" required><label>Confirm New Password</label><input type="password" name="confirm_password" minlength="6" required><button class="btn" type="submit">Change Password</button></form></div></div></section>
<?php require_once "footer.php"; ?>
