<?php
include 'koneksi.php';

$pesan = '';
$warna = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_tempat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_maps = mysqli_real_escape_string($koneksi, $_POST['link_maps']);

    if (empty($nama) || empty($deskripsi) || empty($link_maps)) {
        $pesan = "Semua field harus diisi!";
        $warna = "error";
    } else {
        $query = "INSERT INTO wisata (nama_tempat, deskripsi, link_maps) 
                  VALUES ('$nama', '$deskripsi', '$link_maps')";
        
        if (mysqli_query($koneksi, $query)) {
            $pesan = "Data wisata berhasil ditambahkan!";
            $warna = "success";
        } else {
            $pesan = "Gagal menambahkan: " . mysqli_error($koneksi);
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
    <title>Tambah Wisata - Pesona NTB</title>
    <link rel="stylesheet" href="styleLandingPage.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <style>
        .form-container {
            max-width: 700px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }
        .form-container h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .form-container p {
            color: #5a6874;
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #c17f3b;
            margin-bottom: 8px;
        }
        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            border: 1px solid #e0e4e8;
            border-radius: 16px;
            outline: none;
            transition: all 0.2s ease;
            background: #fafaf8;
        }
        .form-group input:focus, 
        .form-group textarea:focus {
            border-color: #c17f3b;
            background: #ffffff;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn-submit {
            background: #1a1f2c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .btn-submit:hover {
            background: #c17f3b;
        }
        .btn-back {
            display: inline-block;
            margin-top: 16px;
            text-decoration: none;
            color: #c17f3b;
            font-size: 0.9rem;
        }
        .btn-back:hover {
            color: #1a1f2c;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
    </style>
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

        <form method="POST" action="">
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

            <button type="submit" class="btn-submit">+ Simpan Wisata</button>
        </form>

        <div style="text-align: center;">
            <a href="admin.php" class="btn-back">← Kembali ke Halaman Admin</a>
        </div>
    </div>

    <footer style="margin-top: 60px;">
        <div class="footer-content">
            <div class="footer-logo">Pesona NTB</div>
            <p>© 2026 Pesona NTB — Discover the wonders of Nusa Tenggara Barat</p>
        </div>
    </footer>

</body>
</html>