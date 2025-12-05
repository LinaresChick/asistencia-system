<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/core/Model.php';
require __DIR__ . '/models/AdminModel.php';

// BORRAR admin anterior si existe
$db = (new Database())->getConnection();
$db->exec("DELETE FROM admins WHERE username = 'admin'");

// CREAR nuevo admin
$model = new AdminModel();
$model->createAdmin('admin', 'admin123', 'Administrador');

echo "<h2>Admin creado correctamente</h2>";
echo "<p>Usuario: <b>admin</b></p>";
echo "<p>Contraseña: <b>admin123</b></p>";
