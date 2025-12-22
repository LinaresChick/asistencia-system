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

    // Login: buscar empleado por DNI y verificar contraseña
    public function login($dni, $password){
        $empleado = $this->findByDNI($dni);
        if(!$empleado) return null;
        
        // Verificar que tenga una contraseña establecida
        if(empty($empleado['password_hash'])) return null;
        
        if(password_verify($password, $empleado['password_hash'])){
            return $empleado;
        }
        return null;
    }

    // Establecer contraseña para un empleado
    public function setPassword($empleado_id, $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE empleados SET password_hash = :hash WHERE id = :id");
        return $stmt->execute([':hash'=>$hash, ':id'=>$empleado_id]);
    }

    // Obtener historial de asistencia de un empleado
    public function getHistorialAsistencia($empleado_id, $startDate = null, $endDate = null){
        if(!$startDate) $startDate = date('Y-m-d', strtotime('-30 days'));
        if(!$endDate) $endDate = date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT a.* FROM asistencias a
            WHERE a.empleado_id = :empleado_id AND a.fecha BETWEEN :start AND :end
            ORDER BY a.fecha DESC, a.hora DESC
        ");
        $stmt->execute([':empleado_id'=>$empleado_id, ':start'=>$startDate, ':end'=>$endDate]);
        return $stmt->fetchAll();
    }

    // Contar faltas/tardanzas/etc de un empleado
    public function getEstadisticas($empleado_id, $startDate = null, $endDate = null){
        if(!$startDate) $startDate = date('Y-m-d', strtotime('-30 days'));
        if(!$endDate) $endDate = date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado='falta' THEN 1 ELSE 0 END) as faltas,
                SUM(CASE WHEN estado='tardanza' THEN 1 ELSE 0 END) as tardanzas,
                SUM(CASE WHEN estado='normal' THEN 1 ELSE 0 END) as normales,
                SUM(CASE WHEN tipo='entrada' AND estado IS NULL THEN 1 ELSE 0 END) as entradas_ok,
                SUM(CASE WHEN tipo='salida' THEN 1 ELSE 0 END) as salidas
            FROM asistencias
            WHERE empleado_id = :empleado_id AND fecha BETWEEN :start AND :end AND tipo='entrada'
        ");
        $stmt->execute([':empleado_id'=>$empleado_id, ':start'=>$startDate, ':end'=>$endDate]);
        return $stmt->fetch();
    }
}
