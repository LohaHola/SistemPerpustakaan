<?php
session_start();
require_once "config/database.php";
require_once "classes/Akun.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'biasa') {
  header("Location: index.php");
  exit;
}

$db = (new Database())->getConnection();
$akun = new Akun($db);
$user = $_SESSION['user'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update role ke komite
    if ($akun->updateRole($user['id_akun'], 'komite')) {
        $_SESSION['user']['role'] = 'komite';
        $message = "<span style='color:green;'>✅ Anda berhasil mendaftar sebagai Komite Perpustakaan.</span>";
    } else {
        $message = "<span style='color:red;'>❌ Gagal mendaftar sebagai komite.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Daftar Komite Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'layout/header.php'; ?>
<div class="container" style="max-width:600px;">
  <div class="card">
    <h2 style="text-align:center;">Daftar Komite Perpustakaan</h2>
    <?php if($message): ?>
      <div style="text-align:center; margin-bottom:10px;"> <?= $message ?> </div>
    <?php endif; ?>
    <?php if ($_SESSION['user']['role'] === 'komite'): ?>
      <p style="text-align:center;">Anda sudah terdaftar sebagai Komite Perpustakaan.</p>
      <div style="text-align:center; margin-top:15px;">
        <a href="home.php" class="button">Kembali ke Beranda</a>
      </div>
    <?php else: ?>
      <form method="post" style="text-align:center;">
        <p>Dengan mendaftar sebagai Komite Perpustakaan, Anda akan mendapatkan akses tambahan untuk mengelola buku dan peminjaman.</p>
        <button type="submit" class="button primary">Daftar Sebagai Komite</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php include 'layout/footer.php'; ?>
</body>
</html>
