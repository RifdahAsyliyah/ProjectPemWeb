<?php
include 'koneksi.php';

$pesan = '';
$warna = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_tempat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_maps = mysqli_real_escape_string($koneksi, $_POST['link_maps']);
    
    // Proses upload foto
    $gambar = NULL;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $target_file;
            } else {
                $pesan = "Gagal mengupload gambar.";
                $warna = "error";
            }
        } else {
            $pesan = "Format gambar tidak didukung (JPG, PNG, GIF, WEBP).";
            $warna = "error";
        }
    }

    if (empty($pesan)) {
        if (empty($nama) || empty($deskripsi) || empty($link_maps)) {
            $pesan = "Semua field harus diisi!";
            $warna = "error";
        } else {
            if ($gambar) {
                $query = "INSERT INTO wisata (nama_tempat, deskripsi, link_maps, gambar) 
                          VALUES ('$nama', '$deskripsi', '$link_maps', '$gambar')";
            } else {
                $query = "INSERT INTO wisata (nama_tempat, deskripsi, link_maps) 
                          VALUES ('$nama', '$deskripsi', '$link_maps')";
            }
            
            if (mysqli_query($koneksi, $query)) {
                $pesan = "Data wisata berhasil ditambahkan!";
                $warna = "success";
            } else {
                $pesan = "Gagal menambahkan: " . mysqli_error($koneksi);
                $warna = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - Pesona NTB</title>
    <link rel="stylesheet" href="styleLandingPage.css">
    <link rel="stylesheet" href="styleTambahWisata.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">

</head>
<body>

    <nav>
        <div class="logo">Pesona NTB</div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="index.html#about">About</a></li>
            <li><a href="index.html#destinations">Destinations</a></li>
        </ul>
        <div class="auth-buttons">
            <a href="admin.php" class="sign-in">← Kembali ke Admin</a>
        </div>
    </nav>

    <div class="form-container">
        <h1>Tambah Wisata Baru</h1>
        <p>Masukkan informasi destinasi wisata NTB.</p>

        <?php if ($pesan): ?>
            <div class="alert alert-<?php echo $warna; ?>">
                <?php echo $pesan; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Tempat Wisata</label>
                <input type="text" name="nama_tempat" placeholder="Contoh: Pantai Kuta Mandalika" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" placeholder="Deskripsi singkat tentang tempat wisata..." required></textarea>
            </div>

            <div class="form-group">
                <label>Link Google Maps</label>
                <input type="url" name="link_maps" placeholder="https://maps.app.goo.gl/..." required>
            </div>

            <div class="form-group">
                <label>Foto Wisata (Opsional)</label>
                <input type="file" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp" id="fileInput">
                <div class="file-info">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB.</div>
                <img id="preview" class="image-preview" style="display: none;">
            </div>

            <button type="submit" class="btn-submit">+ Simpan Wisata</button>
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