<?php
// scripts/cron_faltantes_automatico.php
// Script que se ejecuta automáticamente cada noche para guardar faltantes del día anterior
// Puede ser llamado por Windows Task Scheduler o cron

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/AsistenciaModel.php';

date_default_timezone_set('America/Lima');

// Argumentos opcionales
$fecha = $argv[1] ?? date('Y-m-d', strtotime('-1 day')); // Por defecto: ayer
$modo = $argv[2] ?? 'guardar'; // guardar, ver, test

if($modo === 'test'){
    echo "🧪 MODO TEST: Mostrando faltantes sin guardar\n\n";
    
    try{
        $model = new AsistenciaModel();
        $faltantes = $model->getFaltantesByDate($fecha);
        
        echo "📅 Fecha: $fecha\n";
        echo "👥 Total empleados sin marcación: " . count($faltantes) . "\n\n";
        
        foreach($faltantes as $emp){
            $tipos = implode(', ', array_keys($emp['faltantes']));
            echo "  • [{$emp['dni']}] {$emp['nombres']} {$emp['apellidos']}\n";
            echo "    Falta: $tipos\n\n";
        }
    } catch(Exception $e){
        fwrite(STDERR, "❌ Error: " . $e->getMessage() . "\n");
        exit(1);
    }
} 
else if($modo === 'ver'){
    echo "📋 MODO VER: Solo mostrando estadísticas\n\n";
    
    try{
        $model = new AsistenciaModel();
        $faltantes = $model->getFaltantesByDate($fecha);
        
        echo "📅 Fecha consultada: $fecha\n";
        echo "👥 Total sin marcación: " . count($faltantes) . "\n";
        
        $sinEntrada = count(array_filter($faltantes, fn($e) => isset($e['faltantes']['entrada'])));
        $sinSalida = count(array_filter($faltantes, fn($e) => isset($e['faltantes']['salida'])));
        
        echo "  - Sin entrada: $sinEntrada\n";
        echo "  - Sin salida: $sinSalida\n";
    } catch(Exception $e){
        fwrite(STDERR, "❌ Error: " . $e->getMessage() . "\n");
        exit(1);
    }
}
else {
    // MODO GUARDAR (por defecto)
    echo "🔄 Guardando faltantes automáticamente...\n\n";
    echo "📅 Fecha: $fecha\n";
    echo "⏳ Procesando...\n\n";
    
    try{
        $model = new AsistenciaModel();
        $guardados = $model->guardarFaltantesPorFecha($fecha);
        
        echo "✅ Completado!\n";
        echo "📊 Registros de falta guardados: $guardados\n";
        echo "⏰ Timestamp: " . date('Y-m-d H:i:s') . "\n";
        
        // Log en archivo
        $logFile = __DIR__ . '/log_faltantes_cron.txt';
        $mensaje = "[".date('Y-m-d H:i:s')."] Guardados $guardados registros para $fecha\n";
        file_put_contents($logFile, $mensaje, FILE_APPEND);
        
        exit(0); // Éxito
    } catch(Exception $e){
        echo "❌ Error: " . $e->getMessage() . "\n";
        
        // Log error
        $logFile = __DIR__ . '/log_faltantes_cron.txt';
        $mensaje = "[".date('Y-m-d H:i:s')."] ERROR: " . $e->getMessage() . "\n";
        file_put_contents($logFile, $mensaje, FILE_APPEND);
        
        exit(1); // Error
    }
}
