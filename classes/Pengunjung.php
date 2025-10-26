<?php
// Pengunjung.php
class Pengunjung {
    protected $nama;
    protected $email;
    protected $id_akun;

    public function __construct($id_akun, $nama, $email) {
        $this->id_akun = $id_akun;
        $this->nama = $nama;
        $this->email = $email;
    }

    public function getNama() { return $this->nama; }
    public function getEmail() { return $this->email; }
    public function getIdAkun() { return $this->id_akun; }
}
?>
