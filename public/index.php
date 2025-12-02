<?php
include '../includes/koneksi.php';

session_start();
$user_name = $_SESSION['user_name'] ?? 'Anggi'; // Nama pengguna default

$search = $_GET['search'] ?? '';

$sql = "SELECT e.*, c.category_name 
        FROM events e 
        LEFT JOIN categories c ON e.category_id = c.category_id";

if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $sql .= " WHERE e.title LIKE '%$s%' ";
}

$sql .= " ORDER BY e.created_at DESC LIMIT 12";
$events = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Event Kampus Kite - All</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; }

html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background: white;
    display: flex;
    flex-direction: column;
}

.content-wrapper {
    flex: 1;
    padding-bottom: 70px; /* ruang untuk bottom navbar */
}

/* HEADER */
header {
    width: 100%;
    background:white;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 15px 15px 0 0;
    box-shadow: 0 4px 8px rgba(136, 140, 144, 0.2);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-radius: 18px;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo img {
    width: 50px;
    height: 50px;
}

.app-title {
    font-weight: 700;
    font-size: 20px;
}

/* SEARCH */
.search-box {
    flex: 1;
    margin: 0 16px;
}

.search-box input {
    width: 100%;
    padding: 7px 12px;
    border-radius: 20px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* BANNER */
.banner-slider {
  width: 100%;
  height: 210px;
  margin: 15px auto;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
  background: #e9e9e9;
}
.slide { width: 100%; height: 100%; position: absolute; opacity: 0; transition: .8s; }
.slide.active { opacity: 1; }
.slide img { width: 100%; height: 100%; object-fit: cover; }

/* KATEGORI */
.categories {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 12px;
    justify-content: center;
}

.cat-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #ececec;
    border-radius: 20px;
    font-size: 14px;
    text-decoration: none;
    color: #000;
    white-space: nowrap;
    box-shadow: 4px 4px 4px rgba(0,0,0,0.1);
}
.cat-btn.active { background: #ff5722; color: #fff; }

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(165px,1fr));
    gap: 14px;
    padding: 15px;
}

.card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 14px;
    box-shadow: 4px 4px 8px rgba(136, 140, 144, 0.2);
    overflow: hidden;
}

.card img {
    width: 100%;
    height: 270px;
    object-fit: cover;
}

.card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10px;
}

.card h4 {
    font-size: 15px;
    line-height: 1.2;
    margin: 0 0 8px 0;
    word-wrap: break-word;
}

.btn-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.btn-detail, .btn-save {
    padding: 6px 13px;
    border-radius: 7px;
    font-size: 13px;
    text-decoration: none;
    color: #fff;
}

.btn-detail { background: #007bff; }
.btn-save   { background: #ffaa00; }

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
/* MOBILE */
@media(max-width:768px){
    .search-box { flex: 1; }
}
</style>
</head>

<body>
<div class="content-wrapper">

<!-- HEADER -->
<header>
    <div class="logo">
        <img src="../uploads/logo.png" alt="logo">
        <div class="app-title">Event Kampus Kite</div>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari event...">
    </form>

    <div class="nav-right">
        <i class="fa fa-bookmark save-logo"></i>
        <span class="user-name">Hi, <?= htmlspecialchars($user_name) ?></span>
    </div>

    
</header>





<!-- BANNER -->
<div class="banner-slider">
<?php
$bannerDir = '../uploads/banner/';
$banners = glob($bannerDir.'*.{jpg,jpeg,png,webp}', GLOB_BRACE);
if (!$banners) $banners = [];
foreach ($banners as $i => $b):
    $active = $i == 0 ? 'active' : '';
?>
    <div class="slide <?= $active ?>">
        <img src="<?= $b ?>" alt="banner-<?= $i ?>">
    </div>
<?php endforeach; ?>
</div>

<script>
let idx = 0;
const s = document.querySelectorAll('.slide');
if(s.length>0){
    setInterval(()=>{
        s[idx].classList.remove('active');
        idx=(idx+1)%s.length;
        s[idx].classList.add('active');
    },3000);
}
</script>

<!-- KATEGORI -->
<div class="categories">
    <a href="index.php" class="cat-btn active"><i class="fa fa-th-large"></i> All</a>
    <a href="workshop.php" class="cat-btn"><i class="fa fa-trophy"></i> Workshop</a>
    <a href="lomba.php" class="cat-btn"><i class="fa fa-calendar-plus"></i> Lomba</a>
    <a href="kepanitiaan.php" class="cat-btn"><i class="fa fa-refresh"></i> Kepanitiaan</a>
     <a href="webinar.php" class="cat-btn"><i class="fa fa-refresh"></i> Webinar</a>
      <a href="seminar.php" class="cat-btn"><i class="fa fa-refresh"></i> Seminar</a>
</div>

<!-- GRID EVENT -->
<div class="grid">
<?php while($e = $events->fetch_assoc()): ?>
    <div class="card">
        <img src="../uploads/<?= htmlspecialchars($e['banner']) ?>" alt="<?= htmlspecialchars($e['title']) ?>">
        <div class="card-content">
            <h4><?= htmlspecialchars(strtoupper($e['title'])) ?></h4>
            <div class="btn-group">
                <a class="btn-detail" href="detail_event.php?id=<?= (int)$e['event_id'] ?>">Detail</a>
                <a class="btn-save" href="save_events.php?id=<?= (int)$e['event_id'] ?>">Simpan</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div> <!-- content-wrapper -->

<footer class="navbar">
  <a href="index.php">
    <i class="fa-solid fa-house"></i><span>Home</span>
  </a>

    <a href="simpan_event.php">
        <i class="fa fa-bookmark save-logo"></i><span>Simpan</span>
    </a>

    <a href="profile.php">
      <i class="fa-solid fa-user"></i><span>Akun</span>
    </a>
    
 

</footer>

</body>
</html>
