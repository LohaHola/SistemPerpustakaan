<?php
class Buku {
    private $conn;
    private $table = "buku";

    public $id_buku;
    public $judul;
    public $penulis;
    public $stok;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBuku() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY id_buku DESC");
    }

    public function getBukuById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_buku = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function tambahBuku($judul, $penulis, $stok) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (judul, penulis, stok) VALUES (?, ?, ?)");
        return $stmt->execute([$judul, $penulis, $stok]);
    }

    public function updateBuku($id, $judul, $penulis, $stok) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET judul=?, penulis=?, stok=? WHERE id_buku=?");
        return $stmt->execute([$judul, $penulis, $stok, $id]);
    }

    public function kurangiStok($id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stok = stok - 1 WHERE id_buku = ? AND stok > 0");
        return $stmt->execute([$id]);
    }

    public function tambahStok($id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stok = stok + 1 WHERE id_buku = ?");
        return $stmt->execute([$id]);
    }
}
?>
