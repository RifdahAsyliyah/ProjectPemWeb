<?php
session_start();
require_once 'db.php';
require_once 'auth_guard.php';

$id    = intval($_GET['id'] ?? 0);
$edit  = $id > 0;
$wisata = ['nama'=>'','kategori'=>'','lokasi'=>'','deskripsi'=>'','fasilitas'=>'','jam_buka'=>'','harga_tiket'=>'','latitude'=>'','longitude'=>'','aktif'=>1,'foto'=>''];
$msg   = '';

if ($edit) {
    $stmt = $conn->prepare("SELECT * FROM wisata WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) { header('Location: wisata.php'); exit; }
    $wisata = $row;
}

// Ambil kategori
$kategori_opts = [];
$res = $conn->query("SELECT nama FROM kategori ORDER BY nama");
if ($res) while ($r = $res->fetch_assoc()) $kategori_opts[] = $r['nama'];

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama']        ?? '');
    $kategori   = trim($_POST['kategori']    ?? '');
    $lokasi     = trim($_POST['lokasi']      ?? '');
    $deskripsi  = trim($_POST['deskripsi']   ?? '');
    $fasilitas  = trim($_POST['fasilitas']   ?? '');
    $jam_buka   = trim($_POST['jam_buka']    ?? '');
    $harga      = trim($_POST['harga_tiket'] ?? '');
    $lat        = trim($_POST['latitude']    ?? '');
    $lng        = trim($_POST['longitude']   ?? '');
    $aktif      = isset($_POST['aktif']) ? 1 : 0;
    $foto_nama  = $wisata['foto'];

    if (empty($nama) || empty($kategori) || empty($lokasi) || empty($deskripsi)) {
        $msg = 'error:Nama, kategori, lokasi, dan deskripsi wajib diisi.';
    } else {
        // Upload foto
        if (!empty($_FILES['foto']['name'])) {
            $ext_allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $ext_allowed)) {
                $msg = 'error:Format foto tidak valid. Gunakan JPG, PNG, atau WebP.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $msg = 'error:Ukuran foto maksimal 2MB.';
            } else {
                $new_name = 'wisata_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = __DIR__ . '/../assets/uploads/destinasi/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                if (move_uploaded_file(
                    $_FILES['foto']['tmp_name'],
                    $upload_dir . $new_name
                )) {
                    if ($foto_nama && file_exists(__DIR__ . '/../assets/uploads/destinasi/' . $foto_nama)) unlink(__DIR__ . '/../assets/uploads/destinasi/' . $foto_nama);
                    $foto_nama = $new_name;
                }
            }
        }

        if (!str_starts_with($msg, 'error')) {
            if ($edit) {
                $stmt = $conn->prepare("UPDATE wisata SET nama=?,kategori=?,lokasi=?,deskripsi=?,fasilitas=?,jam_buka=?,harga_tiket=?,latitude=?,longitude=?,aktif=?,foto=? WHERE id=?");
                $stmt->bind_param('sssssssddssi', $nama,$kategori,$lokasi,$deskripsi,$fasilitas,$jam_buka,$harga,$lat,$lng,$aktif,$foto_nama,$id);
            } else {
                $stmt = $conn->prepare("INSERT INTO wisata (nama,kategori,lokasi,deskripsi,fasilitas,jam_buka,harga_tiket,latitude,longitude,aktif,foto,rating,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,0,NOW())");
                $stmt->bind_param('ssssssssdds', $nama,$kategori,$lokasi,$deskripsi,$fasilitas,$jam_buka,$harga,$lat,$lng,$aktif,$foto_nama);
            }
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: wisata.php?msg=success:Wisata berhasil ' . ($edit ? 'diperbarui.' : 'ditambahkan.'));
                exit;
            } else {
                $msg = 'error:Terjadi kesalahan. Coba lagi.';
                $stmt->close();
            }
        }
    }
    // Isi ulang form jika error
    $wisata = array_merge($wisata, compact('nama','kategori','lokasi','deskripsi','fasilitas','jam_buka','aktif') + ['harga_tiket'=>$harga,'latitude'=>$lat,'longitude'=>$lng]);
}

