<?php
// controllers/AdminController.php
class AdminController extends Controller {
    private $model;

    public function __construct(){
        $this->model = new AdminModel();
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $admin = $this->model->getByUsername($username);
            if($admin && password_verify($password, $admin['password_hash'])){
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['nombre'] ?? $admin['username'];
                $this->redirect('?r=admin/dashboard');
            } else {
                $error = "Credenciales inválidas.";
                $this->view('admin/login', ['error'=>$error, 'csrf'=>$this->generateCsrf()]);
            }
            return;
        }
        $this->view('admin/login', ['csrf'=>$this->generateCsrf()]);
    }

    public function logout(){
        session_unset();
        session_destroy();
        session_start();
        $this->redirect('?r=admin/login');
    }

    public function dashboard(){
        $this->ensureAuth();
        $asistenciaModel = new AsistenciaModel();
        $hoy = date('Y-m-d');
        $registros = $asistenciaModel->getByDate($hoy);
        $this->view('admin/dashboard', ['registros'=>$registros]);
    }

    private function ensureAuth(){
        if(empty($_SESSION['admin_id'])){
            $this->redirect('?r=admin/login');
        }
    }

    // List empleados
    public function empleados(){
        $this->ensureAuth();
        $empModel = new EmpleadoModel();
        $empleados = $empModel->findAll();
        $this->view('admin/empleados', ['empleados'=>$empleados, 'csrf'=>$this->generateCsrf()]);
    }

    // Create empleado (POST)
    public function empleado_create(){
        $this->ensureAuth();
        if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?r=admin/empleados');
        if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/empleados');
        $m = new EmpleadoModel();
        $m->create($_POST);
        $this->redirect('?r=admin/empleados');
    }

    public function empleado_delete(){
        $this->ensureAuth();
        if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?r=admin/empleados');
        if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/empleados');
        $m = new EmpleadoModel();
        $m->delete($_POST['id']);
        $this->redirect('?r=admin/empleados');
    }
    public function asistencias(){
    $this->ensureAuth();
    $a = new AsistenciaModel();

    // FORMULARIO ENVIADO
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $anio = $_POST['anio'] ?? date('Y');
        $mes_ini = $_POST['mes_inicio'] ?? date('m');
        $dia_ini = $_POST['dia_inicio'] ?? "01";

        $mes_fin = $_POST['mes_fin'] ?? date('m');
        $dia_fin = $_POST['dia_fin'] ?? date('t');
        // soportar año final separado en el formulario
        $anio_fin = $_POST['anio_fin'] ?? $anio;

        // RANGO HORARIO PRINCIPAL
        $hora_inicio = $_POST['hora_inicio'] ?? "00:00:00";
        $hora_fin    = $_POST['hora_fin'] ?? "23:59:59";
        $tolerancia  = intval($_POST['tolerancia'] ?? 0);

        // Reglas de refrigerios (si vienen del formulario)
        $ref1_inicio = $_POST['ref1_inicio'] ?? null;
        $ref1_fin    = $_POST['ref1_fin'] ?? null;
        $ref2_inicio = $_POST['ref2_inicio'] ?? null;
        $ref2_fin    = $_POST['ref2_fin'] ?? null;
        $ref3_inicio = $_POST['ref3_inicio'] ?? null;
        $ref3_fin    = $_POST['ref3_fin'] ?? null;

        // Construcción completa de fechas
        $startDate = "$anio-$mes_ini-$dia_ini";
        $endDate   = "$anio_fin-$mes_fin-$dia_fin";

        // Rango con tolerancia aplicado SOLO al filtro de horas del día
        $hora_inicio_real = date("H:i:s", strtotime("$hora_inicio -$tolerancia minutes"));
        $hora_fin_real    = date("H:i:s", strtotime("$hora_fin +$tolerancia minutes"));

        // Guardar reglas en la tabla 'horarios' para que el sistema las use
        $a->saveRules([
            'entrada' => $hora_inicio,
            'salida' => $hora_fin,
            'ref1_inicio' => $ref1_inicio,
            'ref1_fin' => $ref1_fin,
            'ref2_inicio' => $ref2_inicio,
            'ref2_fin' => $ref2_fin,
            'ref3_inicio' => $ref3_inicio,
            'ref3_fin' => $ref3_fin,
        ]);

        // Consulta a la BD con el rango horario
        $registros = $a->getByRangeWithHours(
            $startDate,
            $endDate,
            $hora_inicio_real,
            $hora_fin_real
        );

        // Enviamos todo a la vista
        $this->view('admin/asistencias', [
            'registros' => $registros,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'tolerancia' => $tolerancia,

            // refrescar valores de refrigerios para la vista
            'ref1_inicio' => $ref1_inicio,
            'ref1_fin' => $ref1_fin,
            'ref2_inicio' => $ref2_inicio,
            'ref2_fin' => $ref2_fin,
            'ref3_inicio' => $ref3_inicio,
            'ref3_fin' => $ref3_fin,
        ]);
        return;
    }

    // por defecto
    $registros = $a->getAll();
    $this->view('admin/asistencias', ['registros'=>$registros]);
}



}
