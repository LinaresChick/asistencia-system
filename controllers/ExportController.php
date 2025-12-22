<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/HorarioModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportController extends Controller {

    protected $empModel;

    public function __construct() {
        if (method_exists(parent::class, '__construct')) {
            parent::__construct();
        }
        if (class_exists('EmpleadoModel')) {
            $this->empModel = new EmpleadoModel();
        }
    }

    protected function render($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        }
    }

    public function index(){
        if (isset($this->empModel) && method_exists($this->empModel, 'findAll')) {
            $empleados = $this->empModel->findAll();
        } elseif (class_exists('EmpleadoModel')) {
            $m = new EmpleadoModel();
            $empleados = $m->findAll();
        } else {
            $empleados = [];
        }

        $this->render('export/index', ['empleados' => $empleados]);
    }

    public function excel(){

        $mes  = $_POST['mes'] ?? '12';
        $anio = $_POST['anio'] ?? '2025';
        $dia  = $_POST['dia'] ?? null;
        $empleado_id = $_POST['empleado_id'] ?? null;

        // Traer datos de asistencia
        $asistenciaModel = new AsistenciaModel();
        
        if ($dia) {
            // Si se especifica día, traer solo ese día
            $fecha = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);
            $asistencias = $asistenciaModel->getByDate($fecha);
        } else {
            // Si no, traer todo el mes
            $startDate = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-31';
            $asistencias = $asistenciaModel->getByRange($startDate, $endDate);
        }

        // Filtrar por empleado si se especifica
        if ($empleado_id) {
            $asistencias = array_filter($asistencias, function($a) use ($empleado_id) {
                return $a['empleado_id'] == $empleado_id;
            });
        }

        // Si no hay asistencias, mostrar mensaje
        if (empty($asistencias)) {
            echo "No hay datos de asistencia para los criterios seleccionados.";
            exit;
        }

        // Agrupar por empleado y fecha
        $dataPorEmpleado = [];
        foreach ($asistencias as $registro) {
            $empId = $registro['empleado_id'];
            $fecha = $registro['fecha'];
            
            if (!isset($dataPorEmpleado[$empId])) {
                $dataPorEmpleado[$empId] = [
                    'info' => $registro,
                    'dias' => []
                ];
            }
            
            if (!isset($dataPorEmpleado[$empId]['dias'][$fecha])) {
                $dataPorEmpleado[$empId]['dias'][$fecha] = [
                    'entrada' => null,
                    'salida' => null,
                    'refrigerio1_inicio' => null,
                    'refrigerio1_fin' => null,
                    'refrigerio2_inicio' => null,
                    'refrigerio2_fin' => null,
                    'refrigerio3_inicio' => null,
                    'refrigerio3_fin' => null,
                    'estados' => [],
                    'notas' => []
                ];
            }
            
            // Asignar cada tipo de registro
            $tipo = $registro['tipo'];
            if (in_array($tipo, ['entrada', 'salida', 'refrigerio1_inicio', 'refrigerio1_fin', 
                                 'refrigerio2_inicio', 'refrigerio2_fin', 'refrigerio3_inicio', 'refrigerio3_fin'])) {
                $dataPorEmpleado[$empId]['dias'][$fecha][$tipo] = $registro['hora'];
            }
            
            // Guardar estados y notas
            if (!empty($registro['estado']) && $registro['estado'] != 'puntual') {
                $dataPorEmpleado[$empId]['dias'][$fecha]['estados'][] = $registro['estado'];
            }
            if (!empty($registro['nota'])) {
                $dataPorEmpleado[$empId]['dias'][$fecha]['notas'][] = $registro['nota'];
            }
        }

        // Obtener horarios para calcular tardanzas
        $horarioModel = new HorarioModel();
        $horarios = $horarioModel->getCurrentHorario();
        $horaEntradaEsperada = '08:00:00'; // Default
        if ($horarios && isset($horarios['entrada'])) {
            $horaEntradaEsperada = $horarios['entrada'];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ================= ESTILOS ================= */
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $subHeaderStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498DB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $cellStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $tardanzaStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F39C12']
            ]
        ];

        $faltaStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E74C3C']
            ]
        ];

        /* ================= LOGO Y TITULO ================= */
        $logoPath = __DIR__ . '/../public/img/logo.png';
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(70);
            $drawing->setWorksheet($sheet);
        }

        // Título
        $sheet->mergeCells('C1:L2');
        $titulo = "REPORTE DE ASISTENCIA - SISTEMA DE REGISTRO\n";
        $titulo .= "INTERNATIONAL LOGISTIC GROUP PERÚ S.A.C.\n";
        
        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                  'Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $mesNombre = $meses[intval($mes) - 1];
        
        if ($dia) {
            $titulo .= "Día: " . str_pad($dia, 2, '0', STR_PAD_LEFT) . " de $mesNombre de $anio";
        } else {
            $titulo .= "Mes: $mesNombre de $anio";
        }
        
        $sheet->setCellValue('C1', $titulo);
        $sheet->getStyle('C1')->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C1')->getFont()->setSize(14);

        $sheet->setCellValue('M1', 'Generado:');
        $sheet->setCellValue('N1', date('d/m/Y H:i:s'));

        /* ================= INFORMACIÓN GENERAL ================= */
        $row = 4;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", 'RUC: 20448822304');
        $sheet->mergeCells("E{$row}:H{$row}");
        $sheet->setCellValue("E{$row}", "Período: $mesNombre de $anio");
        $sheet->mergeCells("I{$row}:L{$row}");
        $sheet->setCellValue("I{$row}", "Total Registros: " . count($asistencias));
        $sheet->mergeCells("M{$row}:O{$row}");
        $sheet->setCellValue("M{$row}", "Sede: Puno");
        
        $sheet->getStyle("A{$row}:O{$row}")->applyFromArray($headerStyle);

        /* ================= CABECERA PRINCIPAL ================= */
        $row = 5;
        $sheet->setCellValue("A{$row}", '#');
        $sheet->setCellValue("B{$row}", 'DNI');
        $sheet->setCellValue("C{$row}", 'EMPLEADO');
        $sheet->setCellValue("D{$row}", 'CARGO');
        $sheet->setCellValue("E{$row}", 'FECHA');
        $sheet->setCellValue("F{$row}", 'DÍA');
        $sheet->setCellValue("G{$row}", 'ENTRADA');
        $sheet->setCellValue("H{$row}", 'SALIDA');
        $sheet->setCellValue("I{$row}", 'HORAS TRAB.');
        $sheet->setCellValue("J{$row}", 'TARDANZA');
        $sheet->setCellValue("K{$row}", 'REFRIGERIO 1');
        $sheet->setCellValue("L{$row}", 'REFRIGERIO 2');
        $sheet->setCellValue("M{$row}", 'REFRIGERIO 3');
        $sheet->setCellValue("N{$row}", 'ESTADO');
        $sheet->setCellValue("O{$row}", 'OBSERVACIONES');
        
        $sheet->getStyle("A{$row}:O{$row}")->applyFromArray($subHeaderStyle);

        /* ================= AJUSTAR ANCHO DE COLUMNAS ================= */
        $columnWidths = [
            'A' => 5,    // #
            'B' => 12,   // DNI
            'C' => 25,   // EMPLEADO
            'D' => 20,   // CARGO
            'E' => 12,   // FECHA
            'F' => 10,   // DÍA
            'G' => 10,   // ENTRADA
            'H' => 10,   // SALIDA
            'I' => 12,   // HORAS TRAB.
            'J' => 10,   // TARDANZA
            'K' => 15,   // REFRIGERIO 1
            'L' => 15,   // REFRIGERIO 2
            'M' => 15,   // REFRIGERIO 3
            'N' => 12,   // ESTADO
            'O' => 25    // OBSERVACIONES
        ];
        
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        /* ================= LLENAR DATOS ================= */
        $row = 6;
        $contador = 1;
        
        foreach ($dataPorEmpleado as $empId => $datos) {
            $empleado = $datos['info'];
            $dias = $datos['dias'];
            
            // Ordenar días por fecha
            ksort($dias);
            
            foreach ($dias as $fecha => $registrosDia) {
                $fechaObj = new DateTime($fecha);
                $nombreDia = ['Dom','Lun','Mar','Mié','Jue','Vie','Sab'][$fechaObj->format('w')];
                
                // Calcular horas trabajadas
                $horasTrabajadas = '';
                if ($registrosDia['entrada'] && $registrosDia['salida']) {
                    $entrada = DateTime::createFromFormat('H:i:s', $registrosDia['entrada']);
                    $salida = DateTime::createFromFormat('H:i:s', $registrosDia['salida']);
                    
                    // Restar tiempo de refrigerios
                    $totalRefrigerio = 0;
                    
                    if ($registrosDia['refrigerio1_inicio'] && $registrosDia['refrigerio1_fin']) {
                        $ref1Inicio = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio1_inicio']);
                        $ref1Fin = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio1_fin']);
                        $totalRefrigerio += $ref1Fin->getTimestamp() - $ref1Inicio->getTimestamp();
                    }
                    
                    if ($registrosDia['refrigerio2_inicio'] && $registrosDia['refrigerio2_fin']) {
                        $ref2Inicio = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio2_inicio']);
                        $ref2Fin = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio2_fin']);
                        $totalRefrigerio += $ref2Fin->getTimestamp() - $ref2Inicio->getTimestamp();
                    }
                    
                    if ($registrosDia['refrigerio3_inicio'] && $registrosDia['refrigerio3_fin']) {
                        $ref3Inicio = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio3_inicio']);
                        $ref3Fin = DateTime::createFromFormat('H:i:s', $registrosDia['refrigerio3_fin']);
                        $totalRefrigerio += $ref3Fin->getTimestamp() - $ref3Inicio->getTimestamp();
                    }
                    
                    $totalSegundos = $salida->getTimestamp() - $entrada->getTimestamp() - $totalRefrigerio;
                    $horas = floor($totalSegundos / 3600);
                    $minutos = floor(($totalSegundos % 3600) / 60);
                    $horasTrabajadas = sprintf("%02d:%02d", $horas, $minutos);
                }
                
                // Calcular tardanza
                $tardanza = '';
                if ($registrosDia['entrada']) {
                    $entradaTime = strtotime($registrosDia['entrada']);
                    $entradaEsperada = strtotime($horaEntradaEsperada);
                    
                    if ($entradaTime > $entradaEsperada) {
                        $diferencia = $entradaTime - $entradaEsperada;
                        $minutosTardanza = floor($diferencia / 60);
                        $tardanza = $minutosTardanza . ' min';
                    }
                }
                
                // Formatear refrigerios
                $ref1 = '';
                if ($registrosDia['refrigerio1_inicio'] && $registrosDia['refrigerio1_fin']) {
                    $ref1 = substr($registrosDia['refrigerio1_inicio'], 0, 5) . ' - ' . 
                            substr($registrosDia['refrigerio1_fin'], 0, 5);
                }
                
                $ref2 = '';
                if ($registrosDia['refrigerio2_inicio'] && $registrosDia['refrigerio2_fin']) {
                    $ref2 = substr($registrosDia['refrigerio2_inicio'], 0, 5) . ' - ' . 
                            substr($registrosDia['refrigerio2_fin'], 0, 5);
                }
                
                $ref3 = '';
                if ($registrosDia['refrigerio3_inicio'] && $registrosDia['refrigerio3_fin']) {
                    $ref3 = substr($registrosDia['refrigerio3_inicio'], 0, 5) . ' - ' . 
                            substr($registrosDia['refrigerio3_fin'], 0, 5);
                }
                
                // Determinar estado
                $estado = 'PUNTUAL';
                $estiloEstado = null;
                
                if (!empty($registrosDia['estados'])) {
                    if (in_array('falta', $registrosDia['estados'])) {
                        $estado = 'FALTA';
                        $estiloEstado = $faltaStyle;
                    } elseif (in_array('tardanza', $registrosDia['estados'])) {
                        $estado = 'TARDANZA';
                        $estiloEstado = $tardanzaStyle;
                    } elseif (in_array('invalid', $registrosDia['estados'])) {
                        $estado = 'INVÁLIDO';
                    }
                }
                
                // Observaciones
                $observaciones = implode('; ', $registrosDia['notas']);
                
                // Llenar fila
                $sheet->setCellValue("A{$row}", $contador);
                $sheet->setCellValue("B{$row}", $empleado['dni']);
                $sheet->setCellValue("C{$row}", ($empleado['nombres'] ?? '') . ' ' . ($empleado['apellidos'] ?? ''));
                $sheet->setCellValue("D{$row}", $empleado['cargo'] ?? '');
                $sheet->setCellValue("E{$row}", $fechaObj->format('d/m/Y'));
                $sheet->setCellValue("F{$row}", $nombreDia);
                $sheet->setCellValue("G{$row}", $registrosDia['entrada'] ? substr($registrosDia['entrada'], 0, 5) : '');
                $sheet->setCellValue("H{$row}", $registrosDia['salida'] ? substr($registrosDia['salida'], 0, 5) : '');
                $sheet->setCellValue("I{$row}", $horasTrabajadas);
                $sheet->setCellValue("J{$row}", $tardanza);
                $sheet->setCellValue("K{$row}", $ref1);
                $sheet->setCellValue("L{$row}", $ref2);
                $sheet->setCellValue("M{$row}", $ref3);
                $sheet->setCellValue("N{$row}", $estado);
                $sheet->setCellValue("O{$row}", $observaciones);
                
                // Aplicar estilos básicos
                $sheet->getStyle("A{$row}:O{$row}")->applyFromArray($cellStyle);
                
                // Aplicar estilo de estado si es necesario
                if ($estiloEstado) {
                    $sheet->getStyle("N{$row}")->applyFromArray($estiloEstado);
                }
                
                // Resaltar tardanza en minutos
                if (!empty($tardanza)) {
                    $sheet->getStyle("J{$row}")->applyFromArray($tardanzaStyle);
                }
                
                $row++;
                $contador++;
            }
            
            // Añadir una línea en blanco entre empleados
            $row++;
        }

        /* ================= RESUMEN ================= */
        $row++;
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue("A{$row}", "RESUMEN DEL REPORTE");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        
        $row++;
        $sheet->setCellValue("A{$row}", "Total empleados:");
        $sheet->setCellValue("B{$row}", count($dataPorEmpleado));
        
        $row++;
        $sheet->setCellValue("A{$row}", "Total días registrados:");
        $sheet->setCellValue("B{$row}", $contador - 1);
        
        $row++;
        $sheet->setCellValue("A{$row}", "Período:");
        $sheet->setCellValue("B{$row}", $dia ? "Día {$dia}/{$mes}/{$anio}" : "Mes {$mesNombre}/{$anio}");

        /* ================= FORMATEAR FECHAS Y HORAS ================= */
        $sheet->getStyle("E6:E" . ($row - 4))
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        
        $sheet->getStyle("G6:H" . ($row - 4))
              ->getNumberFormat()
              ->setFormatCode('hh:mm');

        /* ================= CONGELAR PANELES ================= */
        $sheet->freezePane('A6');

        /* ================= AUTOFILTRO ================= */
        $sheet->setAutoFilter("A5:O" . ($row - 5));

        /* ================= EXPORTAR ================= */
        $nombreArchivo = 'Reporte_Asistencia_' . ($dia ? $dia . '_' : '') . $mes . '_' . $anio . '_' . date('His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Método auxiliar para obtener datos si no existe getByRange
     */
    public function getAsistenciasByRange($startDate, $endDate) {
        // Esta función sería implementada en AsistenciaModel
        // Por ahora, asumimos que existe el método getByRange
        $model = new AsistenciaModel();
        return $model->getByRange($startDate, $endDate);
    }
}