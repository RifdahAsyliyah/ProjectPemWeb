<?php
// Pastikan session sudah berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_nama   = $_SESSION['user_nama'] ?? 'Admin';
$inisial      = strtoupper(mb_substr($admin_nama, 0, 2));

// AMBIL FOTO LANGSUNG DARI DATABASEsecara mandiri
$foto_profil_admin = '';
if (!isset($conn)) {
    if (file_exists('koneksi.php')) {
        include 'koneksi.php';
    } elseif (file_exists('koneksi.php')) {
        include 'koneksi.php';
    }
}

// Jika koneksi database berhasil ditemukan/di-include, jalankan query
if (isset($conn) && isset($_SESSION['user_id'])) {
    $stmt_sidebar = $conn->prepare("SELECT foto_profil FROM users WHERE id = ?");
    $stmt_sidebar->bind_param('i', $_SESSION['user_id']);
    $stmt_sidebar->execute();
    $result_sidebar = $stmt_sidebar->get_result()->fetch_assoc();
    $foto_profil_admin = $result_sidebar['foto_profil'] ?? '';
    $stmt_sidebar->close();
}
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <a href="../index.php">Pesona<span>NTB</span></a>
    <button class="sidebar-close" id="sidebarClose">✕</button>
  </div>

  <a href="profil.php" class="sidebar-admin-link" style="text-decoration: none; color: inherit; display: block;">
    <div class="sidebar-admin">
      
      <?php if (!empty($foto_profil_admin)): ?>
        <div class="admin-avatar" style="background: none; overflow: hidden; padding: 0; display: flex; align-items: center; justify-content: center;">
          <img src="../assets/uploads/profil/<?= htmlspecialchars($foto_profil_admin) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" alt="">
        </div>
      <?php else: ?>
        <div class="admin-avatar"><?= $inisial ?></div>
      <?php endif; ?>

      <div class="admin-info">
        <div class="admin-name"><?= htmlspecialchars($admin_nama) ?></div>
        <div class="admin-role">Administrator</div>
      </div>
    </div>
  </a>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Menu Utama</div>
    <a href="dashboard.php" class="sidebar-link <?= $current_page=='dashboard.php'?'active':'' ?>">
      <span class="link-icon">📊</span> Dashboard
    </a>
    <a href="wisata.php" class="sidebar-link <?= $current_page=='wisata.php'||$current_page=='wisata_form.php'?'active':'' ?>">
      <span class="link-icon">🏝️</span> Kelola Wisata
    </a>
    <a href="kategori.php" class="sidebar-link <?= $current_page=='kategori.php'?'active':'' ?>">
      <span class="link-icon">🏷️</span> Kelola Kategori
    </a>

    <div class="nav-group-label">Pengguna</div>
    <a href="pengguna.php" class="sidebar-link <?= $current_page=='pengguna.php'?'active':'' ?>">
      <span class="link-icon">👥</span> Kelola Pengguna
    </a>
    <a href="ulasan.php" class="sidebar-link <?= $current_page=='ulasan.php'?'active':'' ?>">
      <span class="link-icon">⭐</span> Kelola Ulasan
    </a>

    <div class="nav-group-label">Akun</div>
    <a href="../index.php" class="sidebar-link">
      <span class="link-icon">🌐</span> Lihat Website
    </a>
    <a href="logout.php" class="sidebar-link logout-link">
      <span class="link-icon">🚪</span> Keluar
    </a>
  </nav>
</aside>