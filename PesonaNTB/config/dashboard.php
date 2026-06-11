<?php
session_start();
require_once '../config/db.php';
require_once 'auth_guard.php';

// Statistik
$total_wisata   = $conn->query("SELECT COUNT(*) as c FROM wisata")->fetch_assoc()['c'] ?? 0;
$total_user     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'] ?? 0;
$total_ulasan   = $conn->query("SELECT COUNT(*) as c FROM ulasan")->fetch_assoc()['c'] ?? 0;
$total_bookmark = $conn->query("SELECT COUNT(*) as c FROM bookmark")->fetch_assoc()['c'] ?? 0;
$total_kategori = $conn->query("SELECT COUNT(*) as c FROM kategori")->fetch_assoc()['c'] ?? 0;
$wisata_nonaktif= $conn->query("SELECT COUNT(*) as c FROM wisata WHERE aktif=0")->fetch_assoc()['c'] ?? 0;

// Wisata terbaru
$wisata_terbaru = [];
$res = $conn->query("SELECT id, nama, kategori, lokasi, rating, aktif, created_at FROM wisata ORDER BY created_at DESC LIMIT 5");
if ($res) while ($r = $res->fetch_assoc()) $wisata_terbaru[] = $r;

// Pengguna terbaru
$user_terbaru = [];
$res = $conn->query("SELECT id, nama, email, created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5");
if ($res) while ($r = $res->fetch_assoc()) $user_terbaru[] = $r;

// Ulasan terbaru
$ulasan_terbaru = [];
$res = $conn->query("SELECT u.komentar, u.rating, u.created_at, us.nama as user_nama, w.nama as wisata_nama FROM ulasan u JOIN users us ON u.user_id=us.id JOIN wisata w ON u.wisata_id=w.id ORDER BY u.created_at DESC LIMIT 5");
if ($res) while ($r = $res->fetch_assoc()) $ulasan_terbaru[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard &mdash; Admin PesonaNTB</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'config/sidebar.php'; ?>
  <div class="admin-content">
    <?php include 'config/topbar.php'; ?>
    <main class="admin-main">

      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon brown">🏝️</div>
          <div><div class="stat-num"><?= $total_wisata ?></div><div class="stat-label">Total Wisata</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">👥</div>
          <div><div class="stat-num"><?= $total_user ?></div><div class="stat-label">Total Pengguna</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange">⭐</div>
          <div><div class="stat-num"><?= $total_ulasan ?></div><div class="stat-label">Total Ulasan</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon sand">🔖</div>
          <div><div class="stat-num"><?= $total_bookmark ?></div><div class="stat-label">Total Bookmark</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">🏷️</div>
          <div><div class="stat-num"><?= $total_kategori ?></div><div class="stat-label">Kategori</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red">🚫</div>
          <div><div class="stat-num"><?= $wisata_nonaktif ?></div><div class="stat-label">Wisata Nonaktif</div></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">

        <!-- Wisata Terbaru -->
        <div class="admin-card">
          <div class="card-header">
            <h3>Wisata Terbaru</h3>
            <a href="wisata.php" class="btn btn-light btn-sm">Lihat Semua</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Nama</th><th>Kategori</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($wisata_terbaru as $w): ?>
                <tr>
                  <td><?= htmlspecialchars($w['nama']) ?></td>
                  <td><span class="badge badge-brown"><?= htmlspecialchars($w['kategori']) ?></span></td>
                  <td><?= $w['aktif'] ? '<span class="badge badge-green">Aktif</span>' : '<span class="badge badge-gray">Nonaktif</span>' ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pengguna Terbaru -->
        <div class="admin-card">
          <div class="card-header">
            <h3>Pengguna Terbaru</h3>
            <a href="pengguna.php" class="btn btn-light btn-sm">Lihat Semua</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Nama</th><th>Email</th><th>Bergabung</th></tr></thead>
              <tbody>
                <?php foreach ($user_terbaru as $u): ?>
                <tr>
                  <td><?= htmlspecialchars($u['nama']) ?></td>
                  <td class="td-muted"><?= htmlspecialchars($u['email']) ?></td>
                  <td class="td-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Ulasan Terbaru -->
      <div class="admin-card">
        <div class="card-header">
          <h3>Ulasan Terbaru</h3>
          <a href="ulasan.php" class="btn btn-light btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Pengguna</th><th>Destinasi</th><th>Rating</th><th>Komentar</th><th>Tanggal</th></tr></thead>
            <tbody>
              <?php foreach ($ulasan_terbaru as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['user_nama']) ?></td>
                <td><?= htmlspecialchars($u['wisata_nama']) ?></td>
                <td><span class="badge badge-orange"><?= $u['rating'] ?>★</span></td>
                <td class="td-truncate td-muted"><?= htmlspecialchars($u['komentar']) ?></td>
                <td class="td-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>
<?php include 'config/modal.php'; ?>
<script src="js/admin.js"></script>
</body>
</html>