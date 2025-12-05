<?php
// views/admin/asistencias.php
// Variables esperadas desde el controlador:
// $registros (array), $startDate, $endDate, $hora_inicio, $hora_fin, $tolerancia
?>
<h2 class="text-2xl font-bold mb-4">Historial de Asistencias</h2>

<form method="POST" class="bg-white shadow-md rounded-lg p-6 mb-6 border">
    <h3 class="text-xl font-semibold mb-4">Filtrar por rango de fechas</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="font-semibold">Año:</label>
            <input type="number" name="anio"
                value="<?php echo htmlspecialchars(date('Y')); ?>" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Mes inicio:</label>
            <input type="number" name="mes_inicio" min="1" max="12" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Día inicio:</label>
            <input type="number" name="dia_inicio" min="1" max="31" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Mes fin:</label>
            <input type="number" name="mes_fin" min="1" max="12" required
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Día fin:</label>
            <input type="number" name="dia_fin" min="1" max="31" required
                class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <hr class="my-4">

    <h3 class="text-xl font-semibold mb-4">Rango de Hora</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="font-semibold">Hora inicio:</label>
            <input type="time" name="hora_inicio"
                value="<?= htmlspecialchars($hora_inicio ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Hora fin:</label>
            <input type="time" name="hora_fin"
                value="<?= htmlspecialchars($hora_fin ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Tolerancia (minutos):</label>
            <input type="number" name="tolerancia" min="0" max="120"
                value="<?= htmlspecialchars($tolerancia ?? 0) ?>"
                class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <hr class="my-4">

    <h3 class="text-xl font-semibold mb-4">Horarios de Refrigerios (opcional)</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="font-semibold">Refrigerio 1 inicio:</label>
            <input type="time" name="ref1_inicio" value="<?= htmlspecialchars($ref1_inicio ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Refrigerio 1 fin:</label>
            <input type="time" name="ref1_fin" value="<?= htmlspecialchars($ref1_fin ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="text-gray-600 text-sm flex items-center">
            (Opcional)
        </div>

        <div>
            <label class="font-semibold">Refrigerio 2 inicio:</label>
            <input type="time" name="ref2_inicio" value="<?= htmlspecialchars($ref2_inicio ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Refrigerio 2 fin:</label>
            <input type="time" name="ref2_fin" value="<?= htmlspecialchars($ref2_fin ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div></div>

        <div>
            <label class="font-semibold">Refrigerio 3 inicio:</label>
            <input type="time" name="ref3_inicio" value="<?= htmlspecialchars($ref3_inicio ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="font-semibold">Refrigerio 3 fin:</label>
            <input type="time" name="ref3_fin" value="<?= htmlspecialchars($ref3_fin ?? '') ?>"
                class="w-full border rounded px-3 py-2">
        </div>

        <div></div>
    </div>

    <button type="submit" class="mt-5 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Filtrar
    </button>
</form>

<?php if (!empty($startDate) && !empty($endDate)): ?>
<div class="mb-4 bg-gray-100 p-4 rounded border">
    <p class="text-gray-800">
        <strong>Mostrando desde:</strong> <?= htmlspecialchars($startDate) ?>
        <strong>hasta:</strong> <?= htmlspecialchars($endDate) ?><br>

        <?php if (!empty($hora_inicio) && !empty($hora_fin)): ?>
            <strong>Hora inicio:</strong> <?= htmlspecialchars($hora_inicio) ?><br>
            <strong>Hora fin:</strong> <?= htmlspecialchars($hora_fin) ?><br>
            <strong>Tolerancia:</strong> <?= htmlspecialchars($tolerancia) ?> min<br>
        <?php endif; ?>

        <?php if (!empty($ref1_inicio) && !empty($ref1_fin)): ?>
            <strong>Refrigerio 1:</strong>
            <?= htmlspecialchars($ref1_inicio) ?> -
            <?= htmlspecialchars($ref1_fin) ?><br>
        <?php endif; ?>

        <?php if (!empty($ref2_inicio) && !empty($ref2_fin)): ?>
            <strong>Refrigerio 2:</strong>
            <?= htmlspecialchars($ref2_inicio) ?> -
            <?= htmlspecialchars($ref2_fin) ?><br>
        <?php endif; ?>

        <?php if (!empty($ref3_inicio) && !empty($ref3_fin)): ?>
            <strong>Refrigerio 3:</strong>
            <?= htmlspecialchars($ref3_inicio) ?> -
            <?= htmlspecialchars($ref3_fin) ?><br>
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>

