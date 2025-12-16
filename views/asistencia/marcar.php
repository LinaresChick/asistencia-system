<?php
// views/asistencia/marcar.php (multiplataforma)
?>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .touch-target { touch-action: manipulation; }
    .option-active { border-color: #10b981 !important; background: #ecfdf5 !important; }
</style>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-50 to-slate-100 p-4">
    <main class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="lg:flex">
            <aside class="hidden lg:block lg:w-1/3 bg-indigo-600 text-white p-8">
                <h2 class="text-xl font-semibold">Marcación</h2>
                <p class="text-sm opacity-90 mt-2">Busca al trabajador por DNI y registra entrada, salida o refrigerios.</p>
            </aside>

            <section class="w-full lg:w-2/3 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-slate-800">Marcar Asistencia</h1>
                    <div class="text-sm text-slate-500">Multiplataforma</div>
                </div>

                <div class="space-y-5">
                    <?php if(!empty($horario)): ?>
                    <div class="bg-yellow-50 border border-yellow-100 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-amber-900">Horario Actual Configurado</h3>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-xs text-slate-500">Entrada</div>
                                <div class="text-lg font-bold text-amber-900"><?php echo htmlspecialchars($horario['entrada'] ?? '--'); ?></div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Salida</div>
                                <div class="text-lg font-bold text-amber-900"><?php echo htmlspecialchars($horario['salida'] ?? '--'); ?></div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-xs text-slate-500">Ref 1</div>
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($horario['ref1_inicio'] ?? '--'); ?> - <?php echo htmlspecialchars($horario['ref1_fin'] ?? '--'); ?></div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-xs text-slate-500">Ref 2</div>
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($horario['ref2_inicio'] ?? '--'); ?> - <?php echo htmlspecialchars($horario['ref2_fin'] ?? '--'); ?></div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-xs text-slate-500">Ref 3</div>
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($horario['ref3_inicio'] ?? '--'); ?> - <?php echo htmlspecialchars($horario['ref3_fin'] ?? '--'); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div>
                        <label for="dni" class="block text-sm font-medium text-slate-700">DNI</label>
                        <?php if(!empty($_SESSION['admin_id'])): ?>
                        <div class="mt-2 mb-2">
                            <label class="block text-xs text-slate-500">Fecha (solo admin)</label>
                            <input id="fecha" type="date" value="<?= date('Y-m-d') ?>" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <?php endif; ?>
                        <div class="mt-2 flex gap-3">
                            <input id="dni" class="flex-1 rounded-lg border border-slate-200 px-4 py-3 text-lg focus:outline-none" inputmode="numeric" placeholder="Ej. 12345678" aria-label="DNI">
                            <button id="buscar" class="inline-flex items-center gap-2 px-4 py-3 rounded-lg bg-indigo-600 text-white touch-target" aria-label="Buscar trabajador">
                                <i class="fas fa-search"></i>
                                <span class="hidden sm:inline">Buscar</span>
                            </button>
                        </div>
                    </div>

                    <div id="ficha" class="hidden bg-slate-50 border border-slate-100 rounded-lg p-4">
                        <h2 class="text-sm font-semibold text-slate-800 mb-2">Ficha del trabajador</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                            <div><div class="text-xs text-slate-500">Nombre</div><div id="nombre" class="font-medium"></div></div>
                            <div><div class="text-xs text-slate-500">Apellido</div><div id="apellido" class="font-medium"></div></div>
                            <div><div class="text-xs text-slate-500">Cargo</div><div id="cargo" class="font-medium">N/A</div></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tipo de marcación</label>
                        <div id="tipo-container" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button type="button" class="option-btn touch-target bg-white border border-slate-200 rounded-lg py-3 text-center font-semibold" data-type="entrada">Entrada</button>
                            <button type="button" class="option-btn touch-target bg-white border border-slate-200 rounded-lg py-3 text-center font-semibold" data-type="salida">Salida</button>
                            <div class="col-span-2 sm:col-span-1 flex gap-2">
                                <select id="ref-num" class="rounded-lg border border-slate-200 px-3 py-2 w-1/3" aria-label="Seleccionar refrigerio">
                                    <option value="1">R1</option>
                                    <option value="2">R2</option>
                                    <option value="3">R3</option>
                                </select>
                                <button id="ref-inicio" type="button" class="option-btn touch-target flex-1 bg-white border border-slate-200 rounded-lg py-3 text-center font-semibold" data-action="inicio">Inicio</button>
                                <button id="ref-fin" type="button" class="option-btn touch-target flex-1 bg-white border border-slate-200 rounded-lg py-3 text-center font-semibold" data-action="fin">Fin</button>
                            </div>
                        </div>
                        <input type="hidden" id="tipo" aria-hidden="true">
                        <!-- GPS prompt (visible cuando se selecciona Entrada/Salida) -->
                        <div id="gpsPrompt" class="mt-3 p-3 bg-yellow-50 border border-yellow-100 rounded-lg hidden">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-yellow-800">Para marcar Entrada/Salida debe activarse la ubicación del dispositivo.</div>
                                <div class="flex items-center gap-2">
                                    <button id="requestLocationBtn" class="px-3 py-2 bg-yellow-600 text-white rounded-md">Solicitar ubicación</button>
                                </div>
                            </div>
                        </div>

                        <!-- Preview de ubicación (lat, lng, precisión, hora) -->
                        <div id="locationPreview" class="mt-3 p-3 bg-slate-50 border border-slate-100 rounded-lg hidden">
                            <div class="text-sm text-slate-600">Ubicación:</div>
                            <div id="loc-coords" class="text-sm font-medium mt-1">--</div>
                            <div id="loc-accuracy" class="text-xs text-slate-500 mt-1">--</div>
                        </div>
                    </div>

                    <div>
                        <input type="hidden" id="csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                        <button id="marcar" class="w-full bg-emerald-600 text-white rounded-lg py-3 text-lg font-semibold touch-target">Marcar ahora</button>
                    </div>

                    <div id="msg" role="status" aria-live="polite" class="min-h-[1.2rem] text-sm font-medium text-slate-700"></div>
                    <div id="last-action" class="text-xs text-slate-500"></div>
                </div>
            </section>
        </div>
    </main>
</div>

<script>
(() => {
    const dniInput = document.getElementById('dni');
    const buscarBtn = document.getElementById('buscar');
    const fichaEl = document.getElementById('ficha');
    const nombreEl = document.getElementById('nombre');
    const apellidoEl = document.getElementById('apellido');
    const cargoEl = document.getElementById('cargo');
    const msg = document.getElementById('msg');
    const lastAction = document.getElementById('last-action');
    const tipoHidden = document.getElementById('tipo');
    const marcarBtn = document.getElementById('marcar');
    const refNumEl = document.getElementById('ref-num');
    const refInicioBtn = document.getElementById('ref-inicio');
    const refFinBtn = document.getElementById('ref-fin');
    const optionBtns = Array.from(document.querySelectorAll('.option-btn[data-type]'));
    const locationPreview = document.getElementById('locationPreview');
    const locCoords = document.getElementById('loc-coords');
    const locAccuracy = document.getElementById('loc-accuracy');

    // Mostrar última acción desde localStorage
    const showLast = () => {
        const la = localStorage.getItem('last_asist_action');
        lastAction.textContent = la ? `Última acción: ${la}` : '';
    };
    showLast();

    const renderLocationPreview = (obj) => {
        if(!obj){ locationPreview.classList.add('hidden'); locCoords.textContent='--'; locAccuracy.textContent='--'; return; }
        const when = obj.timestamp ? new Date(obj.timestamp).toLocaleString() : new Date().toLocaleString();
        locCoords.textContent = `Lat: ${obj.lat.toFixed(6)} · Lng: ${obj.lng.toFixed(6)} · ${when}`;
        locAccuracy.textContent = `Precisión: ±${Math.round(obj.accuracy || 0)} m`;
        locationPreview.classList.remove('hidden');
    };

    const setLoadingPreview = (loadingText) => {
        locationPreview.classList.remove('hidden');
        locCoords.textContent = loadingText || 'Obteniendo ubicación...';
        locAccuracy.textContent = '';
    };

    // Gestión de selección de tipo (entrada/salida)
    const gpsPrompt = document.getElementById('gpsPrompt');
    const requestLocationBtn = document.getElementById('requestLocationBtn');
    const setGpsPromptVisible = (visible) => {
        if(visible){ gpsPrompt.classList.remove('hidden'); }
        else { gpsPrompt.classList.add('hidden'); }
    };

    optionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            optionBtns.forEach(b => b.classList.remove('option-active'));
            btn.classList.add('option-active');
            tipoHidden.value = btn.dataset.type;
            const needGps = (btn.dataset.type === 'entrada' || btn.dataset.type === 'salida');
            setGpsPromptVisible(needGps);
            if(needGps){
                // mostrar preview: usar cache si es reciente
                const cached = localStorage.getItem('last_coords');
                if(cached){
                    try{
                        const obj = JSON.parse(cached);
                        if(obj && obj.timestamp && (Date.now() - obj.timestamp) <= 2*60*1000){
                            renderLocationPreview({ lat: obj.lat, lng: obj.lng, accuracy: obj.accuracy, timestamp: obj.timestamp });
                            return;
                        }
                    }catch(e){ /* ignore */ }
                }
                // solicitar y mostrar mientras obtiene
                if('geolocation' in navigator){
                    setLoadingPreview('Obteniendo ubicación...');
                    navigator.geolocation.getCurrentPosition((p)=>{
                        const obj = { lat: p.coords.latitude, lng: p.coords.longitude, accuracy: p.coords.accuracy, timestamp: Date.now() };
                        try{ localStorage.setItem('last_coords', JSON.stringify(obj)); }catch(e){}
                        renderLocationPreview(obj);
                    }, (err)=>{
                        renderLocationPreview(null);
                    }, { enableHighAccuracy:true, maximumAge:0, timeout:6000 });
                } else {
                    renderLocationPreview(null);
                }
            } else {
                renderLocationPreview(null);
            }
        });
    });

    // Refrigerio inicio/fin
    refInicioBtn.addEventListener('click', () => {
        const n = refNumEl.value || '1';
        optionBtns.forEach(b => b.classList.remove('option-active'));
        refInicioBtn.classList.add('option-active');
        tipoHidden.value = `refrigerio${n}_inicio`;
        setGpsPromptVisible(false);
    });
    refFinBtn.addEventListener('click', () => {
        const n = refNumEl.value || '1';
        optionBtns.forEach(b => b.classList.remove('option-active'));
        refFinBtn.classList.add('option-active');
        tipoHidden.value = `refrigerio${n}_fin`;
        setGpsPromptVisible(false);
    });

    // Buscar trabajador por DNI
    buscarBtn.addEventListener('click', async () => {
        const dni = dniInput.value.trim();
        if(!dni){ msg.textContent = 'Ingrese DNI'; dniInput.focus(); return; }
        msg.textContent = 'Buscando...';
        try{
            const res = await fetch(`?r=empleado/ficha&dni=${encodeURIComponent(dni)}`);
            const json = await res.json();
            if(json.success){
                fichaEl.classList.remove('hidden');
                nombreEl.textContent = json.data.nombres || '';
                apellidoEl.textContent = json.data.apellidos || '';
                cargoEl.textContent = json.data.cargo || 'N/A';
                msg.textContent = '';
            } else {
                fichaEl.classList.add('hidden');
                msg.textContent = json.message || 'No encontrado';
            }
        } catch(e){
            fichaEl.classList.add('hidden');
            msg.textContent = 'Error de red';
        }
    });

    // Tecla Enter en DNI busca
    dniInput.addEventListener('keydown', (e) => { if(e.key === 'Enter'){ buscarBtn.click(); } });

    // Marcar asistencia
    marcarBtn.addEventListener('click', async () => {
        const tipo = tipoHidden.value;
        const dni = dniInput.value.trim();
        if(!tipo){ msg.textContent = 'Selecciona tipo de marcación'; return; }
        if(!dni){ msg.textContent = 'Ingrese DNI'; dniInput.focus(); return; }

        // Deshabilitar mientras se procesa
        marcarBtn.disabled = true; marcarBtn.classList.add('opacity-70');
        msg.textContent = 'Marcando...';

        const fd = new FormData();
        fd.append('dni', dni);
        fd.append('tipo', tipo);
        fd.append('csrf', document.getElementById('csrf').value);
        const fechaEl = document.getElementById('fecha');
        if(fechaEl){ fd.append('fecha', fechaEl.value); }

        const send = async (coords) => {
            if(coords){ fd.append('lat', coords.latitude); fd.append('lng', coords.longitude); }
            try{
                const res = await fetch('?r=asistencia/marcar_ajax', { method:'POST', body: fd });
                const json = await res.json();
                if(json.success){
                    msg.textContent = json.message || 'Marcado';
                    const timestamp = new Date().toLocaleString();
                    localStorage.setItem('last_asist_action', `${timestamp} · ${dni} · ${tipo} · ${json.estado ?? ''}`);
                    showLast();
                } else {
                    msg.textContent = json.message || 'Error al marcar';
                }
            } catch(e){
                msg.textContent = 'Error de red al marcar';
            } finally {
                marcarBtn.disabled = false; marcarBtn.classList.remove('opacity-70');
                // limpiar selección
                tipoHidden.value = '';
                optionBtns.forEach(b=>b.classList.remove('option-active'));
                refInicioBtn.classList.remove('option-active'); refFinBtn.classList.remove('option-active');
            }
        };

        // Intentar geolocalización con timeout. Usar coords cacheadas si existen y son recientes (<=2min)
        const needGps = (tipo === 'entrada' || tipo === 'salida');
        const cached = localStorage.getItem('last_coords');
        if(cached){
            try{
                const obj = JSON.parse(cached);
                if(obj && obj.timestamp && (Date.now() - obj.timestamp) <= 2*60*1000){
                    // usar coords cacheadas
                    send({ latitude: obj.lat, longitude: obj.lng, accuracy: obj.accuracy });
                    return;
                }
            }catch(e){ /* ignore */ }
        }

        if('geolocation' in navigator){
            let called = false;
            const done = (coords)=>{ if(!called){ called = true; send(coords); } };
            const fail = ()=>{ if(!called){ called = true;
                    if(needGps){ msg.textContent = 'Se requiere ubicación para marcar entrada/salida. Habilita el GPS y vuelve a intentar.'; marcarBtn.disabled = false; marcarBtn.classList.remove('opacity-70'); }
                    else { send(null); }
                } };
            const timer = setTimeout(()=>{ fail(); }, 7000);
            navigator.geolocation.getCurrentPosition((p)=>{ clearTimeout(timer); done(p.coords); }, (e)=>{ clearTimeout(timer); fail(); }, { enableHighAccuracy:true, maximumAge:0, timeout:6000 });
        } else {
            // No hay geolocalización en el navegador
            if(needGps){
                msg.textContent = 'Geolocalización no soportada. No se puede marcar entrada/salida.';
                marcarBtn.disabled = false; marcarBtn.classList.remove('opacity-70');
            } else {
                await send(null);
            }
        }
    });

    // Botón para solicitar ubicación desde el banner (cachea las coordenadas)
    requestLocationBtn.addEventListener('click', (e) => {
        e.preventDefault();
        msg.textContent = 'Solicitando ubicación...';
        if(!('geolocation' in navigator)){
            msg.textContent = 'Geolocalización no soportada por el dispositivo.'; return;
        }
        navigator.geolocation.getCurrentPosition((p)=>{
            const obj = { lat: p.coords.latitude, lng: p.coords.longitude, accuracy: p.coords.accuracy, timestamp: Date.now() };
            try{ localStorage.setItem('last_coords', JSON.stringify(obj)); }catch(e){}
            msg.textContent = `Ubicación obtenida (±${Math.round(p.coords.accuracy)} m). Puedes ahora marcar Entrada/Salida.`;
            renderLocationPreview(obj);
        }, (err)=>{
            msg.textContent = 'No se obtuvo ubicación. Asegura que la ubicación esté activada y que la página tenga permiso.';
        }, { enableHighAccuracy:true, maximumAge:0, timeout:8000 });
    });

})();
</script>
