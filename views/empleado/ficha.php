<?php
// views/empleado/ficha.php
// (esta vista no se usa por la API, pero la dejo por completitud)
?>
<div>
    <h3>Ficha</h3>
    <p>DNI: <?php echo htmlspecialchars($empleado['dni'] ?? ''); ?></p>
    <p>Nombre: <?php echo htmlspecialchars(($empleado['nombres'] ?? '') . ' ' . ($empleado['apellidos'] ?? '')); ?></p>
    <p>Cargo: <?php echo htmlspecialchars($empleado['cargo'] ?? ''); ?></p>
</div>
