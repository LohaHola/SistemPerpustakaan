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
$buku = new Buku($db);
$peminjaman = new Peminjaman($db);

$message = "";

// Ambil semua buku yang masih tersedia
$stmt = $db->prepare("SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC");
$stmt->execute();
$buku_tersedia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_buku = $_POST['id_buku'] ?? '';
    $durasi_pinjam = $_POST['durasi_pinjam'] ?? '';

    if (!$id_buku || !$durasi_pinjam) {
        $message = "<span style='color:red;'>⚠️ Harap isi semua kolom.</span>";
    } else {
        try {
            // Get book info for logging
            $bukuInfo = $buku->getBukuById($id_buku);
            
      // Proses peminjaman dan logging
      if ($peminjaman->pinjam($user['id_akun'], $id_buku, $durasi_pinjam)) {
        // Log the borrowing activity
        require_once "classes/Pengunjung.php";
        $pengunjung = new Pengunjung($db, $user['id_akun'], $user['nama_depan'] . ' ' . $user['nama_belakang'], $user['email']);
        $pengunjung->logKunjungan('meminjam', "Buku: " . $bukuInfo['judul'] . " selama " . $durasi_pinjam . " hari");
        // Success message shown to user (includes book title)
        $message = "<span style='color:green;'>✅ Anda berhasil meminjam buku: " . htmlspecialchars($bukuInfo['judul']) . "</span>";
      }
      // peminjaman sudah diproses dan di-log oleh kode di atas
        } catch (Exception $e) {
            $message = "<span style='color:red;'>❌ " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pinjam Buku - Sistem Perpustakaan</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'layout/header.php'; ?>

<div class="container" style="max-width:600px;">
  <div class="card">
    <h2 style="text-align:center;">📚 Form Peminjaman Buku</h2>

    <?php if($message): ?>
      <div style="text-align:center; margin-bottom:10px;">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="form-pinjam">
      <label for="id_buku">Pilih Buku:</label>
      <select name="id_buku" id="id_buku" required>
        <option value="">-- Pilih Buku --</option>
        <?php foreach ($buku_tersedia as $b): ?>
          <option value="<?= $b['id_buku'] ?>">
            <?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <label for="durasi_pinjam">Durasi Peminjaman (hari):</label>
      <input type="number" name="durasi_pinjam" id="durasi_pinjam" min="1" max="30" placeholder="Contoh: 7" required>

      <button type="submit" class="button">Pinjam Sekarang</button>

      <p style="text-align:center; margin-top:10px;">
        <a href="home.php">← Kembali ke Beranda</a>
      </p>
    </form>
  </div>
</div>

<?php include 'layout/footer.php'; ?>
</body>
</html>
