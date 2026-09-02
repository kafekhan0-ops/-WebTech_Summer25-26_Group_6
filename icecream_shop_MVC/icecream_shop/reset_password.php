<?php
require_once "includes/header.php";
if(isLoggedIn()){ header('Location: account.php'); exit; }
$error=''; $done='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $new=$_POST['new_password']??''; $confirm=$_POST['confirm_password']??'';
    if(!filter_var($email,FILTER_VALIDATE_EMAIL) || $phone==='' || strlen($new)<6 || $new!==$confirm) $error='Enter your registered email, phone, and matching password of at least 6 characters.';
    else { $s=$conn->prepare("SELECT id FROM users WHERE email=? AND phone=? LIMIT 1"); $s->bind_param('ss',$email,$phone); $s->execute(); $u=$s->get_result()->fetch_assoc(); if(!$u)$error='No account matched that email and phone number.'; else { $hash=password_hash($new,PASSWORD_DEFAULT); $up=$conn->prepare("UPDATE users SET password=? WHERE id=?"); $up->bind_param('si',$hash,$u['id']); $up->execute(); $done='Password reset successfully. You can now login.'; } }
}
?>
<section class="auth-section"><div class="form-card auth-card wide"><div class="auth-icon">🔑</div><h1>Reset Password</h1><p>Verify your registered email and phone number.</p><?php if($error):?><div class="form-error"><?=e($error)?></div><?php endif;?><?php if($done):?><div class="flash success"><?=e($done)?></div><a class="btn full" href="login.php">Go to Login</a><?php else:?><form method="post"><label>Registered Email</label><input type="email" name="email" required><label>Registered Phone</label><input type="text" name="phone" required><label>New Password</label><input type="password" name="new_password" minlength="6" required><label>Confirm Password</label><input type="password" name="confirm_password" minlength="6" required><button class="btn full" type="submit">Reset Password</button></form><?php endif;?><p class="form-foot"><a href="login.php">← Back to Login</a></p></div></section>
<?php require_once "includes/footer.php"; ?>
