<?php
// views/admin/login.php
?>
<!-- Tailwind (solo aquí para evitar modificar header.php) -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: 'Montserrat', sans-serif;
    }
    .glass-panel {
        background: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    .glass-input {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s ease;
    }
    .glass-input::placeholder {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }
    .glass-input:focus {
        background: rgba(255, 255, 255, 0.3);
        outline: none;
        border-color: rgba(255, 255, 255, 0.5);
    }
    .link-underline {
        position: relative;
        text-decoration: none;
        color: white;
        font-weight: 600;
    }
    .link-underline::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: white;
        transform: scaleX(1);
        transition: transform 0.3s ease;
    }
    .link-underline:hover::after {
        transform: scaleX(0);
    }
</style>

<!-- Fondo -->
<div class="relative h-screen w-full overflow-hidden flex items-center justify-center">

    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=2070&auto=format&fit=crop"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/10"></div>
    </div>

    <!-- Panel del Login -->
    <div class="relative z-10 glass-panel rounded-[2.5rem] p-10 w-[90%] max-w-[400px] flex flex-col items-center text-center text-white h-auto min-h-[650px] justify-center shadow-2xl">

        <h1 class="text-4xl font-bold tracking-wide mb-10">ADMIN LOGIN</h1>

        <?php if(!empty($error)): ?>
            <div style="color:#ff8a8a;margin-bottom:8px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form class="w-full flex flex-col gap-6" method="post" action="?r=admin/login">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">

            <div class="relative">
                <input type="text" name="username" placeholder="Usuario" required
                       class="glass-input w-full py-4 px-6 rounded-full text-center text-lg shadow-inner">
            </div>

            <div class="relative">
                <input type="password" name="password" placeholder="Contraseña" required
                       class="glass-input w-full py-4 px-6 rounded-full text-center text-lg shadow-inner">
            </div>

            <button type="submit"
                    class="mt-4 w-full bg-white text-gray-800 font-bold text-xl py-4 rounded-full hover:bg-gray-100 hover:scale-[1.02] transition-all duration-300 shadow-lg cursor-pointer">
                Entrar
            </button>
        </form>

        <div class="absolute bottom-6 text-[10px] opacity-70 tracking-wider">
            create by kalkomerlies
        </div>

    </div>

</div>
