<?php
include 'koneksi.php';
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_tempat']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_maps = mysqli_real_escape_string($koneksi, $_POST['link_maps']);

    $query = "UPDATE wisata SET 
              nama_tempat='$nama', 
              deskripsi='$deskripsi', 
              link_maps='$link_maps' 
              WHERE id=$id";

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin.php?status=updated");
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}

$result = mysqli_query($koneksi, "SELECT * FROM wisata WHERE id=$id");
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Wisata - Pesona NTB</title>
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
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            border: 1px solid #e0e4e8;
            border-radius: 16px;
            background: #fafaf8;
        }
        .btn-submit {
            background: #1a1f2c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover {
            background: #c17f3b;
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
            <a href="signin.html" class="sign-in">Sign In</a>
            <a href="signup.html" class="sign-up">Sign Up</a>
        </div>
    </nav>

    <div class="form-container">
        <h1>Edit Wisata</h1>
        <form method="POST">
            <div class="form-group">
                <label>Nama Tempat</label>
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
            <button type="submit" class="btn-submit">Update Wisata</button>
        </form>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">Pesona NTB</div>
            <p>© 2026 Pesona NTB — Discover the wonders of Nusa Tenggara Barat</p>
        </div>
    </footer>
</body>
</html>