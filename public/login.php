<?php
include '../includes/koneksi.php';
session_start();

// Jika sudah login, langsung ke index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Cek email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan ke session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['instansi'] = $user['instansi'];

            header("Location: index.php?login=success");
            exit;
        } else {
            $error = "❌ Password salah!";
        }
    } else {
        $error = "❌ Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Mahasiswa - Kampus Kite</title>
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
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}
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
button:hover {
    background: #0056b3;
}
p {
    text-align: center;
    margin-top: 10px;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
.error {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}
.success {
    color: green;
    text-align: center;
    margin-bottom: 10px;
}
</style>
</head>
<body>
<form method="POST">
    <h2>🔐 Login Akun</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="success">✅ Akun berhasil dibuat, silakan login.</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Login</button>

    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</form>
</body>
</html>
