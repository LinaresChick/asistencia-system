<?php
// views/app/about.php
?>

<div class="max-w-4xl mx-auto px-6 py-12">
    
    <!-- Título -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-gray-800 mb-4">Acerca de</h1>
        <p class="text-xl text-gray-600">Sistema de Asistencia y Control de Personal</p>
    </div>

    <!-- Tarjeta Principal -->
    <div class="bg-white rounded-3xl shadow-lg p-8 mb-12 border border-gray-100">
        
        <div class="flex flex-col md:flex-row items-center gap-8">
            
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-6xl font-bold shadow-lg">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
            </div>

            <!-- Información -->
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Luis Elias Linares Ascencio</h2>
                <p class="text-xl text-purple-600 font-semibold mb-1">Alias: Taytajra</p>
                <p class="text-gray-600 mb-4">CEO - Mallpopache Software</p>
                
                <div class="space-y-3 text-gray-700">
                    <p><strong>Empresa:</strong> Mallpopache Software</p>
                    <p><strong>Posición:</strong> Chief Executive Officer (CEO)</p>
                    <p><strong>Especialidad:</strong> Full Stack Development & Business Management</p>
                    <p><strong>Lenguajes:</strong> PHP, JavaScript, Python</p>
                    <p><strong>Stack:</strong> PHP • MySQL • Tailwind CSS • JavaScript</p>
                    <p><strong>Experiencia:</strong> Desarrollo de sistemas web, aplicaciones de gestión empresarial y liderazgo de proyectos</p>
                    <p><strong>Jefe Directo:</strong> Meka</p>
                    <p><strong>Visión:</strong> Innovación tecnológica para soluciones empresariales de calidad</p>
                </div>
            </div>

        </div>

    </div>

    <!-- Acerca del Proyecto -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-3xl shadow-lg p-8 mb-12 border border-blue-100">
        
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
            <i class="fa-solid fa-briefcase text-blue-600"></i>
            Acerca del Proyecto
        </h3>

        <div class="space-y-4 text-gray-700">
            <p>
                <strong>Sistema de Asistencia</strong> es una aplicación web moderna diseñada para gestionar y controlar la asistencia 
                de empleados en tiempo real.
            </p>
            
            <p>
                <strong>Características principales:</strong>
            </p>
            <ul class="list-disc list-inside space-y-2 ml-4">
                <li>Registro de entrada y salida (fingerprint)</li>
                <li>Gestión de empleados y horarios</li>
                <li>Detección automática de faltas y tardanzas</li>
                <li>Portal de empleado para consultar historial</li>
                <li>Dashboard administrativo con reportes</li>
                <li>Sistema de refrigerios configurables</li>
                <li>Exportación de datos (Excel, PDF)</li>
            </ul>
        </div>

    </div>

    <!-- Tecnologías Utilizadas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
            <div class="text-4xl text-blue-600 mb-4"><i class="fa-brands fa-php"></i></div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">Backend</h4>
            <p class="text-gray-600">PHP 8.x • MySQL • PDO</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
            <div class="text-4xl text-yellow-500 mb-4"><i class="fa-brands fa-js"></i></div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">Frontend</h4>
            <p class="text-gray-600">JavaScript • Tailwind CSS • HTML5</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition">
            <div class="text-4xl text-purple-600 mb-4"><i class="fa-solid fa-database"></i></div>
            <h4 class="text-xl font-bold text-gray-800 mb-2">Base de Datos</h4>
            <p class="text-gray-600">MySQL • MariaDB • Transacciones ACID</p>
        </div>

    </div>

    <!-- Redes Sociales / Contacto -->
    <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100 text-center">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Contacto y Redes</h3>
        
        <div class="flex justify-center gap-6 mb-8">
            <a href="#" class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition text-xl" title="GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
            <a href="#" class="w-12 h-12 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500 transition text-xl" title="LinkedIn">
                <i class="fa-brands fa-linkedin"></i>
            </a>
            <a href="#" class="w-12 h-12 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition text-xl" title="Email">
                <i class="fa-solid fa-envelope"></i>
            </a>
        </div>

        <p class="text-gray-600">© 2025 Luis Elias Linares Ascencio (Taytajra) - Mallpopache Software. Todos los derechos reservados.</p>
    </div>

    <!-- Botón Volver -->
    <div class="text-center mt-12">
        <button onclick="window.history.back()" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition shadow-lg">
            <i class="fa-solid fa-arrow-left mr-2"></i> Volver
        </button>
    </div>

</div>
