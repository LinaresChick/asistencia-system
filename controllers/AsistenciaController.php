<?php
// controllers/AsistenciaController.php
class AsistenciaController extends Controller {
    private $empModel;
    private $asiModel;

    // tolerancia en minutos (15)
    const TOLERANCIA_MINUTOS = 15;

    public function __construct(){
        $this->empModel = new EmpleadoModel();
        $this->asiModel = new AsistenciaModel();
    }

    public function marcar(){
        // página con el form de marcación
        // obtener horario actual desde el modelo para mostrarlo en la vista
        $horario = $this->asiModel->getSchedule();
        $this->view('asistencia/marcar', ['csrf'=>$this->generateCsrf(), 'horario'=>$horario]);
    }

    // Endpoint AJAX que recibe dni + tipo + lat/lng
    public function marcar_ajax(){
        header('Content-Type: application/json; charset=utf-8');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['success'=>false,'message'=>'Método no permitido']); exit;
        }

        // CSRF
        $csrf = $_POST['csrf'] ?? '';
        if(!$this->verifyCsrf($csrf)){
            echo json_encode(['success'=>false,'message'=>'Token inválido']); exit;
        }

        $dni  = trim($_POST['dni'] ?? '');
        $tipo = $_POST['tipo'] ?? 'entrada';
        $lat  = $_POST['lat'] ?? null;
        $lng  = $_POST['lng'] ?? null;

        // Para tipos de entrada/salida, exigir geolocalización
        if(in_array($tipo, ['entrada','salida'])){
            if(empty($lat) || empty($lng)){
                echo json_encode(['success'=>false,'message'=>'Se requiere ubicación (GPS) para marcar entrada/salida']); exit;
            }
        }

        if(!$dni){
            echo json_encode(['success'=>false,'message'=>'DNI requerido']); exit;
        }

        $emp = $this->empModel->findByDNI($dni);
        if(!$emp){
            echo json_encode(['success'=>false,'message'=>'Empleado no encontrado']); exit;
        }

        // TIPOS válidos (3 refrigerios)
        $validos = [
            'entrada','salida',
            'refrigerio1_inicio','refrigerio1_fin',
            'refrigerio2_inicio','refrigerio2_fin',
            'refrigerio3_inicio','refrigerio3_fin'
        ];
        if(!in_array($tipo, $validos)){
            echo json_encode(['success'=>false,'message'=>'Tipo inválido']); exit;
        }

        // permitir override de fecha solo si es admin (para pruebas/registro manual)
        $fecha = date('Y-m-d');
        if(!empty($_POST['fecha'])){
            // solo permitir si hay sesión de admin
            if(!empty($_SESSION['admin_id'])){
                $f = $_POST['fecha'];
                $d = \DateTime::createFromFormat('Y-m-d', $f);
                if($d && $d->format('Y-m-d') === $f){
                    $fecha = $f;
                }
            }
        }
        $hora  = date('H:i:s');

        // Obtener reglas horarias desde BD (tabla horarios)
        $reglas = $this->asiModel->getRules();
        // reglas: entrada, salida, ref1_inicio, ref1_fin, ...

        // VALIDACIONES POR TIPO
        if($tipo === 'salida' && !$this->asiModel->hasEntradaToday($dni, $fecha)){
            echo json_encode(['success'=>false,'message'=>'No existe entrada previa hoy']); exit;
        }

        // Refrigerio inicio: validar que esté dentro del rango del refrigerio correspondiente
        if(str_starts_with($tipo, 'refrigerio') && str_ends_with($tipo, '_inicio')){
            $num = $this->extractRefrigerioNum($tipo); // 1,2 o 3
            $minKey = "ref{$num}_inicio";
            $maxKey = "ref{$num}_fin";
            if(empty($reglas[$minKey]) || empty($reglas[$maxKey])){
                echo json_encode(['success'=>false,'message'=>"Reglas de horario del refrigerio {$num} no configuradas"]); exit;
            }
            if(!$this->asiModel->isHourInRange($hora, $reglas[$minKey], $reglas[$maxKey])){
                echo json_encode(['success'=>false,'message'=>"Fuera del horario permitido para iniciar refrigerio {$num}"]); exit;
            }
        }

        // Refrigerio fin: validar que exista inicio previo del mismo refrigerio y que la hora esté en la ventana de fin
        if(str_starts_with($tipo, 'refrigerio') && str_ends_with($tipo, '_fin')){
            $num = $this->extractRefrigerioNum($tipo);
            // comprobar inicio
            if(!$this->asiModel->hasRefrigerioInicioForNum($dni, $fecha, $num)){
                echo json_encode(['success'=>false,'message'=>"No hay refrigerio {$num} iniciado hoy"]); exit;
            }

            $minKey = "ref{$num}_inicio";
            $maxKey = "ref{$num}_fin";
            if(empty($reglas[$minKey]) || empty($reglas[$maxKey])){
                echo json_encode(['success'=>false,'message'=>"Reglas de horario del refrigerio {$num} no configuradas"]); exit;
            }
            if(!$this->asiModel->isHourInRange($hora, $reglas[$minKey], $reglas[$maxKey])){
                echo json_encode(['success'=>false,'message'=>"Fuera del horario permitido para finalizar refrigerio {$num}"]); exit;
            }
        }

        // Evitar duplicados: si ya hay registro del mismo tipo para este dni/fecha
        if($this->asiModel->hasRecord($dni, $fecha, $tipo)){
            echo json_encode(['success'=>false,'message'=>'Ya se registró este tipo de marcación anteriormente']); exit;
        }

        // Estado: calculamos con tolerancia para entrada (15 minutos)
        $estado = $this->calcularEstado($tipo, $hora, $reglas, $emp);

        // Registrar
        $ok = $this->asiModel->register([
            'empleado_id' => $emp['id'],
            'dni'         => $emp['dni'],
            'tipo'        => $tipo,
            'fecha'       => $fecha,
            'hora'        => $hora,
            'lat'         => $lat,
            'lng'         => $lng,
            'ip_origen'   => $_SERVER['REMOTE_ADDR'] ?? null,
            'estado'      => $estado,
            'nota'        => null
        ]);

        if($ok){
            echo json_encode(['success'=>true,'message'=>'Asistencia registrada','estado'=>$estado]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Error al registrar']);
        }
        exit;
    }

    // Extrae el número del refrigerio (1,2,3) desde el tipo 'refrigerioN_inicio' o 'refrigerioN_fin'
    private function extractRefrigerioNum($tipo){
        if(preg_match('/refrigerio([123])_/', $tipo, $m)) return (int)$m[1];
        return 0;
    }

    /**
     * calcularEstado:
     * - Para entrada: usa la hora de referencia en $reglas['entrada'] + TOLERANCIA_MINUTOS
     *    resultado: 'normal' (puntual dentro del tolerancia) | 'tardanza' | 'falta' (no aplicable aquí)
     * - Para salida: compara con $reglas['salida'] si existe -> 'puntual'|'tardanza'|'ok'
     * - Para refrigerios devuelve 'inicio' / 'fin'
     */
    private function calcularEstado($tipo, $hora, $reglas = [], $emp = null){
        $h = \DateTime::createFromFormat('H:i:s', $hora);
        if(!$h) return 'invalid';
        $timeInt = (int)$h->format('Hi');

        if($tipo === 'entrada'){
            // Si admin definió horario de entrada, aplicamos tolerancia
            if(!empty($reglas['entrada'])){
                $entradaRef = \DateTime::createFromFormat('H:i:s', $reglas['entrada']);
                if($entradaRef){
                    $limite = clone $entradaRef;
                    $limite->modify('+'.self::TOLERANCIA_MINUTOS.' minutes');
                    $entradaInt = (int)$entradaRef->format('Hi');
                    $limiteInt = (int)$limite->format('Hi');

                    if($timeInt <= $limiteInt){
                        return 'normal'; // dentro de tolerancia
                    } else {
                        return 'tardanza';
                    }
                }
            }

            // fallback: si no hay regla, usamos heurística antigua
            if($timeInt <= 605) return 'normal';
            if($timeInt <= 630) return 'tardanza';
            return 'falta';
        }

        if($tipo === 'salida'){
            if(!empty($reglas['salida'])){
                $salRef = \DateTime::createFromFormat('H:i:s', $reglas['salida']);
                if($salRef){
                    $salInt = (int)$salRef->format('Hi');
                    if($timeInt >= $salInt) return 'puntual';
                    return 'tardanza';
                }
            }
            if($timeInt >= 1700) return 'puntual';
            return 'ok';
        }

        if(str_ends_with($tipo, '_inicio')) return 'inicio';
        if(str_ends_with($tipo, '_fin')) return 'fin';

        return 'invalid';
    }
}
