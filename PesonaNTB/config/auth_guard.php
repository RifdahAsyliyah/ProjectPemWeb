<?php
// Guard: hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: config/login.php');
    exit;
}
?>