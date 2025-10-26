<?php
session_start();
require_once "config/database.php";
require_once "classes/Buku.php";

if (!isset($_SESSION['user'])) { header("Location: index.php"); exit; }
$user = $_SESSION['user'];
$db = (new Database())->getConnection();
$bukuModel = new Buku($db);
$message = "";

// Ambil data untuk edit jika ada
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $editData = $bukuModel->getBukuById($edit_id);
}

// Proses tambah/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_buku = !empty($_POST['id_buku']) ? (int)$_POST['id_buku'] : null;
    $judul = trim($_POST['judul']);
    $penulis = trim($_POST['penulis']);
    $stok = (int)$_POST['stok'];

    if ($id_buku) {
        // edit (admin & komite allowed)
        if ($user['role'] === 'admin' || $user['role'] === 'komite') {
            $bukuModel->updateBuku($id_buku, $judul, $penulis, $stok);
            $message = "Buku diperbarui.";
        } else {
            $message = "Akses ditolak.";
        }
    } else {
        // tambah — hanya admin
        if ($user['role'] === 'admin') {
            if ($bukuModel->tambahBuku($judul, $penulis, $stok)) {
                $message = "Buku ditambahkan.";
            } else {
                $message = "Gagal menambahkan buku.";
            }
        } else {
            $message = "Hanya admin yang dapat menambahkan buku.";
        }
    }
}

// ambil semua buku untuk daftar
$allBooks = $bukuModel->getAllBuku();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Kelola Buku</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'layout/header.php'; ?>
<div class="container">
  <div class="card">
    <h2><?= $editData ? 'Edit Buku' : 'Tambah Buku' ?></h2>
    <?php if($message): ?><div class="msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <form method="post" action="update_buku.php">
      <input type="hidden" name="id_buku" value="<?= $editData['id_buku'] ?? '' ?>">
      <label>Judul</label>
      <input type="text" name="judul" required value="<?= htmlspecialchars($editData['judul'] ?? '') ?>">
      <label>Penulis</label>
      <input type="text" name="penulis" required value="<?= htmlspecialchars($editData['penulis'] ?? '') ?>">
      <label>Stok</label>
      <input type="number" name="stok" required value="<?= htmlspecialchars($editData['stok'] ?? 1) ?>">
      <button class="button" type="submit"><?= $editData ? 'Update Buku' : 'Simpan Buku' ?></button>
    </form>
  </div>

  <div class="card">
    <h3>Daftar Buku</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Judul</th><th>Penulis</th><th>Stok</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php while($r = $allBooks->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $r['id_buku'] ?></td>
          <td><?= htmlspecialchars($r['judul']) ?></td>
          <td><?= htmlspecialchars($r['penulis']) ?></td>
          <td><?= $r['stok'] ?></td>
          <td>
            <?php if($user['role']==='admin' || $user['role']==='komite'): ?>
              <a class="button" href="update_buku.php?edit=<?= $r['id_buku'] ?>">Edit</a>
            <?php endif; ?>
            <?php if($user['role']==='admin'): ?>
              &nbsp;<a class="button" href="update_buku.php?delete=<?= $r['id_buku'] ?>" onclick="return confirm('Hapus buku?')">Hapus</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <p style="margin-top:12px;"><a href="home.php">← Kembali ke Beranda</a></p>
</div>
<?php include 'layout/footer.php'; ?>
</body>
</html>
