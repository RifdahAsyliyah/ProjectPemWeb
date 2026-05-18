<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: PesonaNTB/config/login.php");
    exit();
}
include 'PesonaNTB/config/koneksi.php';

$query = "SELECT * FROM wisata ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Wisata | Pesona NTB</title>
    <link rel="stylesheet" href="PesonaNTB/style/styleLandingPage.css">
    <link rel="stylesheet" href="PesonaNTB/style/styleAdmin.css">
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
            <a href="admin.php" class="sign-up active-auth">Admin</a>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Kelola Data Wisata</h1>
            <a href="tambah_wisata.php" class="btn-add">+ Tambah Wisata</a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nama Tempat</th>
                    <th>Deskripsi</th>
                    <th>Link Maps</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <!-- BAGIAN FOTO YANG DIPERBAIKI -->
                            <td>
                                <?php if (!empty($row['gambar'])): 
                                    $imgPath = $row['gambar'];
                                    if (strpos($imgPath, 'uploads/') === 0 && strpos($imgPath, 'assets/') !== 0) {
                                        $imgPath = 'assets/' . $imgPath;
                                    }
                                ?>
                                    <img src="../<?php echo $imgPath; ?>" width="50" height="50" style="object-fit: cover; border-radius: 10px;">
                                <?php else: ?>
                                    <span class="no-image">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <!-- END BAGIAN FOTO -->
                            <td><strong><?php echo htmlspecialchars($row['nama_tempat']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 80)) . (strlen($row['deskripsi']) > 80 ? '...' : ''); ?></td>
                            <td><a href="<?php echo $row['link_maps']; ?>" target="_blank" style="color:#c17f3b;">Lihat Maps</a></td>
                            <td>
                                <a href="edit_wisata.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                <a href="PesonaNTB/config/hapus_wisata.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">Belum ada data wisata. Silakan tambah data.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">Pesona NTB</div>
            <p>© 2026 Pesona NTB — Discover the wonders of Nusa Tenggara Barat</p>
        </div>
    </footer>

</body>
</html>