[$msg_type, $msg_text] = array_pad(explode(':', $msg, 2), 2, '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $edit ? 'Edit' : 'Tambah' ?> Wisata &mdash; Admin PesonaNTB</title>
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

      <div class="admin-card">
        <div class="card-header">
          <h3><?= $edit ? 'Edit Wisata' : 'Tambah Wisata Baru' ?></h3>
          <a href="wisata.php" class="btn btn-light btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">
              <!-- Nama -->
              <div class="form-group">
                <label>Nama Wisata <span style="color:#C0392B">*</span></label>
                <input type="text" name="nama" value="<?= htmlspecialchars($wisata['nama']) ?>" placeholder="Nama destinasi wisata" required>
              </div>

              <!-- Kategori -->
              <div class="form-group">
                <label>Kategori <span style="color:#C0392B">*</span></label>
                <select name="kategori" required>
                  <option value="">-- Pilih Kategori --</option>
                  <?php foreach ($kategori_opts as $k): ?>
                  <option value="<?= $k ?>" <?= $wisata['kategori']===$k?'selected':'' ?>><?= $k ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Lokasi -->
              <div class="form-group">
                <label>Lokasi <span style="color:#C0392B">*</span></label>
                <input type="text" name="lokasi" value="<?= htmlspecialchars($wisata['lokasi']) ?>" placeholder="Contoh: Lombok Utara" required>
              </div>

              <!-- Jam Buka -->
              <div class="form-group">
                <label>Jam Buka</label>
                <input type="text" name="jam_buka" value="<?= htmlspecialchars($wisata['jam_buka']) ?>" placeholder="Contoh: 08.00 - 17.00 WITA">
              </div>

              <!-- Harga Tiket -->
              <div class="form-group">
                <label>Harga Tiket</label>
                <input type="text" name="harga_tiket" value="<?= htmlspecialchars($wisata['harga_tiket']) ?>" placeholder="Contoh: Rp 10.000/orang">
              </div>

              <!-- Latitude -->
              <div class="form-group">
                <label>Latitude (Google Maps)</label>
                <input type="text" name="latitude" value="<?= htmlspecialchars($wisata['latitude']) ?>" placeholder="Contoh: -8.4955">
                <div class="form-hint">Klik kanan lokasi di Google Maps → Salin koordinat</div>
              </div>

              <!-- Longitude -->
              <div class="form-group">
                <label>Longitude (Google Maps)</label>
                <input type="text" name="longitude" value="<?= htmlspecialchars($wisata['longitude']) ?>" placeholder="Contoh: 116.0522">
              </div>

              <!-- Status -->
              <div class="form-group">
                <label>Status</label>
                <label class="toggle-wrap" style="margin-top:0.4rem">
                  <div class="toggle-switch">
                    <input type="checkbox" name="aktif" <?= $wisata['aktif'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                  </div>
                  <span style="font-size:0.88rem;color:var(--text-muted)">Aktif (tampil di website)</span>
                </label>
              </div>

              <!-- Deskripsi -->
              <div class="form-group full">
                <label>Deskripsi <span style="color:#C0392B">*</span></label>
                <textarea name="deskripsi" placeholder="Deskripsi lengkap destinasi wisata..." required><?= htmlspecialchars($wisata['deskripsi']) ?></textarea>
              </div>

              <!-- Fasilitas -->
              <div class="form-group full">
                <label>Fasilitas</label>
                <input type="text" name="fasilitas" value="<?= htmlspecialchars($wisata['fasilitas']) ?>" placeholder="Pisahkan dengan koma. Contoh: Parkir, Toilet, Mushola">
                <div class="form-hint">Pisahkan setiap fasilitas dengan tanda koma (,)</div>
              </div>

              <!-- Foto -->
              <div class="form-group full">
                <label>Foto Destinasi</label>
                <?php if ($edit && $wisata['foto'] && file_exists(__DIR__ . '/../assets/uploads/destinasi/' . $wisata['foto'])): ?>
                <div class="foto-current">
                  <img src="../assets/uploads/destinasi/<?= htmlspecialchars($wisata['foto']) ?>" alt="Foto saat ini">
                  <span style="font-size:0.82rem;color:var(--text-muted)">Foto saat ini. Upload baru untuk mengganti.</span>
                </div>
                <?php endif; ?>
                <div class="foto-upload-area" id="uploadArea">
                  <input type="file" name="foto" id="fotoInput" accept="image/jpg,image/jpeg,image/png,image/webp">
                  <div>📷 Klik untuk pilih foto</div>
                  <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.3rem">JPG, PNG, WebP — Maks. 2MB</div>
                  <div class="foto-preview" id="fotoPreview"></div>
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">💾 <?= $edit ? 'Simpan Perubahan' : 'Tambah Wisata' ?></button>
              <a href="wisata.php" class="btn btn-light">Batal</a>
            </div>
          </form>
        </div>
      </div>

    </main>
  </div>
</div>
<script src="../js/admin.js"></script>
</body>
</html>