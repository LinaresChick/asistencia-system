<?php
// scripts/debug_login.php <dni> <password>
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';

if(empty($argv[1]) || empty($argv[2])){
    echo "Uso: php scripts/debug_login.php <dni> <password>\n";
    exit(1);
}
$dni = $argv[1];
$password = $argv[2];

$model = new EmpleadoModel();
$emp = $model->findByDNI($dni);
if(!$emp){
    echo "Empleado con DNI $dni NO encontrado\n";
    var_export($emp);
    exit(1);
}

echo "Empleado encontrado:\n";
echo "  id: " . $emp['id'] . "\n";
echo "  dni (raw): '" . $emp['dni'] . "'\n";
echo "  nombres: " . $emp['nombres'] . "\n";
echo "  password_hash: " . ($emp['password_hash'] ?? 'NULL') . "\n";

$trimmed = trim($dni);
if($emp['dni'] !== $trimmed) echo "Nota: DNI guardado difiere del DNI ingresado (espacios u otros)\n";

if(empty($emp['password_hash'])){
    echo "No hay password_hash establecido para este empleado\n";
} else {
    $ok = password_verify($password, $emp['password_hash']);
    echo "password_verify('$password', hash) => ".($ok?"TRUE":"FALSE")."\n";
}

// Intentar login via método
$login = $model->login($dni, $password);
echo "\nResultado model->login: ";
var_export($login? ['id'=>$login['id'],'dni'=>$login['dni']]:null);
echo "\n";
