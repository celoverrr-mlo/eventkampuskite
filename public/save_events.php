<?php
session_start();
require "../includes/koneksi.php"; // koneksi mysqli $conn

// ------------------------------------------------------------
// VALIDASI LOGIN
// ------------------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    echo "<script>alert('ID event tidak valid.'); history.back();</script>";
    exit;
}

// ------------------------------------------------------------
// CEK APAKAH EVENT SUDAH PERNAH DISIMPAN
// ------------------------------------------------------------
$check = $conn->prepare("SELECT id FROM save_events WHERE user_id = ? AND event_id = ?");
$check->bind_param("ii", $user_id, $event_id);
$check->execute();
$check->store_result();

$already_saved = $check->num_rows > 0;

// ------------------------------------------------------------
// JIKA BELUM ADA, MASUKKAN KE DATABASE
// ------------------------------------------------------------
if (!$already_saved) {
    $stmt = $conn->prepare("INSERT INTO save_events (user_id, event_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
}

// ------------------------------------------------------------
// TAMPILKAN POPUP THEN AUTO REDIRECT
// ------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Simpan Event</title>

<style>
    body {
        background: #f5f5f5;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    /* Popup Notifikasi */
    .popup-save {
        position: fixed;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
        background: #222;
        color: #fff;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 15px;
        opacity: 0;
        z-index: 999999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.45s ease;
    }
    .popup-save.show {
        bottom: 45px;
        opacity: 1;
    }

    /* Style Halaman Loading */
    .center-box {
        text-align: center;
        margin-top: 160px;
        color: #333;
        font-size: 18px;
        font-weight: 600;
    }
</style>

</head><?php
include '../includes/koneksi.php';
session_start();

$user_name = $_SESSION['user_name'] ?? null;

if (!$user_name) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='login.php';</script>";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Event tidak ditemukan.'); history.back();</script>";
    exit;
}

$event_id = (int) $_GET['id'];

// Cek apakah sudah disimpan
$cek = $conn->prepare("SELECT * FROM saved_events WHERE user_name = ? AND event_id = ?");
$cek->bind_param("si", $user_name, $event_id);
$cek->execute();
$hasil = $cek->get_result();

if ($hasil->num_rows > 0) {
    echo "<script>alert('Event sudah ada di daftar simpan.'); window.location='simpan_event.php';</script>";
    exit;
}

// Simpan baru
$stmt = $conn->prepare("INSERT INTO saved_events (user_name, event_id) VALUES (?, ?)");
$stmt->bind_param("si", $user_name, $event_id);

if ($stmt->execute()) {
    echo "<script>alert('Event berhasil disimpan!'); window.location='simpan_event.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan event.'); history.back();</script>";
}
?>
<body>

<div class="center-box">
    Memproses...
</div>

<div id="popup" class="popup-save">
    <?= $already_saved ? "Event ini sudah disimpan sebelumnya." : "Event berhasil disimpan!" ?>
</div>

<script>
    // Tampilkan popup
    setTimeout(() => {
        document.getElementById("popup").classList.add("show");
    }, 200);

    // Redirect kembali setelah 2 detik
    setTimeout(() => {
        history.back();
    }, 1700);
</script>

</body>
</html>