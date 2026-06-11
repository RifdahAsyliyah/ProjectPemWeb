<?php
session_start();
require_once '../config/db.php';
require_once 'auth_guard.php';

$msg = '';

// Handle hapus
if (isset($_GET['hapus'])) {
    $msg = 'error:Aksi tidak diizinkan. Admin tidak dapat menghapus akun pengguna.';
}

// Handle reset password
if (isset($_GET['reset'])) {
    $rid  = intval($_GET['reset']);
    $hash = password_hash('password123', PASSWORD_BCRYPT);
    $conn->query("UPDATE users SET password='$hash' WHERE id=$rid AND role='user'");
    $msg = 'success:Password berhasil direset ke: password123';
}

// Search & filter
$search = trim($_GET['search'] ?? '');
$page   = max(1, intval($_GET['page'] ?? 1));
$per    = 10; $offset = ($page-1)*$per;

$where = "WHERE role='user'";
$params = []; $types = '';
if ($search) {
    $where .= " AND (nama LIKE ? OR email LIKE ? OR telepon LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like, $like]; $types = 'sss';
}

$total = 0;
if ($stmt = $conn->prepare("SELECT COUNT(*) as c FROM users $where")) {
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
}
$total_pages = ceil($total / $per);

$users = [];
if ($stmt = $conn->prepare("SELECT u.*, (SELECT COUNT(*) FROM bookmark WHERE user_id=u.id) as jml_bookmark, (SELECT COUNT(*) FROM ulasan WHERE user_id=u.id) as jml_ulasan FROM users u $where ORDER BY u.created_at DESC LIMIT ? OFFSET ?")) {
    $p = array_merge($params, [$per, $offset]); $t = $types . 'ii';
    $stmt->bind_param($t, ...$p);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $users[] = $r;
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
  <title>Kelola Pengguna &mdash; Admin PesonaNTB</title>
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
          <h3>Data Pengguna <span class="badge badge-brown"><?= $total ?> data</span></h3>
        </div>
        <div class="card-body" style="padding-bottom:0">
          <div class="table-toolbar" style="margin-bottom:1.25rem">
            <div class="search-input-wrap">
              <span class="search-icon">🔍</span>
              <input type="text" id="searchPengguna" placeholder="Cari nama, email, atau telepon..." value="<?= htmlspecialchars($search) ?>">
            </div>
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>#</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Bookmark</th><th>Ulasan</th><th>Bergabung</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
              <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-muted)">Tidak ada data pengguna.</td></tr>
              <?php else: ?>
              <?php foreach ($users as $i => $u): ?>
              <tr>
                <td class="td-muted"><?= $offset+$i+1 ?></td>
                <td>
                  <div style="display:flex;align-items:center;gap:0.6rem">
                    <div style="width:32px;height:32px;border-radius:50%;background:var(--brown);color:var(--white);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;flex-shrink:0">
                      <?= strtoupper(mb_substr($u['nama'],0,2)) ?>
                    </div>
                    <strong><?= htmlspecialchars($u['nama']) ?></strong>
                  </div>
                </td>
                <td class="td-muted"><?= htmlspecialchars($u['email']) ?></td>
                <td class="td-muted"><?= htmlspecialchars($u['telepon'] ?? '-') ?></td>
                <td><span class="badge badge-green"><?= $u['jml_bookmark'] ?></span></td>
                <td><span class="badge badge-orange"><?= $u['jml_ulasan'] ?></span></td>
                <td class="td-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                  <div class="table-actions">
                    <a href="pengguna.php?reset=<?= $u['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Reset password ke: password123?')">🔑 Reset</a>
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
            <?php for ($i=1;$i<=$total_pages;$i++): ?>
            <a href="?page=<?=$i?>&search=<?=urlencode($search)?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
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
let searchTimer;
document.getElementById('searchPengguna').addEventListener('input', function() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    window.location.href = `pengguna.php?search=${encodeURIComponent(this.value)}&page=1`;
  }, 500);
});
</script>
</body>
</html>