<div class="overflow-x-auto">
    <table class="w-full border-collapse bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="p-3">#</th>
                <th class="p-3">DNI</th>
                <th class="p-3">Nombre</th>
                <th class="p-3">Entrada</th>
                <th class="p-3">Salida</th>
                <th class="p-3">R1 Inicio</th>
                <th class="p-3">R1 Fin</th>
                <th class="p-3">R2 Inicio</th>
                <th class="p-3">R2 Fin</th>
                <th class="p-3">R3 Inicio</th>
                <th class="p-3">R3 Fin</th>
                <th class="p-3">Estado Entrada</th>
                <th class="p-3">Estado Salida</th>
                <th class="p-3">Lat, Lon Entrada</th>
                <th class="p-3">Lat, Lon Salida</th>
            </tr>
        </thead>

        <tbody>
        <?php
        // Agrupar registros por DNI + fecha
        $agrupados = [];
        foreach($registros as $r){
            $key = $r['dni'] . '_' . $r['fecha'];
            if(!isset($agrupados[$key])){
                $agrupados[$key] = [
                    'dni' => $r['dni'],
                    'nombres' => $r['nombres'] ?? '',
                    'apellidos' => $r['apellidos'] ?? '',
                    'entrada' => null,
                    'salida' => null,
                    'refrigerio1_inicio' => null,
                    'refrigerio1_fin' => null,
                    'refrigerio2_inicio' => null,
                    'refrigerio2_fin' => null,
                    'refrigerio3_inicio' => null,
                    'refrigerio3_fin' => null
                ];
            }

            $t = $r['tipo'];
            if($t === 'entrada') $agrupados[$key]['entrada'] = $r;
            elseif($t === 'salida') $agrupados[$key]['salida'] = $r;
            elseif($t === 'refrigerio1_inicio') $agrupados[$key]['refrigerio1_inicio'] = $r;
            elseif($t === 'refrigerio1_fin') $agrupados[$key]['refrigerio1_fin'] = $r;
            elseif($t === 'refrigerio2_inicio') $agrupados[$key]['refrigerio2_inicio'] = $r;
            elseif($t === 'refrigerio2_fin') $agrupados[$key]['refrigerio2_fin'] = $r;
            elseif($t === 'refrigerio3_inicio') $agrupados[$key]['refrigerio3_inicio'] = $r;
            elseif($t === 'refrigerio3_fin') $agrupados[$key]['refrigerio3_fin'] = $r;
        }

        $i = 1;
        foreach($agrupados as $row):
            $entrada = $row['entrada'];
            $salida = $row['salida'];
        ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 text-center"><?= $i++ ?></td>
                <td class="p-3"><?= htmlspecialchars($row['dni']) ?></td>
                <td class="p-3"><?= htmlspecialchars(trim($row['nombres'] . ' ' . $row['apellidos'])) ?></td>

                <td class="p-3"><?= $entrada['hora'] ?? 'N/A' ?></td>
                <td class="p-3"><?= $salida['hora'] ?? 'N/A' ?></td>

                <td class="p-3"><?= $row['refrigerio1_inicio']['hora'] ?? 'N/A' ?></td>
                <td class="p-3"><?= $row['refrigerio1_fin']['hora'] ?? 'N/A' ?></td>

                <td class="p-3"><?= $row['refrigerio2_inicio']['hora'] ?? 'N/A' ?></td>
                <td class="p-3"><?= $row['refrigerio2_fin']['hora'] ?? 'N/A' ?></td>

                <td class="p-3"><?= $row['refrigerio3_inicio']['hora'] ?? 'N/A' ?></td>
                <td class="p-3"><?= $row['refrigerio3_fin']['hora'] ?? 'N/A' ?></td>

                <td class="p-3">
                    <span class="px-2 py-1 rounded text-sm
                        <?= match($entrada['estado'] ?? '') {
                            'normal' => 'bg-green-100 text-green-700',
                            'tardanza' => 'bg-yellow-100 text-yellow-700',
                            'falta' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-200 text-gray-700',
                        } ?>">
                        <?= htmlspecialchars($entrada['estado'] ?? '') ?>
                    </span>
                </td>

                <td class="p-3">
                    <span class="px-2 py-1 rounded text-sm
                        <?= match($salida['estado'] ?? '') {
                            'normal' => 'bg-green-100 text-green-700',
                            'tardanza' => 'bg-yellow-100 text-yellow-700',
                            'falta' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-200 text-gray-700',
                        } ?>">
                        <?= htmlspecialchars($salida['estado'] ?? '') ?>
                    </span>
                </td>

                <td class="p-3"><?= ($entrada['lat'] && $entrada['lng']) ? htmlspecialchars($entrada['lat'] . ', ' . $entrada['lng']) : 'N/A' ?></td>
                <td class="p-3"><?= ($salida['lat'] && $salida['lng']) ? htmlspecialchars($salida['lat'] . ', ' . $salida['lng']) : 'N/A' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
