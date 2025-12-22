<?php
// views/admin/dashboard.php
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Asistencias</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial;
}
</style>
</head>

<body class="bg-slate-50 min-h-screen p-4 sm:p-6">

<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                Panel de Asistencias
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Resumen del día <?= htmlspecialchars($fecha ?? date('Y-m-d')) ?>
            </p>
        </div>

        <!-- ACTIONS -->
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="r" value="admin/dashboard">
                <input type="date"
                       name="fecha"
                       value="<?= htmlspecialchars($fecha ?? date('Y-m-d')) ?>"
                       class="px-3 py-2 border rounded-lg text-sm">
                <button class="px-4 py-2 bg-slate-200 rounded-lg hover:bg-slate-300 text-sm">
                    Filtrar
                </button>
            </form>

            <a href="?r=admin/asistencias"
               class="px-4 py-2 bg-white border rounded-lg shadow-sm hover:shadow-md flex items-center gap-2 text-sm">
                <i class="fas fa-list"></i> Historial
            </a>

            <a href="?r=export/index"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 text-sm">
                <i class="fas fa-file-excel"></i> Exportar
            </a>

            <button onclick="location.reload()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 text-sm">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    <?php
    $total = count($registros ?? []);
    $normal = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='normal'));
    $tardanza = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='tardanza'));
    $falta = count(array_filter($registros ?? [], fn($r)=>($r['estado'] ?? '')==='falta'));
    ?>

    <!-- STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-slate-500">Total registros</p>
            <p class="text-3xl font-bold"><?= $total ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-green-600 font-semibold">Normal</p>
            <p class="text-3xl font-bold"><?= $normal ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-amber-600 font-semibold">Tardanza</p>
            <p class="text-3xl font-bold"><?= $tardanza ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <p class="text-sm text-red-600 font-semibold">Falta</p>
            <p class="text-3xl font-bold"><?= $falta ?></p>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-slate-800">Actividad reciente</h2>
            <p class="text-sm text-slate-500">
                Últimos movimientos del día
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-sm">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
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
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-400">
                            No hay registros
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach(array_slice($registros,0,50) as $i => $r): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3"><?= $i+1 ?></td>
                            <td class="p-3 font-medium">
                                <?= htmlspecialchars(trim(($r['nombres'] ?? '').' '.($r['apellidos'] ?? ''))) ?>
                            </td>
                            <td class="p-3 font-mono"><?= htmlspecialchars($r['dni'] ?? '') ?></td>
                            <td class="p-3"><?= htmlspecialchars($r['tipo'] ?? '') ?></td>
                            <td class="p-3 font-semibold"><?= htmlspecialchars($r['hora'] ?? '') ?></td>
                            <td class="p-3">
                                <?php
                                $st = $r['estado'] ?? '';
                                $colors = [
                                    'normal'=>'bg-green-100 text-green-700',
                                    'tardanza'=>'bg-amber-100 text-amber-700',
                                    'falta'=>'bg-red-100 text-red-700'
                                ];
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?= $colors[$st] ?? 'bg-slate-100' ?>">
                                    <?= ucfirst($st) ?>
                                </span>
                            </td>
                            <td class="p-3">
                                <?php if(!empty($r['lat']) && !empty($r['lng'])): ?>
                                    <a target="_blank"
                                       href="https://www.google.com/maps?q=<?= urlencode($r['lat'].','.$r['lng']) ?>"
                                       class="text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt"></i> Ver
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-slate-500">
                Mostrando <?= min(50,$total) ?> de <?= $total ?> registros
            </p>

            <div class="flex gap-2">
                <a href="?r=admin/asistencias"
                   class="px-4 py-2 border rounded-lg hover:bg-slate-50">
                    Reporte completo
                </a>
                <form method="POST" action="?r=admin/asistencias">
                    <input type="hidden" name="action" value="export_csv">
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Exportar CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>
