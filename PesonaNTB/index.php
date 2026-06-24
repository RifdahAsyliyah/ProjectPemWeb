<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/db.php';

// Statistik dinamis untuk hero section
$stat_wisata = $conn->query("SELECT * FROM wisata WHERE aktif = 1 ORDER BY rating")->num_rows ?? 0;
$stat_kategori = $conn->query("SELECT COUNT(*) as c FROM kategori")->fetch_assoc()['c'] ?? 0;
$stat_user     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'] ?? 0;

// Ambil destinasi populer dari database (limit 6)
$sql = "SELECT * FROM wisata WHERE aktif = 1 ORDER BY rating DESC LIMIT 6";
$result = $conn->query($sql);
$destinasi_populer = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $destinasi_populer[] = $row;
    }
}

// Fallback data statis jika database kosong
if (empty($destinasi_populer)) {
    $destinasi_populer = [
        ['id'=>1,'nama'=>'Pantai Senggigi','lokasi'=>'Lombok Barat','kategori'=>'Pantai','deskripsi'=>'Pantai ikonik dengan pemandangan matahari terbenam yang memukau.','rating'=>4.8,'img_class'=>'dest-img-pantai','emoji'=>'🏖️'],
        ['id'=>2,'nama'=>'Gunung Rinjani','lokasi'=>'Lombok Utara','kategori'=>'Gunung','deskripsi'=>'Gunung berapi tertinggi kedua di Indonesia dengan danau kawah Segara Anak.','rating'=>4.9,'img_class'=>'dest-img-rinjani','emoji'=>'🏔️'],
        ['id'=>3,'nama'=>'Gili Trawangan','lokasi'=>'Lombok Utara','kategori'=>'Pulau','deskripsi'=>'Pulau kecil surga bawah laut dengan snorkeling dan penyu laut.','rating'=>4.7,'img_class'=>'dest-img-gili','emoji'=>'🏝️'],
        ['id'=>4,'nama'=>'Pantai Pink','lokasi'=>'Lombok Timur','kategori'=>'Pantai','deskripsi'=>'Salah satu dari sedikit pantai berpasir merah muda di dunia.','rating'=>4.6,'img_class'=>'dest-img-pink','emoji'=>'🌸'],
        ['id'=>5,'nama'=>'Savana Sumbawa','lokasi'=>'Sumbawa Besar','kategori'=>'Adventure','deskripsi'=>'Padang savana luas dengan kuda liar dan pemandangan alam alami.','rating'=>4.7,'img_class'=>'dest-img-sumbawa','emoji'=>'🌾'],
        ['id'=>6,'nama'=>'Pulau Moyo','lokasi'=>'Sumbawa','kategori'=>'Pulau','deskripsi'=>'Pulau terpencil dengan air terjun tersembunyi dan ekosistem bawah laut.','rating'=>4.8,'img_class'=>'dest-img-moyo','emoji'=>'💧'],
    ];
}

// Ambil kategori + jumlah wisata aktif dari database
$kategori_list = [];
$res_kat = $conn->query("SELECT k.nama, k.emoji, COUNT(w.id) as count FROM kategori k LEFT JOIN wisata w ON w.kategori = k.nama AND w.aktif = 1 GROUP BY k.id ORDER BY k.nama");
if ($res_kat) {
    while ($row = $res_kat->fetch_assoc()) {
        $slug = strtolower(str_replace(' ', '-', $row['nama']));
        $kategori_list[] = [
            'nama'  => $row['nama'],
            'emoji' => $row['emoji'],
            'count' => $row['count'],
            'slug'  => $slug,
        ];
    }
}
// Ambil ulasan terbaik untuk landing page (rating >= 4, terbaru, limit 3)
$testimoni_list = [];
$res_testi = $conn->query("SELECT u.komentar, u.rating, us.nama, us.email, us.foto_profil, w.nama AS wisata_nama FROM ulasan u JOIN users us ON u.user_id = us.id JOIN wisata w ON w.id = u.wisata_id WHERE u.rating >= 4 ORDER BY u.created_at DESC LIMIT 6");
if ($res_testi) {
    while ($row = $res_testi->fetch_assoc()) {
        $testimoni_list[] = $row;
    }
}
// Fallback statis jika belum ada ulasan
if (empty($testimoni_list)) {
    $testimoni_list = [
        ['nama'=>'Andi Ramadhan', 'email'=>'ar@mail.com', 'rating'=>5, 'komentar'=>'PesonaNTB sangat membantu perjalanan saya ke Lombok. Semua informasi lengkap dan akurat, terutama petunjuk Google Maps-nya!'],
        ['nama'=>'Siti Rahma',    'email'=>'sr@mail.com', 'rating'=>5, 'komentar'=>'Akhirnya ada website wisata NTB yang tidak ribet. Kategorinya jelas, dan mudah sekali menemukan lokasi wisata yang dicari.'],
        ['nama'=>'Budi Pratama',  'email'=>'bp@mail.com', 'rating'=>4, 'komentar'=>'Informasi Pulau Moyo di sini paling lengkap dibanding website lain. Sangat membantu untuk persiapan trip ke Sumbawa.'],
    ];
}

