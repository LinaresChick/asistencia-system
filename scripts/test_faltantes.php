<?php
// scripts/test_faltantes.php
// Uso: php scripts/test_faltantes.php [fecha]
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
    $faltantes = $model->getFaltantesByDate($fecha);
    
    // Separar por tipo de faltante
    $soloEntrada = [];
    $soloSalida = [];
    $sinRefrigerios = [];
    $completos = [];
    
    foreach($faltantes as $emp){
        $tipos = array_keys($emp['faltantes']);
        
        if(in_array('entrada', $tipos) && !in_array('salida', $tipos)){
            $soloEntrada[] = $emp;
        } elseif(in_array('salida', $tipos) && !in_array('entrada', $tipos)){
            $soloSalida[] = $emp;
        } elseif(in_array('entrada', $tipos) && in_array('salida', $tipos) && 
                 !array_diff(array_keys($emp['faltantes']), ['entrada', 'salida'])){
            $completos[] = $emp;
        } else {
            $sinRefrigerios[] = $emp;
        }
    }
    
    // Estadísticas
    $total = count($faltantes);
    $sinEntrada = count(array_filter($faltantes, function($e){ return isset($e['faltantes']['entrada']); }));
    $sinSalida = count(array_filter($faltantes, function($e){ return isset($e['faltantes']['salida']); }));
    
    echo "=== REPORTE DE FALTANTES - $fecha ===\n\n";
    echo "Total empleados SIN MARCAR HOY: $total\n";
    echo "  - Sin entrada: $sinEntrada\n";
    echo "  - Sin salida: $sinSalida\n\n";
    
    if(!empty($completos)){
        echo "--- SIN ENTRADA NI SALIDA (".count($completos).") ---\n";
        foreach($completos as $e){
            echo "  [{$e['dni']}] {$e['nombres']} {$e['apellidos']}\n";
        }
        echo "\n";
    }
    
    if(!empty($soloEntrada)){
        echo "--- SIN ENTRADA SOLAMENTE (".count($soloEntrada).") ---\n";
        foreach($soloEntrada as $e){
            echo "  [{$e['dni']}] {$e['nombres']} {$e['apellidos']}\n";
        }
        echo "\n";
    }
    
    if(!empty($soloSalida)){
        echo "--- SIN SALIDA SOLAMENTE (".count($soloSalida).") ---\n";
        foreach($soloSalida as $e){
            echo "  [{$e['dni']}] {$e['nombres']} {$e['apellidos']}\n";
        }
        echo "\n";
    }
    
    if(!empty($sinRefrigerios)){
        echo "--- CON PROBLEMAS EN REFRIGERIOS (".count($sinRefrigerios).") ---\n";
        foreach($sinRefrigerios as $e){
            $ref = implode(', ', array_keys($e['faltantes']));
            echo "  [{$e['dni']}] {$e['nombres']} {$e['apellidos']} - Falta: $ref\n";
        }
        echo "\n";
    }
    
    echo "\n(JSON completo a continuación)\n";
    echo json_encode(['success'=>true,'fecha'=>$fecha,'stats'=>['total'=>$total,'sin_entrada'=>$sinEntrada,'sin_salida'=>$sinSalida],'data'=>$faltantes], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
