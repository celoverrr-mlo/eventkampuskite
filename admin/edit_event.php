<?php
include '../includes/koneksi.php';
session_start();

// --- CEK ID ---
if (!isset($_GET['id'])) {
    die("ID event tidak ditemukan!");
}
$event_id = intval($_GET['id']);

// --- AMBIL DATA EVENT ---
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event tidak ditemukan");
}

// KONVERSI TIMESTAMP KE FORMAT DATETIME-LOCAL (AGAR BISA DI EDIT)
$event_date_value = "";
if (!empty($event['event_date'])) {
    // Jika di DB kolom bernama event_date dan bertipe TIMESTAMP/DATETIME
    $event_date_value = date("Y-m-d\TH:i", strtotime($event['event_date']));
}

// --- PROSES UPDATE DATA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = $_POST['title'];
    $description = $_POST['description'];
    $category_id = intval($_POST['category_id']);
    $latitude    = $_POST['latitude'];
    $longitude   = $_POST['longitude'];
    $google_form_link = $_POST['google_form_link'] ?? null;
    $social_media     = $_POST['social_media'] ?? null;

    // === DATE handling: input datetime-local gives "YYYY-MM-DDTHH:MM"
    // Convert to "YYYY-MM-DD HH:MM:SS" for MySQL TIMESTAMP
    $event_date_input = $_POST['event_date'] ?? '';
    $event_date = null;
    if (!empty($event_date_input)) {
        // replace 'T' with space and append :00 seconds if missing
        $event_date = str_replace('T', ' ', $event_date_input);
        if (strlen($event_date) === 16) $event_date .= ':00';
    }

    // sanitize numeric coords
    $latitude  = is_numeric($latitude) ? floatval($latitude) : 0.0;
    $longitude = is_numeric($longitude) ? floatval($longitude) : 0.0;

    // UPLOAD GAMBAR JIKA ADA FILE BARU
    $newBanner = $event['banner'];

    if (!empty($_FILES['banner']['name'])) {
        $bannerName = time() . "_" . basename($_FILES['banner']['name']);
        $tmpName    = $_FILES['banner']['tmp_name'];

        move_uploaded_file($tmpName, "../uploads/" . $bannerName);
        $newBanner = $bannerName;
    }

    // UPDATE DATABASE + tambahkan kolom event_date
    $sql = "
        UPDATE events 
        SET title=?, description=?, category_id=?, banner=?, latitude=?, longitude=?, google_form_link=?, social_media=?, event_date=?
        WHERE event_id=?
    ";

    $stmt = $conn->prepare($sql);

    // Jika prepare gagal, tampilkan error (menghindari fatal error saat bind_param)
    if (!$stmt) {
        // debug: tampilkan error dan SQL (hapus/disable echo ini di production)
        echo "<pre style='color:red'>Prepare failed: " . htmlspecialchars($conn->error) . "\nSQL: " . htmlspecialchars($sql) . "</pre>";
        exit;
    }

    // Tipe binding:
    // title (s), description (s), category_id (i), banner (s),
    // latitude (d), longitude (d), google_form_link (s), social_media (s),
    // event_date (s), event_id (i)
    $bindTypes = "ssisddsssi";

    // Pastikan event_date tidak null string — jika null, set ke NULL via string 'NULL' tidak boleh;
    // Kita bind sebagai string kosong jika null (MySQL akan accept '' or use proper NULL if needed).
    $event_date_bind = $event_date ?? '';

    $stmt->bind_param(
        $bindTypes,
        $title,
        $description,
        $category_id,
        $newBanner,
        $latitude,
        $longitude,
        $google_form_link,
        $social_media,
        $event_date_bind,
        $event_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Event berhasil diupdate!'); window.location='list_event.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update event!');</script>";
        // debug
        echo "<pre style='color:red'>Execute failed: " . htmlspecialchars($stmt->error) . "</pre>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Event</title>

<style>
    body {
        font-family: Arial, sans-serif; background: #f5f5f5;
        margin: 0; padding: 0;
    }
    .container {
        max-width: 600px; background: #fff;
        padding: 20px; margin: 30px auto;
        border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input, textarea, select {
        width: 100%; padding: 10px; margin-top: 5px;
        border: 1px solid #ccc; border-radius: 5px;
    }
    textarea { min-height: 120px; }
    #map { width: 100%; height: 300px; margin-top: 10px; border-radius: 10px; }
    button {
        width: 100%; margin-top: 15px; padding: 12px;
        background: #007bff; color: white; border: none;
        border-radius: 5px; cursor: pointer;
        font-size: 16px;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

</head>
<body>
<div class="container">
    <h2>Edit Event</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Judul Event</label>
        <input type="text" name="title" value="<?= htmlspecialchars($event['title']); ?>" required>

        <label>Deskripsi</label>
        <textarea name="description" required><?= htmlspecialchars($event['description']); ?></textarea>

        <label>Kategori</label>
        <select name="category_id" required>
            <option value="1" <?= $event['category_id']==1?'selected':'' ?>>All</option>
            <option value="2" <?= $event['category_id']==2?'selected':'' ?>>Workshop</option>
            <option value="3" <?= $event['category_id']==3?'selected':'' ?>>Lomba</option>
            <option value="4" <?= $event['category_id']==4?'selected':'' ?>>Kepanitiaan</option>
            <option value="5" <?= $event['category_id']==5?'selected':'' ?>>Seminar</option>
            <option value="6" <?= $event['category_id']==6?'selected':'' ?>>Webinar</option>
        </select>

        <label>Banner (kosongkan jika tidak diganti)</label>
        <input type="file" name="banner" accept="image/*">

        <?php if ($event['banner']): ?>
            <img src="../uploads/<?= htmlspecialchars($event['banner']); ?>" width="120" style="margin-top:10px; border-radius:5px;">
        <?php endif; ?>

        <label>Google Form / WA Link</label>
        <input type="text" name="google_form_link" value="<?= htmlspecialchars($event['google_form_link']); ?>">

        <label>Sosial Media Penyelenggara</label>
        <input type="text" name="social_media" value="<?= htmlspecialchars($event['social_media']); ?>">

        <!-- === TIMESTAMP DATE === -->
        <label>Tanggal Event</label>
        <input type="datetime-local" name="event_date" value="<?= $event_date_value ?>" required>

        <label>Lokasi Event (klik di map)</label>
        <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($event['latitude']); ?>" readonly required>
        <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($event['longitude']); ?>" readonly required>

        <div id="map"></div>

        <button type="submit">Update Event</button>
    </form>
</div>

<script>
let lat = <?= is_numeric($event['latitude']) ? $event['latitude'] : -6.200 ?>;
let lng = <?= is_numeric($event['longitude']) ? $event['longitude'] : 106.816 ?>;

let map = L.map('map').setView([lat, lng], 14);
let marker;

// MAP TILE
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

// Tampilkan marker awal
marker = L.marker([lat, lng], {draggable: true}).addTo(map);

// Update saat drag
marker.on('dragend', function (e) {
    let pos = marker.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
});

// Klik map untuk pindah lokasi
map.on('click', function(e) {
    let pos = e.latlng;
    marker.setLatLng(pos);

    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
});
</script>

</body>
</html>