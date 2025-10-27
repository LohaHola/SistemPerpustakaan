<?php
session_start();
require_once "config/database.php";
require_once "classes/Pengunjung.php";

// Log logout before destroying session
if (isset($_SESSION['user'])) {
    $db = (new Database())->getConnection();
    $user = $_SESSION['user'];
    
    // Log logout activity
    $pengunjung = new Pengunjung($db, $user['id_akun'], 
        $user['nama_depan'] . ' ' . $user['nama_belakang'], 
        $user['email']);
    $pengunjung->logKunjungan('logout', 'Logout dari sistem');
}

session_unset();
session_destroy();
header("Location: index.php");
exit;
?>
