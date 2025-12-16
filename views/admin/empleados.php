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
    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 order-1 lg:order-1">

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
    <div class="lg:col-span-2 order-2 lg:order-2">

        <!-- Botón móvil: Ver empleados -->
        <div class="flex justify-center mb-4 lg:hidden">
            <button id="btnToggleEmpleados"
                class="px-5 py-3 bg-green-600 text-white rounded-full shadow hover:bg-green-700 transition">
                Ver empleados
            </button>
        </div>

        <!-- Modal / panel móvil con lista vertical -->
        <div id="modalEmpleados" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-start">
            <div class="bg-white w-full h-full overflow-auto p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Empleados</h3>
                    <button id="btnCloseEmpleados" class="text-gray-600 px-3 py-2 rounded hover:bg-gray-100">Cerrar</button>
                </div>

                <div class="space-y-4">
                    <?php foreach($empleados as $e): ?>
                    <div class="p-4 rounded-xl border bg-gray-50">
                        <div class="text-3xl">👤</div>
                        <div class="mt-2">
                            <div class="font-semibold text-lg"><?php echo htmlspecialchars($e['apellidos'] . ', ' . $e['nombres']); ?></div>
                            <div class="text-sm text-gray-600">DNI: <?php echo htmlspecialchars($e['dni']); ?></div>
                            <div class="text-sm text-blue-700 font-medium mt-2"><?php echo htmlspecialchars($e['cargo']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
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

                <script>
                // Toggle modal lista empleados (móvil)
                (function(){
                    var btn = document.getElementById('btnToggleEmpleados');
                    var modal = document.getElementById('modalEmpleados');
                    var btnClose = document.getElementById('btnCloseEmpleados');
                    if(!btn || !modal) return;

                    function openModal(){
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                    }
                    function closeModal(){
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = '';
                    }

                    btn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
                    if(btnClose) btnClose.addEventListener('click', function(e){ e.preventDefault(); closeModal(); });
                    modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });
                })();
                </script>
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
