<?php
class Pengunjung {
    protected $nama;
    protected $email;
    protected $id_akun;
    protected $conn;

    public function __construct($db, $id_akun, $nama, $email) {
        $this->conn = $db;
        $this->id_akun = $id_akun;
        $this->nama = $nama;
        $this->email = $email;
    }

    public function getNama() { return $this->nama; }
    public function getEmail() { return $this->email; }
    public function getIdAkun() { return $this->id_akun; }

    public function logKunjungan($aktivitas = 'login', $detail = '') {
        // Validasi aktivitas yang diperbolehkan
        $allowed_activities = ['meminjam', 'mengembalikkan', 'login', 'logout'];
        if (!in_array($aktivitas, $allowed_activities)) {
            throw new Exception("Aktivitas tidak valid. Aktivitas yang diperbolehkan: " . implode(", ", $allowed_activities));
        }

        $query = "INSERT INTO log_kunjungan (id_akun, waktu_kunjungan, aktivitas, detail) 
                 VALUES (:id_akun, NOW(), :aktivitas, :detail)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_akun", $this->id_akun);
        $stmt->bindParam(":aktivitas", $aktivitas);
        $stmt->bindParam(":detail", $detail);
        return $stmt->execute();
    }

    public static function getLogKunjungan($db, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $countQuery = "SELECT COUNT(*) as total FROM log_kunjungan";
        $countStmt = $db->query($countQuery);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $query = "SELECT lk.id_log, lk.waktu_kunjungan, lk.aktivitas, lk.detail,
                        a.nama_depan, a.nama_belakang, a.email, a.role,
                        CASE 
                            WHEN a.role = 'Biasa' THEN 'Pengunjung Biasa'
                            WHEN a.role = 'admin' THEN 'Administrator'
                            WHEN a.role = 'komite' THEN 'Komite Perpustakaan'
                            ELSE 'Lainnya'
                        END as status
                 FROM log_kunjungan lk
                 JOIN akun a ON lk.id_akun = a.id_akun
                 ORDER BY lk.waktu_kunjungan DESC, lk.id_log DESC
                 LIMIT :offset, :perPage";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $totalRecords,
            'pages' => ceil($totalRecords / $perPage),
            'currentPage' => $page
        ];
    }
}
?>
