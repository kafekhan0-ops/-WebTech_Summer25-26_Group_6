<?php
require_once "includes/header.php";
requireLogin();
$userId=(int)$_SESSION['user_id'];
$success=''; $error='';

$stmt=$conn->prepare("SELECT id,name,email,phone,address,password FROM users WHERE id=? LIMIT 1");
$stmt->bind_param('i',$userId); $stmt->execute(); $user=$stmt->get_result()->fetch_assoc();
if(!$user){ session_destroy(); header('Location: login.php'); exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if($action==='profile'){
        $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $address=trim($_POST['address']??'');
        if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Please enter a valid name and email.';
        else {
            $check=$conn->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1"); $check->bind_param('si',$email,$userId); $check->execute();
            if($check->get_result()->num_rows) $error='That email is already used by another account.';
            else { $u=$conn->prepare("UPDATE users SET name=?,email=?,phone=?,address=? WHERE id=?"); $u->bind_param('ssssi',$name,$email,$phone,$address,$userId); if($u->execute()){$_SESSION['user_name']=$name; flash('success','Profile updated successfully.'); header('Location: account.php'); exit;} $error='Could not update profile.'; }
        }
    } elseif($action==='password'){
        $current=$_POST['current_password']??''; $new=$_POST['new_password']??''; $confirm=$_POST['confirm_password']??'';
        if(!password_verify($current,$user['password'])) $error='Current password is incorrect.';
        elseif(strlen($new)<6) $error='New password must be at least 6 characters.';
        elseif($new!==$confirm) $error='New passwords do not match.';
        else { $hash=password_hash($new,PASSWORD_DEFAULT); $u=$conn->prepare("UPDATE users SET password=? WHERE id=?"); $u->bind_param('si',$hash,$userId); $u->execute(); flash('success','Password changed successfully.'); header('Location: account.php'); exit; }
    } elseif($action==='delete'){
        $password=$_POST['delete_password']??'';
        if(!password_verify($password,$user['password'])) $error='Enter your current password to delete the account.';
        else { $d=$conn->prepare("DELETE FROM users WHERE id=?"); $d->bind_param('i',$userId); $d->execute(); session_unset(); session_destroy(); header('Location: index.php?account_deleted=1'); exit; }
    }
}
?>
<section class="page-banner small-banner"><p class="eyebrow">Account Management</p><h1>My Account</h1><p>View and manage your profile and password.</p></section>
<section class="section account-grid">
<div class="form-card account-card"><div class="auth-icon">👤</div><h2>Profile Information</h2><p class="hint">View and edit your personal details.</p>
<?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="action" value="profile"><label>Full Name</label><input name="name" value="<?=e($user['name'])?>" required><label>Email</label><input type="email" name="email" value="<?=e($user['email'])?>" required><label>Phone</label><input name="phone" value="<?=e($user['phone'])?>"><label>Address</label><textarea name="address" rows="4"><?=e($user['address'])?></textarea><button class="btn" type="submit">Save Profile</button></form></div>
<div class="form-card account-card"><div class="auth-icon">🔐</div><h2>Change Password</h2><p class="hint">Use your current password to set a new one.</p><form method="post"><input type="hidden" name="action" value="password"><label>Current Password</label><input type="password" name="current_password" required><label>New Password</label><input type="password" name="new_password" minlength="6" required><label>Confirm New Password</label><input type="password" name="confirm_password" minlength="6" required><button class="btn" type="submit">Change Password</button></form><p class="form-foot"><a href="reset_password.php">Forgot your password? Reset it</a></p></div>
<div class="form-card account-card danger-card"><div class="auth-icon">⚠️</div><h2>Delete Account</h2><p class="hint">This permanently removes your customer account and its orders.</p><form method="post" onsubmit="return confirm('Delete your account permanently? This cannot be undone.');"><input type="hidden" name="action" value="delete"><label>Current Password</label><input type="password" name="delete_password" required><button class="btn danger-btn" type="submit">Delete My Account</button></form></div>
</section>
<?php require_once "includes/footer.php"; ?>
