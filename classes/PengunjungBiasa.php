<?php
// PengunjungBiasa.php
require_once "Pengunjung.php";
class PengunjungBiasa extends Pengunjung {
    private $umur;
    
    public function pinjamBuku($judul, $durasi) {
        $detail = "Buku: " . $judul . " selama " . $durasi . " hari";
        $this->logKunjungan('meminjam', $detail);
    }
    
    public function kembalikanBuku($judul, $durasi_aktual) {
        $detail = "Buku: " . $judul . " setelah " . $durasi_aktual . " hari";
        $this->logKunjungan('mengembalikkan', $detail);
    }
    
    public function login() {
        $detail = "Login sebagai pengunjung biasa";
        $this->logKunjungan('login', $detail);
    }
    
    public function logout() {
        $detail = "Logout sebagai pengunjung biasa";
        $this->logKunjungan('logout', $detail);
    }
    
    public function daftar() {
        $this->logKunjungan('registrasi', 'User mendaftarkan akun baru');
    }
}
?>