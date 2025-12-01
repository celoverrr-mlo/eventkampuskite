<?php
include '../includes/koneksi.php';
session_start();

$user_name = $_SESSION['user_name'] ?? null;

if (!$user_name) {
    echo "Harap login terlebih dahulu.";
    exit;
}

// QUERY AMBIL EVENT YANG DISIMPAN USER
$sql = "
    SELECT 
        e.event_id, 
        e.title, 
        e.event_date, 
        e.banner,
        e.latitude,
        e.longitude,
        e.google_form_link, 
        s.saved_at
    FROM saved_events s
    JOIN events e ON s.event_id = e.event_id
    WHERE s.user_name = ?
    ORDER BY s.saved_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$data = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Event Tersimpan</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body {
    margin: 0;
    padding: 0 0 90px 0;
    font-family: 'Poppins', sans-serif;
    background: #f1f4f9;
}

/* WRAPPER */
.page {
    padding: 20px;
    max-width: 700px;
    margin: auto;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 24px;
    font-weight: 700;
    margin: 10px 0 25px 0;
    color: #1e293b;
}

/* CARD EVENT */
.card {
    background: #ffffff;
    padding: 15px;
    border-radius: 18px;
    margin-bottom: 22px;
    box-shadow: 0 6px 14px rgba(0,0,0,0.08);
    display: flex;
    gap: 16px;
    align-items: flex-start;
    transition: 0.25s ease;
}
.card:hover {
    transform: scale(1.02);
}

/* GAMBAR EVENT */
.card img {
    width: 115px;
    height: 115px;
    border-radius: 14px;
    object-fit: cover;
}

/* CONTENT */
.card-content {
    flex: 1;
}

/* JUDUL EVENT */
.card-content h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
}

/* INFO TEXT */
.info {
    font-size: 14px;
    color: #475569;
    margin-top: 6px;
}

/* MINI MAP */
.mapbox {
    width: 100%;
    height: 110px;
    border-radius: 12px;
    margin-top: 6px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

/* BUTTON LINK */
.whatsapp a {
    display: inline-block;
    padding: 7px 16px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 14px;
    margin-top: 6px;
    font-weight: 600;
}
.whatsapp a:hover {
    opacity: .9;
}

/* FOOTER NAVBAR */
footer.navbar {
    position: fixed;
    bottom: 15px;
    width: 92%;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    display: flex;
    justify-content: space-around;
    padding: 12px 0;
    border-radius: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
footer.navbar a {
    color: #334155;
    font-size: 13px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
}
footer.navbar i {
    font-size: 20px;
    display: block;
    margin-bottom: 3px;
}

/* RESPONSIVE */
@media(max-width:600px){
    .card {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    .card img {
        width: 100%;
        height: 160px;
    }
    .mapbox {
        height: 150px;
    }
}
</style>
</head>
<body>

<div class="page">
    <div class="title">Event Tersimpan</div>

    <?php if ($data->num_rows === 0): ?>
        <p style="text-align:center; color:#777;">Belum ada event yang disimpan.</p>
    <?php endif; ?>

    <?php while ($row = $data->fetch_assoc()): ?>
        <div class="card">

            <!-- GAMBAR EVENT -->
            <img src="../uploads/<?= htmlspecialchars($row['banner']) ?>" alt="banner">

            <!-- KONTEN -->
            <div class="card-content">
                <h3><?= htmlspecialchars($row['title']) ?></h3>

                <div class="info">
                    <strong>Tanggal:</strong><br>
                    <?= !empty($row['event_date']) ? date('d M Y', strtotime($row['event_date'])) : '-' ?>
                </div>

                <div class="info">
                    <strong>Lokasi:</strong><br>
                    <?php if ($row['latitude'] && $row['longitude']): ?>
                        <div id="map-<?= $row['event_id'] ?>" class="mapbox"></div>
                    <?php else: ?>
                        <span style="color:#888;">Lokasi belum tersedia</span>
                    <?php endif; ?>
                </div>

                <div class="whatsapp">
                    <strong>Pendaftaran:</strong><br>
                    <?php if (!empty($row['google_form_link'])): ?>
                        <a href="<?= htmlspecialchars($row['google_form_link']) ?>" target="_blank">
                            Buka Link
                        </a>
                    <?php else: ?>
                        <span style="color:#888;">Tidak tersedia</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($row['latitude'] && $row['longitude']): ?>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map-<?= $row['event_id'] ?>', {
                zoomControl: false
            }).setView([<?= $row['latitude'] ?>, <?= $row['longitude'] ?>], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            L.marker([<?= $row['latitude'] ?>, <?= $row['longitude'] ?>]).addTo(map);
        });
        </script>
        <?php endif; ?>

    <?php endwhile; ?>
</div>

<footer class="navbar">
    <a href="index.php"><i class="fa-solid fa-house"></i>Home</a>
    <a href="simpan_event.php"><i class="fa fa-bookmark"></i>Simpan</a>
    <a href="profile.php"><i class="fa-solid fa-user"></i>Akun</a>
</footer>

</body>
</html>
