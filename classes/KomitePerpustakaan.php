<?php
// KomitePerpustakaan.php
require_once "Pengunjung.php";
class KomitePerpustakaan extends Pengunjung {
    public function tambahBuku($buku) {
        echo "Komite menambahkan buku baru: " . $buku;
    }
}
?>