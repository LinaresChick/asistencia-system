<?php
require_once __DIR__ . '/config/database.php';
$db = (new Database())->getConnection();

$username = 'admin'; // cámbialo si usas otro usuario
$stmt = $db->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
$stmt->execute([':u'=>$username]);
$admin = $stmt->fetch();

echo "<pre>Test admin\n";
if(!$admin){
    echo "Fila admin NO encontrada para username='$username'\n";
    echo "Query OK, revisa tabla admins.\n";
    exit;
}

echo "Fila encontrada:\n";
var_export($admin);
echo "\n\n";

$hash = $admin['password_hash'] ?? '';
echo "Hash almacenado: $hash\n";

// Comprobación password_verify para 'password' y 'admin123' y 'tu intento'
$tests = ['password','admin123','password1'];
foreach($tests as $t){
    echo "password_verify('$t', hash) => ";
    var_export(password_verify($t, $hash));
    echo "\n";
}

echo "</pre>";
