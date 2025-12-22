<?php
// views/empleado/login.php
?>

<section class="flex justify-center px-4">
    <div class="w-full max-w-md animate-fade-in">

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl p-6 sm:p-8">

            <!-- ICONO -->
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                    <i class="fas fa-fingerprint text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Portal Empleado
                </h1>
                <p class="text-gray-500 mt-1">
                    Consulta tu asistencia
                </p>
            </div>

            <!-- ERROR -->
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 rounded-lg bg-red-100 border-l-4 border-red-500 text-red-700 text-sm">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- FORM -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">
                        DNI
                    </label>
                    <input
                        type="text"
                        name="dni"
                        required
                        inputmode="numeric"
                        placeholder="Ingresa tu DNI"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition text-base"
                    >
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="Ingresa tu contraseña"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition text-base"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-lg hover:shadow-lg active:scale-95 transition"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Ingresar
                </button>
            </form>

            <!-- INFO -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>¿Aún no tienes contraseña?</p>
                <p>Contacta con Administración</p>
            </div>

            <hr class="my-5">

            <!-- LINK -->
            <div class="text-center">
                <a
                    href="?r=asistencia/marcar"
                    class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-800"
                >
                    <i class="fas fa-hand-paper"></i>
                    Marcar Asistencia
                </a>
            </div>

        </div>

        <p class="text-center text-xs text-gray-500 mt-4">
            Sistema de Asistencia v1.0
        </p>

    </div>
</section>
