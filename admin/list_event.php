<?php
include '../includes/koneksi.php';
session_start();

// Ambil semua event (JOIN kategori)
$sql = "SELECT e.*, c.category_name 
        FROM events e 
        LEFT JOIN categories c ON e.category_id = c.category_id 
        ORDER BY e.created_at DESC";
$events = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Kadwa:wght@400;700&display=swap" rel="stylesheet">
<meta charset="UTF-8">
<title>Daftar Event - Admin</title>

<style>
body {
    font-family: 'Kadwa', serif;
    background: #f2f2f2;
    padding: 40px;
}
.container {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    max-width: 1000px;
    margin: auto;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
.success {
    color: green;
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    border: 1px solid #ddd;
    padding: 10px;
}
th {
    background: #007bff;
    color: #fff;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
a.btn {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 13px;
}
.btn-add { background: #28a745; color: #fff; }
.btn-del { background: #dc3545; color: #fff; }
.btn-view { background: #007bff; color: #fff; }
.btn-edit { background: #ffc107; color: #000; }
.btn:hover { opacity: 0.85; }

img { border-radius: 6px; }

@media (max-width: 600px) {
    table, thead, tbody, tr, th, td {
        display: block;
    }
    tr { margin-bottom: 15px; }
    th { background: #444; }
}
</style>
</head>
<body>

<div class="container">
    <h2>📋 Daftar Event Kampus</h2>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">✅ Event berhasil ditambahkan!</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p class="success">🗑 Event berhasil dihapus!</p>
    <?php elseif (isset($_GET['updated'])): ?>
        <p class="success">✏ Event berhasil diperbarui!</p>
    <?php endif; ?>

    <p style="text-align:right;">
        <a href="add_event.php" class="btn btn-add">+ Tambah Event Baru</a>
        <a href="index.php" class="btn btn-add">Kembali</a>
    </p>

    <table>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Banner</th>
            <th>Aksi</th>
        </tr>

        <?php if ($events && $events->num_rows > 0): ?>
            <?php $no = 1; while ($row = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= htmlspecialchars($row['category_name'] ?? '-'); ?></td>

                    <td>
                        <?php if (!empty($row['banner'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['banner']); ?>" width="70">
                        <?php else: ?>
                            <span style="color:#888;">(tidak ada)</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="edit_event.php?id=<?= $row['event_id']; ?>" class="btn btn-edit">✏ Edit</a>
                        <a href="delete_event.php?id=<?= $row['event_id']; ?>" class="btn btn-del"
                           onclick="return confirm('Yakin ingin menghapus event ini?')">🗑 Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">Tidak ada event ditemukan.</td>
            </tr>
        <?php endif; ?>

    </table>
</div>

</body>
</html>
