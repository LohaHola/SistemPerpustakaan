<?php
session_start();
require_once "config/database.php";
require_once "classes/Peminjaman.php";
require_once "classes/Buku.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$id_pinjam = $_GET['id_pinjam'] ?? null;

if (!$id_pinjam) { header("Location: home.php"); exit; }

$db = (new Database())->getConnection();
$peminjaman = new Peminjaman($db);

// Get book info for logging
$stmt = $db->prepare("SELECT p.id_buku, b.judul FROM peminjaman p 
                     JOIN buku b ON p.id_buku = b.id_buku 
                     WHERE p.id_pinjam=?");
$stmt->execute([$id_pinjam]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    if ($peminjaman->kembalikan($id_pinjam, $row['id_buku'])) {
        // Log the return
        require_once "classes/Pengunjung.php";
        $pengunjung = new Pengunjung($db, $_SESSION['user']['id_akun'], 
            $_SESSION['user']['nama_depan'] . ' ' . $_SESSION['user']['nama_belakang'], 
            $_SESSION['user']['email']);
        $pengunjung->logKunjungan('mengembalikkan', "Buku: " . $row['judul']);
    }
}
header("Location: home.php");
exit;
?>
