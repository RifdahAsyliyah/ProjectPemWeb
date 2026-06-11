<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];

// Handle hapus bookmark
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $bid = intval($_POST['hapus_id']);
    $conn->query("DELETE FROM bookmark WHERE user_id=$uid AND wisata_id=$bid");
}

// Ambil bookmark
$bookmarks = [];
$res = $conn->query("SELECT w.*, b.created_at as saved_at FROM bookmark b JOIN wisata w ON b.wisata_id=w.id WHERE b.user_id=$uid ORDER BY b.created_at DESC");
if ($res) while ($row = $res->fetch_assoc()) $bookmarks[] = $row;

$jml_bookmark = count($bookmarks);
$jml_ulasan   = $conn->query("SELECT COUNT(*) as c FROM ulasan WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0;
$jml_riwayat  = $conn->query("SELECT COUNT(*) as c FROM riwayat WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0;

$img_map   = ['Pantai'=>'dest-img-pantai','Gunung'=>'dest-img-rinjani','Pulau'=>'dest-img-gili','Adventure'=>'dest-img-sumbawa','Air Terjun'=>'dest-img-moyo','Budaya'=>'dest-img-sumbawa','Kuliner'=>'dest-img-pink'];
$emoji_map = ['Pantai'=>'🏖️','Gunung'=>'🏔️','Pulau'=>'🏝️','Adventure'=>'🌾','Air Terjun'=>'💧','Budaya'=>'🎭','Kuliner'=>'🍜'];

$stmt = $conn->prepare("SELECT nama, foto_profil FROM users WHERE id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$inisial = strtoupper(mb_substr($user['nama'], 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tersimpan &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="user-page">
  <div class="user-container">
    <div class="section-header">
      <span class="section-label">✦ Akun Saya</span>
      <h1 class="section-title">Destinasi Tersimpan</h1>
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
          <a href="bookmark.php" class="active">🔖 Tersimpan</a>
          <a href="riwayat.php">🕐 Riwayat</a>
          <a href="logout.php" style="color:#C0392B">🚪 Keluar</a>
        </nav>
      </div>

      <div>
        <?php if (empty($bookmarks)): ?>
        <div class="profil-card">
          <div class="bookmark-empty">
            <span>🔖</span>
            <p>Belum ada destinasi yang disimpan.</p>
            <a href="destinasi.php" class="btn-primary">Jelajahi Destinasi</a>
          </div>
        </div>
        <?php else: ?>
        <div class="dest-grid-full">
          <?php foreach ($bookmarks as $d):
            $img_class = $img_map[$d['kategori']] ?? 'dest-img-pantai';
            $emoji     = $emoji_map[$d['kategori']] ?? '🏝️';
          ?>
          <div class="dest-card-full" style="position:relative">
            <a href="detail.php?id=<?= $d['id'] ?>" style="text-decoration:none;color:inherit;display:block">
              
              <div class="dest-img <?= $img_class ?>" style="position:relative; overflow:hidden;">
                <?php if (!empty($d['foto'])): ?>
                  <img src="../assets/uploads/destinasi/<?= htmlspecialchars($d['foto']) ?>" 
                       style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; z-index:1;" 
                       alt="<?= htmlspecialchars($d['nama']) ?>">
                <?php endif; ?>
                
                <div class="dest-emoji" style="z-index:2; position:relative;"><?= $emoji ?></div>
                <span class="dest-badge" style="z-index:2; position:relative;"><?= htmlspecialchars($d['kategori']) ?></span>
              </div>
              
              <div class="dest-body">
                <div class="dest-name"><?= htmlspecialchars($d['nama']) ?></div>
                <div class="dest-loc">📍 <?= htmlspecialchars($d['lokasi']) ?></div>
                <div class="dest-desc"><?= htmlspecialchars(mb_substr($d['deskripsi'],0,90)) ?>...</div>
              </div>
            </a>
            <div class="dest-foot">
              <span class="dest-rating">★ <?= number_format($d['rating'],1) ?></span>
              <form method="POST" onsubmit="return confirm('Hapus dari tersimpan?')">
                <input type="hidden" name="hapus_id" value="<?= $d['id'] ?>">
                <button type="submit" style="background:none;border:none;font-size:0.8rem;color:#C0392B;cursor:pointer;font-family:'Nunito',sans-serif;font-weight:600">🗑 Hapus</button>
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

<?php include 'footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>