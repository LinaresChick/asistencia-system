<?php
// views/empleado/dashboard.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Asistencia - Portal Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Portal Empleado</h1>
            <p class="text-sm text-blue-100">Bienvenido, <?php echo htmlspecialchars($empleado['nombres']); ?></p>
        </div>
        <div class="flex gap-4">
            <a href="?r=empleado/ficha" class="px-4 py-2 bg-blue-400 rounded hover:bg-blue-300 transition">
                <i class="fas fa-user-circle mr-1"></i> Mi Ficha
            </a>
            <a href="?r=empleado/logout" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600 transition">
                <i class="fas fa-sign-out-alt mr-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto p-6">

    <!-- Tarjetas de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Marcaciones</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $estadisticas['total'] ?? 0; ?></p>
                </div>
                <i class="fas fa-calendar-check text-4xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Faltas</p>
                    <p class="text-3xl font-bold text-red-600"><?php echo $estadisticas['faltas'] ?? 0; ?></p>
                </div>
                <i class="fas fa-times-circle text-4xl text-red-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Tardanzas</p>
                    <p class="text-3xl font-bold text-orange-600"><?php echo $estadisticas['tardanzas'] ?? 0; ?></p>
                </div>
                <i class="fas fa-clock text-4xl text-orange-200"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Normales</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $estadisticas['normales'] ?? 0; ?></p>
                </div>
                <i class="fas fa-check-circle text-4xl text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">📋 Filtrar Historial</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="r" value="empleado/dashboard">
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Desde:</label>
                <input type="date" name="startDate" value="<?php echo htmlspecialchars($startDate); ?>"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Hasta:</label>
                <input type="date" name="endDate" value="<?php echo htmlspecialchars($endDate); ?>"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Estado:</label>
                <select id="filtroEstado" class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="normal">Normal</option>
                    <option value="tardanza">Tardanza</option>
                    <option value="falta">Falta</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 rounded hover:bg-blue-600 transition">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Historial -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold">📅 Historial de Asistencia</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Fecha</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tipo</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Hora</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Estado</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Nota</th>
                    </tr>
                </thead>
                <tbody id="tablaHistorial">
                    <?php if(empty($historial)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>No hay registros</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($historial as $registro): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-3">
                                    <span class="font-semibold"><?php echo date('d/m/Y', strtotime($registro['fecha'])); ?></span>
                                </td>
                                <td class="px-6 py-3">
                                    <?php
                                    $tipoIcons = [
                                        'entrada' => '📥',
                                        'salida' => '📤',
                                        'refrigerio1_inicio' => '🍽️ Ref1 Inicio',
                                        'refrigerio1_fin' => '🍽️ Ref1 Fin',
                                        'refrigerio2_inicio' => '🍽️ Ref2 Inicio',
                                        'refrigerio2_fin' => '🍽️ Ref2 Fin',
                                        'refrigerio3_inicio' => '🍽️ Ref3 Inicio',
                                        'refrigerio3_fin' => '🍽️ Ref3 Fin',
                                    ];
                                    echo $tipoIcons[$registro['tipo']] ?? $registro['tipo'];
                                    ?>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-sm"><?php echo substr($registro['hora'], 0, 5); ?></span>
                                </td>
                                <td class="px-6 py-3">
                                    <?php
                                    $estadoClasses = [
                                        'normal' => 'bg-green-100 text-green-800',
                                        'tardanza' => 'bg-orange-100 text-orange-800',
                                        'falta' => 'bg-red-100 text-red-800',
                                        'puntual' => 'bg-green-100 text-green-800',
                                        'ok' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $estado = $registro['estado'] ?? 'ok';
                                    $clase = $estadoClasses[$estado] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $clase; ?>">
                                        <?php echo ucfirst($estado ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($registro['nota'] ?? '-'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="?r=asistencia/marcar" class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3 px-8 rounded-lg hover:shadow-lg transition">
            <i class="fas fa-hand-paper mr-2"></i> Marcar Asistencia Ahora
        </a>
    </div>

</div>

</body>
</html>
