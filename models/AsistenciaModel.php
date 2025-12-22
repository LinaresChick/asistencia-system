<?php
// models/AsistenciaModel.php
class AsistenciaModel extends Model {

    // Registra cualquier tipo de asistencia
    public function register(array $data){
        $stmt = $this->db->prepare("
            INSERT INTO asistencias (empleado_id, dni, tipo, fecha, hora, lat, lng, ip_origen, estado, nota)
            VALUES (:empleado_id, :dni, :tipo, :fecha, :hora, :lat, :lng, :ip_origen, :estado, :nota)
        ");

        return $stmt->execute([
            ':empleado_id' => $data['empleado_id'],
            ':dni'         => $data['dni'],
            ':tipo'        => $data['tipo'],
            ':fecha'       => $data['fecha'],
            ':hora'        => $data['hora'],
            ':lat'         => $data['lat'] ?? null,
            ':lng'         => $data['lng'] ?? null,
            ':ip_origen'   => $data['ip_origen'] ?? null,
            ':estado'      => $data['estado'] ?? null,
            ':nota'        => $data['nota'] ?? null
        ]);
    }

    // Comprueba si existe entrada hoy por DNI
    public function hasEntradaToday($dni, $fecha){
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS c 
            FROM asistencias 
            WHERE dni=:dni AND fecha=:fecha AND tipo='entrada'
        ");
        $stmt->execute([':dni'=>$dni,':fecha'=>$fecha]);
        $r = $stmt->fetch();
        return $r && $r['c'] > 0;
    }
    
    // Comprueba si ya existe un registro del tipo dado para ese dni y fecha
    public function hasRecord($dni, $fecha, $tipo){
        $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM asistencias WHERE dni = :dni AND fecha = :fecha AND tipo = :tipo");
        $stmt->execute([':dni'=>$dni, ':fecha'=>$fecha, ':tipo'=>$tipo]);
        $r = $stmt->fetch();
        return $r && $r['c'] > 0;
    }

    // Devuelve el último registro existente del tipo para dni+fecha
    public function getRecord($dni, $fecha, $tipo){
        $stmt = $this->db->prepare("SELECT * FROM asistencias WHERE dni = :dni AND fecha = :fecha AND tipo = :tipo ORDER BY id DESC LIMIT 1");
        $stmt->execute([':dni'=>$dni, ':fecha'=>$fecha, ':tipo'=>$tipo]);
        return $stmt->fetch();
    }

    // Comprueba si existe entrada hoy por empleado_id
    public function buscarEntradaByEmpleado($empleado_id, $fecha){
        $stmt = $this->db->prepare("
            SELECT * FROM asistencias
            WHERE empleado_id = :id AND fecha = :fecha AND tipo = 'entrada'
            LIMIT 1
        ");
        $stmt->execute([':id'=>$empleado_id, ':fecha'=>$fecha]);
        return $stmt->fetch();
    }

    // Comprueba si existe inicio de refrigerio para el refrigerio N (1,2,3) por DNI
    public function hasRefrigerioInicioForNum($dni, $fecha, $num){
        $tipo = "refrigerio{$num}_inicio";
        $stmt = $this->db->prepare("
            SELECT id, hora FROM asistencias
            WHERE dni=:dni AND fecha=:fecha AND tipo=:tipo
            ORDER BY hora DESC LIMIT 1
        ");
        $stmt->execute([':dni'=>$dni,':fecha'=>$fecha,':tipo'=>$tipo]);
        return $stmt->fetch();
    }

    // Comprueba si hay un inicio pendiente sin su fin para un refrigerio N
    public function hasRefrigerioPendienteForNum($dni, $fecha, $num){
        $inicioTipo = "refrigerio{$num}_inicio";
        $finTipo = "refrigerio{$num}_fin";
        $stmt = $this->db->prepare("
            SELECT i.id
            FROM asistencias i
            LEFT JOIN asistencias f
              ON f.dni = i.dni AND f.fecha = i.fecha AND f.tipo = :finTipo
            WHERE i.dni = :dni AND i.fecha = :fecha AND i.tipo = :inicioTipo
              AND (f.id IS NULL OR f.hora < i.hora)
            ORDER BY i.hora DESC LIMIT 1
        ");
        $stmt->execute([
            ':dni'=>$dni, ':fecha'=>$fecha,
            ':inicioTipo'=>$inicioTipo, ':finTipo'=>$finTipo
        ]);
        return $stmt->fetch();
    }

    // ------------------------------------------------------------
    // Reglas horarias del horario (tabla horarios)
    // ------------------------------------------------------------
    // Se asume que hay una sola fila de configuracion de horarios.
    public function getRules(){
        $stmt = $this->db->query("SELECT * FROM horarios LIMIT 1");
        $r = $stmt->fetch();

        return [
            'entrada'       => $r['entrada']       ?? null,
            'salida'        => $r['salida']        ?? null,
            'ref1_inicio'   => $r['ref1_inicio']   ?? $r['ref_inicio_min'] ?? null,
            'ref1_fin'      => $r['ref1_fin']      ?? $r['ref_fin_min'] ?? null,
            'ref2_inicio'   => $r['ref2_inicio']   ?? null,
            'ref2_fin'      => $r['ref2_fin']      ?? null,
            'ref3_inicio'   => $r['ref3_inicio']   ?? null,
            'ref3_fin'      => $r['ref3_fin']      ?? null,
        ];
    }

    // Guarda o actualiza la fila única de la tabla `horarios`.
    // $data puede contener keys: entrada, salida, ref1_inicio, ref1_fin, ref2_inicio, ref2_fin, ref3_inicio, ref3_fin
    public function saveRules(array $data){
        // Comprobamos si ya existe una fila
        $stmt = $this->db->query("SELECT COUNT(*) AS c FROM horarios");
        $r = $stmt->fetch();
        $exists = ($r && $r['c'] > 0);

        if($exists){
            $sql = "UPDATE horarios SET entrada = :entrada, salida = :salida,
                        ref1_inicio = :ref1_inicio, ref1_fin = :ref1_fin,
                        ref2_inicio = :ref2_inicio, ref2_fin = :ref2_fin,
                        ref3_inicio = :ref3_inicio, ref3_fin = :ref3_fin,
                        vigente_desde = :vigente_desde, vigente_hasta = :vigente_hasta
                    ";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':entrada' => $data['entrada'] ?? null,
                ':salida' => $data['salida'] ?? null,
                ':ref1_inicio' => $data['ref1_inicio'] ?? null,
                ':ref1_fin' => $data['ref1_fin'] ?? null,
                ':ref2_inicio' => $data['ref2_inicio'] ?? null,
                ':ref2_fin' => $data['ref2_fin'] ?? null,
                ':ref3_inicio' => $data['ref3_inicio'] ?? null,
                ':ref3_fin' => $data['ref3_fin'] ?? null,
                ':vigente_desde' => $data['vigente_desde'] ?: null,
                ':vigente_hasta' => $data['vigente_hasta'] ?: null,
            ]);
        } else {
            $sql = "INSERT INTO horarios (entrada, salida, ref1_inicio, ref1_fin, ref2_inicio, ref2_fin, ref3_inicio, ref3_fin, vigente_desde, vigente_hasta)
                    VALUES (:entrada, :salida, :ref1_inicio, :ref1_fin, :ref2_inicio, :ref2_fin, :ref3_inicio, :ref3_fin, :vigente_desde, :vigente_hasta)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':entrada' => $data['entrada'] ?? null,
                ':salida' => $data['salida'] ?? null,
                ':ref1_inicio' => $data['ref1_inicio'] ?? null,
                ':ref1_fin' => $data['ref1_fin'] ?? null,
                ':ref2_inicio' => $data['ref2_inicio'] ?? null,
                ':ref2_fin' => $data['ref2_fin'] ?? null,
                ':ref3_inicio' => $data['ref3_inicio'] ?? null,
                ':ref3_fin' => $data['ref3_fin'] ?? null,
                ':vigente_desde' => $data['vigente_desde'] ?: null,
                ':vigente_hasta' => $data['vigente_hasta'] ?: null,
            ]);
        }
    }

