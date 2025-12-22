<?php
// views/layout/header.php
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Sistema de Asistencia</title>

<!-- TailwindCSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="/public/css/styles.css">

<script>
window.BASE_URL = '/index.php';
</script>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f0f4ff 0%, #e6efff 100%);
    }

    /* Animación entrada */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }

    /* Estado botones */
    .nav-item {
        transition: all 0.25s ease;
        position: relative;
        white-space: nowrap;
    }
    .nav-item-inactive {
        background-color: white;
        color: #6b7280;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
    }
    .nav-item-inactive:hover {
        transform: translateY(-2px);
        color: #4b5563;
        box-shadow: 0 10px 22px -4px rgba(0,0,0,0.10);
    }
    .nav-item-active {
        background: linear-gradient(135deg, #0066ff 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 30px -5px rgba(37,99,235,0.45);
    }

    /* MULTIPLATAFORMA: NAV scroll mobile */
    .nav-scroll {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .nav-scroll::-webkit-scrollbar {
        display: none;
    }
</style>
</head>

<body>

<!-- 🔵 NAVBAR MULTIPLATAFORMA -->
<header class="w-full flex justify-center py-4 px-3 lg:py-6">
    <nav class="nav-scroll flex items-center gap-3 lg:gap-5 p-3 rounded-full bg-white/40 backdrop-blur-xl shadow-xl max-w-[95vw] lg:max-w-max">

        <?php if (!empty($_SESSION['admin_id'])): ?>

            <!-- DASHBOARD -->
            <button onclick="window.location='?r=admin/dashboard'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-gauge-high text-base lg:text-xl"></i>
                <span>Dashboard</span>
            </button>

            <!-- EMPLEADOS -->
            <button onclick="window.location='?r=admin/empleados'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-users text-base lg:text-xl"></i>
                <span>Empleados</span>
            </button>

            <!-- ASISTENCIAS -->
            <button onclick="window.location='?r=admin/asistencias'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-calendar-check text-base lg:text-xl"></i>
                <span>Asistencias</span>
            </button>

            <!-- FALTANTES -->
            <button onclick="window.location='?r=admin/faltantes'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-exclamation-circle text-base lg:text-xl"></i>
                <span>Faltantes</span>
            </button>

            <!-- CONFIGURACIÓN -->
            <button onclick="window.location='?r=admin/config'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-cog text-base lg:text-xl"></i>
                <span>Config</span>
            </button>

            <!-- SALIR -->
            <button onclick="window.location='?r=admin/logout'"
                class="nav-item nav-item-active flex items-center gap-2 lg:gap-3 px-6 lg:px-10 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-right-from-bracket text-base lg:text-xl"></i>
                <span>Salir</span>
            </button>

        <?php else: ?>

            <!-- ABOUT -->
            <button onclick="window.location='?r=app/about'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-circle-info text-base lg:text-xl"></i>
                <span>About</span>
            </button>

            <!-- ADMIN LOGIN -->
            <button onclick="window.location='?r=admin/login'"
                class="nav-item nav-item-inactive flex items-center gap-2 lg:gap-3 px-5 lg:px-8 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-user-shield text-base lg:text-xl"></i>
                <span>Admin</span>
            </button>

            <!-- MARCAR -->
            <button onclick="window.location='?r=asistencia/marcar'"
                class="nav-item nav-item-active flex items-center gap-2 lg:gap-3 px-6 lg:px-10 py-3 lg:py-4 rounded-full text-sm lg:text-lg font-medium animate-fade-in">
                <i class="fa-solid fa-fingerprint text-base lg:text-xl"></i>
                <span>Marcar</span>
            </button>

        <?php endif; ?>

    </nav>
</header>

<main class="max-w-[1000px] mx-auto mt-10">

<script>
function setActive(btn) {
    const buttons = document.querySelectorAll('.nav-item');
    buttons.forEach(b => {
        b.classList.remove('nav-item-active');
        b.classList.add('nav-item-inactive');
    });
    btn.classList.add('nav-item-active');
    btn.classList.remove('nav-item-inactive');
}
</script>
