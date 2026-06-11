<?php
session_start();
require_once 'db.php';
require_once 'auth_guard.php';

$msg = '';

// Handle tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $nama  = trim($_POST['nama']  ?? '');
    $emoji = trim($_POST['emoji'] ?? '');
    $id    = intval($_POST['id']  ?? 0);

    if (empty($nama)) {
        $msg = 'error:Nama kategori wajib diisi.';
    } else {
        if ($_POST['action'] === 'tambah') {
            $stmt = $conn->prepare("INSERT INTO kategori (nama, emoji) VALUES (?, ?)");
            $stmt->bind_param('ss', $nama, $emoji);
            $stmt->execute(); $stmt->close();
            $msg = 'success:Kategori berhasil ditambahkan.';
        } elseif ($_POST['action'] === 'edit' && $id) {
            $stmt = $conn->prepare("UPDATE kategori SET nama=?, emoji=? WHERE id=?");
            $stmt->bind_param('ssi', $nama, $emoji, $id);
            $stmt->execute(); $stmt->close();
            $msg = 'success:Kategori berhasil diperbarui.';
        }
    }
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $hid = intval($_GET['hapus']);
    // Cek apakah ada wisata dengan kategori ini
    $kat_nama = $conn->query("SELECT nama FROM kategori WHERE id=$hid")->fetch_assoc()['nama'] ?? '';
    $used = $conn->query("SELECT COUNT(*) as c FROM wisata WHERE kategori='$kat_nama'")->fetch_assoc()['c'] ?? 0;
    if ($used > 0) {
        $msg = 'error:Kategori tidak bisa dihapus karena masih digunakan oleh ' . $used . ' wisata.';
    } else {
        $conn->query("DELETE FROM kategori WHERE id=$hid");
        $msg = 'success:Kategori berhasil dihapus.';
    }
}

// Ambil semua kategori
$kategori_list = [];
$res = $conn->query("SELECT k.*, COUNT(w.id) as jml_wisata FROM kategori k LEFT JOIN wisata w ON w.kategori=k.nama GROUP BY k.id ORDER BY k.nama");
if ($res) while ($r = $res->fetch_assoc()) $kategori_list[] = $r;

// Edit mode
$edit_data = null;
if (isset($_GET['edit'])) {
    $eid  = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE id=?");
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

[$msg_type, $msg_text] = array_pad(explode(':', $msg, 2), 2, '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Kategori &mdash; Admin PesonaNTB</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include 'sidebar.php'; ?>
  <div class="admin-content">
    <?php include 'topbar.php'; ?>
    <main class="admin-main">

      <?php if ($msg_text): ?>
      <div class="alert alert-<?= $msg_type==='success'?'success':'error' ?>"><?= htmlspecialchars($msg_text) ?></div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start">

        <!-- Tabel Kategori -->
        <div class="admin-card">
          <div class="card-header">
            <h3>Data Kategori <span class="badge badge-brown"><?= count($kategori_list) ?> data</span></h3>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>#</th><th>Emoji</th><th>Nama Kategori</th><th>Jumlah Wisata</th><th>Aksi</th></tr>
              </thead>
              <tbody>
                <?php if (empty($kategori_list)): ?>
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">Belum ada kategori.</td></tr>
                <?php else: ?>
                <?php foreach ($kategori_list as $i => $k): ?>
                <tr>
                  <td class="td-muted"><?= $i+1 ?></td>
                  <td style="font-size:1.4rem"><?= htmlspecialchars($k['emoji']) ?></td>
                  <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
                  <td><span class="badge badge-green"><?= $k['jml_wisata'] ?> wisata</span></td>
                  <td>
                    <div class="table-actions">
                      <a href="kategori.php?edit=<?= $k['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                      <?php if ($k['jml_wisata'] == 0): ?>
                      <form method="GET" style="display:inline">
                        <input type="hidden" name="hapus" value="<?= $k['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-confirm-delete" data-msg="Kategori '<?= htmlspecialchars($k['nama']) ?>' akan dihapus.">🗑</button>
                      </form>
                      <?php else: ?>
                      <button class="btn btn-sm" style="background:var(--sand-light);color:var(--text-muted);cursor:not-allowed" title="Tidak bisa dihapus, masih digunakan">🔒</button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Form -->
        <div class="admin-card">
          <div class="card-header">
            <h3><?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>
            <?php if ($edit_data): ?>
            <a href="kategori.php" class="btn btn-light btn-sm">+ Tambah Baru</a>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
              <?php if ($edit_data): ?>
              <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
              <?php endif; ?>

              <div class="form-group" style="margin-bottom:1rem">
                <label>Nama Kategori <span style="color:#C0392B">*</span></label>
                <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" placeholder="Contoh: Pantai" required>
              </div>

              <div class="form-group" style="margin-bottom:1.5rem">
                <label>Emoji</label>
                <input type="text" name="emoji" value="<?= htmlspecialchars($edit_data['emoji'] ?? '') ?>" placeholder="Contoh: 🏖️">
                <div class="form-hint">Salin emoji dari: emojipedia.org</div>
              </div>

              <div class="form-actions" style="margin-top:0">
                <button type="submit" class="btn btn-primary">💾 <?= $edit_data ? 'Simpan' : 'Tambah' ?></button>
                <?php if ($edit_data): ?><a href="kategori.php" class="btn btn-light">Batal</a><?php endif; ?>
              </div>
            </form>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>
<?php include 'modal.php'; ?>
<script src="../js/admin.js"></script>
</body>
</html>