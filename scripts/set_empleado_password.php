<?php
// scripts/set_empleado_password.php
// Uso: php scripts/set_empleado_password.php [dni] [password]
// Ejemplo: php scripts/set_empleado_password.php 74859612 miContraseña123

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';

if(empty($argv[1]) || empty($argv[2])){
    echo "❌ Uso: php scripts/set_empleado_password.php [dni] [password]\n";
    echo "Ejemplo: php scripts/set_empleado_password.php 74859612 miContraseña123\n";
    exit(1);
}

$dni = $argv[1];
$password = $argv[2];

try{
    $model = new EmpleadoModel();
    $empleado = $model->findByDNI($dni);
    
    if(!$empleado){
        echo "❌ Empleado con DNI $dni no encontrado\n";
        exit(1);
    }
    
    if($model->setPassword($empleado['id'], $password)){
        echo "✅ Contraseña asignada correctamente!\n";
        echo "   Empleado: {$empleado['nombres']} {$empleado['apellidos']}\n";
        echo "   DNI: {$empleado['dni']}\n";
        echo "   ID: {$empleado['id']}\n";
        exit(0);
    } else {
        echo "❌ Error al asignar la contraseña\n";
        exit(1);
    }
} catch(Exception $e){
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
