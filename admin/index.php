<?php
session_start();

// Cegah akses tanpa login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin - Event Kampus Kite</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f6f6f6;
    margin: 0;
    color: #333;
}
header {
    background: #007bff;
    color: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    font-weight: bold;
}
header .logout a {
    background: #dc3545;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: normal;
}
header .logout a:hover {
    background: #b02a37;
}

.container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 30px;
}
h2 {
    text-align: center;
    margin-bottom: 25px;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
}
.card {
    background: #f9f9f9;
    border-radius: 10px;
    padding: 25px 15px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.card:hover {
    transform: translateY(-5px);
}
.card h3 {
    margin-bottom: 15px;
}
.card a {
    display: inline-block;
    text-decoration: none;
    background: #007bff;
    color: #fff;
    padding: 8px 14px;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
}
.card a:hover {
    background: #0056b3;
}
footer {
    text-align: center;
    padding: 15px;
    margin-top: 50px;
    color: #555;
    font-size: 14px;
}
</style>
</head>
<body>

<header>
    🎓 Dashboard Admin - Event Kampus Kite
    <div class="logout">
        <a href="logout.php">Logout</a>
        <a href="../public/index.php">Dashboard</a>
        
    </div>
</header>

<div class="container">
    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!</h2>
    <p style="text-align:center; margin-bottom:30px;">
        Gunakan panel di bawah untuk mengelola seluruh event kampus.
    </p>

    <div class="grid">
        <div class="card">
            <h3>📋 Daftar Event</h3>
            <p>Lihat semua event kampus yang telah dibuat.</p>
            <a href="list_event.php">Buka</a>
        </div>

        <div class="card">
            <h3>➕ Tambah Event</h3>
            <p>Buat event baru dan tampilkan di halaman publik.</p>
            <a href="add_event.php">Buka</a>
        </div>

        <div class="card">
            <h3>✏️ Edit Event</h3>
            <p>Ubah informasi event yang sudah ada.</p>
            <a href="list_event.php">Pilih Event</a>
        </div>

        <div class="card">
            <h3>🗑️ Hapus Event</h3>
            <p>Hapus event yang sudah tidak berlaku.</p>
            <a href="list_event.php">Kelola</a>
        </div>
    </div>
</div>

<footer>
    © <?= date('Y'); ?> Kampus Kite — Admin Panel
</footer>

</body>
</html>
