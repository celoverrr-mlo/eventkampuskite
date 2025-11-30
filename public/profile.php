<?php
session_start();
include '../includes/koneksi.php';

// Jika belum login → lempar balik
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$q = $conn->query("SELECT name, email, instansi, photo FROM users WHERE user_id = $user_id");
$user = $q->fetch_assoc();

// Cek foto profil
$photo = $user['photo'] ? "../uploads/" . $user['photo'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: #eef1f5;
    display: flex;
    justify-content: center;
    padding-bottom: 100px;
}

/* MAIN CONTAINER */
.container {
    width: 95%;
    max-width: 430px;
    background: #ffffff;
    border-radius: 22px;
    margin-top: 20px;
    padding-bottom: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

/* HEADER */
header {
    font-size: 21px;
    text-align: center;
    padding: 18px 0;
    font-weight: 600;
    border-bottom: 1px solid #e5e7eb;
}

/* PROFILE AREA */
.profile-box {
    text-align: center;
    padding: 25px;
}

.profile-photo {
    width: 120px;
    height: 120px;
    background: #e0e0e0;
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 55px;
    color: #6b7280;
    overflow: hidden;
}
.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    margin-top: 12px;
    font-size: 22px;
    font-weight: 600;
}

.profile-email {
    margin-top: 4px;
    color: #6b7280;
    font-size: 14px;
}

.profile-instansi {
    margin-top: 6px;
    color: #374151;
    font-size: 15px;
    font-weight: 500;
}

/* DATA CARD */
.data-card {
    width: 90%;
    margin: 20px auto;
    background: #f8fafc;
    padding: 18px;
    border-radius: 14px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.data-card p {
    margin: 8px 0;
    font-size: 15px;
}

/* LOGOUT BUTTON */
.logout-btn {
    width: 90%;
    margin: 22px auto;
    display: block;
    text-align: center;
    padding: 12px;
    background: #ef4444;
    color: white;
    font-size: 16px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
}

/* BOTTOM NAVBAR */
footer.navbar {
    position: fixed;
    bottom: 10px;
    width: 90%;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    display: flex;
    justify-content: space-evenly;
    padding: 12px 0;
    border-radius: 30px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}
footer.navbar a {
    color: #1e40af;
    font-size: 13px;
    text-decoration: none;
    text-align: center;
}
footer.navbar i {
    font-size: 18px;
    display: block;
    margin-bottom: 2px;
}
</style>
</head>

<body>

<div class="container">

    <header>Profil Saya</header>

    <div class="profile-box">

        <!-- Foto profil / ikon orang -->
        <div class="profile-photo">
            <?php if ($photo): ?>
                <img src="<?= $photo ?>">
            <?php else: ?>
                <i class="fa-solid fa-user"></i>
            <?php endif; ?>
        </div>

        <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
        <div class="profile-instansi">Instansi: <b><?= htmlspecialchars($user['instansi']) ?></b></div>

    </div>

    <div class="data-card">
        <p><b>Nama:</b> <?= htmlspecialchars($user['name']) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
        <p><b>Instansi:</b> <?= htmlspecialchars($user['instansi']) ?></p>
    </div>

    <a href="logout.php" class="logout-btn">
        Logout
    </a>
</div>

<footer class="navbar">
    <a href="index.php">
        <i class="fa-solid fa-house"></i>Home
    </a>
    <a href="all_kategories.php">
        <i class="fa fa-th-large"></i>Kategori
    </a>
    <a href="simpan_event.php">
        <i class="fa fa-bookmark"></i>Simpan
    </a>
    <a href="profile.php" style="color:#2563eb;">
        <i class="fa-solid fa-user"></i>Akun
    </a>
</footer>

</body>
</html>