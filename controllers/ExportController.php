<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

        $mes  = $_POST['mes'] ?? '11';
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

        // Agrupar por empleado
        $dataPorEmpleado = [];
        foreach ($asistencias as $registro) {
            $empId = $registro['empleado_id'];
            if (!isset($dataPorEmpleado[$empId])) {
                $dataPorEmpleado[$empId] = [
                    'info' => $registro,
                    'registros' => []
                ];
            }
            $dataPorEmpleado[$empId]['registros'][] = $registro;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ================= ESTILOS ================= */
        $verde = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $borde = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        /* ================= LOGO ================= */
        $logoPath = __DIR__ . '/../public/img/logo.png';
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(70);
            $drawing->setWorksheet($sheet);
        }

        /* ================= TITULO ================= */
        $sheet->mergeCells('B1:J2');
        $tituloAux = "CONTROL DE ASISTENCIA\nINTERNATIONAL LOGISTIC GROUP PERÚ S.A.C.";
        if ($dia) {
            $tituloAux .= "\nDía: " . str_pad($dia, 2, '0', STR_PAD_LEFT) . "/" . $mes . "/" . $anio;
        }
        $sheet->setCellValue('B1', $tituloAux);
        $sheet->getStyle('B1')->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('K1', 'Código:');
        $sheet->setCellValue('L1', '001-1');
        $sheet->setCellValue('K2', 'Fecha:');
        $sheet->setCellValue('L2', date('d/m/Y'));

        /* ================= DATOS GENERALES ================= */
        $sheet->setCellValue('A4', 'RUC');
        $sheet->setCellValue('B4', '20448822304');
        $sheet->setCellValue('D4', 'Mes');

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                  'Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $sheet->setCellValue('E4', $meses[intval($mes) - 1]);

        $sheet->setCellValue('G4', 'Año');
        $sheet->setCellValue('H4', $anio);

        $sheet->getStyle('A4:H4')->applyFromArray($verde);

        $sheet->setCellValue('A5', 'Nombres y Apellidos:');
        $sheet->mergeCells('B5:E5');
        $sheet->setCellValue('F5', 'N° DNI:');
        $sheet->mergeCells('G5:H5');
        $sheet->setCellValue('I5', 'Sede:');
        $sheet->setCellValue('J5', 'Puno');

        $sheet->getStyle('A5:J5')->applyFromArray($borde);

        /* ================= BLOQUE HORARIOS ================= */
        $sheet->mergeCells('A6:C6');
        $sheet->mergeCells('D6:F6');
        $sheet->mergeCells('G6:I6');

        $sheet->setCellValue('A6', "Hora de ingreso y salida\n(lunes a viernes)");
        $sheet->setCellValue('D6', "Hora de refrigerio\n(lunes a viernes)");
        $sheet->setCellValue('G6', "Hora de ingreso\nsábado");

        $sheet->getStyle('A6:I6')->applyFromArray($verde);
        $sheet->getStyle('A6:I6')->getAlignment()->setWrapText(true);

        /* ================= SUB CABECERA ================= */
        $sheet->mergeCells('C7:G7');
        $sheet->setCellValue('C7', 'Hora de registro');
        $sheet->getStyle('C7')->applyFromArray($verde);

        /* ================= CABECERA TABLA ================= */
        $sheet->fromArray([
            'Fecha','Día','Ingreso','Firma','Refrigerio','Salida','Firma','Horas Extra','Incidencias'
        ], NULL, 'A8');

        $sheet->getStyle('A8:I8')->applyFromArray($verde);
        $sheet->getRowDimension(8)->setRowHeight(28);

        /* ================= ANCHO COLUMNAS ================= */
        foreach (range('A','I') as $col) {
            $sheet->getColumnDimension($col)->setWidth(14);
        }
        $sheet->getColumnDimension('I')->setWidth(18);

        /* ================= LLENAR DATOS DE ASISTENCIA ================= */
        $rowActual = 9;
        $rowInicio = 0;

        foreach ($dataPorEmpleado as $empId => $datos) {
            $emp = $datos['info'];
            $registros = $datos['registros'];

            // Encabezado del empleado
            if ($rowInicio === 0) {
                $rowInicio = $rowActual;
            }

            $sheet->setCellValue('A' . $rowActual, 'Nombres y Apellidos:');
            $sheet->mergeCells('B' . $rowActual . ':E' . $rowActual);
            $sheet->setCellValue('B' . $rowActual, ($emp['nombres'] ?? '') . ' ' . ($emp['apellidos'] ?? ''));
            $sheet->setCellValue('F' . $rowActual, 'N° DNI:');
            $sheet->mergeCells('G' . $rowActual . ':H' . $rowActual);
            $sheet->setCellValue('G' . $rowActual, $emp['dni'] ?? '');
            $sheet->setCellValue('I' . $rowActual, 'Sede:');
            $sheet->setCellValue('J' . $rowActual, 'Puno');
            
            $sheet->getStyle('A' . $rowActual . ':J' . $rowActual)->applyFromArray($borde);
            $rowActual++;

            // Agrupar registros por fecha
            $registrosPorFecha = [];
            foreach ($registros as $reg) {
                $fecha = $reg['fecha'];
                if (!isset($registrosPorFecha[$fecha])) {
                    $registrosPorFecha[$fecha] = [];
                }
                $registrosPorFecha[$fecha][] = $reg;
            }

            // Llenar datos por fecha
            foreach ($registrosPorFecha as $fecha => $regsDelDia) {
                $fechaObj = new DateTime($fecha);
                $diaNum = $fechaObj->format('d');
                $nombreDia = ['Dom','Lun','Mar','Mié','Jue','Vie','Sab'][$fechaObj->format('w')];

                $ingreso = '';
                $refrigerio = '';
                $salida = '';

                foreach ($regsDelDia as $reg) {
                    if ($reg['tipo'] === 'entrada') {
                        $ingreso = $reg['hora'];
                    } elseif (strpos($reg['tipo'], 'refrigerio') !== false) {
                        $refrigerio = $reg['hora'];
                    } elseif ($reg['tipo'] === 'salida') {
                        $salida = $reg['hora'];
                    }
                }

                $sheet->setCellValue('A' . $rowActual, $fecha);
                $sheet->setCellValue('B' . $rowActual, $nombreDia);
                $sheet->setCellValue('C' . $rowActual, $ingreso);
                $sheet->setCellValue('D' . $rowActual, '');
                $sheet->setCellValue('E' . $rowActual, $refrigerio);
                $sheet->setCellValue('F' . $rowActual, $salida);
                $sheet->setCellValue('G' . $rowActual, '');
                $sheet->setCellValue('H' . $rowActual, '');
                $sheet->setCellValue('I' . $rowActual, '');

                $sheet->getStyle('A' . $rowActual . ':I' . $rowActual)->applyFromArray($borde);
                $rowActual++;
            }

            $rowActual++;
        }

        /* ================= BORDES GENERALES ================= */
        if ($rowInicio > 0) {
            $sheet->getStyle('A' . $rowInicio . ':I' . ($rowActual - 1))->applyFromArray($borde);
        }

        /* ================= EXPORTAR ================= */
        $nombreArchivo = 'Asistencia_' . $mes . '_' . ($dia ? $dia . '_' : '') . $anio . '_' . date('His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