    // Obtener el horario actual guardado
    public function getSchedule(){
        $stmt = $this->db->query("SELECT * FROM horarios LIMIT 1");
        return $stmt->fetch();
    }

    // Eliminar todos los horarios (para crear uno nuevo)
    public function deleteSchedule(){
        $stmt = $this->db->prepare("DELETE FROM horarios");
        return $stmt->execute();
    }

    // Comparación simple de horas "HH:MM:SS" (o "HH:MM")
    public function isHourInRange($horaActual, $min, $max){
        if(empty($min) || empty($max)) return false;
        // normalizar a H:i:s
        $ha = substr($horaActual,0,8);
        $mi = substr($min,0,8);
        $ma = substr($max,0,8);
        return ($ha >= $mi && $ha <= $ma);
    }

    // ------------------------------------------------------------
    // Consultas utilitarias para cron y vistas
    // ------------------------------------------------------------
    // Listar empleados (para cron de faltas)
    public function listarEmpleados(){
        $stmt = $this->db->query("SELECT id, dni, nombres, apellidos FROM empleados");
        return $stmt->fetchAll();
    }

    // Devuelve por cada empleado los tipos de marcación faltantes o fuera de ventana
    // Retorna sólo empleados con al menos un tipo 'missing' o 'out_of_window' o 'tardanza'
    public function getFaltantesByDate($fecha){
        $rules = $this->getRules();
        $empleados = $this->listarEmpleados();

        $tipos = ['entrada','salida'];
        // Añadir refrigerios sólo si están configurados
        for($n=1;$n<=3;$n++){
            $minKey = "ref{$n}_inicio";
            $maxKey = "ref{$n}_fin";
            if(!empty($rules[$minKey]) || !empty($rules[$maxKey])){
                $tipos[] = "refrigerio{$n}_inicio";
                $tipos[] = "refrigerio{$n}_fin";
            }
        }

        $res = [];
        foreach($empleados as $e){
            $faltantes = [];
            foreach($tipos as $tipo){
                // buscar registro
                $rec = $this->getRecord($e['dni'], $fecha, $tipo);
                if(!$rec){
                    $faltantes[$tipo] = 'missing';
                    continue;
                }

                // si hay registro, validar si está dentro de la ventana según reglas
                if($tipo === 'entrada'){
                    if(!empty($rules['entrada'])){
                        $ref = \DateTime::createFromFormat('H:i:s', $rules['entrada']);
                        if($ref){
                            $limite = clone $ref;
                            $limite->modify('+15 minutes');
                            $h = \DateTime::createFromFormat('H:i:s', $rec['hora']);
                            if($h){
                                if((int)$h->format('Hi') <= (int)$limite->format('Hi')){
                                    // dentro de tolerancia -> ok
                                } else {
                                    $faltantes[$tipo] = 'tardanza';
                                }
                            }
                        }
                    }
                } elseif($tipo === 'salida'){
                    if(!empty($rules['salida'])){
                        $ref = \DateTime::createFromFormat('H:i:s', $rules['salida']);
                        $h = \DateTime::createFromFormat('H:i:s', $rec['hora']);
                        if($ref && $h){
                            if((int)$h->format('Hi') >= (int)$ref->format('Hi')){
                                // ok
                            } else {
                                $faltantes[$tipo] = 'tardanza';
                            }
                        }
                    }
                } else {
                    // refrigerioN_inicio / refrigerioN_fin
                    if(preg_match('/refrigerio([123])_/', $tipo, $m)){
                        $n = (int)$m[1];
                        $min = $rules["ref{$n}_inicio"] ?? null;
                        $max = $rules["ref{$n}_fin"] ?? null;
                        if(!empty($min) && !empty($max)){
                            // comparar hora del registro con min/max
                            $horaReg = substr($rec['hora'],0,8);
                            $minN = substr($min,0,8);
                            $maxN = substr($max,0,8);
                            if($horaReg < $minN || $horaReg > $maxN){
                                $faltantes[$tipo] = 'out_of_window';
                            }
                        }
                    }
                }
            }

            if(!empty($faltantes)){
                $res[] = [
                    'empleado_id' => $e['id'],
                    'dni' => $e['dni'],
                    'nombres' => $e['nombres'],
                    'apellidos' => $e['apellidos'],
                    'faltantes' => $faltantes
                ];
            }
        }

        return $res;
    }

