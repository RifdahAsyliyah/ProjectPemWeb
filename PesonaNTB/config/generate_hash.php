<?php
// Jalankan file ini sekali di browser: http://localhost/Guest/generate_hash.php
// Setelah dapat hash, update ke database lalu hapus file ini

$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<h3>Hash untuk password: <strong>$password</strong></h3>";
echo "<p>Hash: <code>$hash</code></p>";
echo "<hr>";
echo "<p>Jalankan SQL ini di phpMyAdmin:</p>";
echo "<pre>UPDATE users SET password = '$hash' WHERE email = 'admin@pesonantb.com';</pre>";
echo "<br>";
echo "<p style='color:red'><strong>Hapus file ini setelah selesai!</strong></p>";
?>