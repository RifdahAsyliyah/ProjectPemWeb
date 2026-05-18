<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: PesonaNTB/config/login.php");
    exit();
}
include 'PesonaNTB/config/koneksi.php';

$id = $_GET['id'];
$pesan = '';
$warna = '';

// Ambil data lama
$query = "SELECT * FROM wisata WHERE id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_tempat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_maps = mysqli_real_escape_string($koneksi, $_POST['link_maps']);
    
    $gambar = $data['gambar']; // pakai foto lama sebagai default
    
    // Proses upload foto baru (jika ada)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "PesonaNTB/assets/uploads/";
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                // Hapus foto lama jika ada
                $oldImgPath = $data['gambar'];
                if (strpos($oldImgPath, 'uploads/') === 0 && strpos($oldImgPath, 'assets/') !== 0) {
                    $oldImgPath = 'assets/' . $oldImgPath;
                }
                if ($oldImgPath && file_exists($oldImgPath)) {
                    unlink($oldImgPath);
                }
                $gambar = $target_file;
                $pesan = "Data berhasil diupdate, foto diganti!";
                $warna = "success";
            } else {
                $pesan = "Gagal mengupload gambar.";
                $warna = "error";
            }
        } else {
            $pesan = "Format gambar tidak didukung (JPG, PNG, GIF, WEBP).";
            $warna = "error";
        }
    }
    
    if (empty($pesan) || $warna == 'success') {
        $query = "UPDATE wisata SET 
                  nama_tempat='$nama', 
                  deskripsi='$deskripsi', 
                  link_maps='$link_maps', 
                  gambar='$gambar' 
                  WHERE id=$id";
        
        if (mysqli_query($koneksi, $query)) {
            if ($pesan == '') {
                $pesan = "Data wisata berhasil diupdate!";
                $warna = "success";
            }
            // Refresh data
            $result = mysqli_query($koneksi, "SELECT * FROM wisata WHERE id=$id");
            $data = mysqli_fetch_assoc($result);
        } else {
            $pesan = "Gagal mengupdate: " . mysqli_error($koneksi);
            $warna = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wisata - Pesona NTB</title>
    <link rel="stylesheet" href="PesonaNTB/style/styleLandingPage.css">
    <link rel="stylesheet" href="PesonaNTB/style/styleEditWisata.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">

</head>
<body>

    <nav>
        <div class="logo">Pesona NTB</div>
        <ul class="nav-links">
            <li><a href="PesonaNTB/index.html">Home</a></li>
            <li><a href="index.html#about">About</a></li>
            <li><a href="index.html#destinations">Destinations</a></li>
        </ul>
        <div class="auth-buttons">
            <a href="PesonaNTB/logout.php" class="sign-in">Logout</a>
            <a href="admin.php" class="sign-up">Admin</a>
        </div>
    </nav>

    <div class="form-container">
        <h1>Edit Wisata</h1>
        <p>Ubah informasi destinasi wisata NTB.</p>

        <?php if ($pesan): ?>
            <div class="alert alert-<?php echo $warna; ?>">
                <?php echo $pesan; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Tempat Wisata</label>
                <input type="text" name="nama_tempat" value="<?php echo htmlspecialchars($data['nama_tempat']); ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Link Google Maps</label>
                <input type="url" name="link_maps" value="<?php echo htmlspecialchars($data['link_maps']); ?>" required>
            </div>

            <div class="form-group">
                <label>Foto Wisata (Opsional)</label>
                <?php 
                $imgPath = $data['gambar'];
                if (strpos($imgPath, 'uploads/') === 0 && strpos($imgPath, 'assets/') !== 0) {
                    $imgPath = 'assets/' . $imgPath;
                }
                if ($imgPath && file_exists($imgPath)): ?>
                    <div class="current-image">
                        <img src="<?php echo $imgPath; ?>" alt="Foto saat ini">
                        <span>Foto saat ini (akan diganti jika upload foto baru)</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp" id="fileInput">
                <div class="file-info">Format: JPG, PNG, GIF, WEBP. Biarkan kosong jika tidak ingin mengganti foto.</div>
                <img id="preview" class="image-preview" style="display: none;">
            </div>

            <button type="submit" class="btn-submit">Update Wisata</button>
        </form>

        <div style="text-align: center;">
            <a href="admin.php" class="btn-back">← Kembali ke Halaman Admin</a>
        </div>
    </div>

    <script>
        // Preview gambar sebelum upload
        document.getElementById('fileInput').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('preview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        };
    </script>

    <footer style="margin-top: 60px;">
        <div class="footer-content">
            <div class="footer-logo">Pesona NTB</div>
            <p>© 2026 Pesona NTB — Discover the wonders of Nusa Tenggara Barat</p>
        </div>
    </footer>

</body>
</html>