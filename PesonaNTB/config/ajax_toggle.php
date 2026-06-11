<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { http_response_code(403); exit; }

$id    = intval($_POST['id']   ?? 0);
$aktif = intval($_POST['aktif'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("UPDATE wisata SET aktif=? WHERE id=?");
    $stmt->bind_param('ii', $aktif, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>