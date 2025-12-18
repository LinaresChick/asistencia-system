<?php
// views/admin/dashboard.php
// Variables esperadas: $registros (array)
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard - Asistencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;}</style>
</head>
<body class="bg-slate-50 p-6">

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Panel - Asistencias</h1>
                <p class="text-sm text-slate-500 mt-1">Resumen y actividad reciente (<?php echo htmlspecialchars($fecha ?? date('Y-m-d')); ?>)</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="r" value="admin/dashboard">
                    <label class="text-sm text-slate-600">Fecha:</label>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha ?? date('Y-m-d')) ?>" class="px-3 py-2 border rounded-lg text-sm">
                    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-300">Filtrar</button>
                </form>
                <a href="?r=admin/asistencias" class="inline-flex items-center gap-2 px-4 py-2 bg-white border rounded-lg shadow-sm hover:shadow-md">
                    <i class="fas fa-list text-slate-600"></i>
                    Ver historial
                </a>
                <a href="?r=export/index" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                    <i class="fas fa-file-excel"></i>
                    Exportar
                </a>
                <button onclick="location.reload()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>

        <!-- Cards -->
        <?php
            $total = count($registros ?? []);
            $normal = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='normal'));
            $tardanza = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='tardanza'));
            $falta = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='falta'));
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-slate-500">Total registros</p>
                <p class="text-2xl font-bold text-slate-800"><?= $total ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-green-600 font-semibold">Normal</p>
                <p class="text-2xl font-bold text-slate-800"><?= $normal ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-amber-600 font-semibold">Tardanza</p>
                <p class="text-2xl font-bold text-slate-800"><?= $tardanza ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-red-600 font-semibold">Falta</p>
                <p class="text-2xl font-bold text-slate-800"><?= $falta ?></p>
            </div>
        </div>

        <!-- Recent table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-slate-800">Actividad reciente</h2>
                <p class="text-sm text-slate-500">Últimas entradas y salidas registradas en <?= htmlspecialchars($fecha ?? date('Y-m-d')) ?></p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto min-w-[800px]">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="p-3 text-left">#</th>
                            <th class="p-3 text-left">Empleado</th>
                            <th class="p-3 text-left">DNI</th>
                            <th class="p-3 text-left">Tipo</th>
                            <th class="p-3 text-left">Hora</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Ubicación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if(empty($registros)): ?>
                            <tr><td class="p-4" colspan="7">No hay registros para hoy.</td></tr>
                        <?php else: ?>
                            <?php foreach(array_slice($registros,0,50) as $i => $r): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 align-top"><?= $i+1 ?></td>
                                    <td class="p-3 align-top font-medium"><?= htmlspecialchars(trim(($r['nombres'] ?? '') . ' ' . ($r['apellidos'] ?? ''))) ?></td>
                                    <td class="p-3 align-top font-mono text-sm"><?= htmlspecialchars($r['dni'] ?? '') ?></td>
                                    <td class="p-3 align-top text-sm text-slate-700"><?= htmlspecialchars($r['tipo'] ?? '') ?></td>
                                    <td class="p-3 align-top font-semibold"><?= htmlspecialchars($r['hora'] ?? '') ?></td>
                                    <td class="p-3 align-top">
                                        <?php $st = $r['estado'] ?? ''; ?>
                                        <?php if($st === 'normal'): ?>
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Normal</span>
                                        <?php elseif($st === 'tardanza'): ?>
                                            <span class="px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded-full">Tardanza</span>
                                        <?php elseif($st === 'falta'): ?>
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Falta</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded-full"><?= htmlspecialchars($st) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 align-top">
                                        <?php if(!empty($r['lat']) && !empty($r['lng'])): ?>
                                            <a target="_blank" class="inline-flex items-center gap-2 px-2 py-1 bg-blue-50 text-blue-700 rounded" href="https://www.google.com/maps?q=<?= urlencode($r['lat'].','.$r['lng']) ?>">
                                                <i class="fas fa-map-marker-alt"></i> Ver
                                            </a>
                                        <?php else: ?>
                                            <span class="text-sm text-slate-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div class="text-sm text-slate-500">Mostrando <?= min(50, $total) ?> de <?= $total ?> registros</div>
                <div class="flex items-center gap-2">
                    <a href="?r=admin/asistencias" class="px-3 py-2 bg-white border rounded hover:bg-slate-50">Ir al reporte completo</a>
                    <form method="POST" action="?r=admin/asistencias" class="inline">
                        <input type="hidden" name="action" value="export_csv">
                        <button type="submit" class="px-3 py-2 bg-emerald-600 text-white rounded">Exportar CSV</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
