<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telp     = trim($_POST['telp']     ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirmasi']    ?? '';

    if (empty($nama) || empty($email) || empty($telp) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // cek email
      $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows > 0) {
          $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
          $stmt->close();
      } else {
          $stmt->close();
          // cek telepon
          $stmt = $conn->prepare("SELECT id FROM users WHERE telepon = ?");
          $stmt->bind_param('s', $telp);
          $stmt->execute();
          $stmt->store_result();
          if ($stmt->num_rows > 0) {
              $error = 'Nomor telepon sudah terdaftar. Silakan gunakan nomor lain.';
              $stmt->close();
          } else {
              $stmt->close();
              $hash = password_hash($password, PASSWORD_BCRYPT);
              $stmt = $conn->prepare("
                  INSERT INTO users
                  (nama, email, telepon, password, role, created_at)
                  VALUES (?, ?, ?, ?, 'user', NOW())
              ");
              $stmt->bind_param('ssss', $nama, $email, $telp, $hash);
              if ($stmt->execute()) {
                  $success = 'Akun berhasil dibuat! Silakan login.';
              } else {
                  $error = 'Terjadi kesalahan. Silakan coba lagi.';
              }
              $stmt->close();
          }
      }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <a href="../index.php">Pesona<span>NTB</span></a>
      <p>Sistem Informasi Wisata NTB</p>
    </div>

    <h2 class="auth-title">Buat Akun Baru</h2>
    <p class="auth-sub">Daftar gratis dan jelajahi destinasi wisata NTB.</p>

    <div id="alertBox" class="alert <?= $error ? 'alert-error show' : ($success ? 'alert-success show' : '') ?>">
      <?= htmlspecialchars($error ?: $success) ?>
    </div>

    <?php if (!$success): ?>
    <form id="registerForm" method="POST" action="register.php" novalidate>

      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap"
               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" autocomplete="name">
        <span class="form-error" id="namaError"></span>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="contoh@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email">
        <span class="form-error" id="emailError"></span>
      </div>

      <div class="form-group">
        <label for="telp">Nomor Telepon</label>
        <input type="tel" id="telp" name="telp" placeholder="08xxxxxxxxxx"
               value="<?= htmlspecialchars($_POST['telp'] ?? '') ?>" autocomplete="tel">
        <span class="form-error" id="telpError"></span>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="Min. 8 karakter" autocomplete="new-password">
            <button type="button" class="toggle-pass" aria-label="Toggle password">👁️</button>
          </div>
          <span class="form-error" id="passwordError"></span>
        </div>
        <div class="form-group">
          <label for="konfirmasi">Konfirmasi Password</label>
          <div class="input-wrap">
            <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password" autocomplete="new-password">
            <button type="button" class="toggle-pass" aria-label="Toggle password">👁️</button>
          </div>
          <span class="form-error" id="konfirmasiError"></span>
        </div>
      </div>

      <div class="terms-check">
        <input type="checkbox" id="terms" name="terms">
        <label for="terms">Saya menyetujui <a href="syarat.php" target="_blank">syarat &amp; ketentuan</a> yang berlaku</label>
      </div>

      <button type="submit" class="btn-auth">Daftar Sekarang</button>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>

    <?php else: ?>
    <div style="text-align:center;padding:1rem 0">
      <a href="login.php" class="btn-auth" style="display:inline-block;width:auto;padding:0.85rem 2rem;text-decoration:none">Masuk Sekarang</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<script src="../js/auth.js"></script>
</body>
</html>