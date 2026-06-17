<?php
session_start();
require_once 'db.php';

// Filter & Search
$search    = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$kategori  = isset($_GET['kategori']) ? trim($_GET['kategori']) : 'all';
$kategoriMap = [
    'pantai' => 'Pantai',
    'gunung' => 'Gunung',
    'air-terjun' => 'Air Terjun',
    'budaya' => 'Budaya',
    'pulau' => 'Pulau',
    'kuliner' => 'Kuliner',
    'adventure' => 'Adventure'
];

if (isset($kategoriMap[$kategori])) {
    $kategori = $kategoriMap[$kategori];
}
$page      = isset($_GET['page'])     ? max(1, intval($_GET['page'])) : 1;
$per_page  = 9;
$offset    = ($page - 1) * $per_page;

// Build query
$where = ["aktif = 1"];
$params = [];
$types  = '';

if ($search !== '') {
    $where[] = "(nama LIKE ? OR lokasi LIKE ? OR deskripsi LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}

if ($kategori !== 'all' && $kategori !== '') {
    $where[] = "kategori = ?";
    $params[] = $kategori;
    $types .= 's';
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM wisata $where_sql";
$total_rows = 0;
if ($stmt = $conn->prepare($count_sql)) {
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_rows = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
}
$total_pages = ceil($total_rows / $per_page);

// Fetch wisata
$sql = "SELECT * FROM wisata $where_sql ORDER BY rating DESC LIMIT ? OFFSET ?";
$params_page = array_merge($params, [$per_page, $offset]);
$types_page  = $types . 'ii';
$wisata_list = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param($types_page, ...$params_page);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $wisata_list[] = $row;
    $stmt->close();
}

// Fallback statis jika database benar-benar kosong (Menggunakan kolom 'foto' agar seragam)
if (empty($wisata_list) && $search === '' && $kategori === 'all') {
    $wisata_list = [
        ['id'=>1,'nama'=>'Pantai Senggigi','lokasi'=>'Lombok Barat','kategori'=>'Pantai','deskripsi'=>'Pantai ikonik dengan pemandangan matahari terbenam yang memukau dan ombak yang tenang.','rating'=>5.0, 'foto'=>''],
        ['id'=>2,'nama'=>'Gunung Rinjani','lokasi'=>'Lombok Utara','kategori'=>'Gunung','deskripsi'=>'Gunung berapi tertinggi kedua di Indonesia dengan danau kawah Segara Anak yang menakjubkan.','rating'=>4.9, 'foto'=>''],
        ['id'=>3,'nama'=>'Gili Trawangan','lokasi'=>'Lombok Utara','kategori'=>'Pulau','deskripsi'=>'Pulau kecil surga bawah laut dengan snorkeling, penyu laut, dan suasana pantai santai.','rating'=>4.7, 'foto'=>''],
        ['id'=>4,'nama'=>'Pantai Pink','lokasi'=>'Lombok Timur','kategori'=>'Pantai','deskripsi'=>'Salah satu dari sedikit pantai berpasir merah muda di dunia.','rating'=>4.6, 'foto'=>''],
        ['id'=>5,'nama'=>'Savana Sumbawa','lokasi'=>'Sumbawa Besar','kategori'=>'Adventure','deskripsi'=>'Padang savana luas dengan kuda liar dan pemandangan alam yang sangat alami.','rating'=>4.7, 'foto'=>''],
        ['id'=>6,'nama'=>'Pulau Moyo','lokasi'=>'Sumbawa','kategori'=>'Pulau','deskripsi'=>'Pulau terpencil dengan air terjun tersembunyi dan ekosistem bawah laut.','rating'=>4.8, 'foto'=>''],
        ['id'=>7,'nama'=>'Pantai Kuta Lombok','lokasi'=>'Lombok Tengah','kategori'=>'Pantai','deskripsi'=>'Pantai eksotis dengan pasir putih halus dan ombak yang cocok untuk surfing.','rating'=>4.7, 'foto'=>''],
        ['id'=>8,'nama'=>'Air Terjun Sendang Gile','lokasi'=>'Lombok Utara','kategori'=>'Air Terjun','deskripsi'=>'Air terjun megah di kaki Gunung Rinjani dengan kolam alami yang sejuk.','rating'=>4.6, 'foto'=>''],
        ['id'=>9,'nama'=>'Desa Sade','lokasi'=>'Lombok Tengah','kategori'=>'Budaya','deskripsi'=>'Desa adat Sasak yang masih mempertahankan tradisi dan arsitektur leluhur.','rating'=>4.5, 'foto'=>''],
    ];
}

$img_map   = ['Pantai'=>'dest-img-pantai','Gunung'=>'dest-img-rinjani','Pulau'=>'dest-img-gili','Adventure'=>'dest-img-sumbawa','Air Terjun'=>'dest-img-moyo','Budaya'=>'dest-img-sumbawa','Kuliner'=>'dest-img-pink'];
$emoji_map = ['Pantai'=>'🏖️','Gunung'=>'🏔️','Pulau'=>'🏝️','Adventure'=>'🌾','Air Terjun'=>'💧','Budaya'=>'🎭','Kuliner'=>'🍜'];

$kategori_list = [
    'all' => 'Semua',
    'Pantai' => 'Pantai',
    'Gunung' => 'Gunung',
    'Air Terjun' => 'Air Terjun',
    'Budaya' => 'Budaya',
    'Pulau' => 'Pulau',
    'Kuliner' => 'Kuliner',
    'Adventure' => 'Adventure'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destinasi Wisata &mdash; PesonaNTB</title>
  <link rel="stylesheet" href="../css/style.css">
<style>

  .filter-btn.active{
  background:#8B5E3C !important;
  color:#fff !important;
  border:1px solid #8B5E3C !important;
  font-weight:600;
  }

  .filter-btn:hover{
  background:#8B5E3C;
  color:#fff;
  }

</style>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-destinasi" style="max-width:1100px;margin: 40px auto;padding:0 20px;">
  <div class="page-header" style="margin-bottom: 30px;">
    <span class="section-label">✦ Jelajahi NTB</span>
    <h1 style="font-size: 32px;color:#3d2514;margin: 10px 0;">Destinasi Wisata</h1>
    <p style="color:#666;">Temukan keindahan alam dan budaya Nusa Tenggara Barat di sini.</p>
  </div>

  <div class="search-filter" style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:30px;align-items:center;justify-content:space-between;">
    <div class="search-box" style="position:relative;flex:1;max-width:400px;">
      <input type="text" id="searchInput" placeholder="Cari destinasi wisata..."
             value="<?= htmlspecialchars($search) ?>" style="width:100%;padding:12px 12px 12px 40px;border:1px solid #ddd;border-radius:25px;outline:none;">
      <span style="position:absolute;left:15px;top:14px;color:#aaa;">🔍</span>
    </div>
    <div class="filter-group" style="display:flex;flex-wrap:wrap;gap:10px;">
      <?php foreach ($kategori_list as $slug => $label): ?>
      <button
      class="filter-btn <?= strtolower($kategori)==strtolower($label) || ($slug=='all' && $kategori=='all') ? 'active' : '' ?>"
      data-kat="<?= $slug ?>"
      style="padding:8px 16px;border-radius:20px;border:1px solid #ddd;cursor:pointer;">
      <?= $label ?>
      </button>
      <?php endforeach; ?>
      </div>
  </div>

  <div class="dest-grid" id="destGrid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:30px;">
    <?php if (empty($wisata_list)): ?>
    <div class="no-results" style="grid-column:1/-1;text-align:center;padding:40px;color:#888;">
      <span>🔍</span> Destinasi tidak ditemukan. Coba kata kunci lain.
    </div>
    <?php else: ?>
    <?php foreach ($wisata_list as $d):
      $img_class  = $img_map[$d['kategori']] ?? 'dest-img-pantai';
      $emoji      = $emoji_map[$d['kategori']] ?? '🏝️';
      $detail_url = isset($_SESSION['user_id']) ? "detail.php?id={$d['id']}" : "login.php?redirect=detail.php?id={$d['id']}";
    ?>
    <a href="<?= $detail_url ?>" class="dest-card">
      
      <div class="dest-img <?= htmlspecialchars($img_class) ?>" style="position: relative; overflow: hidden; background-color: #f0f0f0; height: 200px; border-radius: 12px 12px 0 0;">
        
        <?php if (!empty($d['foto'])): ?>
          <img src="../assets/uploads/destinasi/<?= htmlspecialchars($d['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 1;" alt="<?= htmlspecialchars($d['nama']) ?>">
        <?php else: ?>
          <div class="dest-emoji" style="z-index:2; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:40px;"><?= $emoji ?></div>
        <?php endif; ?>

        <span class="dest-badge" style="z-index: 2; position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,0.9); padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; color:#3d2514;"><?= htmlspecialchars($d['kategori']) ?></span>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
        <span class="dest-lock" style="z-index: 2; position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 11px;">🔒 Login</span>
        <?php endif; ?>
      </div>
      
      <div class="dest-body" style="padding:20px; background:#fff; border-left:1px solid #eee; border-right:1px solid #eee;">
        <div class="dest-name" style="font-weight:bold; font-size:18px; color:#3d2514; margin-bottom:5px;"><?= htmlspecialchars($d['nama']) ?></div>
        <div class="dest-loc" style="color:#888; font-size:14px; margin-bottom:10px;">📍 <?= htmlspecialchars($d['lokasi']) ?></div>
        <div class="dest-desc" style="color:#666; font-size:14px; line-height:1.5;"><?= htmlspecialchars(mb_substr($d['deskripsi'], 0, 90)) ?>...</div>
      </div>
      <div class="dest-foot" style="padding:15px 20px; background:#fff; border:1px solid #eee; border-radius:0 0 12px 12px; display:flex; justify-content:between; align-items:center;">
        <span class="dest-rating" style="color:#f39c12; font-weight:bold;">★ <?= number_format($d['rating'], 1) ?></span>
        <span class="dest-link" style="color:#3d2514; font-weight:bold; font-size:14px; margin-left:auto;">Lihat Detail →</span>
      </div>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if ($total_pages > 1): ?>
  <div class="pagination" style="display:flex; justify-content:center; gap:10px; margin-top:40px;">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a href="?search=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori) ?>&page=<?= $i ?>"
       class="page-btn <?= $i === $page ? 'active' : '' ?>" style="padding:8px 14px; border:1px solid #ddd; border-radius:4px; color:#333; text-decoration:none;"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script src="../js/main.js"></script>
<script>
// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const kat    = this.dataset.kat;
    const search = document.getElementById('searchInput').value;
    window.location.href = `destinasi.php?kategori=${kat}&search=${encodeURIComponent(search)}&page=1`;
  });
});

// Search on Enter
document.getElementById('searchInput').addEventListener('keydown', function (e) {
  if (e.key === 'Enter') {
    const kat = document.querySelector('.filter-btn.active')?.dataset.kat || 'all';
    window.location.href = `destinasi.php?search=${encodeURIComponent(this.value)}&kategori=${kat}&page=1`;
  }
});

// Search debounce
let searchTimer;
document.getElementById('searchInput').addEventListener('input', function () {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    const kat = document.querySelector('.filter-btn.active')?.dataset.kat || 'all';
    window.location.href = `destinasi.php?search=${encodeURIComponent(this.value)}&kategori=${kat}&page=1`;
  }, 600);
});
</script>
</body>
</html>