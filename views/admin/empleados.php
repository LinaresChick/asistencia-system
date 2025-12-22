<?php
// views/admin/empleados.php
?>

<section class="px-4 pb-12 animate-fade-in">

    <!-- TÍTULO -->
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center lg:text-left">
        Gestión de Empleados
    </h1>

    <!-- CONTRASEÑA ASIGNADA -->
    <?php if(!empty($_SESSION['assigned_password'])): $ap = $_SESSION['assigned_password']; ?>
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl shadow-sm">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                <div>
                    <div class="font-semibold text-gray-800">Contraseña asignada</div>
                    <div class="text-sm text-gray-700 mt-1">
                        <?= htmlspecialchars($ap['nombre'].' (DNI: '.$ap['dni'].')') ?>
                    </div>

                    <?php if(!empty($ap['cargo'])): ?>
                        <div class="text-sm text-gray-600 mt-1 font-medium">
                            <?= htmlspecialchars($ap['cargo']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3 bg-white p-3 rounded-lg border flex items-center justify-between gap-3">
                        <span id="assignedPwdBox"
                              data-pwd="<?= htmlspecialchars($ap['password']) ?>"
                              data-revealed="0"
                              class="font-mono text-lg">
                            ********
                        </span>

                        <div class="flex gap-2">
                            <button onclick="toggleRevealTop()" class="px-3 py-2 bg-gray-100 rounded-lg">
                                <i id="assignedEyeTop" class="fa-solid fa-eye"></i>
                            </button>
                            <button onclick="copyAssignedPwd()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                Copiar
                            </button>
                        </div>
                    </div>
                </div>

                <form method="post" action="?r=admin/empleados">
                    <button class="px-4 py-2 bg-gray-200 rounded-lg">
                        Cerrar
                    </button>
                </form>

            </div>
        </div>
    <?php endif; ?>

    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- FORMULARIO -->
        <div class="bg-white rounded-2xl shadow-md border p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                Registrar empleado
            </h2>

            <form method="post" action="?r=admin/empleado_create" class="space-y-4">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf); ?>">

                <div>
                    <label class="text-sm font-medium text-gray-600">DNI</label>
                    <input name="dni" required
                           inputmode="numeric"
                           class="w-full mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Nombres</label>
                    <input name="nombres" required
                           class="w-full mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Apellidos</label>
                    <input name="apellidos" required
                           class="w-full mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Edad</label>
                    <input name="edad" type="number"
                           class="w-full mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Cargo</label>
                    <input name="cargo"
                           class="w-full mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                    Registrar empleado
                </button>
            </form>
        </div>

        <!-- LISTA DE EMPLEADOS -->
        <div class="lg:col-span-2">

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

                <?php foreach($empleados as $i => $e): ?>
                <div class="bg-white rounded-3xl p-5 border shadow hover:shadow-lg transition flex flex-col">

                    <!-- HEADER -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-4xl">👤</div>

                        <div class="flex gap-2">
                            <form method="post" action="?r=admin/empleado_delete"
                                  onsubmit="return confirm('Eliminar empleado?')">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf); ?>">
                                <input type="hidden" name="id" value="<?= $e['id']; ?>">
                                <button class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            <button onclick="togglePwdForm(<?= $i ?>)"
                                    class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <i class="fa-solid fa-key"></i>
                            </button>
                        </div>
                    </div>

                    <!-- INFO -->
                    <h3 class="font-bold text-gray-800">
                        <?= htmlspecialchars($e['apellidos'].', '.$e['nombres']); ?>
                    </h3>

                    <p class="text-sm text-gray-500">
                        DNI: <?= htmlspecialchars($e['dni']); ?>
                    </p>

                    <p class="mt-2 text-blue-600 font-semibold">
                        <?= htmlspecialchars($e['cargo']); ?>
                    </p>

                    <!-- PASSWORD -->
                    <?php
                        $ap = $_SESSION['assigned_password'] ?? null;
                        $recentPwd = $_SESSION['recent_passwords'][$e['id']] ?? null;
                        $showPlain = false;
                        $pwdToShow = null;

                        if($ap && (intval($ap['id']) === intval($e['id']) || ($ap['dni'] ?? '') === ($e['dni'] ?? ''))){
                            $showPlain = true;
                            $pwdToShow = $ap['password'];
                        } elseif($recentPwd){
                            $showPlain = true;
                            $pwdToShow = $recentPwd;
                        }
                    ?>

                    <?php if($showPlain && $pwdToShow): ?>
                        <p class="text-sm mt-2">
                            password:
                            <span id="assignedPwdCard-<?= $e['id'] ?>"
                                  class="font-mono"
                                  data-pwd="<?= htmlspecialchars($pwdToShow) ?>"
                                  data-revealed="1">
                                <?= htmlspecialchars($pwdToShow) ?>
                            </span>

                            <button onclick="toggleReveal('<?= $e['id'] ?>')"
                                    class="ml-2 px-2 py-1 bg-gray-100 rounded text-xs">
                                <i id="eye-<?= $e['id'] ?>" class="fa-solid fa-eye-slash"></i>
                            </button>

                            <button onclick="copyPwd('<?= $e['id'] ?>')"
                                    class="ml-2 px-2 py-1 bg-blue-600 text-white rounded text-xs">
                                Copiar
                            </button>
                        </p>
                    <?php endif; ?>

                    <!-- FORM ASIGNAR -->
                    <div id="pwdForm-<?= $i ?>" class="hidden mt-4 bg-gray-50 p-3 rounded-xl border">
                        <form method="post" action="?r=admin/empleado_set_password"
                              onsubmit="return confirm('Asignar contraseña?')"
                              class="flex gap-2">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf); ?>">
                            <input type="hidden" name="id" value="<?= $e['id']; ?>">
                            <input name="password" required
                                   placeholder="Nueva contraseña"
                                   class="flex-1 px-3 py-2 border rounded-lg">
                            <button class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                OK
                            </button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>

            </div>

            <?php if(empty($empleados)): ?>
                <p class="text-center text-gray-500 mt-10">
                    No hay empleados registrados aún.
                </p>
            <?php endif; ?>

        </div>
    </div>
</section>

<?php if(!empty($_SESSION['assigned_password'])){ unset($_SESSION['assigned_password']); } ?>

<script>
function togglePwdForm(idx){
    const el = document.getElementById('pwdForm-' + idx);
    if(el) el.classList.toggle('hidden');
}

function toggleRevealTop(){
    const span = document.getElementById('assignedPwdBox');
    const eye = document.getElementById('assignedEyeTop');
    if(!span) return;

    const revealed = span.dataset.revealed === '1';
    span.textContent = revealed ? '********' : span.dataset.pwd;
    span.dataset.revealed = revealed ? '0' : '1';
    eye.className = revealed ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}

function copyAssignedPwd(){
    const el = document.getElementById('assignedPwdBox');
    if(el) navigator.clipboard.writeText(el.dataset.pwd);
}

function toggleReveal(id){
    const span = document.getElementById('assignedPwdCard-' + id);
    const eye = document.getElementById('eye-' + id);
    if(!span) return;

    const revealed = span.dataset.revealed === '1';
    span.textContent = revealed ? '********' : span.dataset.pwd;
    span.dataset.revealed = revealed ? '0' : '1';
    eye.className = revealed ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}

function copyPwd(id){
    const el = document.getElementById('assignedPwdCard-' + id);
    if(el) navigator.clipboard.writeText(el.dataset.pwd);
}
</script>
