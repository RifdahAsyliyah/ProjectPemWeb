<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i",$uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$jml_bookmark = $conn->query("
SELECT COUNT(*) c
FROM bookmark
WHERE user_id=$uid
")->fetch_assoc()['c'];

$jml_riwayat = $conn->query("
SELECT COUNT(*) c
FROM riwayat
WHERE user_id=$uid
")->fetch_assoc()['c'];

$jml_ulasan = $conn->query("
SELECT COUNT(*) c
FROM ulasan
WHERE user_id=$uid
AND parent_id IS NULL
")->fetch_assoc()['c'];

$aktivitas = [];

$sql = "
SELECT
w.nama,
'Bookmark' AS aktivitas,
b.created_at AS tanggal
FROM bookmark b
JOIN wisata w ON w.id=b.wisata_id
WHERE b.user_id=$uid

UNION ALL

SELECT
w.nama,
'Riwayat' AS aktivitas,
r.dilihat_at AS tanggal
FROM riwayat r
JOIN wisata w ON w.id=r.wisata_id
WHERE r.user_id=$uid

UNION ALL

SELECT
w.nama,
'Ulasan' AS aktivitas,
u.created_at AS tanggal
FROM ulasan u
JOIN wisata w ON w.id=u.wisata_id
WHERE u.user_id=$uid
AND u.parent_id IS NULL
ORDER BY tanggal DESC
LIMIT 10
";

$res = $conn->query($sql);

if($res){
    while($row=$res->fetch_assoc()){
        $aktivitas[]=$row;
    }
}

$favorit = [];

$resFav = $conn->query("
SELECT w.*
FROM bookmark b
JOIN wisata w ON w.id=b.wisata_id
WHERE b.user_id=$uid
LIMIT 3
");

if($resFav){
    while($row=$resFav->fetch_assoc()){
        $favorit[]=$row;
    }
}

$inisial = strtoupper(substr($user['nama'],0,2));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Saya</title>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/user.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="user-page">
<div class="user-container">

<div class="section-header">
<span class="section-label">✦ Dashboard Saya</span>
<h1 class="section-title">Selamat Datang, <?= htmlspecialchars($user['nama']) ?></h1>
</div>

<div class="stats-grid">

<div class="stat-card">
<div class="stat-icon">🔖</div>
<div class="stat-number"><?= $jml_bookmark ?></div>
<div class="stat-label">Tersimpan</div>
</div>

<div class="stat-card">
<div class="stat-icon">🕒</div>
<div class="stat-number"><?= $jml_riwayat ?></div>
<div class="stat-label">Riwayat</div>
</div>

<div class="stat-card">
<div class="stat-icon">⭐</div>
<div class="stat-number"><?= $jml_ulasan ?></div>
<div class="stat-label">Ulasan</div>
</div>

<div class="stat-card">
<div class="stat-icon">📅</div>
<div class="stat-number">
<?= date('d M Y', strtotime($user['created_at'])) ?>
</div>
<div class="stat-label">Bergabung</div>
</div>

</div>

<div class="dashboard-grid">

<div class="profil-card">

<?php if(!empty($user['foto_profil'])): ?>

<?php
$foto = '../assets/uploads/profil/' . $user['foto_profil'];
?>

<?php if(!empty($user['foto_profil']) && file_exists($foto)): ?>
    <img
        src="<?= $foto ?>"
        class="dashboard-avatar"
        alt="Foto Profil">
<?php else: ?>
    <div class="profil-avatar">
        <?= $inisial ?>
    </div>
<?php endif; ?>

<?php else: ?>

<div class="profil-avatar">
<?= $inisial ?>
</div>

<?php endif; ?>

<h3><?= htmlspecialchars($user['nama']) ?></h3>

<div class="user-role">
👤 Wisatawan NTB
</div>

<div class="profile-info">
    <div class="info-item">
        <span class="info-icon">📧</span>
        <span><?= htmlspecialchars($user['email']) ?></span>
    </div>

    <div class="info-item">
        <span class="info-icon">📱</span>
        <span><?= htmlspecialchars($user['telepon']) ?></span>
    </div>
</div>

<a href="profil.php" class="btn-primary">
Edit Profil
</a>

</div>

<div class="profil-card">

<h3>Aktivitas Terakhir</h3>

<?php if(empty($aktivitas)): ?>

<p>Belum ada aktivitas.</p>

<?php else: ?>

<table class="dashboard-table">

<tr>
<th>Destinasi</th>
<th>Aktivitas</th>
<th>Tanggal</th>
</tr>

<?php foreach($aktivitas as $a): ?>

<tr>
<td><?= htmlspecialchars($a['nama']) ?></td>
<td>
<?php if($a['aktivitas']=='Bookmark'): ?>
    <span class="badge-bookmark">
        🔖 Bookmark
    </span>
<?php elseif($a['aktivitas']=='Riwayat'): ?>
    <span class="badge-riwayat">
        👀 Riwayat
    </span>
<?php elseif($a['aktivitas']=='Ulasan'): ?>
    <span class="badge-ulasan">
        ⭐ Ulasan
    </span>
<?php endif; ?>
</td>
<td><?= date('d M Y', strtotime($a['tanggal'])) ?></td>
</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</div>

</div>

<div class="profil-card" style="margin-top:25px">

<h3>Destinasi Favorit</h3>

<div class="dest-grid-full">

<?php foreach($favorit as $d): ?>

<a href="detail.php?id=<?= $d['id'] ?>" class="favorite-card">

    <img
        src="../assets/uploads/destinasi/<?= htmlspecialchars($d['foto']) ?>"
        class="favorite-img"
        alt="<?= htmlspecialchars($d['nama']) ?>">

    <div class="favorite-body">

        <h4><?= htmlspecialchars($d['nama']) ?></h4>

        <p>📍 <?= htmlspecialchars($d['lokasi']) ?></p>

        <span class="favorite-category">
            <?= htmlspecialchars($d['kategori']) ?>
        </span>

    </div>

</a>

<?php endforeach; ?>

</div>

</div>

</div>
</div>

<?php include 'footer.php'; ?>
<script src="../js/main.js"></script>

</body>
</html>
</body>
</html>