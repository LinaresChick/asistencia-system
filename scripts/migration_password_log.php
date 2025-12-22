<?php
// scripts/migration_password_log.php
// Crear tabla admin_password_log para guardar contraseñas asignadas recientemente

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->pdo;

    // Verificar si la tabla ya existe
    $checkTable = $db->query("SHOW TABLES LIKE 'admin_password_log'");
    if($checkTable->rowCount() > 0){
        echo "✅ Tabla admin_password_log ya existe.\n";
        exit(0);
    }

    // Crear tabla
    $sql = "
        CREATE TABLE admin_password_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            empleado_id INT NOT NULL,
            password_plain TEXT NOT NULL COMMENT 'Contraseña en texto plano (temporal)',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
            FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
            INDEX idx_admin_id (admin_id),
            INDEX idx_empleado_id (empleado_id),
            INDEX idx_created_at (created_at)
        )
    ";

    $db->exec($sql);
    echo "✅ Tabla admin_password_log creada exitosamente.\n";

} catch(Exception $e){
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
