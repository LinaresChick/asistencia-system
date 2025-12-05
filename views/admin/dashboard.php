<?php
// views/admin/dashboard.php
?>
<h2>Panel - Asistencias de hoy (<?php echo date('Y-m-d'); ?>)</h2>
<table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;">
    <thead>
        <tr><th>#</th><th>DNI</th><th>Nombre</th><th>Tipo</th><th>Hora</th><th>Estado</th><th>Ubicación</th></tr>
    </thead>
    <tbody>
        <?php foreach($registros as $i => $r): ?>
            <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo htmlspecialchars($r['dni']); ?></td>
                <td><?php echo htmlspecialchars(($r['nombres'] ?? '') . ' ' . ($r['apellidos'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars($r['tipo']); ?></td>
                <td><?php echo htmlspecialchars($r['hora']); ?></td>
                <td><?php echo htmlspecialchars($r['estado']); ?></td>
                <td><?php echo ($r['lat'] && $r['lng']) ? htmlspecialchars($r['lat'].', '.$r['lng']) : 'N/A'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
