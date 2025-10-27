<?php

// wahai teman-teman sekalian

// sesungguhya saat saya buat kode ini hanya saya dan tuhan yang ngerti

// tapi setelah saya gaya-gayaan mau nambah fitur keamanan extra dan lupa nge commit hanya tuhan yang tau

// sumpah aku harusnya ga ngubah sesuatu yang sudah berjalan :V

// AI pun kek taik setiap kali kutanya apa yang salah malah dibuat kode dengan struktur baru aseemm

session_start();
require_once "config/database.php";

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$db = (new Database())->getConnection();
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = trim($_POST['login_input'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($input && $password) {
        // cari user berdasarkan email atau username
        $stmt = $db->prepare("SELECT * FROM akun WHERE (email = :input OR username = :input) LIMIT 1");
        $stmt->execute([':input' => $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hash = $user['password'];
            // verifikasi password (mendukung password_hash dan md5 lama)
            if (password_verify($password, $hash) || $hash === md5($password)) {
                // upgrade hash lama ke bcrypt jika masih md5
                if ($hash === md5($password)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $update = $db->prepare("UPDATE akun SET password=? WHERE id_akun=?");
                    $update->execute([$newHash, $user['id_akun']]);
                }

                $_SESSION['user'] = $user;
                
                // Log login activity
                require_once "classes/Pengunjung.php";
                $pengunjung = new Pengunjung($db, $user['id_akun'], 
                    $user['nama_depan'] . ' ' . $user['nama_belakang'], 
                    $user['email']);
                $pengunjung->logKunjungan('login', 'Login ke sistem');
                
                header("Location: home.php");
                exit;
            } else {
                $error = "⚠️ Password salah!";
            }
        } else {
            $error = "⚠️ Akun tidak ditemukan!";
        }
    } else {
        $error = "⚠️ Mohon isi semua kolom.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Sistem Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'layout/header.php'; ?>

<div class="container" style="max-width:600px;">
  <h2 style="text-align:center;">Login ke Sistem Perpustakaan</h2>

  <?php if ($error): ?>
    <p style="color:red; text-align:center; margin-bottom:10px;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="form-login">
    <div class="form-row">
      <label for="login_input">Email / Username</label>
      <input type="text" name="login_input" id="login_input" required>
    </div>

    <div class="form-row">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>

    <div style="text-align:center; margin-top:15px;">
      <button type="submit" class="button">Masuk</button>
    </div>

    <p style="text-align:center; margin-top:10px;">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </p>
  </form>
</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
