<?php
// controllers/EmpleadoController.php
class EmpleadoController extends Controller {
    private $empModel;
    private $asiModel;

    public function __construct(){
        $this->empModel = new EmpleadoModel();
        $this->asiModel = new AsistenciaModel();
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $dni = $_POST['dni'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $empleado = $this->empModel->login($dni, $password);
            if($empleado){
                $_SESSION['empleado_id'] = $empleado['id'];
                $_SESSION['empleado_dni'] = $empleado['dni'];
                $_SESSION['empleado_nombre'] = $empleado['nombres'];
                $this->redirect('?r=empleado/dashboard');
            } else {
                $error = "DNI o contraseña inválidos.";
                $this->view('empleado/login', ['error'=>$error, 'csrf'=>$this->generateCsrf()]);
            }
            return;
        }
        $this->view('empleado/login', ['csrf'=>$this->generateCsrf()]);
    }

    public function logout(){
        session_unset();
        session_destroy();
        session_start();
        $this->redirect('?r=empleado/login');
    }

    public function dashboard(){
        $this->ensureAuth();
        
        $empleado_id = $_SESSION['empleado_id'];
        $empleado = $this->empModel->findById($empleado_id);
        
        // Rango de fechas (GET params)
        $startDate = $_GET['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['endDate'] ?? date('Y-m-d');
        
        // Validar fechas
        $d1 = \DateTime::createFromFormat('Y-m-d', $startDate);
        $d2 = \DateTime::createFromFormat('Y-m-d', $endDate);
        if(!($d1 && $d2 && $d1->format('Y-m-d') === $startDate && $d2->format('Y-m-d') === $endDate)){
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
        }
        
        // Obtener historial y estadísticas
        $historial = $this->empModel->getHistorialAsistencia($empleado_id, $startDate, $endDate);
        $estadisticas = $this->empModel->getEstadisticas($empleado_id, $startDate, $endDate);
        
        $this->view('empleado/dashboard', [
            'empleado' => $empleado,
            'historial' => $historial,
            'estadisticas' => $estadisticas,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function ficha(){
        $this->ensureAuth();
        $empleado_id = $_SESSION['empleado_id'];
        $empleado = $this->empModel->findById($empleado_id);
        
        $this->view('empleado/ficha', ['empleado'=>$empleado]);
    }

    // Endpoint AJAX para obtener historial filtrado
    public function historial_ajax(){
        header('Content-Type: application/json; charset=utf-8');
        
        $this->ensureAuth();
        
        $empleado_id = $_SESSION['empleado_id'];
        $startDate = $_GET['startDate'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['endDate'] ?? date('Y-m-d');
        $filtroEstado = $_GET['estado'] ?? null; // 'falta', 'tardanza', 'normal'
        $filtroTipo = $_GET['tipo'] ?? null; // 'entrada', 'salida', etc
        
        $historial = $this->empModel->getHistorialAsistencia($empleado_id, $startDate, $endDate);
        
        // Filtrar por estado
        if($filtroEstado){
            $historial = array_filter($historial, function($h) use ($filtroEstado){
                return $h['estado'] === $filtroEstado;
            });
        }
        
        // Filtrar por tipo
        if($filtroTipo){
            $historial = array_filter($historial, function($h) use ($filtroTipo){
                return $h['tipo'] === $filtroTipo;
            });
        }
        
        echo json_encode(['success'=>true, 'data'=>array_values($historial)]);
        exit;
    }

    private function ensureAuth(){
        if(empty($_SESSION['empleado_id'])){
            $this->redirect('?r=empleado/login');
        }
    }
}
