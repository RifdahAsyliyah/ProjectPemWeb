<?php
session_start();
include 'koneksi.php';

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN PESONA NTB
|--------------------------------------------------------------------------
| Email    : adminpesonantb@gmail.com
| Password : admin123
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: PesonaNTB/html/admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email === 'adminpesonantb@gmail.com' && $password === 'admin123') {
        $_SESSION['user_id'] = 0;
        $_SESSION['nama'] = 'Administrator';
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';

        header("Location: PesonaNTB/html/admin.php");
        exit();
    }

    $email = mysqli_real_escape_string($koneksi, $email);

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");

    if ($query && mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'user';

            header("Location: PesonaNTB/index.html");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Pesona NTB</title>

    <link rel="stylesheet" href="PesonaNTB/style/styleAuth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">Pesona NTB</div>
</nav>

<div class="auth-container">
    <div class="auth-card">
        <h2>Login Admin</h2>
        <p>Masuk untuk mengelola data wisata</p>

        <?php if (!empty($error)): ?>
            <p style="color:red; margin-bottom:15px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input
                type="email"
                name="email"
                placeholder="adminpesonantb@gmail.com"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button type="submit">Login</button>
        </form>

        <p class="switch">
            Kembali ke <a href="PesonaNTB/html/signin.html">Sign In</a>
        </p>
    </div>
</div>

</body>
</html>