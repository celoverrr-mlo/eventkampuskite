<?php
include '../includes/koneksi.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list_event.php");
    exit;
}

$id = intval($_GET['id']);

// Hapus event
$sql = "DELETE FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list_event.php?deleted=1");
    exit;
} else {
    echo "❌ Gagal menghapus event: " . $stmt->error;
}
?>
