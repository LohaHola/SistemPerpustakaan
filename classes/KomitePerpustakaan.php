<?php
// KomitePerpustakaan.php
require_once "Pengunjung.php";
class KomitePerpustakaan extends Pengunjung {
    public $tanggalDaftar;

    public function tambahBuku($buku) {
        $detail = "Buku baru ditambahkan: " . $buku;
        $this->logKunjungan('login', $detail); // Using login since it's an administrative action
    }

    public function pinjamBuku($judul, $durasi) {
        $detail = "Buku: " . $judul . " selama " . $durasi . " hari";
        $this->logKunjungan('meminjam', $detail);
    }

    public function kembalikanBuku($judul, $durasi_aktual) {
        $detail = "Buku: " . $judul . " setelah " . $durasi_aktual . " hari";
        $this->logKunjungan('mengembalikkan', $detail);
    }

    public function login() {
        $detail = "Login sebagai komite perpustakaan";
        $this->logKunjungan('login', $detail);
    }

    public function logout() {
        $detail = "Logout sebagai komite perpustakaan";
        $this->logKunjungan('logout', $detail);
    }
}
?>