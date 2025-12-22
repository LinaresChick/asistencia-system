# 🕐 Configuración de Cron Automático - Guardar Faltantes

Este documento explica cómo configurar la ejecución automática diaria para guardar registros de falta de empleados que no marcaron.

## 📋 Opciones de Ejecución

### Opción 1: Windows Task Scheduler (Recomendado para Windows)

1. **Abre Task Scheduler:**
   - Presiona `Win + R`
   - Escribe `taskschd.msc` y presiona Enter

2. **Crea una nueva tarea:**
   - Click derecho en "Task Scheduler Library" → "Create Basic Task"
   - Nombre: "Guardar Faltantes Asistencia"
   - Descripción: "Guarda automáticamente registros de falta cada noche"

3. **Configura el trigger (Disparador):**
   - Click en "Trigger"
   - New → Daily
   - Hora: 23:30 (o la hora que prefieras)
   - Repetir cada: 1 día

4. **Configura la acción:**
   - Click en "Action"
   - New → Program/script: `php.exe`
   - Arguments: `C:\xampp\htdocs\asistencia-system\scripts\cron_faltantes_automatico.php`
   - Start in: `C:\xampp\htdocs\asistencia-system`

5. **Guarda y prueba:**
   - Clic en OK
   - Task Scheduler → Right click en tu tarea → Run

---

### Opción 2: Script CLI Manual

```powershell
# Guardar faltantes del día anterior
php scripts\cron_faltantes_automatico.php

# Ver faltantes sin guardar (test)
php scripts\cron_faltantes_automatico.php 2025-12-22 test

# Ver estadísticas
php scripts\cron_faltantes_automatico.php 2025-12-22 ver

# Guardar faltantes para una fecha específica
php scripts\cron_faltantes_automatico.php 2025-12-21 guardar
```

---

### Opción 3: Endpoint HTTP (via cURL o wget)

```bash
# Generar token del día actual
TOKEN=$(php -r "echo md5('faltantes_cron_'.date('Y-m-d'));")

# Ejecutar endpoint
curl -X POST "http://localhost/asistencia-system/index.php?controller=asistencia&action=cron_guardar_faltantes" \
  -d "token=$TOKEN" \
  -d "fecha=$(date +%Y-%m-%d)"
```

---

## 📊 Logs

Los registros de ejecución se guardan en:
```
scripts/log_faltantes_cron.txt
```

Puedes revisar este archivo para verificar:
- Cuántos registros se guardaron
- Si hubo errores
- Timestamp de ejecución

Ejemplo de log:
```
[2025-12-22 23:30:15] Guardados 21 registros para 2025-12-21
[2025-12-23 23:30:12] Guardados 5 registros para 2025-12-22
[2025-12-24 23:30:08] ERROR: Database connection failed
```

---

## ⚙️ Comportamiento Automático

**¿Qué hace el script cada noche?**

1. Se ejecuta automáticamente a la hora configurada (ej: 23:30)
2. Identifica a todos los empleados que **NO marcaron entrada** en el día anterior
3. Crea un registro automático en la tabla `asistencias` con:
   - `tipo`: 'entrada'
   - `estado`: 'falta'
   - `nota`: 'registrada_por_cron'
   - `hora`: '00:00:00'
4. Guarda un log de la operación

**¿Qué empleados se registran como "falta"?**

Solo aquellos que:
- NO tienen ningún registro de entrada en esa fecha
- NO tienen un registro previo de falta para esa fecha

---

## 🔒 Seguridad

El script incluye validaciones:
- **CLI (local):** Sin autenticación requerida (se ejecuta en servidor)
- **HTTP:** Requiere un token CRON válido del día actual
- **DB:** Evita registros duplicados

---

## 📱 Testing Rápido

```powershell
# Ver qué faltantes habrá mañana (sin guardar)
php scripts\cron_faltantes_automatico.php 2025-12-23 test
```

---

## Preguntas Frecuentes

**P: ¿Se ejecuta automáticamente cada día?**
A: Sí, una vez configurada la tarea en Task Scheduler o cron.

**P: ¿Qué pasa si un empleado no marca por 3 días?**
A: Se crea un registro de falta para cada día que no marque (si se ejecuta el script).

**P: ¿Puedo editar/eliminar un registro de falta?**
A: Sí, desde phpMyAdmin en la tabla `asistencias` o desde el panel admin.

**P: ¿Se registra si solo falta la salida?**
A: No. Solo si falta la ENTRADA. Consulta `getFaltantesByDate()` para ver todos los faltantes.
