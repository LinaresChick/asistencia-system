<?php
// views/admin/config.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%); }
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
        .header-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .input-focus:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="header-gradient w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Configuración</h1>
                    <p class="text-gray-500 text-sm mt-1">Gestiona usuarios administradores</p>
                </div>
            </div>
            
            <a href="?r=admin/dashboard" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Volver</span>
            </a>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- LEFT: Editar Admin Actual -->
            <div class="card-gradient rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                    <h2 class="text-xl font-bold text-white mb-1">
                        <i class="fas fa-user-circle mr-2"></i>
                        Mi Perfil
                    </h2>
                    <p class="text-blue-100 text-sm">Actualiza tu información de administrador</p>
                </div>

                <!-- Form -->
                <form method="POST" action="?r=admin/admin_update" class="p-6 space-y-4">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-500 mr-1"></i>
                            Nombre de Usuario
                        </label>
                        <input type="text" name="username" required
                            placeholder="nuevo_usuario"
                            value="<?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?>"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card text-blue-500 mr-1"></i>
                            Nombre Completo
                        </label>
                        <input type="text" name="nombre"
                            placeholder="Juan Pérez"
                            value="<?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?>"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-blue-500 mr-1"></i>
                            Nueva Contraseña (opcional)
                        </label>
                        <input type="password" name="password"
                            placeholder="Dejar en blanco para mantener la actual"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                        <p class="text-xs text-gray-500 mt-1">Si no completas este campo, se mantendrá tu contraseña actual.</p>
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </form>
            </div>

            <!-- RIGHT: Agregar Nuevo Admin -->
            <div class="card-gradient rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
                    <h2 class="text-xl font-bold text-white mb-1">
                        <i class="fas fa-user-plus mr-2"></i>
                        Nuevo Administrador
                    </h2>
                    <p class="text-green-100 text-sm">Crea una nueva cuenta de administrador</p>
                </div>

                <!-- Form -->
                <form method="POST" action="?r=admin/admin_create" class="p-6 space-y-4">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-green-500 mr-1"></i>
                            Nombre de Usuario *
                        </label>
                        <input type="text" name="username" required
                            placeholder="nuevo_admin"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-id-card text-green-500 mr-1"></i>
                            Nombre Completo
                        </label>
                        <input type="text" name="nombre"
                            placeholder="Carlos García"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-green-500 mr-1"></i>
                            Contraseña *
                        </label>
                        <input type="password" name="password" required
                            placeholder="Contraseña segura"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm input-focus transition">
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus"></i>
                        Crear Administrador
                    </button>
                </form>
            </div>
        </div>

        <!-- Administradores Existentes -->
        <div class="card-gradient rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            
            <!-- Header -->
            <div class="bg-white p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-users text-purple-500 mr-2"></i>
                    Administradores del Sistema
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    Total: <span class="font-bold text-purple-600"><?= count($admins ?? []) ?></span> usuario(s)
                </p>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-hashtag mr-1"></i> ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> Usuario
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-id-card mr-1"></i> Nombre
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1"></i> Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($admins ?? [] as $admin): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-gray-700"><?= $admin['id'] ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-bold">
                                        <?= strtoupper(substr($admin['username'], 0, 1)) ?>
                                    </div>
                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($admin['username']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700"><?= htmlspecialchars($admin['nombre'] ?? 'N/A') ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <?php if($admin['id'] == $_SESSION['admin_id']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        <i class="fas fa-user-check mr-1"></i> Actual
                                    </span>
                                    <?php else: ?>
                                    <form method="POST" action="?r=admin/admin_delete" class="inline" 
                                        onsubmit="return confirm('¿Eliminar este administrador?');">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                        <button type="submit" 
                                            class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition flex items-center gap-1">
                                            <i class="fas fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if(empty($admins)): ?>
            <!-- Empty State -->
            <div class="p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-users text-4xl text-gray-400"></i>
                </div>
                <p class="text-gray-500">No hay administradores registrados</p>
            </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
