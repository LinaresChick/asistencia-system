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
        // Aceptar fecha desde GET o POST; si no, usar hoy
        $fecha = $_GET['fecha'] ?? $_POST['fecha'] ?? date('Y-m-d');
        // Validar formato Y-m-d
        $d = \DateTime::createFromFormat('Y-m-d', $fecha);
        if(!$d || $d->format('Y-m-d') !== $fecha){
            $fecha = date('Y-m-d');
        }
        $registros = $asistenciaModel->getByDate($fecha);
        $this->view('admin/dashboard', ['registros'=>$registros, 'fecha'=>$fecha]);
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

    // ELIMINAR HORARIO ACTUAL
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_schedule'){
        if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/asistencias');
        $a->deleteSchedule();
        $this->redirect('?r=admin/asistencias');
    }

    // FORMULARIO ENVIADO
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {

        // Rango de fechas: soportar nuevo campo date `startDate`/`endDate' o el antiguo desglosado
        if(!empty($_POST['startDate']) && !empty($_POST['endDate'])){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
        } else {
            $anio = $_POST['anio'] ?? date('Y');
            $mes_ini = $_POST['mes_inicio'] ?? date('m');
            $dia_ini = $_POST['dia_inicio'] ?? "01";

            $mes_fin = $_POST['mes_fin'] ?? date('m');
            $dia_fin = $_POST['dia_fin'] ?? date('t');
            // soportar año final separado en el formulario
            $anio_fin = $_POST['anio_fin'] ?? $anio;

            // Construcción completa de fechas
            $startDate = "$anio-$mes_ini-$dia_ini";
            $endDate   = "$anio_fin-$mes_fin-$dia_fin";
        }

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
        // Vigencia del horario (opcional)
        $vigente_desde = !empty($_POST['vigente_desde']) ? $_POST['vigente_desde'] : null;
        $vigente_hasta = !empty($_POST['vigente_hasta']) ? $_POST['vigente_hasta'] : null;

        

        // Rango con tolerancia aplicado SOLO al filtro de horas del día
        $hora_inicio_real = date("H:i:s", strtotime("$hora_inicio -$tolerancia minutes"));
        $hora_fin_real    = date("H:i:s", strtotime("$hora_fin +$tolerancia minutes"));

        // Vigencia del horario (opcional). Si startDate/endDate vienen, úsalos como vigencia si no se enviaron vigentes explícitos
        $vigente_desde = !empty($_POST['vigente_desde']) ? $_POST['vigente_desde'] : null;
        $vigente_hasta = !empty($_POST['vigente_hasta']) ? $_POST['vigente_hasta'] : null;
        if(empty($vigente_desde) && !empty($startDate)) $vigente_desde = $startDate;
        if(empty($vigente_hasta) && !empty($endDate)) $vigente_hasta = $endDate;

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
            'vigente_desde' => $vigente_desde,
            'vigente_hasta' => $vigente_hasta,
        ]);

        // Consulta a la BD con el rango horario
        $registros = $a->getByRangeWithHours(
            $startDate,
            $endDate,
            $hora_inicio_real,
            $hora_fin_real
        );

        // Obtener horario actual guardado
        $horario_actual = $a->getSchedule();

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
            
            // Horario actual
            'horario_actual' => $horario_actual,
            'csrf' => $this->generateCsrf()
        ]);
        return;
    }

    // por defecto
    $registros = $a->getAll();
    $horario_actual = $a->getSchedule();
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');
    $this->view('admin/asistencias', ['registros'=>$registros, 'horario_actual' => $horario_actual, 'csrf' => $this->generateCsrf(), 'startDate'=>$startDate, 'endDate'=>$endDate]);
}

// Configuración de admins
public function config(){
    $this->ensureAuth();
    $m = new AdminModel();
    $admins = $m->getAllAdmins();
    $this->view('admin/config', ['admins' => $admins, 'csrf' => $this->generateCsrf()]);
}

// Actualizar admin actual
public function admin_update(){
    $this->ensureAuth();
    if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?r=admin/config');
    if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/config');

    $m = new AdminModel();
    $id = $_SESSION['admin_id'] ?? null;
    if(!$id) $this->redirect('?r=admin/config');

    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null; // opcional
    $nombre = $_POST['nombre'] ?? null;

    if(!$username){
        $this->redirect('?r=admin/config');
    }

    $m->updateAdmin($id, $username, $password, $nombre);
    $_SESSION['admin_name'] = $nombre ?? $username;
    $this->redirect('?r=admin/config');
}

// Crear nuevo admin
public function admin_create(){
    $this->ensureAuth();
    if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?r=admin/config');
    if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/config');

    $m = new AdminModel();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nombre = $_POST['nombre'] ?? '';

    if(!$username || !$password){
        $this->redirect('?r=admin/config');
    }

    // Verificar que el username no existe
    if($m->getByUsername($username)){
        $this->redirect('?r=admin/config');
    }

    $m->createAdmin($username, $password, $nombre);
    $this->redirect('?r=admin/config');
}

// Eliminar admin
public function admin_delete(){
    $this->ensureAuth();
    if($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('?r=admin/config');
    if(!$this->verifyCsrf($_POST['csrf'] ?? '')) $this->redirect('?r=admin/config');

    $id = $_POST['id'] ?? null;
    if(!$id) $this->redirect('?r=admin/config');

    // Prevenir eliminación de si mismo
    if($id == $_SESSION['admin_id']){
        $this->redirect('?r=admin/config');
    }

    $m = new AdminModel();
    $m->deleteAdmin($id);
    $this->redirect('?r=admin/config');
}

}
