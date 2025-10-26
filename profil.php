<?php
session_start();
require_once "config/database.php";
require_once "classes/Akun.php";

// Redirect jika belum login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$db = (new Database())->getConnection();
$akun = new Akun($db);
$user = $_SESSION['user'];
$message = "";

// Update data profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akun->id_akun = $user['id_akun'];
    $akun->nama_depan = trim($_POST['nama_depan']);
    $akun->nama_belakang = trim($_POST['nama_belakang']);
    $akun->email = trim($_POST['email']);
    $akun->nomor_telpon = trim($_POST['nomor_telpon']);

    if ($akun->updateProfile()) {
        // sinkronisasi session
        $_SESSION['user']['nama_depan'] = $akun->nama_depan;
        $_SESSION['user']['nama_belakang'] = $akun->nama_belakang;
        $_SESSION['user']['email'] = $akun->email;
        $_SESSION['user']['nomor_telpon'] = $akun->nomor_telpon;
        $message = "<span style='color:green;'>✅ Profil berhasil diperbarui.</span>";
    } else {
        $message = "<span style='color:red;'>❌ Gagal memperbarui profil.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Profil Saya - Sistem Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'layout/header.php'; ?>

<div class="container" style="max-width:700px;">
  <h2 style="text-align:center;">Profil Saya</h2>

  <?php if ($message): ?>
    <div style="text-align:center; margin-bottom:10px;"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" class="form-profil">
    <div class="form-row">
      <label for="nama_depan">Nama Depan</label>
      <input type="text" name="nama_depan" id="nama_depan" value="<?= htmlspecialchars($user['nama_depan']) ?>" required>
    </div>

    <div class="form-row">
      <label for="nama_belakang">Nama Belakang</label>
      <input type="text" name="nama_belakang" id="nama_belakang" value="<?= htmlspecialchars($user['nama_belakang']) ?>" required>
    </div>

    <div class="form-row">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="form-row">
      <label for="nomor_telpon">Nomor Telepon</label>
      <input type="text" name="nomor_telpon" id="nomor_telpon" value="<?= htmlspecialchars($user['nomor_telpon']) ?>" required>
    </div>

    <div style="text-align:center; margin-top:15px;">
      <button type="submit" class="button">💾 Simpan Perubahan</button>
    </div>

    <p style="text-align:center; margin-top:15px;">
      <a href="home.php">← Kembali ke Beranda</a>
    </p>
  </form>
</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
