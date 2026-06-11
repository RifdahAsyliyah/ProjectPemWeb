<?php
session_start();
require_once '../config/db.php';
require_once 'auth_guard.php';

$msg = '';

// Handle hapus
if (isset($_GET['hapus'])) {
    $hid = intval($_GET['hapus']);
    // Ambil wisata_id untuk update rating
    $row = $conn->query("SELECT wisata_id FROM ulasan WHERE id=$hid")->fetch_assoc();
    $conn->query("DELETE FROM ulasan WHERE id=$hid");
    if ($row) {
        $wid = $row['wisata_id'];
        $avg = $conn->query("SELECT AVG(rating) as avg FROM ulasan WHERE wisata_id=$wid")->fetch_assoc()['avg'] ?? 0;
        $conn->query("UPDATE wisata SET rating=$avg WHERE id=$wid");
    }
    header('Location: ulasan.php?msg=success:Ulasan berhasil dihapus.'); exit;
}

// Filter
$search  = trim($_GET['search']  ?? '');
$rating  = $_GET['rating'] ?? '';
$page    = max(1, intval($_GET['page'] ?? 1));
$per     = 10; $offset = ($page-1)*$per;

$where = []; $params = []; $types = '';
if ($search) {
    $where[] = "(us.nama LIKE ? OR w.nama LIKE ? OR u.komentar LIKE ?)";
    $like = "%$search%"; $params = array_merge($params, [$like,$like,$like]); $types .= 'sss';
}
if ($rating) { $where[] = "u.rating=?"; $params[] = intval($rating); $types .= 'i'; }
$wsql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$total = 0;
$count_sql = "SELECT COUNT(*) as c FROM ulasan u JOIN users us ON u.user_id=us.id JOIN wisata w ON u.wisata_id=w.id $wsql";
if ($stmt = $conn->prepare($count_sql)) {
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
}
$total_pages = ceil($total / $per);

$ulasan_list = [];
$sql = "SELECT u.id, u.rating, u.komentar, u.created_at, us.nama as user_nama, w.nama as wisata_nama, w.id as wisata_id FROM ulasan u JOIN users us ON u.user_id=us.id JOIN wisata w ON u.wisata_id=w.id $wsql ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
if ($stmt = $conn->prepare($sql)) {
    $p = array_merge($params, [$per, $offset]); $t = $types.'ii';
    $stmt->bind_param($t, ...$p);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $ulasan_list[] = $r;
    $stmt->close();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
[$msg_type, $msg_text] = array_pad(explode(':', $msg, 2), 2, '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Ulasan &mdash; Admin PesonaNTB</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'includes/sidebar.php'; ?>
  <div class="admin-content">
    <?php include 'includes/topbar.php'; ?>
    <main class="admin-main">

      <?php if ($msg_text): ?>
      <div class="alert alert-<?= $msg_type==='success'?'success':'error' ?>"><?= htmlspecialchars($msg_text) ?></div>
      <?php endif; ?>

      <div class="admin-card">
        <div class="card-header">
          <h3>Data Ulasan <span class="badge badge-brown"><?= $total ?> data</span></h3>
        </div>
        <div class="card-body" style="padding-bottom:0">
          <div class="table-toolbar" style="margin-bottom:1.25rem">
            <div class="search-input-wrap">
              <span class="search-icon">🔍</span>
              <input type="text" id="tableSearch" placeholder="Cari nama pengguna, destinasi, atau komentar..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select class="filter-select" onchange="applyFilter('rating', this.value)">
              <option value="">Semua Rating</option>
              <?php for ($i=5;$i>=1;$i--): ?>
              <option value="<?=$i?>" <?= $rating==(string)$i?'selected':'' ?>><?= str_repeat('★',$i) ?> (<?=$i?>)</option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>#</th><th>Pengguna</th><th>Destinasi</th><th>Rating</th><th>Komentar</th><th>Tanggal</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              <?php if (empty($ulasan_list)): ?>
              <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">Tidak ada ulasan.</td></tr>
              <?php else: ?>
              <?php foreach ($ulasan_list as $i => $u): ?>
              <tr>
                <td class="td-muted"><?= $offset+$i+1 ?></td>
                <td><?= htmlspecialchars($u['user_nama']) ?></td>
                <td>
                  <a href="../detail.php?id=<?= $u['wisata_id'] ?>" target="_blank" style="color:var(--green);font-weight:600;text-decoration:none;font-size:0.85rem">
                    <?= htmlspecialchars($u['wisata_nama']) ?> ↗
                  </a>
                </td>
                <td>
                  <span class="badge badge-<?= $u['rating']>=4?'green':($u['rating']==3?'orange':'red') ?>">
                    <?= str_repeat('★',$u['rating']) ?> <?= $u['rating'] ?>
                  </span>
                </td>
                <td class="td-truncate td-muted" style="max-width:220px"><?= htmlspecialchars($u['komentar']) ?></td>
                <td class="td-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                  <form method="GET" style="display:inline">
                    <input type="hidden" name="hapus" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm btn-confirm-delete" data-msg="Ulasan ini akan dihapus dan rating destinasi akan diperbarui.">🗑 Hapus</button>
                  </form>
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
            <?php for ($i=1;$i<=$total_pages;$i++): ?>
            <a href="?page=<?=$i?>&search=<?=urlencode($search)?>&rating=<?=urlencode($rating)?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>
<?php include 'includes/modal.php'; ?>
<script src="js/admin.js"></script>
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