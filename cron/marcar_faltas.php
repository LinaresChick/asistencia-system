<?php
/**
 * Script CRON para marcar faltas automáticamente
 * Se ejecuta diariamente (idealmente a las 23:59 o 00:00 del siguiente día)
 * Marca como "falta" a empleados sin registro de entrada en el día
 * 
 * SOLO debe ejecutarse desde línea de comandos (cron/Task Scheduler)
 */

// Proteger: solo ejecutar desde CLI (línea de comandos)
if(php_sapi_name() !== 'cli'){
    http_response_code(403);
    echo "Error: Este script solo puede ejecutarse desde línea de comandos (cron).\n";
    exit;
}

// Incluir configuración y modelos
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/AsistenciaModel.php';

// Timezone Lima
date_default_timezone_set('America/Lima');

$hoy = date('Y-m-d');
$lockFile = __DIR__ . "/.lock_$hoy";

// Evitar ejecución duplicada el mismo día
if(file_exists($lockFile)){
    echo "=== Marcación de Faltas - $hoy ===\n";
    echo "⚠️  Ya se ejecutó hoy. Cancelando...\n";
    exit;
}

// Crear candado
file_put_contents($lockFile, time());

// Conectar a BD
$db = new Database();

// Obtener todos los empleados
$empleados = $empModel->findAll();

if(empty($empleados)){
    echo $mensaje . "No hay empleados registrados.\n";
    exit;
}

$faltas_marcadas = 0;
$empleados_con_entrada = 0;

foreach($empleados as $emp){
    $dni = $emp['dni'];
    $nombre = trim($emp['nombres'] . ' ' . $emp['apellidos']);
    
    // Verificar si tiene registro de entrada hoy
    if($asiModel->hasEntradaToday($dni, $hoy)){
        $empleados_con_entrada++;
        $mensaje .= "✓ $nombre ($dni) - Entrada registrada\n";
    } else {
        // Marcar como falta
        $ok = $asiModel->register([
            'empleado_id' => $emp['id'],
            'dni'         => $dni,
            'tipo'        => 'entrada',
            'fecha'       => $hoy,
            'hora'        => '00:00:00',
            'lat'         => null,
            'lng'         => null,
            'ip_origen'   => 'cron',
            'estado'      => 'falta',
            'nota'        => 'Falta registrada automáticamente por CRON'
        ]);
        
        if($ok){
            $faltas_marcadas++;
            $mensaje .= "✗ $nombre ($dni) - FALTA marcada automáticamente\n";
        } else {
            $mensaje .= "! $nombre ($dni) - Error al marcar falta\n";
        }
    }
}

$mensaje .= "\n=== Resumen ===\n";
$mensaje .= "Empleados con entrada: $empleados_con_entrada\n";
$mensaje .= "Faltas marcadas: $faltas_marcadas\n";
$mensaje .= "Total empleados: " . count($empleados) . "\n";

// Log a archivo
$logFile = __DIR__ . '/log_faltas.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $mensaje . "\n\n", FILE_APPEND);

// Salida en terminal
echo $mensaje;
?>
