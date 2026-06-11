<?php
session_start();
require_once 'db.php';
require_once 'auth_guard.php';

$msg = '';

// Handle hapus
if (isset($_GET['hapus'])) {
    $hid  = intval($_GET['hapus']);
    // Hapus foto jika ada
    $foto = $conn->query("SELECT foto FROM wisata WHERE id=$hid")->fetch_assoc()['foto'] ?? '';
    if ($foto && file_exists("uploads/$foto")) unlink("uploads/$foto");
    $conn->query("DELETE FROM wisata WHERE id=$hid");
    $msg = 'success:Wisata berhasil dihapus.';
    header('Location: wisata.php?msg=' . urlencode($msg)); exit;
}

// Filter & search
$search   = trim($_GET['search']   ?? '');
$kat      = trim($_GET['kategori'] ?? '');
$status   = $_GET['status'] ?? '';
$page     = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset   = ($page - 1) * $per_page;

$where  = []; $params = []; $types = '';
if ($search) { $where[] = "(nama LIKE ? OR lokasi LIKE ?)"; $like = "%$search%"; $params = array_merge($params, [$like, $like]); $types .= 'ss'; }
if ($kat)    { $where[] = "kategori=?"; $params[] = $kat; $types .= 's'; }
if ($status !== '') { $where[] = "aktif=?"; $params[] = intval($status); $types .= 'i'; }
$wsql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$total = 0;
if ($stmt = $conn->prepare("SELECT COUNT(*) as c FROM wisata $wsql")) {
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
}
$total_pages = ceil($total / $per_page);

$wisata_list = [];
if ($stmt = $conn->prepare("SELECT * FROM wisata $wsql ORDER BY created_at DESC LIMIT ? OFFSET ?")) {
    $p = array_merge($params, [$per_page, $offset]); $t = $types . 'ii';
    $stmt->bind_param($t, ...$p);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $wisata_list[] = $r;
    $stmt->close();
}

// Ambil kategori untuk filter
$kategori_opts = [];
$res = $conn->query("SELECT nama FROM kategori ORDER BY nama");
if ($res) while ($r = $res->fetch_assoc()) $kategori_opts[] = $r['nama'];

if (isset($_GET['msg'])) $msg = $_GET['msg'];
[$msg_type, $msg_text] = array_pad(explode(':', $_GET['msg'] ?? '', 2), 2, '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Wisata &mdash; Admin PesonaNTB</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="admin-content">
    <?php include 'topbar.php'; ?>
    <main class="admin-main">

      <?php if ($msg_text): ?>
      <div class="alert alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($msg_text) ?></div>
      <?php endif; ?>

      <div class="admin-card">
        <div class="card-header">
          <h3>Data Wisata <span class="badge badge-brown" style="font-size:0.75rem"><?= $total ?> data</span></h3>
          <a href="wisata_form.php" class="btn btn-primary">+ Tambah Wisata</a>
        </div>
        <div class="card-body" style="padding-bottom:0">
          <!-- Toolbar -->
          <div class="table-toolbar" style="margin-bottom:1.25rem">
            <div class="search-input-wrap">
              <span class="search-icon">🔍</span>
              <input type="text" id="tableSearch" placeholder="Cari nama atau lokasi..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select class="filter-select" onchange="applyFilter('kategori', this.value)">
              <option value="">Semua Kategori</option>
              <?php foreach ($kategori_opts as $k): ?>
              <option value="<?= $k ?>" <?= $kat === $k ? 'selected' : '' ?>><?= $k ?></option>
              <?php endforeach; ?>
            </select>
            <select class="filter-select" onchange="applyFilter('status', this.value)">
              <option value="">Semua Status</option>
              <option value="1" <?= $status==='1'?'selected':'' ?>>Aktif</option>
              <option value="0" <?= $status==='0'?'selected':'' ?>>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Nama Wisata</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($wisata_list)): ?>
              <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted)">Tidak ada data wisata.</td></tr>
              <?php else: ?>
              <?php foreach ($wisata_list as $i => $w): ?>
              <tr>
                <td class="td-muted"><?= $offset + $i + 1 ?></td>
                <td>
                  <?php if ($w['foto'] && file_exists("uploads/{$w['foto']}")): ?>
                  <img src="uploads/<?= htmlspecialchars($w['foto']) ?>" class="foto-thumb" alt="">
                  <?php else: ?>
                  <div style="width:40px;height:40px;border-radius:6px;background:var(--sand-light);display:flex;align-items:center;justify-content:center;font-size:1.1rem">🏝️</div>
                  <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($w['nama']) ?></strong></td>
                <td><span class="badge badge-brown"><?= htmlspecialchars($w['kategori']) ?></span></td>
                <td class="td-muted"><?= htmlspecialchars($w['lokasi']) ?></td>
                <td><span class="badge badge-orange">★ <?= number_format($w['rating'],1) ?></span></td>
                <td>
                  <label class="toggle-wrap">
                    <div class="toggle-switch">
                      <input type="checkbox" class="toggle-aktif" data-id="<?= $w['id'] ?>" <?= $w['aktif'] ? 'checked' : '' ?>>
                      <span class="toggle-slider"></span>
                    </div>
                    <span style="font-size:0.78rem;color:var(--text-muted)"><?= $w['aktif'] ? 'Aktif' : 'Nonaktif' ?></span>
                  </label>
                </td>
                <td>
                  <div class="table-actions">
                    <a href="wisata_form.php?id=<?= $w['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                    <form method="GET" style="display:inline">
                      <input type="hidden" name="hapus" value="<?= $w['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm btn-confirm-delete" data-msg="Wisata '<?= htmlspecialchars($w['nama']) ?>' akan dihapus permanen.">🗑</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <div style="padding:1rem 1.5rem">
          <div class="pagination">
            <?php for ($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?=$i?>&search=<?=urlencode($search)?>&kategori=<?=urlencode($kat)?>&status=<?=urlencode($status)?>"
               class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>
<?php include 'modal.php'; ?>
<script src="../js/admin.js"></script>
<script>
function applyFilter(key, val) {
  const url = new URL(window.location);
  url.searchParams.set(key, val);
  url.searchParams.set('page', 1);
  window.location = url;
}
</script>
</body>
</html>