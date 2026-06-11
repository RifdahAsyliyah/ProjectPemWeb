<?php
session_start();
require_once 'db.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id,nama,email FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($user){

        $token = bin2hex(random_bytes(32));
        $expired = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $stmt = $conn->prepare("
        UPDATE users
        SET reset_token=?,
            reset_expired=?
        WHERE id=?
        ");

        $stmt->bind_param(
            "ssi",
            $token,
            $expired,
            $user['id']
        );

        $stmt->execute();
        $stmt->close();

        $link = "http://localhost/ProjectPemWeb/ProjectPemWeb/PesonaNTB/config/reset_password.php?token=".$token;

        $msg = "
            Password berhasil direset.<br><br>
            <a href='$link' class='btn-auth' style='display:inline-block;text-decoration:none'> Reset Password </a>";
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Lupa Password</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-page">
<div class="auth-card">

<h2 class="auth-title">Lupa Password</h2>

<?php if($error): ?>
<div class="alert alert-error show">
<?= $error ?>
</div>
<?php endif; ?>

<?php if($msg): ?>
<div class="alert alert-success show">
<?= $msg ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<button class="btn-auth" type="submit">
Kirim Link Reset
</button>

</form>

<div style="margin-top:15px">
    <a href="login.php"
       style="
       display:block;
       text-align:center;
       text-decoration:none;
       padding:12px 20px;
       border:2px solid #6B4B3E;
       color:#6B4B3E;
       border-radius:12px;
       font-weight:600;
       transition:0.3s;
       ">
        Kembali ke Login
    </a>
</div>

</div>
</div>

</body>
</html>