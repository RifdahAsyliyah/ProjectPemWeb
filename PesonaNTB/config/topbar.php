<?php
$titles = [
  'dashboard.php'   => 'Dashboard',
  'wisata.php'      => 'Kelola Wisata',
  'wisata_form.php' => 'Form Wisata',
  'kategori.php'    => 'Kelola Kategori',
  'pengguna.php'    => 'Kelola Pengguna',
  'ulasan.php'      => 'Kelola Ulasan',
];
$page_title = $titles[basename($_SERVER['PHP_SELF'])] ?? 'Admin Panel';
?>
<header class="admin-topbar">
  <button class="topbar-toggle" id="sidebarToggle">☰</button>
  <div class="topbar-title"><?= $page_title ?></div>
  <div class="topbar-right">
    <a href="../index.php" class="topbar-web-btn">🌐 Lihat Web</a>
    <a href="logout.php" class="topbar-logout">Keluar</a>
  </div>
</header>