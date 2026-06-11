<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "pesonantb2"
);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>