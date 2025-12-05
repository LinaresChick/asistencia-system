<?php
// views/asistencia/marcar.php
?>

<!-- CDN Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { background: #e0e2e5; }
    h1, h2, h3 { font-family: 'DM Serif Display', serif; }
    .bg-sidebar { background-color: #474a5e; }
    .bg-content { background-color: #f7eceb; }
    .text-dark { color: #474a5e; }
    .option-btn {
        border: 2px solid #ccc;
        padding: 12px;
        border-radius: 10px;
        cursor: pointer;
        background: white;
        text-align: center;
    }
    .option-btn.active {
        border-color: #e2b8ba;
        background: #e2b8ba;
        font-weight: bold;
    }
</style>

<div class="flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-[1100px] rounded-[2rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row">

        <!-- BARRA LATERAL -->
        <div class="bg-sidebar w-full lg:w-[35%] p-10 text-white flex flex-col justify-between">

            <div class="flex items-center gap-3 mb-10">
                <div class="bg-[#e2b8ba] w-10 h-10 rounded-full flex items-center justify-center text-dark">
                    <i class="fa-solid fa-paw text-lg"></i>
                </div>
                <span class="tracking-[0.15em] text-sm font-bold uppercase">Asistencia</span>
            </div>

            <h2 class="text-xl font-bold">Marcación</h2>
            <p class="opacity-80 text-sm">Busca al trabajador por DNI y registra entrada o salida.</p>

            <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=60"
                class="opacity-40 mt-10 rounded-xl">
        </div>

        <!-- CONTENIDO DEL FORM -->
        <div class="bg-content w-full lg:w-[65%] p-10">

            <h1 class="text-3xl text-dark mb-6">Marcar Asistencia</h1>

            <div class="grid grid-cols-1 gap-8">

                <!-- DNI -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">DNI</label>
                    <div class="flex gap-3 mt-2">
                        <input id="dni" class="flex-1 p-3 pl-4 rounded border bg-white"
                               placeholder="Ingrese DNI">

                        <button id="buscar"
                                class="bg-sidebar text-white px-6 rounded-lg hover:bg-[#3a3d4d]">
                            Buscar
                        </button>
                    </div>
                </div>

                <!-- FICHA -->
                <div id="ficha" style="display:none;"
                     class="bg-white rounded-xl p-5 shadow border border-gray-200">

                    <h2 class="text-lg text-dark mb-4 font-bold">Ficha del Trabajador</h2>

                    <div><strong>Nombre:</strong> <span id="nombre"></span></div>
                    <div><strong>Apellido:</strong> <span id="apellido"></span></div>
                    <div><strong>Cargo:</strong> <span id="cargo"></span></div>
                </div>

                <!-- TIPO (BOTONES) -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Tipo</label>

                    <div id="tipo-container" class="grid grid-cols-2 gap-4 mt-3">

                        <!-- Entrada -->
                        <div class="option-btn" data-type="entrada">Entrada</div>

                        <!-- Salida -->
                        <div class="option-btn" data-type="salida">Salida</div>

                        <!-- Refrigerio inicio -->
                        <div class="option-btn refrigerio-inicio hidden" data-type="refrigerio_inicio">
                            Refrigerio - Inicio
                        </div>

                        <!-- Refrigerio fin -->
                        <div class="option-btn refrigerio-fin hidden" data-type="refrigerio_fin">
                            Refrigerio - Fin
                        </div>

                    </div>

                    <input type="hidden" id="tipo">
                </div>

                <!-- CSRF -->
                <input type="hidden" id="csrf" value="<?php echo $csrf; ?>">

                <!-- BOTÓN MARCAR -->
                <div>
                    <button id="marcar"
                            class="bg-[#474a5e] text-white w-full py-3 rounded-full hover:bg-[#3a3d4d]">
                        Marcar ahora
                    </button>
                </div>

                <div id="msg" class="text-green-600 font-bold text-sm"></div>

            </div>
        </div>
    </div>
</div>

<script>
const fichaEl = document.getElementById('ficha');
const nombreEl = document.getElementById('nombre');
const apellidoEl = document.getElementById('apellido');
const cargoEl = document.getElementById('cargo');
const msg = document.getElementById('msg');
const tipoHidden = document.getElementById('tipo');

// VALIDACIÓN HORARIA PARA REFRIGERIO
function validarRefrigerio() {
    const now = new Date();
    const hour = now.getHours();
    const min = now.getMinutes();

    const inicioBtn = document.querySelector(".refrigerio-inicio");
    const finBtn = document.querySelector(".refrigerio-fin");

    // Mostrar 12:00 - 12:59
    if (hour === 12) {
        inicioBtn.classList.remove("hidden");
    } else {
        inicioBtn.classList.add("hidden");
    }

    // Mostrar 13:00 - 13:59
    if (hour === 13) {
        finBtn.classList.remove("hidden");
    } else {
        finBtn.classList.add("hidden");
    }
}

// Ejecutar al cargar
validarRefrigerio();

// Selección de tipo
document.querySelectorAll(".option-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".option-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        tipoHidden.value = btn.dataset.type;
    });
});

// BUSCAR TRABAJADOR
document.getElementById('buscar').addEventListener('click', async () => {
    const dni = document.getElementById('dni').value.trim();
    if(!dni){ alert('Ingrese DNI'); return; }

    msg.textContent = 'Buscando...';

    const res = await fetch('?r=empleado/ficha&dni=' + encodeURIComponent(dni));
    const json = await res.json();

    if(json.success){
        fichaEl.style.display = 'block';
        nombreEl.textContent = json.data.nombres;
        apellidoEl.textContent = json.data.apellidos;
        cargoEl.textContent = json.data.cargo || 'N/A';
        msg.textContent = '';
    } else {
        fichaEl.style.display = 'none';
        msg.textContent = json.message;
    }
});

// MARCAR ASISTENCIA
document.getElementById('marcar').addEventListener('click', async () => {

    if(!tipoHidden.value){
        alert("Seleccione un tipo de marcación");
        return;
    }

    const dni = document.getElementById('dni').value.trim();
    if(!dni){ alert('Ingrese DNI'); return; }

    msg.textContent = 'Marcando...';

    const fd = new FormData();
    fd.append('dni', dni);
    fd.append('tipo', tipoHidden.value);
    fd.append('csrf', document.getElementById('csrf').value);

    const res = await fetch('?r=asistencia/marcar_ajax', {
        method: 'POST',
        body: fd
    });

    const json = await res.json();
    msg.textContent = json.message || 'Marcado!';
});
</script>
