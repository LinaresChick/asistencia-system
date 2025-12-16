<?php
// Forzar zona horaria de la aplicación a America/Lima (Perú)
date_default_timezone_set('America/Lima');
// core/Controller.php
class Controller {
    protected function view($path, $data = []){
        extract($data);
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/' . $path . '.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function json($data){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url){
        header("Location: {$url}");
        exit;
    }

    protected function generateCsrf(){
        if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf($token){
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
