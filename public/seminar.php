<?php
include '../includes/koneksi.php';

$search = $_GET['search'] ?? '';

$sql = "SELECT e.*, c.category_name 
        FROM events e 
        LEFT JOIN categories c ON e.category_id = c.category_id
        WHERE c.category_name = 'Seminar'";

if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $sql .= " AND e.title LIKE '%$s%'";
}

$sql .= " ORDER BY e.created_at DESC";
$events = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Event Rekruitment Kepanitiaan</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; }

html, body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    background: #f5f5f5;
    font-family: 'Poppins', sans-serif;
}

/* HEADER */
header {
    width: 100%;
    background: #fff;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    display: flex;
    align-items: center;
    gap: 8px;
}

.logo img {
    width: 38px;
    height: 38px;
}

.app-title {
    font-weight: 700;
    font-size: 18px;
}

/* SEARCH */
.search-box {
    flex: 0.6;
}

.search-box input {
    width: 100%;
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* HEADER BUTTON */
.header-right {
    display: flex;
    gap: 8px;
}

.header-btn {
    background: #eee;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 13px;
}

.menu-btn {
    font-size: 26px;
    display: none;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 0;
    left: -240px;
    width: 220px;
    height: 100%;
    background: #fff;
    padding: 15px;
    box-shadow: 1px 0 6px rgba(0,0,0,0.15);
    transition: .3s;
    z-index: 5000;
}
.sidebar.active {
    left: 0;
}
.sidebar a {
    display: block;
    padding: 10px 0;
    color: #333;
    font-size: 16px;
}

/* BANNER */
.banner-slider {
  width: 92%;
  height: 210px;
  margin: 15px auto;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
  background: #e9e9e9;
}
.slide {
  width: 100%;
  height: 100%;
  position: absolute;
  opacity: 0;
  transition: .8s;
}
.slide.active {
  opacity: 1;
}
.slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* KATEGORI */
.categories {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 12px;
    max-width: 100%;
     justify-content: center;
}

.cat-btn {
    padding: 8px 18px;
    background: #ececec;
    border-radius: 20px;
    font-size: 14px;
    text-decoration: none;
    color: #000;
    white-space: nowrap;
}

.cat-btn.active {
    background: #ff5722;
    color: #fff;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(165px,1fr));
    gap: 14px;
    padding: 15px;
}

.card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 4px 4px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    padding-bottom: 13px;
}

.card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.card h4 {
    padding: 4px 10px;
    font-size: 15px;
    height: 45px;
    overflow: hidden;
}

.card p {
    margin: -5px 10px 8px;
    color: gray;
    font-size: 12px;
}

.btn-group {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding-top: 8px;
}

.btn-detail, .btn-save {
    padding: 6px 13px;
    border-radius: 7px;
    font-size: 13px;
    text-decoration: none;
    color: #fff;
}

.btn-detail {
    background: #007bff;
}

.btn-save {
    background: #ffaa00;
}

/* MOBILE */
@media(max-width:768px){
    .search-box { flex: 1; }
    .header-right { display: none; }
    .menu-btn { display: block; margin-left: auto; }
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="../uploads/logo.png" alt="logo">
        <div class="app-title">Event Kampus Kite</div>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari event...">
    </form>

    <div class="menu-btn" onclick="toggleMenu()">
        <i class="fa fa-bars"></i>
    </div>
</header>

<script>
function toggleMenu(){ document.getElementById('sidebar').classList.toggle('active'); }
</script>

<div class="categories">
    <a href="index.php" class="cat-btn active"><i class="fa fa-th-large"></i> All</a>
    <a href="workshop.php" class="cat-btn"><i class="fa fa-trophy"></i> Workshop</a>
    <a href="lomba.php" class="cat-btn"><i class="fa fa-calendar-plus"></i> Lomba</a>
    <a href="kepanitiaan.php" class="cat-btn"><i class="fa fa-refresh"></i> Kepanitiaan</a>
     <a href="webinar.php" class="cat-btn"><i class="fa fa-refresh"></i> Webinar</a>
      <a href="seminar.php" class="cat-btn"><i class="fa fa-refresh"></i> Seminar</a>
</div>

<div class="grid">
<?php while($e = $events->fetch_assoc()): ?>
    <div class="card">
        <img src="../uploads/<?= htmlspecialchars($e['banner']) ?>" alt="<?= htmlspecialchars($e['title']) ?>">
        <h4><?= htmlspecialchars(strtoupper($e['title'])) ?></h4>
        <p><?= htmlspecialchars($e['category_name']) ?></p>

        <div class="btn-group">
            <a class="btn-detail" href="detail_event.php?id=<?= (int)$e['event_id'] ?>">Detail</a>
            <a class="btn-save" href="save_events.php?id=<?= (int)$e['event_id'] ?>">Simpan</a>
        </div>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
