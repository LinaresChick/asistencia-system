<?php
// scripts/guardar_faltantes_cli.php
// Uso: php scripts/guardar_faltantes_cli.php [fecha]
// fecha: 'hoy' (por defecto), 'mañana' o 'YYYY-MM-DD'

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/AsistenciaModel.php';

date_default_timezone_set('America/Lima');

$fechaArg = $argv[1] ?? 'hoy';
if($fechaArg === 'hoy'){
    $fecha = date('Y-m-d');
} elseif($fechaArg === 'mañana' || $fechaArg === 'manana'){
    $fecha = date('Y-m-d', strtotime('+1 day'));
} else {
    $d = DateTime::createFromFormat('Y-m-d', $fechaArg);
    if(!($d && $d->format('Y-m-d') === $fechaArg)){
        fwrite(STDERR, "Fecha inválida. Usar 'hoy' o 'YYYY-MM-DD'\n");
        exit(1);
    }
    $fecha = $fechaArg;
}

try{
    $model = new AsistenciaModel();
    
    echo "📅 Guardando faltantes para: $fecha\n";
    echo "⏳ Procesando...\n\n";
    
    $guardados = $model->guardarFaltantesPorFecha($fecha);
    
    echo "✅ Operación completada!\n";
    echo "📊 Registros de falta guardados: $guardados\n\n";
    
    // Mostrar los faltantes que se registraron
    $faltantes = $model->getFaltantesByDate($fecha);
    echo "📋 Resumen de faltantes:\n";
    echo "   Total empleados sin marcación: " . count($faltantes) . "\n";
    
} catch(Exception $e){
    fwrite(STDERR, "❌ Error: " . $e->getMessage() . "\n");
    exit(1);
}