    // Registrar falta automática (entrada con estado 'falta')
    public function registrarFalta($empleado_id, $dni, $fecha){
        $stmt = $this->db->prepare("
            INSERT INTO asistencias (empleado_id, dni, tipo, fecha, hora, estado, nota)
            VALUES (:empleado_id, :dni, 'entrada', :fecha, '00:00:00', 'falta', 'registrada_por_cron')
        ");
        return $stmt->execute([
            ':empleado_id' => $empleado_id,
            ':dni' => $dni,
            ':fecha' => $fecha
        ]);
    }

    // Guarda registros automáticos de falta para todos los que no marcaron entrada
    // Retorna conteo de registros guardados
    public function guardarFaltantesPorFecha($fecha){
        $rules = $this->getRules();
        $empleados = $this->listarEmpleados();
        
        $guardados = 0;
        
        foreach($empleados as $e){
            // Verificar si tiene entrada para esa fecha
            $hasEntrada = $this->hasEntradaToday($e['dni'], $fecha);
            
            if(!$hasEntrada){
                // Verificar que no exista ya un registro de falta
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) AS c FROM asistencias 
                    WHERE dni = :dni AND fecha = :fecha AND estado = 'falta'
                ");
                $stmt->execute([':dni'=>$e['dni'], ':fecha'=>$fecha]);
                $r = $stmt->fetch();
                
                if(!$r || $r['c'] == 0){
                    // Registrar la falta
                    if($this->registrarFalta($e['id'], $e['dni'], $fecha)){
                        $guardados++;
                    }
                }
            }
        }
        
        return $guardados;
    }

