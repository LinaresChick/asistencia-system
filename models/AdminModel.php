<?php
// models/AdminModel.php
class AdminModel extends Model {
    public function getByUsername($username){
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        return $stmt->fetch();
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAllAdmins(){
        $stmt = $this->db->query("SELECT id, username, nombre FROM admins ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function createAdmin($username, $password, $nombre = null){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO admins (username, password_hash, nombre) VALUES (:u,:p,:n)");
        return $stmt->execute([':u'=>$username, ':p'=>$hash, ':n'=>$nombre]);
    }

    public function updateAdmin($id, $username = null, $password = null, $nombre = null){
        $admin = $this->getById($id);
        if(!$admin) return false;
        $u = $username ?? $admin['username'];
        $n = $nombre ?? $admin['nombre'];
        $p = $admin['password_hash'];
        if($password){
            $p = password_hash($password, PASSWORD_DEFAULT);
        }
        $stmt = $this->db->prepare("UPDATE admins SET username = :u, password_hash = :p, nombre = :n WHERE id = :id");
        return $stmt->execute([':u'=>$u, ':p'=>$p, ':n'=>$n, ':id'=>$id]);
    }

    public function deleteAdmin($id){
        $stmt = $this->db->prepare("DELETE FROM admins WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
