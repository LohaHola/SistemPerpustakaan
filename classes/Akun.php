<?php
class Akun {
    private $conn;
    private $table = "akun";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        $query = "INSERT INTO {$this->table} 
                  (username, nama_depan, nama_belakang, email, password, nomor_telpon, role)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $data['username'],
            $data['nama_depan'],
            $data['nama_belakang'],
            $data['email'],
            $hash,
            $data['nomor_telpon'],
            $data['role']
        ]);
    }

    public function login($input, $password) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE email = :input OR username = :input 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':input' => $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;
        $hash = $user['password'];

        if (password_verify($password, $hash) || $hash === md5($password)) {
            if ($hash === md5($password)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $u = $this->conn->prepare("UPDATE {$this->table} SET password=? WHERE id_akun=?");
                $u->execute([$newHash, $user['id_akun']]);
            }
            return $user;
        }
        return false;
    }

    public function updateProfile() {
        $query = "UPDATE {$this->table} 
                  SET nama_depan = ?, nama_belakang = ?, email = ?, nomor_telpon = ? 
                  WHERE id_akun = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $this->nama_depan,
            $this->nama_belakang,
            $this->email,
            $this->nomor_telpon,
            $this->id_akun
        ]);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id_akun ASC");
        $stmt->execute();
        return $stmt;
    }

    public function updateRole($id, $role) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET role=? WHERE id_akun=?");
        return $stmt->execute([$role, $id]);
    }
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_akun=?");
        return $stmt->execute([$id]);
    }
}
?>
