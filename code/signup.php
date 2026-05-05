<?php

$conn = mysqli_connect("localhost", "root", "", "signup");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$nama = trim($_POST["nama"]);
$email = trim($_POST["email"]);
$telp = trim($_POST["telp"]);
$password = trim($_POST["password"]);

$pesan = "";
$warna = "red";

if ($nama == "") {
    $pesan = "Nama tidak boleh kosong!";
} else if ($email == "") {
    $pesan = "Email tidak boleh kosong!";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $pesan = "Format email tidak valid!";
} else if ($telp == "") {
    $pesan = "Nomor telepon tidak boleh kosong!";
} else if (!preg_match("/^(?:\+62|08)[0-9]{8,13}$/", $telp)) {
    $pesan = "Format nomor telepon tidak valid!";
} else if ($password == "") {
    $pesan = "Password tidak boleh kosong!";
} else {

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO daftar (nama, email, telpon, password) 
              VALUES ('$nama', '$email', '$telp', '$passwordHash')";

    if (mysqli_query($conn, $query)) {
        $pesan = "Data berhasil disimpan! Halo, $nama!";
        $warna = "purple";
    } else {
        $pesan = "Gagal menyimpan data!";
    }
}
?>