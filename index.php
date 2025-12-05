<?php
// index.php
session_start();
date_default_timezone_set('America/Lima');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';

// Autoloader simple para controllers y models
spl_autoload_register(function($class){
    $paths = [__DIR__ . '/controllers/', __DIR__ . '/models/'];
    foreach($paths as $p){
        $file = $p . $class . '.php';
        if(file_exists($file)) require_once $file;
    }
});

$route = $_GET['r'] ?? 'admin/login';
[$controllerName, $action] = array_pad(explode('/', $route, 2), 2, '');
$controllerName = $controllerName ?: 'admin';
$action = $action ?: 'login';

$controllerClass = ucfirst($controllerName) . 'Controller';
if(!class_exists($controllerClass)){
    header("HTTP/1.0 404 Not Found");
    echo "Controlador no encontrado.";
    exit;
}

$controller = new $controllerClass();
if(!method_exists($controller, $action)){
    header("HTTP/1.0 404 Not Found");
    echo "Acción no encontrada.";
    exit;
}

$controller->{$action}();

// Autoloader simple para controllers y models
spl_autoload_register(function($class){
    $paths = [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/',
        __DIR__ . '/core/'
    ];
    foreach($paths as $p){
        $file = $p . $class . '.php';
        if(file_exists($file)){
            require_once $file;
            return;
        }
    }
});

