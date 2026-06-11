<?php
session_start();
require_once 'db.php';

$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("
SELECT id
FROM users
WHERE reset_token=?
");

$stmt->bind_param("s",$token);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$user){
    die("Link reset password tidak valid atau sudah kadaluarsa.");
}

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if($password !== $confirm){
        $error = "Konfirmasi password tidak sama.";
    }
    elseif(strlen($password) < 6){
        $error = "Password minimal 6 karakter.";
    }
    else{

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare("
        UPDATE users
        SET password=?,
            reset_token=NULL,
            reset_expired=NULL
        WHERE id=?
        ");

        $stmt->bind_param(
            "si",
            $hash,
            $user['id']
        );

        $stmt->execute();
        $stmt->close();

        $success = "Password berhasil diubah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-page">
<div class="auth-card">

<h2 class="auth-title">
Reset Password
</h2>

<?php if($error): ?>
<div class="alert alert-error show">
<?= $error ?>
</div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert alert-success show">
    <?= $success ?>

    <div style="margin-top:20px;">
        <a href="login.php" class="btn-auth" style="text-decoration:none;display:block;text-align:center;">
            Login Sekarang
        </a>
    </div>
</div>
<?php endif; ?>

<?php if(!$success): ?>

<form method="POST">

<div class="form-group">
    <label>Password Baru</label>
    <input type="password" name="password" required>
</div>

<div class="form-group">
    <label>Konfirmasi Password</label>
    <input type="password" name="confirm" required>
</div>

<button type="submit" class="btn-auth">
    Simpan Password
</button>

</form>

<?php endif; ?>

</div>
</div>

</body>
</html>