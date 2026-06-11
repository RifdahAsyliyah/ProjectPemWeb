<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin'
    ? 'config/dashboard.php'
    : 'index.php';
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $stmt = $conn->prepare("SELECT id, nama, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['foto_profil'] = $user['foto_profil'];

            // Ambil parameter tujuan redirect
            $redirect = trim($_GET['redirect'] ?? ($_POST['redirect'] ?? ''));
            
            // VALIDASI REDIRECT: Cegah open-redirect ke situs luar
            // Jika redirect mengandung "http://" atau "https://", abaikan demi keamanan
            if (empty($redirect) || strpos($redirect, 'http://') === 0 || strpos($redirect, 'https://') === 0) {
                $redirect = $user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php';
            }
            
            header("Location: " . $redirect);
            exit;
        } else {
            $error = 'Email atau password salah. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="index.php">Pesona<span>NTB</span></a>
      <p>Sistem Informasi Wisata NTB</p>
    </div>

    <h2 class="auth-title">Selamat Datang Kembali</h2>
    <p class="auth-sub">Masuk untuk mengakses semua fitur PesonaNTB.</p>

    <!-- Alert error dari server -->
    <div id="alertBox" class="alert alert-error <?= $error ? 'show' : '' ?>">
      <?= htmlspecialchars($error) ?>
    </div>

    <form id="loginForm" method="POST" action="login.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>" novalidate>
      <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="contoh@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email">
        <span class="form-error" id="emailError"></span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password">
          <button type="button" class="toggle-pass" aria-label="Toggle password">👁️</button>
        </div>
        <span class="form-error" id="passwordError"></span>
      </div>

      <div class="forgot-link">
        <a href="#">Lupa password?</a>
      </div>

      <button type="submit" class="btn-auth">Masuk</button>
    </form>

    <div class="auth-divider"><span>atau</span></div>

    <p class="auth-switch">Belum punya akun? <a href="register.php">Daftar gratis</a></p>
  </div>
</div>

<script src="js/auth.js"></script>
</body>
</html>