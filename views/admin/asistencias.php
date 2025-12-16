<?php
// views/admin/asistencias.php
// Variables esperadas desde el controlador:
// $registros (array), $startDate, $endDate, $hora_inicio, $hora_fin, $tolerancia
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Asistencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-sA+4Yk5fB0b3k9f+g0Yv5wY1bYxQY2fZ6k9gY1wQwRo=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o9N1j7k9nFqYq0G8z3b1z2FZ7x1s9sY2l3r9g1wQwRo=" crossorigin=""></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); }
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .input-focus:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .refrigerio-badge { position: relative; }
        .refrigerio-badge::before { content: ''; position: absolute; left: 50%; top: -4px; transform: translateX(-50%); width: 8px; height: 8px; border-radius: 50%; }
        .ref1-badge::before { background: #3b82f6; } /* Azul */
        .ref2-badge::before { background: #10b981; } /* Verde */
        .ref3-badge::before { background: #8b5cf6; } /* Púrpura */
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="header-gradient w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Historial de Asistencias</h1>
                    <p class="text-gray-500 text-sm mt-1">Sistema de gestión de registro de personal</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow flex items-center gap-2">
                    <i class="fas fa-file-export text-blue-500"></i>
                    <span>Exportar PDF</span>
                </button>
                <button class="px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    <span>Actualizar</span>
                </button>
            </div>
        </div>

        <!-- Horario Actual Section -->
        <?php if(!empty($horario_actual)): ?>
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl shadow-lg border-2 border-amber-200 p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-amber-900 mb-2">
                        <i class="fas fa-hourglass-start text-amber-600 mr-2"></i>
                        Horario Actual Configurado
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3">
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase">Entrada</p>
                            <p class="text-lg font-bold text-amber-900"><?= htmlspecialchars($horario_actual['entrada'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase">Salida</p>
                            <p class="text-lg font-bold text-amber-900"><?= htmlspecialchars($horario_actual['salida'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase">Ref 1</p>
                            <p class="text-sm font-bold text-amber-900">
                                <?= htmlspecialchars($horario_actual['ref1_inicio'] ?? '--') ?> - <?= htmlspecialchars($horario_actual['ref1_fin'] ?? '--') ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase">Ref 2</p>
                            <p class="text-sm font-bold text-amber-900">
                                <?= htmlspecialchars($horario_actual['ref2_inicio'] ?? '--') ?> - <?= htmlspecialchars($horario_actual['ref2_fin'] ?? '--') ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase">Ref 3</p>
                            <p class="text-sm font-bold text-amber-900">
                                <?= htmlspecialchars($horario_actual['ref3_inicio'] ?? '--') ?> - <?= htmlspecialchars($horario_actual['ref3_fin'] ?? '--') ?>
                            </p>
                        </div>

                        <div class="col-span-3">
                            <p class="text-xs font-semibold text-amber-700 uppercase">Vigencia</p>
                            <p class="text-sm font-bold text-amber-900">
                                Desde: <?= htmlspecialchars($horario_actual['vigente_desde'] ?? '--') ?> &nbsp;•&nbsp; Hasta: <?= htmlspecialchars($horario_actual['vigente_hasta'] ?? '--') ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <form method="POST" class="flex-shrink-0" onsubmit="return confirm('¿Eliminar el horario actual? Esto permitirá crear un nuevo horario.');">
                    <input type="hidden" name="csrf" value="<?= $csrf ?? '' ?>">
                    <input type="hidden" name="action" value="delete_schedule">
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Eliminar Horario
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Registros</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= count($registros ?? []) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-database text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Periodo Actual</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            <?= !empty($startDate) ? htmlspecialchars($startDate) : '--' ?> 
                            <span class="text-gray-400">→</span> 
                            <?= !empty($endDate) ? htmlspecialchars($endDate) : '--' ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Horario Laboral</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            <?= !empty($hora_inicio) ? htmlspecialchars($hora_inicio) : '08:00' ?> - 
                            <?= !empty($hora_fin) ? htmlspecialchars($hora_fin) : '17:00' ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Tolerancia</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            <?= htmlspecialchars($tolerancia ?? 10) ?> min
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Left Panel - Filters -->
            <div class="lg:col-span-1">
                <div class="card-gradient rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    
                    <!-- Filters Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                        <h2 class="text-xl font-bold text-white mb-1">
                            <i class="fas fa-filter mr-2"></i>
                            Filtros Avanzados
                        </h2>
                        <p class="text-blue-100 text-sm">Configure los parámetros de búsqueda</p>
                    </div>

                    <!-- Filters Form -->
                    <form method="POST" class="p-6 space-y-6">
                        
                        <!-- Date Range -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                Rango de Fechas
                            </h3>
                            
                            <!-- Start Date (single date input) -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <i class="fas fa-calendar-plus text-blue-500 mr-1"></i>
                                    Fecha Inicio
                                </label>
                                <input type="date" name="startDate" required
                                    value="<?= htmlspecialchars($_POST['startDate'] ?? ($startDate ?? date('Y-m-d'))) ?>"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm input-focus transition">
                            </div>

                            <!-- End Date (single date input) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <i class="fas fa-calendar-minus text-blue-500 mr-1"></i>
                                    Fecha Fin
                                </label>
                                <input type="date" name="endDate" required
                                    value="<?= htmlspecialchars($_POST['endDate'] ?? ($endDate ?? date('Y-m-d'))) ?>"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm input-focus transition">
                            </div>
                        </div>

                        <!-- Time Range -->
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                Horario Laboral
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">
                                        <i class="fas fa-sign-in-alt text-purple-500 mr-1"></i>
                                        Hora Inicio
                                    </label>
                                    <input type="time" name="hora_inicio"
                                        value="<?= htmlspecialchars($hora_inicio ?? '08:00') ?>"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm input-focus transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">
                                        <i class="fas fa-sign-out-alt text-purple-500 mr-1"></i>
                                        Hora Fin
                                    </label>
                                    <input type="time" name="hora_fin"
                                        value="<?= htmlspecialchars($hora_fin ?? '17:00') ?>"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm input-focus transition">
                                </div>
                            </div>
                        </div>

                        <!-- Tolerance -->
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                Tolerancia
                            </h3>
                            <label class="block text-xs font-medium text-gray-600 mb-2">
                                <i class="fas fa-hourglass-half text-orange-500 mr-1"></i>
                                Minutos permitidos
                            </label>
                            <div class="relative">
                                <input type="range" name="tolerancia" min="0" max="60" step="5"
                                    value="<?= htmlspecialchars($tolerancia ?? 10) ?>"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                    oninput="this.nextElementSibling.value = this.value + ' min'">
                                <output class="absolute -top-6 right-0 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded">
                                    <?= htmlspecialchars($tolerancia ?? 10) ?> min
                                </output>
                            </div>
                        </div>

                        <!-- Refrigerios Configuration -->
                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                Horarios de Refrigerios
                            </h3>
                            
                            <!-- Refrigerio 1 -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-100 text-blue-600 rounded-full text-xs mr-1">1</span>
                                    Refrigerio 1
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="time" name="ref1_inicio" 
                                        value="<?= htmlspecialchars($ref1_inicio ?? '10:00') ?>"
                                        placeholder="Inicio"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                    <input type="time" name="ref1_fin" 
                                        value="<?= htmlspecialchars($ref1_fin ?? '10:30') ?>"
                                        placeholder="Fin"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                </div>
                            </div>
                            
                            <!-- Refrigerio 2 -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <span class="inline-flex items-center justify-center w-5 h-5 bg-green-100 text-green-600 rounded-full text-xs mr-1">2</span>
                                    Refrigerio 2
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="time" name="ref2_inicio" 
                                        value="<?= htmlspecialchars($ref2_inicio ?? '12:00') ?>"
                                        placeholder="Inicio"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                    <input type="time" name="ref2_fin" 
                                        value="<?= htmlspecialchars($ref2_fin ?? '13:00') ?>"
                                        placeholder="Fin"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                </div>
                            </div>
                            
                            <!-- Refrigerio 3 -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">
                                    <span class="inline-flex items-center justify-center w-5 h-5 bg-purple-100 text-purple-600 rounded-full text-xs mr-1">3</span>
                                    Refrigerio 3
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="time" name="ref3_inicio" 
                                        value="<?= htmlspecialchars($ref3_inicio ?? '15:30') ?>"
                                        placeholder="Inicio"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                    <input type="time" name="ref3_fin" 
                                        value="<?= htmlspecialchars($ref3_fin ?? '16:00') ?>"
                                        placeholder="Fin"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm input-focus transition">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                            <i class="fas fa-search"></i>
                            Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Panel - Results -->
            <div class="lg:col-span-3">
                <div class="card-gradient rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    
                    <!-- Results Header -->
                    <div class="bg-white p-6 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">
                                    <i class="fas fa-list-check text-blue-500 mr-2"></i>
                                    Registros de Asistencia
                                </h2>
                                <p class="text-gray-500 text-sm mt-1">
                                    <?php if (!empty($startDate)): ?>
                                        Mostrando registros del <span class="font-semibold text-blue-600"><?= htmlspecialchars($startDate) ?></span> 
                                        al <span class="font-semibold text-blue-600"><?= htmlspecialchars($endDate) ?></span>
                                    <?php else: ?>
                                        Seleccione un rango de fechas para ver los registros
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <!-- Status Badges -->
                            <div class="flex flex-wrap gap-2">
                                <div class="px-3 py-1.5 bg-green-50 border border-green-100 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-xs font-semibold text-green-700">
                                            Normal: <?= count(array_filter($registros ?? [], fn($r) => ($r['estado'] ?? '') === 'normal')) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="px-3 py-1.5 bg-yellow-50 border border-yellow-100 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        <span class="text-xs font-semibold text-yellow-700">
                                            Tardanza: <?= count(array_filter($registros ?? [], fn($r) => ($r['estado'] ?? '') === 'tardanza')) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="px-3 py-1.5 bg-red-50 border border-red-100 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                        <span class="text-xs font-semibold text-red-700">
                                            Falta: <?= count(array_filter($registros ?? [], fn($r) => ($r['estado'] ?? '') === 'falta')) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Content -->
                    <div class="p-6">
                        <?php if (!empty($registros)): ?>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full min-w-[1000px]">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-user mr-1"></i> Empleado
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-id-card mr-1"></i> DNI
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Entrada
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-sign-out-alt mr-1"></i> Salida
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-coffee mr-1"></i> Refrigerios
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-clock mr-1"></i> Estado
                                        </th>
                                        <th class="p-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Ubicación
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                <?php
                                $agrupados = [];
                                foreach($registros as $r){
                                    $key = $r['dni'] . '_' . $r['fecha'];
                                    if(!isset($agrupados[$key])){
                                        $agrupados[$key] = [
                                            'dni' => $r['dni'],
                                            'nombres' => $r['nombres'] ?? '',
                                            'apellidos' => $r['apellidos'] ?? '',
                                            'fecha' => $r['fecha'],
                                            'entrada' => null,
                                            'salida' => null,
                                            'estado_entrada' => null,
                                            'estado_salida' => null,
                                            'refrigerios' => [
                                                'ref1' => ['inicio' => null, 'fin' => null],
                                                'ref2' => ['inicio' => null, 'fin' => null],
                                                'ref3' => ['inicio' => null, 'fin' => null]
                                            ]
                                        ];
                                    }
                                    
                                    $t = $r['tipo'];
                                    if($t === 'entrada'){
                                        $agrupados[$key]['entrada'] = $r['hora'];
                                        $agrupados[$key]['estado_entrada'] = $r['estado'] ?? '';
                                        $agrupados[$key]['lat_entrada'] = $r['lat'] ?? '';
                                        $agrupados[$key]['lng_entrada'] = $r['lng'] ?? '';
                                    }elseif($t === 'salida'){
                                        $agrupados[$key]['salida'] = $r['hora'];
                                        $agrupados[$key]['estado_salida'] = $r['estado'] ?? '';
                                        $agrupados[$key]['lat_salida'] = $r['lat'] ?? '';
                                        $agrupados[$key]['lng_salida'] = $r['lng'] ?? '';
                                    }elseif($t === 'refrigerio1_inicio'){
                                        $agrupados[$key]['refrigerios']['ref1']['inicio'] = $r['hora'];
                                    }elseif($t === 'refrigerio1_fin'){
                                        $agrupados[$key]['refrigerios']['ref1']['fin'] = $r['hora'];
                                    }elseif($t === 'refrigerio2_inicio'){
                                        $agrupados[$key]['refrigerios']['ref2']['inicio'] = $r['hora'];
                                    }elseif($t === 'refrigerio2_fin'){
                                        $agrupados[$key]['refrigerios']['ref2']['fin'] = $r['hora'];
                                    }elseif($t === 'refrigerio3_inicio'){
                                        $agrupados[$key]['refrigerios']['ref3']['inicio'] = $r['hora'];
                                    }elseif($t === 'refrigerio3_fin'){
                                        $agrupados[$key]['refrigerios']['ref3']['fin'] = $r['hora'];
                                    }
                                }

                                $i = 1;
                                foreach($agrupados as $row):
                                    $nombre_completo = trim($row['nombres'] . ' ' . $row['apellidos']);
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                                    <?= substr($row['nombres'], 0, 1) ?>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($nombre_completo) ?></p>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($row['fecha']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="font-mono text-sm text-gray-700"><?= htmlspecialchars($row['dni']) ?></span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900"><?= $row['entrada'] ?? '--:--' ?></span>
                                                <?php if($row['estado_entrada']): ?>
                                                <span class="text-xs px-2 py-1 rounded-full w-fit mt-1 <?= match($row['estado_entrada']) {
                                                    'normal' => 'bg-green-100 text-green-800',
                                                    'tardanza' => 'bg-yellow-100 text-yellow-800',
                                                    'falta' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } ?>">
                                                    <?= htmlspecialchars($row['estado_entrada']) ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900"><?= $row['salida'] ?? '--:--' ?></span>
                                                <?php if($row['estado_salida']): ?>
                                                <span class="text-xs px-2 py-1 rounded-full w-fit mt-1 <?= match($row['estado_salida']) {
                                                    'normal' => 'bg-green-100 text-green-800',
                                                    'tardanza' => 'bg-yellow-100 text-yellow-800',
                                                    'falta' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } ?>">
                                                    <?= htmlspecialchars($row['estado_salida']) ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="space-y-2">
                                                <!-- Refrigerio 1 -->
                                                <div class="flex items-center gap-2 refrigerio-badge ref1-badge">
                                                    <span class="text-xs font-medium text-gray-500">R1:</span>
                                                    <?php if($row['refrigerios']['ref1']['inicio'] || $row['refrigerios']['ref1']['fin']): ?>
                                                    <div class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg">
                                                        <?= $row['refrigerios']['ref1']['inicio'] ?? '--:--' ?> 
                                                        <span class="mx-1 text-gray-400">→</span>
                                                        <?= $row['refrigerios']['ref1']['fin'] ?? '--:--' ?>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-xs text-gray-400">No registrado</span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Refrigerio 2 -->
                                                <div class="flex items-center gap-2 refrigerio-badge ref2-badge">
                                                    <span class="text-xs font-medium text-gray-500">R2:</span>
                                                    <?php if($row['refrigerios']['ref2']['inicio'] || $row['refrigerios']['ref2']['fin']): ?>
                                                    <div class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-lg">
                                                        <?= $row['refrigerios']['ref2']['inicio'] ?? '--:--' ?> 
                                                        <span class="mx-1 text-gray-400">→</span>
                                                        <?= $row['refrigerios']['ref2']['fin'] ?? '--:--' ?>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-xs text-gray-400">No registrado</span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Refrigerio 3 -->
                                                <div class="flex items-center gap-2 refrigerio-badge ref3-badge">
                                                    <span class="text-xs font-medium text-gray-500">R3:</span>
                                                    <?php if($row['refrigerios']['ref3']['inicio'] || $row['refrigerios']['ref3']['fin']): ?>
                                                    <div class="text-xs bg-purple-50 text-purple-700 px-2 py-1 rounded-lg">
                                                        <?= $row['refrigerios']['ref3']['inicio'] ?? '--:--' ?> 
                                                        <span class="mx-1 text-gray-400">→</span>
                                                        <?= $row['refrigerios']['ref3']['fin'] ?? '--:--' ?>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-xs text-gray-400">No registrado</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <?php 
                                            $estado_final = 'Pendiente';
                                            $color = 'gray';
                                            if($row['estado_entrada'] == 'falta' || $row['estado_salida'] == 'falta'){
                                                $estado_final = 'Inasistencia';
                                                $color = 'red';
                                            }elseif($row['estado_entrada'] == 'tardanza' || $row['estado_salida'] == 'tardanza'){
                                                $estado_final = 'Tardanza';
                                                $color = 'yellow';
                                            }elseif($row['estado_entrada'] == 'normal' && $row['estado_salida'] == 'normal'){
                                                $estado_final = 'Completo';
                                                $color = 'green';
                                            }
                                            ?>
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-<?= $color ?>-500"></div>
                                                <span class="text-sm font-medium text-gray-900"><?= $estado_final ?></span>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex flex-col gap-1">
                                                <?php if(!empty($row['lat_entrada']) && !empty($row['lng_entrada'])): ?>
                                                <button class="location-btn text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition flex items-center gap-1"
                                                    data-type="entrada"
                                                    data-lat="<?= htmlspecialchars($row['lat_entrada']) ?>"
                                                    data-lng="<?= htmlspecialchars($row['lng_entrada']) ?>"
                                                    data-time="<?= htmlspecialchars($row['entrada'] ?? '') ?>"
                                                    data-name="<?= htmlspecialchars($nombre_completo) ?>"
                                                    data-dni="<?= htmlspecialchars($row['dni']) ?>">
                                                    <i class="fas fa-map-pin"></i>
                                                    Entrada
                                                </button>
                                                <?php endif; ?>
                                                <?php if(!empty($row['lat_salida']) && !empty($row['lng_salida'])): ?>
                                                <button class="location-btn text-xs px-2 py-1 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition flex items-center gap-1"
                                                    data-type="salida"
                                                    data-lat="<?= htmlspecialchars($row['lat_salida']) ?>"
                                                    data-lng="<?= htmlspecialchars($row['lng_salida']) ?>"
                                                    data-time="<?= htmlspecialchars($row['salida'] ?? '') ?>"
                                                    data-name="<?= htmlspecialchars($nombre_completo) ?>"
                                                    data-dni="<?= htmlspecialchars($row['dni']) ?>">
                                                    <i class="fas fa-map-pin"></i>
                                                    Salida
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                $i++;
                                endforeach; 
                                ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Mostrando <?= count($agrupados) ?> registros de <?= count($registros) ?> totales
                            </div>
                            <div class="flex items-center gap-1">
                                <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="w-10 h-10 rounded-lg bg-blue-600 text-white font-medium">1</button>
                                <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50">2</button>
                                <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50">3</button>
                                <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay registros para mostrar</h3>
                            <p class="text-gray-500 max-w-md mx-auto mb-8">
                                Seleccione un rango de fechas y aplique los filtros para ver los registros de asistencia.
                            </p>
                            <div class="flex justify-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                                <div class="w-3 h-3 rounded-full bg-blue-400 animate-pulse delay-75"></div>
                                <div class="w-3 h-3 rounded-full bg-blue-300 animate-pulse delay-150"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize range slider output
        document.addEventListener('DOMContentLoaded', function() {
            const toleranceInput = document.querySelector('input[name="tolerancia"]');
            if(toleranceInput) {
                const toleranceOutput = toleranceInput.nextElementSibling;
                toleranceInput.addEventListener('input', function() {
                    toleranceOutput.value = this.value + ' min';
                });
            }
        });
    </script>

    <!-- Map Modal -->
    <div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
                <div>
                    <h3 id="mapModalTitle" class="font-bold text-lg">Ubicación</h3>
                    <p id="mapModalSubtitle" class="text-sm text-gray-500">Detalle</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="openInMaps" href="#" target="_blank" class="text-sm text-blue-600 underline hidden">Abrir en Google Maps</a>
                    <button id="closeMapModal" class="px-3 py-2 bg-gray-100 rounded-md">Cerrar</button>
                </div>
            </div>
            <div class="p-4">
                <div id="mapContainer" style="height:320px;" class="rounded"></div>
                <div class="mt-3 text-sm text-gray-700">
                    <div id="mapDetails">Lat: -- · Lng: -- · Hora: --</div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        let map, marker;
        const modal = document.getElementById('mapModal');
        const closeBtn = document.getElementById('closeMapModal');
        const titleEl = document.getElementById('mapModalTitle');
        const subtitleEl = document.getElementById('mapModalSubtitle');
        const detailsEl = document.getElementById('mapDetails');
        const openInMaps = document.getElementById('openInMaps');

        const initMap = (lat, lng) => {
            if(!map){
                map = L.map('mapContainer').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
            }
        };

        const openModal = ({lat,lng,type,name,dni,time}) => {
            titleEl.textContent = `${type === 'entrada' ? 'Entrada' : 'Salida'} · ${name}`;
            subtitleEl.textContent = `DNI: ${dni}`;
            detailsEl.textContent = `Lat: ${parseFloat(lat).toFixed(6)} · Lng: ${parseFloat(lng).toFixed(6)} · Hora: ${time || '--'}`;
            openInMaps.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lat + ',' + lng)}`;
            openInMaps.classList.remove('hidden');
            modal.classList.remove('hidden');
            // init map after modal visible
            setTimeout(()=>{ initMap(parseFloat(lat), parseFloat(lng)); map.invalidateSize(); }, 80);
        };

        document.querySelectorAll('.location-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const lat = btn.getAttribute('data-lat');
                const lng = btn.getAttribute('data-lng');
                const type = btn.getAttribute('data-type');
                const time = btn.getAttribute('data-time');
                const name = btn.getAttribute('data-name');
                const dni = btn.getAttribute('data-dni');
                if(lat && lng){
                    openModal({lat,lng,type,name,dni,time});
                }
            });
        });

        closeBtn.addEventListener('click', ()=>{ modal.classList.add('hidden'); });
        // close on backdrop click
        modal.addEventListener('click', (e)=>{ if(e.target === modal) modal.classList.add('hidden'); });
    })();
    </script>

</body>
</html>