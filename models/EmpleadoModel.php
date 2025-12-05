<?php
// models/EmpleadoModel.php
class EmpleadoModel extends Model {
    public function create($data){
        $stmt = $this->db->prepare("
            INSERT INTO empleados (dni, nombres, apellidos, edad, cargo)
            VALUES (:dni,:nombres,:apellidos,:edad,:cargo)
        ");
        return $stmt->execute([
            ':dni' => $data['dni'],
            ':nombres' => $data['nombres'],
            ':apellidos' => $data['apellidos'],
            ':edad' => $data['edad'] ?? null,
            ':cargo' => $data['cargo'] ?? null
        ]);
    }

    public function update($id, $data){
        $stmt = $this->db->prepare("
            UPDATE empleados SET dni=:dni, nombres=:nombres, apellidos=:apellidos, edad=:edad, cargo=:cargo WHERE id=:id
        ");
        $data[':id'] = $id;
        return $stmt->execute([
            ':dni'=>$data['dni'],':nombres'=>$data['nombres'],':apellidos'=>$data['apellidos'],
            ':edad'=>$data['edad'] ?? null,':cargo'=>$data['cargo'] ?? null,':id'=>$id
        ]);
    }

    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM empleados WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function findByDNI($dni){
        $stmt = $this->db->prepare("SELECT * FROM empleados WHERE dni = :dni LIMIT 1");
        $stmt->execute([':dni'=>$dni]);
        return $stmt->fetch();
    }

    public function findAll(){
        $stmt = $this->db->query("SELECT * FROM empleados ORDER BY apellidos, nombres");
        return $stmt->fetchAll();
    }

    public function findById($id){
        $stmt = $this->db->prepare("SELECT * FROM empleados WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }
}
