<?php
session_start();
require_once "config/database.php";
require_once "classes/Akun.php";

$db = (new Database())->getConnection();
$akun = new Akun($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama_depan = trim($_POST['nama_depan'] ?? '');
    $nama_belakang = trim($_POST['nama_belakang'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nomor_telpon = trim($_POST['nomor_telpon'] ?? '');
    $password = $_POST['password'] ?? '';
    $ulang_password = $_POST['ulang_password'] ?? '';

    // Validasi dasar
    if (!$username || !$nama_depan || !$nama_belakang || !$email || !$nomor_telpon || !$password || !$ulang_password) {
        $message = "⚠️ Semua kolom wajib diisi.";
    } elseif ($password !== $ulang_password) {
        $message = "⚠️ Password dan konfirmasi tidak sama.";
    } else {
        // Cek email atau username sudah digunakan
        $cek = $db->prepare("SELECT COUNT(*) FROM akun WHERE email=? OR username=?");
        $cek->execute([$email, $username]);
        if ($cek->fetchColumn() > 0) {
            $message = "⚠️ Email atau username sudah terdaftar.";
        } else {
            // Simpan ke database
            $query = "INSERT INTO akun (username, nama_depan, nama_belakang, email, password, nomor_telpon, role)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $result = $stmt->execute([
                $username,
                $nama_depan,
                $nama_belakang,
                $email,
                $hash,
                $nomor_telpon,
                'biasa'
            ]);

            if ($result) {
                $message = "<span style='color:green;'>✅ Registrasi berhasil! <a href='index.php'>Login sekarang</a>.</span>";
            } else {
                $message = "❌ Gagal menyimpan data ke database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Akun - Sistem Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="container" style="max-width:650px;">
  <h2 style="text-align:center;">Formulir Pendaftaran Akun</h2>

  <?php if ($message): ?>
    <p style="text-align:center; color:red;"><?= $message ?></p>
  <?php endif; ?>

  <form method="POST" class="form-registrasi">
    <div class="form-row">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>
    </div>

    <div class="form-row">
      <label for="nama_depan">Nama Depan</label>
      <input type="text" name="nama_depan" id="nama_depan" required>
    </div>

    <div class="form-row">
      <label for="nama_belakang">Nama Belakang</label>
      <input type="text" name="nama_belakang" id="nama_belakang" required>
    </div>

    <div class="form-row">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>
    </div>

    <div class="form-row">
      <label for="nomor_telpon">Nomor Telepon</label>
      <input type="text" name="nomor_telpon" id="nomor_telpon" required>
    </div>

    <div class="form-row">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>
    </div>

    <div class="form-row">
      <label for="ulang_password">Ulangi Password</label>
      <input type="password" name="ulang_password" id="ulang_password" required>
    </div>

    <div style="text-align:center; margin-top:15px;">
      <button type="submit" class="button">Daftar</button>
    </div>

    <p style="text-align:center; margin-top:10px;">
      Sudah punya akun? <a href="index.php">Login di sini</a>
    </p>
  </form>
</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
