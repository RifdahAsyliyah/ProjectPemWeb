<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current    = basename($_SERVER['PHP_SELF']);
$is_login   = isset($_SESSION['user_id']);
$user_nama  = $_SESSION['user_nama'] ?? '';
$user_role  = $_SESSION['role'] ?? 'user';
$inisial    = strtoupper(substr($user_nama, 0, 2));

$is_config = strpos($_SERVER['PHP_SELF'], '/config/') !== false;
$base = $is_config ? '../' : '';
?>

<nav class="navbar" id="mainNavbar">
  <a href="<?= $base ?>index.php" class="nav-logo">Pesona<span>NTB</span></a>

  <ul class="nav-links" id="navLinks">
    <li><a href="<?= $base ?>index.php" id="nav-beranda">Beranda</a></li>
    <li><a href="<?= $is_login ? $base . 'config/destinasi.php' : $base . 'index.php#destinasi' ?>" id="nav-destinasi" class="<?= $current == 'destinasi.php' ? 'active' : '' ?>">Destinasi</a></li>
    <li><a href="<?= $base ?>index.php#kategori" id="nav-kategori">Kategori</a></li>
    <li><a href="<?= $base ?>index.php#tentang" id="nav-tentang">Tentang</a></li>
  </ul>

  <div class="nav-actions">
    <?php if ($is_login): ?>
      <?php if ($user_role === 'admin'): ?>
        <a href="<?= $base ?>config/dashboard.php"
        class="btn-outline">
        Dashboard Admin
        </a>

        <?php else: ?>

        <a href="<?= $base ?>config/user_dashboard.php"
        class="btn-outline">
        Dashboard Saya
        </a>

      <?php endif; ?>
      <div class="nav-user-menu">
        <?php
        // Ambil foto profil dari database
        $foto_profil_nav = '';
        if (isset($_SESSION['user_id'])) {
            $stmt_nav = $conn->prepare("SELECT foto_profil FROM users WHERE id=?");
            $stmt_nav->bind_param('i', $_SESSION['user_id']);
            $stmt_nav->execute();
            $row_nav = $stmt_nav->get_result()->fetch_assoc();
            $stmt_nav->close();
            $foto_profil_nav = $row_nav['foto_profil'] ?? '';
        }
        $foto_path_nav = __DIR__ . '/../assets/uploads/profil/' . $foto_profil_nav;
        ?>
        <button class="nav-user-btn" id="userMenuBtn">
          <?php if (!empty($foto_profil_nav) && file_exists($foto_path_nav)): ?>
          <span class="nav-avatar" style="background:none;overflow:hidden;padding:0">
            <img src="<?= $base ?>assets/uploads/profil/<?= htmlspecialchars($foto_profil_nav) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%" alt="">
          </span>
          <?php else: ?>
          <span class="nav-avatar"><?= $inisial ?></span>
          <?php endif; ?>
          <span class="nav-user-name"><?= htmlspecialchars($user_nama) ?></span>
          <span>▾</span>
        </button>
        
        <div class="nav-dropdown" id="userDropdown">
          <a href="<?= $is_config ? 'profil.php' : 'config/profil.php' ?>">👤 Profil Saya</a>
          
          <?php if ($user_role !== 'admin'): ?>
            <a href="<?= $is_config ? 'bookmark.php' : 'config/bookmark.php' ?>">🔖 Tersimpan</a>
            <a href="<?= $is_config ? 'riwayat.php' : 'config/riwayat.php' ?>">🕐 Riwayat</a>
          <?php endif; ?>
          
          <div class="dropdown-divider"></div>
          <a href="<?= $is_config ? 'logout.php' : 'config/logout.php' ?>" style="color:#C0392B">🚪 Keluar</a>
        </div>
      </div>
    <?php else: ?>
      <a href="<?= $base ?>config/login.php" class="btn-outline">Masuk</a>
      <a href="<?= $base ?>config/register.php" class="btn-fill">Daftar</a>
    <?php endif; ?>
  </div>

  <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<script>
(function() {
  var path = window.location.pathname;
  var isIndex = path.includes('index.php') || path.endsWith('/Guest/') || path.endsWith('/');

  if (!isIndex) return;

  var navBeranda   = document.getElementById('nav-beranda');
  var navDestinasi= document.getElementById('nav-destinasi');
  var navKategori = document.getElementById('nav-kategori');
  var navTentang   = document.getElementById('nav-tentang');

  function clearActive() {
    [navBeranda, navDestinasi, navKategori, navTentang].forEach(function(a) {
      if (a) a.classList.remove('active');
    });
  }

  function updateActive() {
    var scrollY = window.scrollY;
    var secTestimoni = document.getElementById('testimoni');
    var secTentang   = document.getElementById('tentang');
    var secKategori  = document.getElementById('kategori');
    var secDestinasi = document.getElementById('destinasi');

    clearActive();

    // Di section testimoni → tidak ada yang aktif
    if (secTestimoni && scrollY >= secTestimoni.offsetTop - 150) {
      // tidak ada yang aktif
    } else if (secTentang && scrollY >= secTentang.offsetTop - 150) {
      navTentang && navTentang.classList.add('active');
    } else if (secKategori && scrollY >= secKategori.offsetTop - 150) {
      navKategori && navKategori.classList.add('active');
    } else if (secDestinasi && scrollY >= secDestinasi.offsetTop - 150) {
      navDestinasi && navDestinasi.classList.add('active');
    } else {
      navBeranda && navBeranda.classList.add('active');
    }
  }

  window.addEventListener('load', updateActive);
  window.addEventListener('scroll', updateActive);

  // Klik langsung update active
  if (navBeranda)   navBeranda.addEventListener('click',   function() { clearActive(); navBeranda.classList.add('active'); });
  if (navDestinasi) navDestinasi.addEventListener('click', function() { clearActive(); navDestinasi.classList.add('active'); });
  if (navKategori)  navKategori.addEventListener('click',  function() { clearActive(); navKategori.classList.add('active'); });
  if (navTentang)   navTentang.addEventListener('click',   function() { clearActive(); navTentang.classList.add('active'); });
})();
</script>