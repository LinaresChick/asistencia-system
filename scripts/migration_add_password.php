<?php
// scripts/migration_add_password_to_empleados.php
// Script de migración para añadir columna password_hash

require_once __DIR__ . '/../core/Model.php';

try{
    $db = new Database();
    $conn = $db->getConnection();
    
    // Verificar si la columna ya existe
    $stmt = $conn->query("SHOW COLUMNS FROM empleados LIKE 'password_hash'");
    $exists = $stmt->rowCount() > 0;
    
    if($exists){
        echo "✅ La columna password_hash ya existe en la tabla empleados\n";
    } else {
        // Añadir la columna
        $sql = "ALTER TABLE empleados ADD COLUMN password_hash VARCHAR(255) NULL DEFAULT NULL";
        $conn->exec($sql);
        echo "✅ Columna password_hash añadida a la tabla empleados\n";
    }
    
    exit(0);
} catch(Exception $e){
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
