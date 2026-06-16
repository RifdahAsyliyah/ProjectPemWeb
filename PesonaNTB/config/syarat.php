<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Syarat & Ketentuan - PesonaNTB</title>

<link rel="stylesheet" href="../css/style.css">

<style>
.terms-page{
    padding:60px 20px;
    background:#f7f3ee;
    min-height:100vh;
}

.terms-container{
    max-width:900px;
    margin:auto;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.terms-title{
    text-align:center;
    margin-bottom:10px;
    color:#3e2a1f;
}

.terms-subtitle{
    text-align:center;
    color:#777;
    margin-bottom:40px;
}

.terms-section{
    margin-bottom:30px;
}

.terms-section h2{
    color:#5f422d;
    margin-bottom:12px;
    font-size:1.25rem;
}

.terms-section p,
.terms-section li{
    line-height:1.8;
    color:#555;
}

.terms-section ul,
.terms-section ol{
    padding-left:25px;
}

.btn-back{
    display:inline-block;
    margin-top:20px;
    background:#6b4c3b;
    color:#fff;
    text-decoration:none;
    padding:12px 24px;
    border-radius:10px;
    transition:.3s;
}

.btn-back:hover{
    background:#54382a;
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="terms-page">

    <div class="terms-container">

        <h1 class="terms-title">
            Syarat & Ketentuan PesonaNTB
        </h1>

        <p class="terms-subtitle">
            Mohon baca dengan seksama sebelum menggunakan layanan PesonaNTB.
        </p>

        <div class="terms-section">
            <h2>1. Ketentuan Umum</h2>
            <p>
                PesonaNTB merupakan platform informasi wisata yang menyediakan
                berbagai informasi destinasi wisata di wilayah Nusa Tenggara Barat.
                Dengan membuat akun dan menggunakan layanan yang tersedia,
                pengguna dianggap telah membaca, memahami, dan menyetujui seluruh
                syarat dan ketentuan yang berlaku.
            </p>
        </div>

        <div class="terms-section">
            <h2>2. Pendaftaran Akun</h2>
            <ol>
                <li>Pengguna wajib memberikan data yang benar dan akurat.</li>
                <li>Setiap email dan nomor telepon hanya dapat digunakan untuk satu akun.</li>
                <li>Pengguna bertanggung jawab menjaga kerahasiaan password akun.</li>
                <li>Dilarang memberikan akses akun kepada pihak lain tanpa izin.</li>
            </ol>
        </div>

        <div class="terms-section">
            <h2>3. Penggunaan Layanan</h2>
            <ol>
                <li>Pengguna dapat melihat informasi destinasi wisata yang tersedia.</li>
                <li>Pengguna dapat menyimpan destinasi ke bookmark.</li>
                <li>Pengguna dapat memberikan ulasan dan komentar secara bertanggung jawab.</li>
                <li>Pengguna tidak diperbolehkan menyalahgunakan sistem untuk tujuan yang merugikan pihak lain.</li>
            </ol>
        </div>

        <div class="terms-section">
            <h2>4. Ulasan dan Komentar</h2>
            <p>
                Setiap ulasan dan komentar harus menggunakan bahasa yang sopan
                dan tidak mengandung:
            </p>

            <ul>
                <li>Ujaran kebencian.</li>
                <li>Konten SARA.</li>
                <li>Pornografi.</li>
                <li>Ancaman atau pelecehan.</li>
                <li>Informasi palsu atau menyesatkan.</li>
            </ul>

            <p>
                Administrator berhak menghapus ulasan yang melanggar ketentuan.
            </p>
        </div>

        <div class="terms-section">
            <h2>5. Hak dan Kewajiban Pengguna</h2>

            <p><strong>Hak Pengguna:</strong></p>
            <ul>
                <li>Mengakses informasi wisata.</li>
                <li>Menyimpan destinasi favorit.</li>
                <li>Memberikan ulasan dan masukan.</li>
            </ul>

            <p><strong>Kewajiban Pengguna:</strong></p>
            <ul>
                <li>Menjaga keamanan akun.</li>
                <li>Memberikan informasi yang benar.</li>
                <li>Menggunakan layanan secara bertanggung jawab.</li>
            </ul>
        </div>

        <div class="terms-section">
            <h2>6. Hak Administrator</h2>
            <ol>
                <li>Mengelola seluruh data pada sistem.</li>
                <li>Menghapus ulasan yang melanggar aturan.</li>
                <li>Menonaktifkan akun yang melakukan pelanggaran.</li>
                <li>Melakukan perubahan fitur dan layanan sewaktu-waktu.</li>
            </ol>
        </div>

        <div class="terms-section">
            <h2>7. Privasi Data</h2>
            <p>
                Data pengguna seperti nama, email, dan nomor telepon hanya digunakan
                untuk kebutuhan layanan PesonaNTB dan tidak akan diperjualbelikan
                kepada pihak lain.
            </p>
        </div>

        <div class="terms-section">
            <h2>8. Batasan Tanggung Jawab</h2>
            <p>
                Informasi wisata yang tersedia disajikan sebagai media informasi
                dan dapat berubah sewaktu-waktu sesuai kondisi lapangan.
            </p>
        </div>

        <div class="terms-section">
            <h2>9. Perubahan Ketentuan</h2>
            <p>
                PesonaNTB berhak mengubah syarat dan ketentuan ini sewaktu-waktu
                tanpa pemberitahuan terlebih dahulu.
            </p>
        </div>

        <div class="terms-section">
            <h2>10. Persetujuan</h2>
            <p>
                Dengan mencentang kotak persetujuan pada halaman pendaftaran,
                pengguna dianggap telah membaca, memahami, dan menyetujui seluruh
                syarat dan ketentuan yang berlaku.
            </p>
        </div>

        <a href="register.php" class="btn-back">
            ← Kembali ke Pendaftaran
        </a>

    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>