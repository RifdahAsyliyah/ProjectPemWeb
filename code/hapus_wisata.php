<?php
include 'koneksi.php';

$id = $_GET['id'];

$query = "DELETE FROM wisata WHERE id = $id";

if (mysqli_query($koneksi, $query)) {
    header("Location: admin.php?status=deleted");
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>