// Fallback jika database kosong
if (empty($kategori_list)) {
    $kategori_list = [
        ['nama'=>'Pantai',    'emoji'=>'🏖️','count'=>0,'slug'=>'pantai'],
        ['nama'=>'Gunung',    'emoji'=>'🏔️','count'=>0,'slug'=>'gunung'],
        ['nama'=>'Air Terjun','emoji'=>'💧', 'count'=>0,'slug'=>'air-terjun'],
        ['nama'=>'Budaya',    'emoji'=>'🎭','count'=>0,'slug'=>'budaya'],
        ['nama'=>'Pulau',     'emoji'=>'🏝️','count'=>0,'slug'=>'pulau'],
        ['nama'=>'Kuliner',   'emoji'=>'🍜','count'=>0,'slug'=>'kuliner'],
        ['nama'=>'Adventure', 'emoji'=>'🧗','count'=>0,'slug'=>'adventure'],
    ];
}

$img_map = ['Pantai'=>'dest-img-pantai','Gunung'=>'dest-img-rinjani','Pulau'=>'dest-img-gili','Adventure'=>'dest-img-sumbawa'];
$emoji_map = ['Pantai'=>'🏖️','Gunung'=>'🏔️','Pulau'=>'🏝️','Adventure'=>'🌾','Air Terjun'=>'💧','Budaya'=>'🎭','Kuliner'=>'🍜'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PesonaNTB &mdash; Sistem Informasi Wisata Nusa Tenggara Barat</title>
  <meta name="description" content="Temukan destinasi wisata terbaik di Nusa Tenggara Barat. Pantai, gunung, pulau, budaya, dan banyak lagi.">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-content">
    <div class="hero-badge">🌴 Jelajahi Nusa Tenggara Barat</div>
    <h1>Temukan <em>Pesona</em> Alam &amp; Budaya NTB</h1>
    <p>Dari pantai berpasir putih Lombok hingga savana liar Sumbawa &mdash; PesonaNTB hadir sebagai panduan wisata lengkap Nusa Tenggara Barat yang terpercaya.</p>
    <div class="hero-actions">
      <a href="<?= isset($_SESSION['user_id']) ? 'config/destinasi.php' : '#destinasi' ?>" class="btn-primary">Jelajahi Destinasi</a>
      <a href="#tentang" class="btn-secondary">Pelajari Lebih Lanjut</a>
    </div>
    <div class="hero-stats">
      <div class="stat-item"><div class="stat-num"><?= $stat_wisata ?></div><div class="stat-label">Destinasi Wisata</div></div>
      <div class="stat-item"><div class="stat-num"><?= $stat_kategori ?></div><div class="stat-label">Kategori Wisata</div></div>
      <div class="stat-item"><div class="stat-num"><?= $stat_user ?></div><div class="stat-label">Pengguna Aktif</div></div>
    </div>
  </div>
</section>

<!-- DESTINASI POPULER -->
<section class="section-destinasi" id="destinasi">
  <div style="max-width:1100px;margin:0 auto">
    <div class="section-header">
      <span class="section-label">✦ Destinasi Populer</span>
      <h2 class="section-title">Wisata Pilihan di NTB</h2>
      <p class="section-sub">Destinasi terbaik yang paling banyak dikunjungi wisatawan dari seluruh penjuru Indonesia.</p>
    </div>
    <div class="dest-grid">
      <?php foreach ($destinasi_populer as $d): 
        $img_class = $img_map[$d['kategori']] ?? 'dest-img-pantai';
        $emoji     = $emoji_map[$d['kategori']] ?? '🏝️';
        if (isset($d['img_class'])) $img_class = $d['img_class'];
        if (isset($d['emoji']))     $emoji     = $d['emoji'];
      ?>
      <a href="<?= isset($_SESSION['user_id']) ? 'config/detail.php?id='.$d['id'] : 'config/login.php' ?>" class="dest-card">
        <div class="dest-img <?= htmlspecialchars($img_class) ?>" style="<?= (!empty($d['foto'])) ? 'background: none;' : '' ?>">
  <?php if (!empty($d['foto'])): ?>
    <img src="assets/uploads/destinasi/<?= htmlspecialchars($d['foto']) ?>" alt="<?= htmlspecialchars($d['nama']) ?>" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; border-radius: 12px 12px 0 0;">
  <?php else: ?>
    <div class="dest-emoji"><?= $emoji ?></div>
  <?php endif; ?>
  
  <span class="dest-badge" style="z-index: 2;"><?= htmlspecialchars($d['kategori']) ?></span>
</div>
        <div class="dest-body">
          <div class="dest-name"><?= htmlspecialchars($d['nama']) ?></div>
          <div class="dest-loc">📍 <?= htmlspecialchars($d['lokasi']) ?></div>
          <div class="dest-desc"><?= htmlspecialchars(mb_substr($d['deskripsi'], 0, 90)) ?>...</div>
        </div>
        <div class="dest-foot">
          <span class="dest-rating">★ <?= number_format($d['rating'], 1) ?></span>
          <span class="dest-link">Lihat Detail →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="view-all">
      <a href="<?= isset($_SESSION['user_id']) ? 'config/destinasi.php' : 'config/login.php' ?>" class="btn-view-all">Lihat Semua Destinasi</a>
    </div>
  </div>
</section>

<!-- KATEGORI -->
<section class="section-kategori" id="kategori">
  <div style="max-width:900px;margin:0 auto">
    <div class="section-header centered">
      <span class="section-label">✦ Jelajahi Berdasarkan</span>
      <h2 class="section-title">Kategori Wisata</h2>
      <p class="section-sub">Temukan destinasi sesuai minat dan gaya perjalananmu.</p>
    </div>
    <div class="kat-grid">
      <?php foreach ($kategori_list as $k): ?>
      <?php 
        // Cek apakah aktor pengguna sudah login atau masih guest
        if (isset($_SESSION['user_id'])) {
            // Sudah Login: langsung bawa ke destinasi.php sesuai parameternya
            $target_url = "config/destinasi.php?kategori=" . $k['slug'];
        } else {
            // Masih Guest: lempar ke login.php, titip parameter redirect biar setelah login sukses langsung meluncur ke kategori tsb
            $target_url = "config/login.php?redirect=destinasi.php?kategori=" . $k['slug'];
        }
      ?>
      <a href="<?= $target_url ?>" class="kat-card">
        <div class="kat-icon"><?= $k['emoji'] ?></div>
        <div class="kat-name"><?= $k['nama'] ?></div>
        <div class="kat-count"><?= $k['count'] ?> destinasi</div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TENTANG -->
<section class="section-tentang" id="tentang">
  <div class="tentang-inner">
    <div class="tentang-visual" style="background-image: url('assets/foto/about.jpg'); background-size: cover; background-position: center;">
      <div class="tentang-overlay">
      </div>
    </div>
    <div>
      <span class="section-label">✦ Tentang Kami</span>
      <h2 class="section-title">Panduan Wisata NTB Terpercaya</h2>
      <p class="tentang-desc">PesonaNTB hadir sebagai platform informasi wisata yang menyatukan semua destinasi di Nusa Tenggara Barat dalam satu tempat yang mudah diakses oleh wisatawan maupun masyarakat umum.</p>
      <p class="tentang-desc">Kami menyediakan informasi yang lengkap, akurat, dan selalu diperbarui agar perjalananmu ke NTB menjadi lebih mudah dan menyenangkan.</p>
      <ul class="tentang-features">
        <li><span class="feat-dot"></span>Informasi destinasi wisata lengkap dan selalu diperbarui oleh admin</li>
        <li><span class="feat-dot"></span>Fitur pencarian cepat berdasarkan nama dan kategori wisata</li>
        <li><span class="feat-dot"></span>Integrasi Google Maps untuk petunjuk arah langsung ke lokasi</li>
        <li><span class="feat-dot"></span>Mencakup destinasi di Pulau Lombok dan Pulau Sumbawa</li>
        <li><span class="feat-dot"></span>Gratis diakses oleh seluruh wisatawan dan masyarakat umum</li>
      </ul>
    </div>
  </div>
</section>

<!-- TESTIMONI -->
<section class="section-testimoni" id="testimoni">
  <div style="max-width:1000px;margin:0 auto">
    <div class="section-header centered">
      <span class="section-label">✦ Testimoni</span>
      <h2 class="section-title">Kata Mereka tentang PesonaNTB</h2>
      <p class="section-sub">Pengalaman nyata dari wisatawan yang telah menggunakan PesonaNTB.</p>
    </div>
    <div class="testi-grid">
  <?php foreach ($testimoni_list as $t): 
    $stars = str_repeat('★', $t['rating']) . str_repeat('☆', 5 - $t['rating']);
    // Cek apakah ada foto profil dan apakah file-nya ada di folder
    $path_foto = "assets/uploads/profil/" . $t['foto_profil'];
    $ada_foto = (!empty($t['foto_profil']) && file_exists($path_foto));
  ?>
  <div class="testi-card">
    <div class="testi-stars"><?= $stars ?></div>

      <div class="testi-wisata">
      📍 <?= htmlspecialchars($t['wisata_nama']) ?>
      </div>

      <p class="testi-text">
      "<?= htmlspecialchars($t['komentar'] ?: 'Tidak ada komentar') ?>"
      </p>
    <div class="testi-author">
      
      <?php if ($ada_foto): ?>
        <img src="<?= $path_foto ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover; margin-right:12px;">
      <?php else: ?>
        <div class="testi-avatar av-brown"><?= strtoupper(mb_substr($t['nama'], 0, 2)) ?></div>
      <?php endif; ?>
      
      <div>
        <div class="testi-name"> <?= htmlspecialchars($t['nama'] ?? 'Pengguna') ?> </div>
        <div class="testi-origin">Pengguna PesonaNTB</div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
  </div>
</section>

<!-- CTA -->
<section class="section-cta">
  <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Ditampilkan hanya jika PENGGUNA SUDAH LOGIN -->
    <h2>Ayo Temukan Destinasi Impianmu!</h2>
    <p>Mulai jelajahi berbagai keindahan alam dan budaya Nusa Tenggara Barat sekarang juga.</p>
    <div class="cta-actions">
      <a href="config/destinasi.php" class="btn-cta-main">Mulai Jelajahi</a>
    </div>
  <?php else: ?>
    <!-- Ditampilkan hanya jika MASIH GUEST -->
    <h2>Siap Menjelajahi NTB?</h2>
    <p>Daftarkan akun gratis dan nikmati akses penuh ke semua destinasi wisata Nusa Tenggara Barat.</p>
    <div class="cta-actions">
      <a href="config/register.php" class="btn-cta-main">Daftar Sekarang</a>
      <a href="config/login.php" class="btn-cta-sec">Lihat Destinasi</a>
    </div>
  <?php endif; ?>
</section>

<?php include 'config/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
