<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];

// Handle hapus riwayat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus_semua'])) {
        $conn->query("DELETE FROM riwayat WHERE user_id=$uid");
    } elseif (isset($_POST['hapus_id'])) {
        $rid = intval($_POST['hapus_id']);
        $conn->query("DELETE FROM riwayat WHERE user_id=$uid AND wisata_id=$rid");
    }
}

// Ambil riwayat
$riwayat = [];
$res = $conn->query("SELECT w.*, r.dilihat_at FROM riwayat r JOIN wisata w ON r.wisata_id=w.id WHERE r.user_id=$uid ORDER BY r.dilihat_at DESC LIMIT 30");
if ($res) while ($row = $res->fetch_assoc()) $riwayat[] = $row;

$jml_bookmark = $conn->query("SELECT COUNT(*) as c FROM bookmark WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0;
$jml_ulasan   = $conn->query("SELECT COUNT(*) as c FROM ulasan WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0;
$jml_riwayat  = count($riwayat);

$emoji_map = ['Pantai'=>'🏖️','Gunung'=>'🏔️','Pulau'=>'🏝️','Adventure'=>'🌾','Air Terjun'=>'💧','Budaya'=>'🎭','Kuliner'=>'🍜'];

$stmt = $conn->prepare("SELECT nama, foto_profil FROM users WHERE id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$inisial = strtoupper(mb_substr($user['nama'], 0, 2));

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)         return 'Baru saja';
    if ($diff < 3600)       return floor($diff/60) . ' menit lalu';
    if ($diff < 86400)      return floor($diff/3600) . ' jam lalu';
    if ($diff < 2592000)    return floor($diff/86400) . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="user-page">
  <div class="user-container">
    <div class="section-header">
      <span class="section-label">✦ Akun Saya</span>
      <h1 class="section-title">Riwayat Kunjungan</h1>
    </div>

    <div class="profil-grid">
      <div class="profil-sidebar">
        <?php if (!empty($user['foto_profil'] ?? '') && file_exists('../assets/uploads/profil/' . $user['foto_profil'])): ?>
      <div class="profil-avatar" style="background:none;overflow:hidden;padding:0">
        <img src="../assets/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
      </div>
      <?php else: ?>
      <div class="profil-avatar"><?= $inisial ?></div>
      <?php endif; ?>
        <div class="profil-nama"><?= htmlspecialchars($user['nama']) ?></div>
        <div class="profil-stats">
          <div><div class="pstat-num"><?= $jml_bookmark ?></div><div class="pstat-label">Tersimpan</div></div>
          <div><div class="pstat-num"><?= $jml_ulasan ?></div><div class="pstat-label">Ulasan</div></div>
          <div><div class="pstat-num"><?= $jml_riwayat ?></div><div class="pstat-label">Dilihat</div></div>
        </div>
        <nav class="profil-nav">
          <a href="profil.php">👤 Profil Saya</a>
          <a href="bookmark.php">🔖 Tersimpan</a>
          <a href="riwayat.php" class="active">🕐 Riwayat</a>
          <a href="logout.php" style="color:#C0392B">🚪 Keluar</a>
        </nav>
      </div>

      <div>
        <div class="profil-card">
          <?php if (empty($riwayat)): ?>
          <div class="riwayat-empty">
            <span>🕐</span>
            <p>Belum ada riwayat kunjungan. Mulai jelajahi destinasi!</p>
            <a href="destinasi.php" class="btn-primary" style="margin-top:1rem">Jelajahi Destinasi</a>
          </div>
          <?php else: ?>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;padding-bottom:0.75rem;border-bottom:1px solid var(--sand-light)">
            <h3 style="margin:0;border:none;padding:0"><?= $jml_riwayat ?> Destinasi Dilihat</h3>
            <form method="POST" onsubmit="return confirm('Hapus semua riwayat?')">
              <button name="hapus_semua" type="submit" style="background:none;border:none;font-size:0.82rem;color:#C0392B;cursor:pointer;font-family:'Nunito',sans-serif;font-weight:600">🗑 Hapus Semua</button>
            </form>
          </div>
          <div class="riwayat-list">
            <?php foreach ($riwayat as $r):
              $emoji = $emoji_map[$r['kategori']] ?? '🏝️';
            ?>
            <div class="riwayat-item">
              <a href="detail.php?id=<?= $r['id'] ?>" style="display:flex;align-items:center;gap:1rem;flex:1;text-decoration:none;color:inherit">
                
                <div class="riwayat-thumb" style="width:60px; height:60px; border-radius:8px; overflow:hidden; position:relative; background:#eee;">
                  <?php if (!empty($r['foto'])): ?>
                    <img src="../assets/uploads/destinasi/<?= htmlspecialchars($r['foto']) ?>" style="width:100%; height:100%; object-fit:cover;">
                  <?php else: ?>
                    <div style="display:flex; align-items:center; justify-content:center; height:100%;"><?= $emoji ?></div>
                  <?php endif; ?>
                </div>

                <div class="riwayat-info">
                  <div class="riwayat-nama"><?= htmlspecialchars($r['nama']) ?></div>
                  <div class="riwayat-meta">📍 <?= htmlspecialchars($r['lokasi']) ?> &middot; <?= htmlspecialchars($r['kategori']) ?></div>
                </div>
              </a>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.5rem">
                <span class="riwayat-time"><?= timeAgo($r['dilihat_at']) ?></span>
                <form method="POST">
                  <input type="hidden" name="hapus_id" value="<?= $r['id'] ?>">
                  <button type="submit" style="background:none;border:none;font-size:0.75rem;color:var(--text-muted);cursor:pointer;font-family:'Nunito',sans-serif">✕</button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>