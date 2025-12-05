<?php
// models/AdminModel.php
class AdminModel extends Model {
    public function getByUsername($username){
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        return $stmt->fetch();
    }

    public function createAdmin($username, $password, $nombre = null){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO admins (username, password_hash, nombre) VALUES (:u,:p,:n)");
        return $stmt->execute([':u'=>$username, ':p'=>$hash, ':n'=>$nombre]);
    }
}
