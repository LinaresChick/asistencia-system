<?php
// views/empleado/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Empleado - Asistencia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="w-full max-w-md">
    <div class="bg-white rounded-lg shadow-2xl p-8">
        <div class="text-center mb-8">
            <div class="inline-block w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-fingerprint text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Portal Empleado</h1>
            <p class="text-gray-500 mt-2">Consulta tu asistencia</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <p>❌ <?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">DNI</label>
                <input type="text" name="dni" placeholder="Ingresa tu DNI" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    autofocus>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                <input type="password" name="password" placeholder="Ingresa tu contraseña" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition">
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                <i class="fas fa-sign-in-alt mr-2"></i> Ingresar
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">¿Aún no tienes contraseña?</p>
            <p class="text-gray-600 text-sm">Contacta con Administración</p>
        </div>

        <hr class="my-6">

        <div class="text-center">
            <a href="?r=asistencia/marcar" class="text-blue-500 hover:text-blue-700 font-semibold">
                <i class="fas fa-hand-paper mr-1"></i> Marcar Asistencia
            </a>
        </div>
    </div>

    <div class="text-center text-white mt-6 text-sm">
        <p>Sistema de Asistencia v1.0</p>
    </div>
</div>

</body>
</html>
