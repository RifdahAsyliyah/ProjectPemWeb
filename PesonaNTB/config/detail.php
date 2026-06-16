<?php
session_start();
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: destinasi.php'); exit; }

// Ambil data wisata
$stmt = $conn->prepare("SELECT * FROM wisata WHERE id = ? AND aktif = 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$wisata = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$wisata) { header('Location: destinasi.php'); exit; }

// Catat riwayat jika sudah login
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $conn->query("DELETE FROM riwayat WHERE user_id=$uid AND wisata_id=$id");
    $conn->query("INSERT INTO riwayat (user_id, wisata_id, dilihat_at) VALUES ($uid, $id, NOW())");
}

// Cek bookmark
$is_bookmarked = false;
if (isset($_SESSION['user_id'])) {
    $uid  = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM bookmark WHERE user_id=? AND wisata_id=?");
    $stmt->bind_param('ii', $uid, $id);
    $stmt->execute();
    $is_bookmarked = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

// Handle POST: bookmark toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    if ($_POST['action'] === 'bookmark') {
        if ($is_bookmarked) {
            $conn->query("DELETE FROM bookmark WHERE user_id=$uid AND wisata_id=$id");
            $is_bookmarked = false;
        } else {
            $conn->query("INSERT IGNORE INTO bookmark (user_id, wisata_id, created_at) VALUES ($uid, $id, NOW())");
            $is_bookmarked = true;
        }
    }
    if ($_POST['action'] === 'ulasan') {

    $rating = !empty($_POST['rating'])
        ? intval($_POST['rating'])
        : null;    

    $komentar = trim($_POST['komentar'] ?? '');
    $komentar = $komentar !== '' ? $komentar : null;

    if ($rating !== null || $komentar !== null) {

      $stmt = $conn->prepare("
        INSERT INTO ulasan
        (user_id,wisata_id,rating,komentar,created_at)
        VALUES (?,?,?, ?,NOW())
        ");

        $stmt->bind_param("iiis",$uid,$id,$rating,$komentar);

        $stmt->execute();

        $stmt->close();

        $conn->query("
        UPDATE wisata
        SET rating=
        (
        SELECT AVG(rating)
        FROM ulasan
        WHERE wisata_id=$id
        AND rating IS NOT NULL
        )
        WHERE id=$id
        ");
        header("Location: detail.php?id=".$id);
        exit;
        $conn->query("
        UPDATE wisata
        SET rating=
        (
        SELECT AVG(rating)
        FROM ulasan
        WHERE wisata_id=$id
        AND rating IS NOT NULL
        )
        WHERE id=$id
        ");
    }
  }
    if ($_POST['action'] === 'reply') {

    $parent_id = intval($_POST['parent_id']);
    $komentar  = trim($_POST['komentar']);

    if($komentar !== ''){

        $stmt = $conn->prepare("
            INSERT INTO ulasan
            (
                user_id,
                wisata_id,
                parent_id,
                komentar,
                created_at
            )
            VALUES
            (?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "iiis",
            $uid,
            $id,
            $parent_id,
            $komentar
        );

        $stmt->execute();
        $stmt->close();
    }

    header("Location: detail.php?id=".$id);
    exit;
  }

  if($_POST['action']==='edit'){

      $id_ulasan=(int)$_POST['id_ulasan'];

      $komentar=trim($_POST['komentar']);

      $rating=(int)$_POST['rating'];

      $stmt=$conn->prepare("
      UPDATE ulasan
      SET komentar=?,
      rating=?
      WHERE id=?
      AND user_id=?
      ");

      $stmt->bind_param(
      "siii",
      $komentar,
      $rating,
      $id_ulasan,
      $uid
      );

      $stmt->execute();

      $stmt->close();

      header("Location: detail.php?id=".$id);

      exit;
      $conn->query("
      UPDATE wisata
      SET rating=
      (
      SELECT AVG(rating)
      FROM ulasan
      WHERE wisata_id=$id
      AND rating IS NOT NULL
      )
      WHERE id=$id
      ");

    }


    if($_POST['action']==='hapus'){

      $id_ulasan=(int)$_POST['id_ulasan'];

      $stmt=$conn->prepare("
      DELETE FROM ulasan
      WHERE id=?
      AND user_id=?
      ");

      $stmt->bind_param(
      "ii",
      $id_ulasan,
      $uid
      );

      $stmt->execute();

      $stmt->close();

      header("Location: detail.php?id=".$id);

      exit;
      $conn->query("
      UPDATE wisata
      SET rating=
      (
      SELECT AVG(rating)
      FROM ulasan
      WHERE wisata_id=$id
      AND rating IS NOT NULL
      )
      WHERE id=$id
      ");
    }
}

// Ambil ulasan
$ulasan_list = [];
$res = $conn->query("SELECT u.id, u.user_id, u.komentar, u.rating, u.created_at, us.nama, us.foto_profil, us.role FROM ulasan u JOIN users us ON u.user_id=us.id WHERE u.wisata_id=$id AND u.parent_id IS NULL ORDER BY u.created_at DESC LIMIT 10");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $ulasan_list[] = $row;
    }
}
$total_ulasan = $conn->query("
SELECT COUNT(*) c
FROM ulasan
WHERE wisata_id=$id
AND parent_id IS NULL
")->fetch_assoc()['c'];

// Wisata terkait
$kategori_esc = $conn->real_escape_string($wisata['kategori']);
$related = [];
$res = $conn->query("SELECT id,nama,lokasi,kategori,foto FROM wisata WHERE kategori='$kategori_esc' AND id<>$id AND aktif=1 LIMIT 4");
if ($res) while ($row = $res->fetch_assoc()) $related[] = $row;

$img_map   = ['Pantai'=>'dest-img-pantai','Gunung'=>'dest-img-rinjani','Pulau'=>'dest-img-gili','Adventure'=>'dest-img-sumbawa','Air Terjun'=>'dest-img-moyo','Budaya'=>'dest-img-sumbawa','Kuliner'=>'dest-img-pink'];
$emoji_map = ['Pantai'=>'🏖️','Gunung'=>'🏔️','Pulau'=>'🏝️','Adventure'=>'🌾','Air Terjun'=>'💧','Budaya'=>'🎭','Kuliner'=>'🍜'];
$img_class = $img_map[$wisata['kategori']] ?? 'dest-img-pantai';
$emoji     = $emoji_map[$wisata['kategori']] ?? '🏝️';

$maps_embed = '';
$maps_url   = '#';

if (!empty($wisata['latitude']) && !empty($wisata['longitude'])) {
    $lat = $wisata['latitude'];
    $lng = $wisata['longitude'];

    // TANPA API KEY
    $maps_embed = "https://maps.google.com/maps?q={$lat},{$lng}&z=14&output=embed";
    $maps_url   = "https://www.google.com/maps?q={$lat},{$lng}";
}

function renderStars($n) {
    $n = round($n);
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($wisata['nama']) ?> &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="user-page">
  <div class="user-container">

    <div style="margin-bottom:1.5rem;">
      <a href="destinasi.php" class="btn-back">
          <span>←</span>
          <span>Kembali ke Destinasi</span>
      </a>
    </div>

    <!-- Hero -->
    <div class="detail-hero" style="position: relative; overflow: hidden; height: 400px;">
      
      <?php if (!empty($wisata['foto'])): ?>
        <img src="../assets/uploads/destinasi/<?= htmlspecialchars($wisata['foto']) ?>" 
             alt="<?= htmlspecialchars($wisata['nama']) ?>" 
             style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 0;">
      <?php else: ?>
        <div class="<?= $img_class ?>" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 0;"></div>
      <?php endif; ?>

      <div class="detail-hero-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(transparent, rgba(0,0,0,0.6)); z-index: 1;"></div>
      
      <span class="detail-hero-badge" style="position: relative; z-index: 2;"><?= htmlspecialchars($wisata['kategori']) ?></span>
      
      <div class="detail-hero-info" style="position: relative; z-index: 2;">
        <h1><?= htmlspecialchars($wisata['nama']) ?></h1>
        <div class="loc">📍 <?= htmlspecialchars($wisata['lokasi']) ?>  |  <?= renderStars($wisata['rating']) ?> <?= number_format($wisata['rating'],1) ?> (<?= $total_ulasan ?> ulasan)</div>
      </div>
    </div>

    <div class="detail-grid">

      <!-- MAIN -->
      <div class="detail-main">

        <!-- Deskripsi -->
        <div class="detail-card">
          <h3>Tentang Destinasi</h3>
          <p><?= nl2br(htmlspecialchars($wisata['deskripsi'])) ?></p>
        </div>

        <!-- Info -->
        <div class="detail-card">
          <h3>Informasi Wisata</h3>
          <div class="detail-meta">
            <div class="meta-item">
              <span class="meta-icon">🕐</span>
              <div><div class="meta-label">Jam Buka</div><div class="meta-val"><?= htmlspecialchars($wisata['jam_buka'] ?: '-') ?></div></div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">🎫</span>
              <div><div class="meta-label">Harga Tiket</div><div class="meta-val"><?= htmlspecialchars($wisata['harga_tiket'] ?: '-') ?></div></div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">📍</span>
              <div><div class="meta-label">Lokasi</div><div class="meta-val"><?= htmlspecialchars($wisata['lokasi']) ?></div></div>
            </div>
            <div class="meta-item">
              <span class="meta-icon">⭐</span>
              <div><div class="meta-label">Rating</div><div class="meta-val"><?= number_format($wisata['rating'],1) ?> / 5.0</div></div>
            </div>
          </div>
        </div>

        <!-- Fasilitas -->
        <?php if ($wisata['fasilitas']): ?>
        <div class="detail-card">
          <h3>Fasilitas</h3>
          <div class="fasilitas-list">
            <?php foreach (explode(',', $wisata['fasilitas']) as $f): ?>
            <span class="fasilitas-tag"><?= htmlspecialchars(trim($f)) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Ulasan -->
        <div class="detail-card">
          <h3>Rating &amp; Ulasan</h3>
          <div class="rating-summary">
            <div class="rating-big"><?= number_format($wisata['rating'],1) ?></div>
            <div>
              <div class="rating-stars"><?= renderStars($wisata['rating']) ?></div>
              <div class="rating-count"><?= $total_ulasan ?> ulasan</div>
            </div>
          </div>

          <?php if (isset($_SESSION['user_id'])): ?>
          <div class="ulasan-form">
            <p style="font-size:0.88rem;font-weight:600;color:var(--brown);margin-bottom:0.5rem">Tulis Ulasanmu</p>
            <form method="POST">
              <input type="hidden" name="action" value="ulasan">
              <div class="star-input">
                <?php for ($i=5;$i>=1;$i--): ?>
                <input type="radio" name="rating" id="star<?=$i?>" value="<?=$i?>">
                <label for="star<?=$i?>">★</label>
                <?php endfor; ?>
              </div>
              <textarea name="komentar" class="ulasan-textarea" placeholder="Bagikan pengalamanmu..."></textarea>
              <button type="submit" class="btn-ulasan">Kirim Ulasan</button>
            </form>
          </div>
          <?php else: ?>
          <p style="font-size:0.85rem;color:var(--text-muted);margin-top:0.5rem">
            <a href="login.php" style="color:var(--green);font-weight:600">Login</a> untuk memberikan ulasan.
          </p>
          <?php endif; ?>

          <!-- List Ulasan -->
          <?php if ($ulasan_list): ?>
<div style="margin-top:1.25rem">
  <?php foreach ($ulasan_list as $u): ?>
  <div class="ulasan-item">
    <div class="ulasan-header">
      <div class="ulasan-author">
        <?php if (!empty($u['foto_profil'])): ?>
          <img src="../assets/uploads/profil/<?= htmlspecialchars($u['foto_profil']) ?>" 
               style="width:40px; height:40px; border-radius:50%; object-fit:cover; margin-right:10px;">
        <?php else: ?>
          <div class="ulasan-avatar"><?= strtoupper(mb_substr($u['nama'],0,2)) ?></div>
        <?php endif; ?>
        <div>
          <div class="ulasan-nama"><?= htmlspecialchars($u['nama']) ?></div>
          <?php if($u['rating'] > 0): ?>
            <div class="ulasan-stars">
                <?= renderStars($u['rating']) ?>
            </div>
            <?php endif; ?>
        </div>
      </div>
      <span class="ulasan-date"><?= date('d M Y', strtotime($u['created_at'])) ?></span>
    </div>
    
    <?php if(!empty($u['komentar'])): ?>
      <p class="ulasan-text">
          <?= htmlspecialchars($u['komentar']) ?>
      </p>
      <?php endif; ?>
      
      <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']==$u['user_id']): ?>
          <div style="margin-top:10px">
            
                <button
                  type="button"
                  onclick="showEditForm(<?= $u['id'] ?>)"
                  class="btn-ulasan"
                  style="margin-right:8px">
                  ✏️ Edit
                </button>
            <form
                  method="POST"
                  style="display:inline"
                  onsubmit="return confirm('Hapus ulasan ini?')">
                <input
                  type="hidden"
                  name="action"
                  value="hapus">
                <input
                  type="hidden"
                  name="id_ulasan"
                  value="<?= $u['id'] ?>">
                <button
                  type="submit"
                  class="btn-ulasan">
                  🗑 Hapus
                </button>
            </form>
          </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['user_id'])): ?>
          <button
          type="button"
          onclick="showReplyForm(<?= $u['id'] ?>)"
          class="btn-ulasan"
          style="margin-top:10px">
          💬 Balas
          </button>
        <?php endif; ?>
        <div id="edit-form-<?= $u['id'] ?>" style="display:none;margin-top:15px">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_ulasan" value="<?= $u['id'] ?>">
                <div class="star-input edit-star-input">
                  <?php for($i=5;$i>=1;$i--): ?>
                  <input
                  type="radio"
                  name="rating"
                  id="edit-star<?= $u['id'].$i ?>"
                  value="<?= $i ?>"
                  <?= $i==$u['rating'] ? 'checked':'' ?>>
                  <label for="edit-star<?= $u['id'].$i ?>">★</label>
                  <?php endfor; ?>
                </div>
                <textarea name="komentar" class="ulasan-textarea" required><?= htmlspecialchars($u['komentar']) ?></textarea>
                <button type="submit" class="btn-ulasan" style="margin-top:10px">
                    Simpan Perubahan
                </button>
            </form>
        </div>
        <div id="reply-form-<?= $u['id'] ?>" style="display:none;margin-top:10px">
            <form method="POST">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="parent_id" value="<?= $u['id'] ?>">

                <textarea name="komentar" class="ulasan-textarea" placeholder="Tulis balasan..." required></textarea>

                <button type="submit" class="btn-ulasan">
                    Kirim Balasan
                </button>
            </form>
        </div>
    <?php
      $replies = $conn->query("
      SELECT u.*, us.nama, us.role, us.foto_profil
      FROM ulasan u
      JOIN users us ON us.id=u.user_id
      WHERE u.parent_id={$u['id']}
      ORDER BY u.created_at ASC
      ");
    while($r = $replies->fetch_assoc()): ?>
      <div style="margin-left:55px;margin-top:10px;background:#fafafa;padding:10px 14px;border-radius:12px">
          <div style="display:flex;gap:12px;margin-left:50px;margin-top:12px;padding:12px;background:#f8f8f8;border-left:4px solid #8B5E3C;border-radius:8px">
    <?php if(!empty($r['foto_profil']) && file_exists('../assets/uploads/profil/'.$r['foto_profil'])): ?>
        <img src="../assets/uploads/profil/<?= htmlspecialchars($r['foto_profil']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
    <?php else: ?>
        <div style="width:40px;height:40px;border-radius:50%;background:#6b4d3e;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold">
            <?= strtoupper(substr($r['nama'],0,1)) ?>
        </div>
    <?php endif; ?>
    <div>
        <div style="display:flex;align-items:center;gap:8px">
            <strong><?= htmlspecialchars($r['nama']) ?></strong>
            <?php if($r['role']=='admin'): ?>
                <span style="background:#4CAF50;color:#fff;padding:2px 8px;border-radius:20px;font-size:11px">Admin</span>
            <?php endif; ?>
            <span style="font-size:12px;color:#888">
                <?= date('d M Y', strtotime($r['created_at'])) ?>
            </span>
        </div>
        <p style="margin-top:8px">
            <?= htmlspecialchars($r['komentar']) ?>
        </p>
      </div>
  </div>
</div>

      <?php endwhile; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<p style="font-size:0.85rem;color:var(--text-muted);margin-top:1rem">Belum ada ulasan. Jadilah yang pertama!</p>
<?php endif; ?>
        </div>

      </div><!-- /detail-main -->

      <!-- SIDEBAR -->
      <div class="detail-sidebar">

        <!-- Bookmark -->
        <div class="sidebar-card">
          <?php if (isset($_SESSION['user_id'])): ?>
          <form method="POST">
            <input type="hidden" name="action" value="bookmark">
            <button type="submit" class="btn-bookmark <?= $is_bookmarked ? 'saved' : '' ?>">
              <?= $is_bookmarked ? '🔖 Tersimpan' : '🔖 Simpan Destinasi' ?>
            </button>
          </form>
          <?php else: ?>
          <a href="login.php" class="btn-bookmark">🔖 Login untuk Menyimpan</a>
          <?php endif; ?>
        </div>

        <!-- Google Maps -->
        <div class="sidebar-card">
          <h4>📍 Lokasi di Peta</h4>
          <div class="maps-wrap">
            <?php if ($maps_embed): ?>
            <iframe
              src="<?= htmlspecialchars($maps_embed) ?>"
              width="100%"
              height="260"
              style="border:0"
              allowfullscreen
              loading="lazy">
            </iframe>
            <?php else: ?>
            <div style="height:260px;background:var(--sand-light);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:0.5rem">
              <span style="font-size:2rem">🗺️</span>
              <span style="font-size:0.82rem;color:var(--text-muted)">Peta tidak tersedia</span>
            </div>
            <?php endif; ?>
            <a href="<?= $maps_url ?>" target="_blank" class="maps-btn">
              🗺️ Buka di Google Maps
            </a>
          </div>
        </div>

        <!-- Destinasi Terkait -->
        <?php if ($related): ?>
<div class="sidebar-card">
  <h4>Destinasi Terkait</h4>
  <?php foreach ($related as $r): ?>
  <a href="detail.php?id=<?= $r['id'] ?>" class="related-item" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit; margin-bottom:15px;">
    
    <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #eee; flex-shrink: 0;">
      <?php if (!empty($r['foto'])): ?>
        <img src="../assets/uploads/destinasi/<?= htmlspecialchars($r['foto']) ?>" 
             style="width: 100%; height: 100%; object-fit: cover;" 
             alt="<?= htmlspecialchars($r['nama']) ?>">
      <?php else: ?>
        <div class="<?= $img_map[$r['kategori']] ?? 'dest-img-pantai' ?>" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
          <?= $emoji_map[$r['kategori']] ?? '🏝️' ?>
        </div>
      <?php endif; ?>
    </div>

    <div>
      <div class="related-name" style="font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($r['nama']) ?></div>
      <div class="related-loc" style="font-size: 0.75rem; color: #888;">📍 <?= htmlspecialchars($r['lokasi']) ?></div>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

      </div><!-- /sidebar -->

    </div><!-- /detail-grid -->
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="../js/main.js"></script>
<script>
function showReplyForm(id){
    const form =
        document.getElementById(
            'reply-form-' + id
        );
    if(form.style.display === 'none'){
        form.style.display = 'block';
    }else{
        form.style.display = 'none';
    }
}
function showEditForm(id){
  const form=document.getElementById('edit-form-'+id);
  if(form.style.display==='none'){
    form.style.display='block';
  }else{
    form.style.display='none';
  }
}
</script>
</body>
</html>