<?php
include '../includes/koneksi.php';

if (!isset($_GET['id'])) {
    echo "Event tidak ditemukan!";
    exit;
}

$event_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT e.*, c.category_name 
    FROM events e 
    LEFT JOIN categories c ON e.category_id = c.category_id 
    WHERE e.event_id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "Event tidak ditemukan!";
    exit;
}

$bannerPath = "../uploads/" . ($event['banner'] ?? '');
$bannerUrl = (empty($event['banner']) || !file_exists($bannerPath))
    ? "https://via.placeholder.com/900x600?text=No+Banner"
    : $bannerPath;

$hasCoords = is_numeric($event['latitude']) && is_numeric($event['longitude']);
$lat = $hasCoords ? floatval($event['latitude']) : null;
$lng = $hasCoords ? floatval($event['longitude']) : null;

$created_at = !empty($event['created_at']) 
    ? date('d M Y H:i', strtotime($event['created_at'])) 
    : '-';

/* ============================
   CEK EVENT SUDAH FINISHED / BELUM
   ============================ */
$eventDate = $event['event_date'] ?? null;
$today = date('Y-m-d H:i:s');
$isFinished = (!empty($eventDate) && $eventDate < $today);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($event['title']) ?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
:root {
    --bg: #f4f5f7;
    --card: #ffffff;
    --shadow: 0 6px 20px rgba(0,0,0,0.08);
    --radius: 16px;
}
body {
    margin:0;
    background:var(--bg);
    font-family:'Poppins', sans-serif;
}
.page {
    max-width:1100px;
    margin:20px auto;
    padding:14px;
}
.header {
    display:flex;
    align-items:center;
    gap:12px;
}
.header a.back {
    width:42px;
    height:42px;
    background:white;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:var(--shadow);
    text-decoration:none;
    color:black;
}
.header h1 {
    margin:0;
    font-size:20px;
    font-weight:700;
}

.event-wrapper {
    display:flex;
    flex-direction:column;
    gap:20px;
}

/* Banner box */
.banner-box {
    width:100%;
    background:white;
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    overflow:hidden;
    position:relative;
    transition:0.3s;
}

/* 💥 TAMBAHAN FINISHED GRAY */
.banner-box.finished-gray img.banner {
    filter: grayscale(100%);
    opacity: 0.55;
}

/* Banner image */
.banner {
    width:100%;
    height:auto;
    display:block;
}

/* CAP FINISHED */
.finished-stamp {
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%) rotate(-10deg);
    background:rgba(255, 0, 0, 0.9);
    color:white;
    padding:20px 60px;
    font-size:55px;
    font-weight:900;
    text-transform:uppercase;
    border-radius:10px;
    letter-spacing:4px;
    box-shadow:0 6px 16px rgba(0,0,0,0.3);
}

/* Info */
.info-box {
    background:white;
    border-radius:var(--radius);
    padding:18px;
    box-shadow:var(--shadow);
}

/* Map */
#map {
    width:100%;
    height:260px;
    border-radius:16px;
    margin-top:10px;
}

/* Deskripsi */
.description-box {
    background:white;
    border-radius:var(--radius);
    padding:20px;
    box-shadow:var(--shadow);
    margin-top:20px;
}
.description-title {
    font-size:20px;
    font-weight:700;
    margin-bottom:8px;
}

@media(min-width:900px){
    .event-wrapper {
        flex-direction:row;
        align-items:flex-start;
    }
    .banner-box {
        flex:2;
    }
    .info-box {
        flex:1;
        position:sticky;
        top:20px;
    }
}
</style>
</head>
<body>

<div class="page">

    <div class="header">
        <a href="index.php" class="back"><i class="fa fa-arrow-left"></i></a>
        <h1>Detail Event</h1>
    </div>

    <div class="event-wrapper">

        <!-- Banner -->
        <div class="banner-box <?= $isFinished ? 'finished-gray' : '' ?>">
            <img src="<?= $bannerUrl ?>" class="banner">

            <?php if ($isFinished): ?>
                <div class="finished-stamp">FINISHED</div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="info-box">

            <h2 style="margin:0;"><?= htmlspecialchars($event['title']) ?></h2>
            <div style="color:#6b7280; font-size:14px; margin-top:4px;">
                Kategori: <b><?= htmlspecialchars($event['category_name'] ?: 'Umum') ?></b> <br>
                Dibuat: <?= $created_at ?> <br>
                Tanggal Event: 
                <b><?= !empty($eventDate) ? date('d M Y', strtotime($eventDate)) : '-' ?></b>
            </div>

            <div style="margin-top:16px; font-size:15px;">
                <strong>Pendaftaran / WhatsApp:</strong><br>
                <?php if (!empty($event['google_form_link'])): ?>
                    <a href="<?= htmlspecialchars($event['google_form_link']) ?>" target="_blank">
                        <?= htmlspecialchars($event['google_form_link']) ?>
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>

            <div style="margin-top:16px;">
                <strong style="font-size:16px;">Sosial Media Penyelenggara</strong>

                <?php
                $old_type = '';
                $old_link = '';
            
                if (!empty($event['social_media'])) {
            
                    // Format: instagram|https://instagram.com/xxxx
                    $explode = explode('|', $event['social_media']);
                    $old_type = strtolower($explode[0] ?? '');
                    $old_link = $explode[1] ?? '';
            
                    // Default
                    $platform = ucfirst($old_type);
                    $icon = "fas fa-link";
            
                    if ($old_type === 'instagram') {
                        $icon = "fab fa-instagram";
                    } elseif ($old_type === 'facebook') {
                        $icon = "fab fa-facebook";
                    } elseif ($old_type === 'tiktok') {
                        $icon = "fab fa-tiktok";
                    } elseif ($old_type === 'youtube') {
                        $icon = "fab fa-youtube";
                    }
                ?>
            
                <a href="<?= htmlspecialchars($old_link) ?>" target="_blank" style="text-decoration:none;">
                    <div style="
                        margin-top:12px;
                        display:flex;
                        align-items:center;
                        gap:14px;
                        padding:12px;
                        background:#f8fafc;
                        border-radius:14px;
                        border:1px solid #e5e7eb;
                    ">
                        <div style="
                            width:38px; height:38px;
                            background:white;
                            border-radius:50%;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            box-shadow:0 2px 4px rgba(0,0,0,0.08);
                            font-size:18px;
                        ">
                            <i class="<?= $icon ?>"></i>
                        </div>
            
                        <div style="font-size:15px; color:#111;">
                            <?= $platform ?>
                        </div>
                    </div>
                </a>
            
                <?php } else { ?>
                    <p style="color:#6b7280;">Tidak ada sosial media.</p>
                <?php } ?>
            </div>

            <div style="margin-top:16px;">
                <strong>Lokasi Event</strong><br>
                <small style="color:#6b7280;">(Ditampilkan pada peta)</small>
                <?php if ($hasCoords): ?>
                    <div id="map"></div>
                <?php else: ?>
                    <p style="color:#6b7280; margin-top:10px;">Lokasi belum tersedia.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- Deskripsi -->
    <div class="description-box">
        <div class="description-title">Deskripsi Event</div>
        <div style="line-height:1.7; font-size:15px;">
            <?= nl2br(htmlspecialchars($event['description'])) ?>
        </div>
    </div>

</div>

<?php if ($hasCoords): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map)
        .bindPopup("Lokasi Event");
});
</script>
<?php endif; ?>

</body>
</html>