    // ------------------------------------------------------------
    // Consultas existentes de reporte
    // ------------------------------------------------------------
    public function getByDate($fecha){
        $stmt = $this->db->prepare("
            SELECT a.*, e.nombres, e.apellidos
            FROM asistencias a
            LEFT JOIN empleados e ON e.id = a.empleado_id
            WHERE a.fecha = :fecha
            ORDER BY a.hora
        ");
        $stmt->execute([':fecha'=>$fecha]);
        return $stmt->fetchAll();
    }

    public function getAll(){
        $stmt = $this->db->query("
            SELECT a.*, e.nombres, e.apellidos
            FROM asistencias a
            LEFT JOIN empleados e ON e.id = a.empleado_id
            ORDER BY a.timestamp_reg DESC
            LIMIT 500
        ");
        return $stmt->fetchAll();
    }

    public function getByRange($startDate, $endDate){
        $stmt = $this->db->prepare("
            SELECT a.*, e.nombres, e.apellidos
            FROM asistencias a
            LEFT JOIN empleados e ON e.dni = a.dni
            WHERE a.fecha BETWEEN :ini AND :fin
            ORDER BY a.fecha ASC, a.hora ASC
        ");
        $stmt->execute([':ini'=>$startDate,':fin'=>$endDate]);
        return $stmt->fetchAll();
    }

    public function getByRangeWithHours($startDate, $endDate, $hIni, $hFin){
        $stmt = $this->db->prepare("
            SELECT a.*, e.nombres, e.apellidos
            FROM asistencias a
            LEFT JOIN empleados e ON e.dni = a.dni
            WHERE a.fecha BETWEEN :f1 AND :f2
              AND a.hora BETWEEN :h1 AND :h2
            ORDER BY a.fecha ASC, a.hora ASC
        ");
        $stmt->execute([':f1'=>$startDate,':f2'=>$endDate,':h1'=>$hIni,':h2'=>$hFin]);
        return $stmt->fetchAll();
    }
}
