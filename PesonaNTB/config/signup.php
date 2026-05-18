<?php
include 'koneksi.php';

$nama = trim($_POST["nama"]);
$email = trim($_POST["email"]);
$telp = trim($_POST["telp"]);
$password = trim($_POST["password"]);

if ($nama == "") {
    echo "<script>
            alert('Nama tidak boleh kosong!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

if ($email == "") {
    echo "<script>
            alert('Email tidak boleh kosong!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
            alert('Format email tidak valid!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

if ($telp == "") {
    echo "<script>
            alert('Nomor telepon tidak boleh kosong!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

if (!preg_match("/^(?:\+62|08)[0-9]{8,13}$/", $telp)) {
    echo "<script>
            alert('Format nomor telepon tidak valid!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

if ($password == "") {
    echo "<script>
            alert('Password tidak boleh kosong!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
            alert('Email sudah terdaftar!');
            window.location='../html/signup.html';
          </script>";
    exit();
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO users (nama, email, telp, password)
          VALUES ('$nama', '$email', '$telp', '$passwordHash')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
            alert('Pendaftaran berhasil! Silakan login.');
            window.location='../html/signin.html';
          </script>";
} else {
    echo "<script>
            alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');
            window.location='../html/signup.html';
          </script>";
}

mysqli_close($koneksi);
?>