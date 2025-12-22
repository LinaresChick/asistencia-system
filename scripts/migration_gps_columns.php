<?php
// scripts/migration_gps_columns.php
// Verificar y agregar columnas GPS a la tabla asistencias

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->pdo;

    // Verificar si las columnas ya existen
    $checkColumns = $db->query("SHOW COLUMNS FROM asistencias LIKE 'lat'");
    if($checkColumns->rowCount() > 0){
        echo "✅ Columnas GPS ya existen en la tabla asistencias.\n";
        exit(0);
    }

    // Agregar columnas GPS
    $sql = "ALTER TABLE asistencias ADD COLUMN lat DECIMAL(10,7) DEFAULT NULL AFTER timestamp_reg, ADD COLUMN lng DECIMAL(10,7) DEFAULT NULL AFTER lat";
    $db->exec($sql);
    echo "✅ Columnas GPS (lat, lng) agregadas exitosamente a la tabla asistencias.\n";

} catch(Exception $e){
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
