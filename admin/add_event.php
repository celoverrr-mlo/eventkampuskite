<?php
// add_event.php
include '../includes/koneksi.php';

// PROSES SIMPAN EVENT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    // convert latitude & longitude ke float
    $latitude    = floatval($_POST['latitude']);
    $longitude   = floatval($_POST['longitude']);

    $google_form_link = $_POST['google_form_link'] ?? null;

    // === FIELD DATE (TIMESTAMP) ===
    $event_date = $_POST['event_date']; // pastikan format 'YYYY-MM-DDTHH:MM'
    // ubah ke format MySQL DATETIME: 'YYYY-MM-DD HH:MM:SS'
    $event_date = date('Y-m-d H:i:s', strtotime($event_date));
    
    // --- UPLOAD GAMBAR ---
    $bannerName = $_FILES['banner']['name'];
    $tmpName    = $_FILES['banner']['tmp_name'];

    // pastikan folder uploads ada dan bisa ditulis
    $uploadPath = "../uploads/" . basename($bannerName);
    if (!move_uploaded_file($tmpName, $uploadPath)) {
        echo "<script>alert('Gagal upload banner!');</script>";
        exit;
    }

    // SET DEFAULT CREATED_BY DAN STATUS
    $created_by = 1; // atau ambil dari $_SESSION
    $status     = "active";

    // --- INSERT DATABASE ---
    $stmt = $conn->prepare("
        INSERT INTO events
        (category_id, title, description, banner, google_form_link, created_by, status, latitude, longitude, event_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if(!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "issssissds",
        $category_id,
        $title,
        $description,
        $bannerName,
        $google_form_link,
        $created_by,
        $status,
        $latitude,
        $longitude,
        $event_date
    );

    if ($stmt->execute()) {
        echo "<script>alert('Event berhasil ditambahkan!'); window.location='list_event.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan event! Error: " . $stmt->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Event</title>
<style>
    body { font-family: Arial; margin:0; padding:0; background:#f5f5f5; }
    .container { max-width:600px; background:#fff; padding:20px; margin:30px auto; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
    h2 { margin-top:0; }
    label { font-weight:bold; display:block; margin-top:10px; }
    input, textarea, select { width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; margin-top:5px; }
    textarea { min-height:120px; }
    button { padding:12px 20px; background:#007bff; color:white; border:none; margin-top:15px; width:100%; cursor:pointer; border-radius:5px; font-size:16px; }
    #map { width:100%; height:300px; border-radius:10px; margin-top:10px; }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
<div class="container">
    <h2>Tambah Event</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Judul Event</label>
        <input type="text" name="title" required>

        <label>Deskripsi</label>
        <textarea name="description" required></textarea>

        <label>Lokasi (Nama Tempat)</label>
        <input type="text" name="location" placeholder="Contoh: GBK Jakarta" required>

        <label>Kategori</label>
        <select name="category_id" required>
            <option value="1">All</option>
            <option value="2">Workshop</option>
            <option value="3">Lomba</option>
            <option value="4">Kepanitiaan</option>
            <option value="5">Seminar</option>
            <option value="6">Webinar</option>
        </select>

        <label>Banner Event</label>
        <input type="file" name="banner" accept="image/*" required>

        <label>Google Form Link</label>
        <input type="text" name="google_form_link" placeholder="https://forms.gle/...">

        <label>Tanggal Event</label>
        <input type="datetime-local" name="event_date" required>

        <label>Pilih Lokasi Event (klik map)</label>
        <input type="text" id="latitude" name="latitude" placeholder="Latitude" readonly required>
        <input type="text" id="longitude" name="longitude" placeholder="Longitude" readonly required>
        
        <div id="map"></div>

        <button type="submit">Simpan Event</button>
    </form>
</div>

<script>
let map = L.map('map').setView([-6.200, 106.816], 13);
let marker;
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Geolocation otomatis
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        map.setView([pos.coords.latitude, pos.coords.longitude], 15);
    });
}

// Klik map → set lokasi
map.on('click', function(e) {
    let lat = e.latlng.lat;
    let lng = e.latlng.lng;

    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);
});
</script>
</body>
</html>