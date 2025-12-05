<?php
// views/admin/empleados.php
?>

<!-- Título -->
<h1 class="text-3xl font-bold text-gray-800 mb-6 text-center lg:text-left">
    Gestión de Empleados
</h1>

<!-- CONTENEDOR RESPONSIVE -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

    <!-- FORMULARIO DE REGISTRO -->
    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 order-2 lg:order-1">

        <h2 class="text-xl font-semibold text-gray-700 mb-4">Registrar empleado</h2>

        <form method="post" action="?r=admin/empleado_create" class="space-y-4">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">

            <div>
                <label class="text-sm font-medium text-gray-600">DNI</label>
                <input name="dni" required
                    class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Nombres</label>
                <input name="nombres" required
                    class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Apellidos</label>
                <input name="apellidos" required
                    class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Edad</label>
                <input name="edad" type="number"
                    class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" />
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Cargo</label>
                <input name="cargo"
                    class="w-full mt-1 px-4 py-2 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" />
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-xl shadow hover:bg-blue-700 transition">
                Registrar empleado
            </button>
        </form>
    </div>

    <!-- LISTA DE EMPLEADOS -->
    <div class="lg:col-span-2 order-1 lg:order-2">

        <!-- FAB flotante en móvil -->
        <div class="flex justify-end mb-4 lg:hidden">
            <button onclick="window.scrollTo({top:0,behavior:'smooth'});"
                class="w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl shadow-lg hover:bg-blue-700 transition fixed bottom-6 right-6 z-50">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>

        <!-- GRID RESPONSIVE DE CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <?php foreach($empleados as $i => $e): ?>
            <div class="bg-white shadow-md rounded-3xl p-5 flex flex-col justify-between border hover:shadow-lg transition">

                <!-- Header con avatar + acciones -->
                <div class="flex justify-between items-start">
                    <div class="text-4xl">👤</div>

                    <form method="post" action="?r=admin/empleado_delete"
                        onsubmit="return confirm('Eliminar empleado?')">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                        <input type="hidden" name="id" value="<?php echo $e['id']; ?>">

                        <button
                            class="text-red-500 hover:text-red-600 bg-red-100 w-10 h-10 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>

                <!-- Información del empleado -->
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-800">
                        <?php echo htmlspecialchars($e['apellidos'] . ', ' . $e['nombres']); ?>
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        DNI: <?php echo htmlspecialchars($e['dni']); ?>
                    </p>

                    <p class="text-sm text-blue-600 font-semibold mt-2">
                        <?php echo htmlspecialchars($e['cargo']); ?>
                    </p>
                </div>

            </div>
            <?php endforeach; ?>

        </div>

        <!-- MENSAJE SI NO HAY EMPLEADOS -->
        <?php if(empty($empleados)): ?>
        <p class="text-center text-gray-500 mt-8 text-lg">
            No hay empleados registrados aún.
        </p>
        <?php endif; ?>

    </div>

</div>
