<?php
class Peminjaman {
    private $conn;
    private $table = "peminjaman";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function pinjam($id_akun, $id_buku, $durasi) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table}
            (id_akun, id_buku, tanggal_pinjam, durasi_pinjam)
            VALUES (?, ?, NOW(), ?)");
        return $stmt->execute([$id_akun, $id_buku, $durasi]);
    }

    public function kembalikan($id_pinjam, $id_buku) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_pinjam=?");
        $stmt->execute([$id_pinjam]);
        $buku = new Buku($this->conn);
        $buku->tambahStok($id_buku);
        return true;
    }

    public function lihatPeminjaman($id_akun) {
        $query = "SELECT p.id_pinjam, b.judul, p.tanggal_pinjam, p.durasi_pinjam, b.id_buku
                  FROM {$this->table} p
                  JOIN buku b ON p.id_buku = b.id_buku
                  WHERE p.id_akun = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_akun]);
        return $stmt;
    }
}
?>
