<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>FAQ - Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'layout/header.php'; ?>
<div class="container">
  <div class="card">
    <h2>FAQ — Pertanyaan yang Sering Diajukan</h2>

    <h3>Bagaimana cara mendaftar?</h3>
    <p>Klik <b>Daftar</b> pada halaman Login, isi form, lalu gunakan email & password untuk login.</p>

    <h3>Bagaimana meminjam buku?</h3>
    <p>Pilih buku di beranda, klik <b>Pinjam</b>. Sistem otomatis mencatat akun yang sedang login.</p>

    <h3>Bagaimana mengembalikan buku?</h3>
    <p>Buka <b>Status Peminjaman Saya</b> di beranda lalu klik <b>Kembalikan</b> pada baris yang sesuai.</p>

    <h3>Siapa yang dapat menambah buku?</h3>
    <p>Hanya user dengan peran <b>admin</b> yang dapat menambah buku.</p>

    <p style="margin-top:12px;"><a href="home.php">← Kembali ke Beranda</a></p>
  </div>
</div>
<?php include 'layout/footer.php'; ?>
</body>
</html>
