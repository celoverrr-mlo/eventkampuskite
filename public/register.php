<?php
include '../includes/koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $instansi = trim($_POST['instansi']);
    $password = trim($_POST['password']);

    // Cek apakah email sudah terdaftar
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Email sudah digunakan. Silakan login.";
    } else {
        // Enkripsi password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Simpan data ke database
        $sql = "INSERT INTO users (name, email, instansi, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $instansi, $hashed);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            header("Location: login.php?registered=1");
            exit;
        } else {
            $error = "❌ Gagal mendaftar. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Akun - Kampus Kite</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef2ff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
form {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    width: 380px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
h2 { text-align: center; margin-bottom: 20px; }
input {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
button {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
}
button:hover { background: #0056b3; }
p { text-align: center; margin-top: 10px; }
.error { color: red; text-align: center; margin-bottom: 10px; }
</style>
</head>
<body>
<form method="POST">
    <h2>🧑‍🎓 Daftar Akun</h2>
    <?php if (isset($error)): ?><div class="error"><?= htmlspecialchars($error); ?></div><?php endif; ?>

    <input type="text" name="name" placeholder="Nama Lengkap" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="instansi" placeholder="Instansi (contoh: Universitas X)" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Daftar</button>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>
</form>
</body>
</html>
