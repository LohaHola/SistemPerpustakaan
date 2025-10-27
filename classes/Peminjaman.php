<?php
class Peminjaman {
    private $conn;
    private $table = "peminjaman";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cekPeminjamanBukuSama($id_akun, $id_buku) {
        // Check whether the same book (by id) is currently loaned by this user
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE id_akun = ? AND id_buku = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_akun, $id_buku]);
        return $stmt->fetchColumn() > 0;
    }

    public function cekStokBuku($id_buku) {
        $query = "SELECT stok FROM buku WHERE id_buku = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_buku]);
        return $stmt->fetchColumn();
    }

    public function pinjam($id_akun, $id_buku, $durasi) {
        // Cek stok buku
        $stok = $this->cekStokBuku($id_buku);
        if ($stok <= 0) {
            throw new Exception("Stok buku tidak tersedia");
        }

        // Cek apakah user sudah meminjam buku yang sama (by id)
        if ($this->cekPeminjamanBukuSama($id_akun, $id_buku)) {
            throw new Exception("Anda sudah meminjam buku ini.");
        }

        // Gunakan transaction agar insert + kurangi stok atomik
        try {
            $this->conn->beginTransaction();

            // Insert peminjaman
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (id_akun, id_buku, tanggal_pinjam, durasi_pinjam) VALUES (?, ?, NOW(), ?)");
            $ok = $stmt->execute([$id_akun, $id_buku, $durasi]);
            if (!$ok) {
                throw new Exception("Gagal menyimpan data peminjaman");
            }

            // Kurangi stok buku (gunakan method Buku untuk business logic)
            $buku = new Buku($this->conn);
            if (!$buku->kurangiStok($id_buku)) {
                // jika update stok gagal, rollback
                throw new Exception("Gagal mengurangi stok buku");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
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
