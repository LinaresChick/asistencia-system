<?php
// views/admin/faltantes.php
?>

<section class="px-4 pb-12 animate-fade-in">

    <!-- TÍTULO -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            📋 Reporte de Faltantes de Asistencia
        </h1>
        <p class="text-gray-500 mt-1">
            Empleados que no marcaron correctamente en una fecha determinada
        </p>
    </div>

    <!-- CONTROLES -->
    <div class="bg-white rounded-2xl shadow-md border p-5 mb-6 flex flex-col lg:flex-row gap-4 lg:items-end">

        <div>
            <label class="block text-sm font-semibold text-gray-600">
                Fecha
            </label>
            <input type="date" id="fechaInput"
                   value="<?= date('Y-m-d') ?>"
                   class="mt-1 px-4 py-3 border rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none">
        </div>

        <div class="flex flex-wrap gap-3">
            <button onclick="cargarFaltantes()"
                    class="px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
                🔍 Buscar
            </button>

            <button onclick="guardarFaltantes()"
                    class="px-5 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition">
                💾 Guardar faltantes
            </button>
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div id="statsContainer"
         class="hidden grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-indigo-500 to-blue-600 text-center">
            <div id="totalCount" class="text-4xl font-bold">0</div>
            <div class="text-sm opacity-90">Total sin marcar</div>
        </div>

        <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-amber-400 to-orange-500 text-center">
            <div id="sinEntradaCount" class="text-4xl font-bold">0</div>
            <div class="text-sm opacity-90">Sin entrada</div>
        </div>

        <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-rose-500 to-red-600 text-center">
            <div id="sinSalidaCount" class="text-4xl font-bold">0</div>
            <div class="text-sm opacity-90">Sin salida</div>
        </div>
    </div>

    <!-- FILTROS -->
    <div id="filtersContainer" class="hidden mb-4">
        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Filtrar por tipo
        </label>

        <div class="flex flex-wrap gap-2">
            <button class="filter-btn px-4 py-2 rounded-xl border bg-blue-600 text-white" data-filter="todos">
                Todos
            </button>
            <button class="filter-btn px-4 py-2 rounded-xl border bg-white" data-filter="entrada">
                Sin Entrada
            </button>
            <button class="filter-btn px-4 py-2 rounded-xl border bg-white" data-filter="salida">
                Sin Salida
            </button>
            <button class="filter-btn px-4 py-2 rounded-xl border bg-white" data-filter="refrigerio">
                Refrigerios
            </button>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div id="contenido"
         class="bg-white rounded-2xl shadow-md border overflow-x-auto p-4">
    </div>

</section>

<script>
const csrf = '<?= $csrf ?? "" ?>';
let faltantesData = [];

async function cargarFaltantes(){
    const fecha = document.getElementById('fechaInput').value;
    const cont = document.getElementById('contenido');

    cont.innerHTML = '<div class="text-center py-10 text-gray-500">Cargando...</div>';

    try{
        const fd = new FormData();
        fd.append('csrf', csrf);
        fd.append('fecha', fecha);

        const res = await fetch('?r=asistencia/faltantes_ajax', {
            method:'POST',
            body: fd
        });

        const json = await res.json();

        if(!json.success){
            cont.innerHTML = `<div class="text-red-600">${json.message}</div>`;
            return;
        }

        // Stats
        document.getElementById('totalCount').textContent = json.stats.total;
        document.getElementById('sinEntradaCount').textContent = json.stats.sin_entrada;
        document.getElementById('sinSalidaCount').textContent = json.stats.sin_salida;

        document.getElementById('statsContainer').classList.remove('hidden');
        document.getElementById('filtersContainer').classList.remove('hidden');

        faltantesData = json.data;
        mostrarTabla('todos');

    } catch(e){
        cont.innerHTML = `<div class="text-red-600">Error de red</div>`;
    }
}

function mostrarTabla(filtro){
    let datos = faltantesData;

    if(filtro === 'entrada'){
        datos = datos.filter(e => e.faltantes.entrada);
    } else if(filtro === 'salida'){
        datos = datos.filter(e => e.faltantes.salida && !e.faltantes.entrada);
    } else if(filtro === 'refrigerio'){
        datos = datos.filter(e =>
            Object.keys(e.faltantes).some(k => k.includes('refrigerio'))
        );
    }

    let html = `
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">DNI</th>
                <th class="p-3 text-left">Nombre</th>
                <th class="p-3 text-left">Apellido</th>
                <th class="p-3 text-left">Faltantes</th>
            </tr>
        </thead>
        <tbody>`;

    if(datos.length === 0){
        html += `
        <tr>
            <td colspan="4" class="text-center text-gray-500 p-6">
                ✅ Sin faltantes
            </td>
        </tr>`;
    } else {
        datos.forEach(e => {
            const badges = Object.keys(e.faltantes).map(t =>
                `<span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-700 mr-1">
                    ${t.replaceAll('_',' ')}
                 </span>`
            ).join('');

            html += `
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3 font-semibold">${e.dni}</td>
                <td class="p-3">${e.nombres}</td>
                <td class="p-3">${e.apellidos}</td>
                <td class="p-3">${badges}</td>
            </tr>`;
        });
    }

    html += '</tbody></table>';
    document.getElementById('contenido').innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-blue-600','text-white');
                b.classList.add('bg-white');
            });
            btn.classList.add('bg-blue-600','text-white');
            mostrarTabla(btn.dataset.filter);
        });
    });

    cargarFaltantes();
});

async function guardarFaltantes(){
    const fecha = document.getElementById('fechaInput').value;

    if(!confirm(`¿Guardar faltantes del ${fecha}?`)) return;

    const fd = new FormData();
    fd.append('csrf', csrf);
    fd.append('fecha', fecha);

    const res = await fetch('?r=asistencia/guardar_faltantes_ajax',{
        method:'POST',
        body: fd
    });

    const json = await res.json();
    alert(json.message || 'Proceso terminado');
    cargarFaltantes();
}
</script>
