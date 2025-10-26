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

$stmt = $db->prepare("SELECT id_buku FROM peminjaman WHERE id_pinjam=?");
$stmt->execute([$id_pinjam]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $peminjaman->kembalikan($id_pinjam, $row['id_buku']);
}
header("Location: home.php");
exit;
?>
