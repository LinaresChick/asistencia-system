<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Asistencia</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .container { max-width: 600px; margin-top: 50px; }
        .card { box-shadow: 0 0 20px rgba(0,0,0,0.1); border: none; }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; }
        .btn-export { width: 100%; padding: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">📊 Exportar Reporte de Asistencia</h3>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo BASE_URL; ?>?r=export/excel">
                    <div class="form-group mb-3">
                        <label for="mes" class="form-label">Mes:</label>
                        <select name="mes" id="mes" class="form-control" required>
                            <option value="">Selecciona un mes</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11" selected>Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="dia" class="form-label">Día (opcional):</label>
                        <input type="number" name="dia" id="dia" class="form-control" min="1" max="31" placeholder="Dejar vacío para todo el mes">
                    </div>

                    <div class="form-group mb-3">
                        <label for="anio" class="form-label">Año:</label>
                        <input type="number" name="anio" id="anio" class="form-control" value="2025" min="2020" max="2099" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="empleado" class="form-label">Empleado (opcional):</label>
                        <select name="empleado_id" id="empleado" class="form-control">
                            <option value="">Todos los empleados</option>
                            <?php if (!empty($empleados)): ?>
                                <?php foreach($empleados as $emp): ?>
                                    <option value="<?php echo htmlspecialchars($emp['id'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars(($emp['nombre'] ?? '') . ' ' . ($emp['apellido'] ?? ''), ENT_QUOTES); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-export">
                        Descargar Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/bootstrap.bundle.min.js"></script>
</body>
</html>