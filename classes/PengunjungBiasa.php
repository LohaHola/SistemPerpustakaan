<?php
// PengunjungBiasa.php
require_once "Pengunjung.php";
class PengunjungBiasa extends Pengunjung {
    public function pinjamBuku($judul) {
        echo "Pengunjung meminjam buku: " . $judul;
    }
}
?>