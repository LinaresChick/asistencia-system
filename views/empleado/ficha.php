<?php
// views/empleado/ficha.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Ficha - Portal Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Portal Empleado</h1>
            <p class="text-sm text-blue-100">Mi Información</p>
        </div>
        <div class="flex gap-4">
            <a href="?r=empleado/dashboard" class="px-4 py-2 bg-blue-400 rounded hover:bg-blue-300 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <a href="?r=empleado/logout" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600 transition">
                <i class="fas fa-sign-out-alt mr-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<div class="max-w-2xl mx-auto p-6">

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-user text-4xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($empleado['nombres'] . ' ' . $empleado['apellidos']); ?></h2>
            <p class="text-gray-500 text-lg mt-1">DNI: <?php echo htmlspecialchars($empleado['dni']); ?></p>
        </div>

        <div class="space-y-6">
            <!-- Información General -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">📋 Información General</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">Nombre</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['nombres']); ?></p>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">Apellido</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['apellidos']); ?></p>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">DNI</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['dni']); ?></p>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">Cargo</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['cargo'] ?? 'N/A'); ?></p>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">Edad</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['edad'] ?? 'N/A'); ?></p>
                    </div>

                    <div>
                        <label class="block text-gray-500 text-sm font-semibold mb-1">ID Empleado</label>
                        <p class="text-gray-800 text-lg"><?php echo htmlspecialchars($empleado['id']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Información de Horario -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">🕐 Horario Laboral</h3>
                
                <div class="bg-blue-50 p-6 rounded-lg">
                    <p class="text-gray-600 text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        El horario se configura a nivel de administración.
                    </p>
                    <p class="text-gray-600 text-center text-sm mt-2">
                        Consulta con tu supervisor para más detalles.
                    </p>
                </div>
            </div>

            <!-- Opciones -->
            <div class="flex flex-col gap-3">
                <a href="?r=empleado/dashboard" class="block w-full bg-blue-500 text-white font-bold py-3 rounded-lg hover:bg-blue-600 transition text-center">
                    <i class="fas fa-chart-bar mr-2"></i> Ver mi Asistencia
                </a>

                <a href="?r=asistencia/marcar" class="block w-full bg-green-500 text-white font-bold py-3 rounded-lg hover:bg-green-600 transition text-center">
                    <i class="fas fa-hand-paper mr-2"></i> Marcar Asistencia
                </a>

                <a href="?r=empleado/logout" class="block w-full bg-red-500 text-white font-bold py-3 rounded-lg hover:bg-red-600 transition text-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

</div>

</body>
</html>
