<?php
// config/database.php
class Database {
    private $host = '127.0.0.1';
    private $db   = 'asistencia_system';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    public $pdo;

    public function __construct(){
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
        // Asegurar que la sesión MySQL use la zona horaria de Lima (UTC-5)
        try{
            // Usamos offset fijo -05:00 porque MySQL puede no tener tablas de zona cargadas
            $this->pdo->exec("SET time_zone = '-05:00'");
        }catch(Exception $e){
            // no fatal: si falla, continuamos sin cambiar time_zone
        }
    }

    public function getConnection(){
        return $this->pdo;
    }
}
