<?php include 'layout/header.php'; ?>
<?php include 'layout/footer.php'; ?>

<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'komite') {
    header("Location: index.php");
    exit;
}

$db = (new Database())->getConnection();
$query = "SELECT p.id_pinjam, b.judul, p.tanggal_pinjam, p.durasi_pinjam,
          a.nama_depan, a.nama_belakang, a.email, a.nomor_telpon
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          JOIN akun a ON p.id_akun = a.id_akun
          ORDER BY p.tanggal_pinjam DESC";
$stmt = $db->prepare($query);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Peminjaman - Komite</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
  <h2>📋 Daftar Seluruh Peminjaman</h2>
  <table>
    <thead><tr>
      <th>ID</th><th>Judul</th><th>Peminjam</th>
      <th>Email</th><th>Telepon</th><th>Tanggal</th><th>Durasi</th>
    </tr></thead>
    <tbody>
      <?php while($r = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
      <tr>
        <td><?= $r['id_pinjam'] ?></td>
        <td><?= htmlspecialchars($r['judul']) ?></td>
        <td><?= htmlspecialchars($r['nama_depan'] . ' ' . $r['nama_belakang']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['nomor_telpon']) ?></td>
        <td><?= $r['tanggal_pinjam'] ?></td>
        <td><?= $r['durasi_pinjam'] ?> hari</td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <p style="margin-top:15px;"><a href="home.php">← Kembali ke Beranda</a></p>
</div>

</body>
</html>
