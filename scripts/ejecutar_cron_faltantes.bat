@echo off
REM Script para Windows Task Scheduler
REM Ejecuta automáticamente cada noche para guardar faltantes

setlocal enabledelayedexpansion

REM Ruta al PHP de XAMPP
set PHP_PATH=C:\xampp\php\php.exe

REM Ruta del proyecto
set PROJECT_PATH=C:\xampp\htdocs\asistencia-system

REM Cambiar a la carpeta del proyecto
cd /d %PROJECT_PATH%

REM Ejecutar el script (guardar faltantes del día anterior)
%PHP_PATH% scripts\cron_faltantes_automatico.php "%date:~10,4%-%date:~4,2%-%date:~7,2%" guardar

REM Guardar resultado en log
echo. >> scripts\log_faltantes_cron.txt
echo [%date% %time%] Ejecución completada >> scripts\log_faltantes_cron.txt

endlocal
