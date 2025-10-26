<?php
session_start();
require_once "config/database.php";
require_once "classes/Buku.php";
require_once "classes/Peminjaman.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$db = (new Database())->getConnection();
$bukuModel = new Buku($db);
$allBooks = $bukuModel->getAllBuku();

$peminjamanModel = new Peminjaman($db);

// Daftar pinjaman milik user login
$userLoansStmt = $peminjamanModel->lihatPeminjaman($user['id_akun']);

// Daftar semua pinjaman (untuk admin dan komite)
$allLoansStmt = null;
if (in_array($user['role'], ['admin', 'komite'])) {
    $query = "SELECT p.id_pinjam, b.judul, p.tanggal_pinjam, p.durasi_pinjam,
              a.nama_depan, a.nama_belakang, a.email, a.nomor_telpon
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN akun a ON p.id_akun = a.id_akun
              ORDER BY p.tanggal_pinjam DESC";
    $allLoansStmt = $db->prepare($query);
    $allLoansStmt->execute();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Beranda - Sistem Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'layout/header.php'; ?>

<div class="container">

  <!-- 🏠 WELCOME MESSAGE -->
  <div class="card welcome-card">
    <h2>Selamat Datang, <?= htmlspecialchars($user['nama_depan'] . ' ' . $user['nama_belakang']) ?> 👋</h2>
    <p>Senang melihatmu kembali di Sistem Perpustakaan Universitas Bengkulu</p>
  </div>

  <!-- 📚 DAFTAR BUKU -->
  <div class="card">
    <h2>📚 Daftar Buku</h2>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Judul</th>
          <th>Penulis</th>
          <th>Stok</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($b = $allBooks->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $b['id_buku'] ?></td>
          <td><?= htmlspecialchars($b['judul']) ?></td>
          <td><?= htmlspecialchars($b['penulis']) ?></td>
          <td><?= $b['stok'] ?></td>
          <td>
            <?php if($b['stok'] > 0): ?>
              <a class="button" href="pinjam.php?id_buku=<?= $b['id_buku'] ?>">Pinjam</a>
            <?php else: ?>
              <span class="small">Stok kosong</span>
            <?php endif; ?>

            <?php if(in_array($user['role'], ['admin', 'komite'])): ?>
              &nbsp;<a class="button secondary" href="update_buku.php?edit=<?= $b['id_buku'] ?>">Edit</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <?php if(in_array($user['role'], ['admin', 'komite'])): ?>
      <div style="text-align:center; margin-top:15px;">
        <a class="button primary" href="update_buku.php">+ Tambah Buku</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- 📦 STATUS PEMINJAMAN SAYA -->
  <div class="card">
    <h2>📦 Status Peminjaman Saya</h2>
    <table class="table">
      <thead>
        <tr>
          <th>ID Pinjam</th>
          <th>Judul Buku</th>
          <th>Tanggal Pinjam</th>
          <th>Durasi (hari)</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $userLoansStmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $row['id_pinjam'] ?></td>
          <td><?= htmlspecialchars($row['judul']) ?></td>
          <td><?= $row['tanggal_pinjam'] ?></td>
          <td><?= $row['durasi_pinjam'] ?></td>
          <td>
            <a class="button danger" href="kembalikan.php?id_pinjam=<?= $row['id_pinjam'] ?>">Kembalikan</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- 🧾 DAFTAR SEMUA PEMINJAMAN (ADMIN/KOMITE) -->
  <?php if($allLoansStmt): ?>
  <div class="card">
    <h2>🧾 Daftar Semua Peminjaman</h2>
    <table class="table">
      <thead>
        <tr>
          <th>ID Pinjam</th>
          <th>Judul</th>
          <th>Peminjam</th>
          <th>Email</th>
          <th>Telepon</th>
          <th>Tanggal</th>
          <th>Durasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p = $allLoansStmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $p['id_pinjam'] ?></td>
          <td><?= htmlspecialchars($p['judul']) ?></td>
          <td><?= htmlspecialchars($p['nama_depan'].' '.$p['nama_belakang']) ?></td>
          <td><?= htmlspecialchars($p['email']) ?></td>
          <td><?= htmlspecialchars($p['nomor_telpon']) ?></td>
          <td><?= $p['tanggal_pinjam'] ?></td>
          <td><?= $p['durasi_pinjam'] ?> hari</td>
          <td><a class="button danger" href="kembalikan.php?id_pinjam=<?= $p['id_pinjam'] ?>">Kembalikan</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
