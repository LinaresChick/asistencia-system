# CRON: Marcación Automática de Faltas

## Descripción
Script que se ejecuta automáticamente cada día y marca como "falta" a los empleados que no registraron entrada.

## Ubicación
`/cron/marcar_faltas.php`

## Ejecución en Windows (Task Scheduler)

### Paso 1: Abrir Task Scheduler
1. Presiona `Win + R`
2. Escribe `taskschd.msc` y presiona Enter

### Paso 2: Crear tarea programada
1. Click en "Crear tarea básica" (o "Create Basic Task")
2. **Nombre**: `Marcar Faltas Asistencia`
3. **Descripción**: `Marca automáticamente como falta a empleados sin entrada`
4. Haz click en "Siguiente"

### Paso 3: Configurar disparador
1. Selecciona "Diariamente"
2. Click "Siguiente"
3. Configura la hora: **23:59** (última hora del día) o **00:01** (inicio del siguiente día)
4. Click "Siguiente"

### Paso 4: Configurar acción
1. Selecciona "Iniciar un programa"
2. Click "Siguiente"
3. En "Programa o script", ingresa la ruta a PHP:
   ```
   C:\xampp\php\php.exe
   ```
4. En "Agregar argumentos", ingresa la ruta al script:
   ```
   C:\xampp\htdocs\asistencia-system\cron\marcar_faltas.php
   ```
5. En "Iniciar en", ingresa:
   ```
   C:\xampp\htdocs\asistencia-system
   ```
6. Click "Siguiente" y luego "Finalizar"

### Paso 5: Verificar log
El script crea un archivo de log en:
```
C:\xampp\htdocs\asistencia-system\cron\log_faltas.txt
```
Abre este archivo para verificar que se ejecutó correctamente.

---

## Ejecución Manual (para pruebas)

Abre PowerShell en el directorio del proyecto y ejecuta:

```powershell
C:\xampp\php\php.exe C:\xampp\htdocs\asistencia-system\cron\marcar_faltas.php
```

---

## Qué hace el script

1. Se conecta a la BD
2. Recorre todos los empleados
3. Verifica si cada uno tiene registro de **entrada** hoy
4. Si **NO** tiene entrada:
   - Inserta un registro automático con:
     - `tipo`: 'entrada'
     - `estado`: 'falta'
     - `hora`: '00:00:00'
     - `nota`: 'Falta registrada automáticamente por CRON'
5. Genera un log con el resumen

---

## Salida del script

```
=== Marcación de Faltas - 2025-12-18 ===
✓ Carlos Pedro Miko Macro (74859612) - Entrada registrada
✗ Juan Carlos Pérez Gómez (70123456) - FALTA marcada automáticamente
✗ Ana Lucía Castillo Vega (70456789) - FALTA marcada automáticamente
...
=== Resumen ===
Empleados con entrada: 5
Faltas marcadas: 15
Total empleados: 20
```

---

## Notas
- El script se ejecuta **una sola vez al día** a la hora configurada.
- Solo marca como falta si **NO existe** registro de entrada ese día.
- Si el empleado tiene entrada (aunque sea fuera de horario), NO se marca como falta.
- El log se guarda en `log_faltas.txt` para auditoría.
