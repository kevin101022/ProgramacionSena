<?php
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session parameters before starting
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = isset($_GET['error']) ? $_GET['error'] : '';
$success_message = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login - Programaciones SENA</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS via CDN with Container Queries and Forms plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#39A900", // SENA Green
                        "primary-dark": "#2d8a00",
                        secondary: "#00324D", // SENA Blue
                    },
                    fontFamily: {
                        sans: ["Manrope", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body class="font-sans antialiased text-slate-800 bg-white h-screen overflow-hidden selection:bg-primary selection:text-white">

    <!-- SPLIT SCREEN LAYOUT -->
    <div class="flex h-full w-full relative">

        <!-- LEFT SIDE: Glassmorphism / Image Background (Hidden on very small screens) -->
        <div class="hidden lg:flex w-1/2 relative bg-slate-900 items-center justify-center overflow-hidden h-full">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="SENA Background" class="w-full h-full object-cover opacity-60 mix-blend-overlay transition-transform duration-[20s] ease-in-out hover:scale-110" />
            </div>

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/80 to-secondary/90 z-10"></div>

            <!-- Abstract Shapes -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-20 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-[30rem] h-[30rem] bg-primary/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Content Container -->
            <div class="relative z-30 flex flex-col items-start justify-center h-full px-16 xl:px-24">
                <div class="glass-panel p-10 rounded-3xl border border-white/20 shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] backdrop-blur-md bg-white/10 text-white max-w-lg">
                    <img src="assets/imagenes/LOGOsena.png" alt="SENA Logo" class="h-20 mb-8 filter brightness-0 invert drop-shadow-md">
                    <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-6 tracking-tight drop-shadow-sm">
                        Sistema de <br />
                        <span class="text-green-300">Programación</span>
                    </h1>
                    <p class="text-lg text-slate-100/90 leading-relaxed font-medium">
                        Plataforma integral para la gestión, asignación y seguimiento de instructores y ambientes de formación.
                    </p>

                    <div class="mt-10 flex items-center gap-4 text-sm font-medium text-white/80">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-400">check_circle</span>
                            Optimización
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-white/40"></div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-400">insights</span>
                            Control
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-16 xl:p-24 bg-white relative h-full overflow-y-auto custom-scrollbar">
            <!-- Mobile Background (Only visible on small screens) -->
            <div class="absolute inset-0 z-0 lg:hidden block">
                <img src="assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="Background" class="w-full h-full object-cover opacity-10">
                <div class="absolute inset-0 bg-white/90 backdrop-blur-sm"></div>
            </div>

            <div class="w-full max-w-[420px] relative z-10">

                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-8">
                    <img src="assets/imagenes/LOGOsena.png" alt="Logo SENA" class="h-16 w-auto object-contain">
                </div>

                <div class="mb-10 lg:text-left text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-3">
                        Bienvenido de nuevo
                    </h2>
                    <p class="text-[15px] text-slate-500 font-medium">
                        Por favor, ingresa tus credenciales para acceder.
                    </p>
                </div>

                <!-- Messages -->
                <?php if ($error_message): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-8 flex items-start gap-3 shadow-sm animate-fade-in">
                        <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="bg-green-50 border-l-4 border-primary text-slate-700 p-4 rounded-r-lg mb-8 flex items-start gap-3 shadow-sm animate-fade-in">
                        <span class="material-symbols-outlined text-primary mt-0.5">check_circle</span>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($success_message); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="routing.php?controller=login&action=login" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700" for="email">
                            Correo Electrónico
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </div>
                            <input autocomplete="email" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="email" name="email" placeholder="ejemplo@correo.com" required="" type="email" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-slate-700" for="password">
                                Contraseña
                            </label>
                            <a class="text-sm text-primary hover:text-primary-dark font-semibold transition-colors" href="#">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative group rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input autocomplete="current-password" class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-12 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="password_input" name="password" placeholder="••••••••" required="" type="password" />
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center justify-center w-12 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" onclick="togglePassword()">
                                <span class="material-symbols-outlined text-[20px]" id="toggle_icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border-2 border-slate-300 checked:border-primary checked:bg-primary transition-all" id="remember-me" name="remember-me" type="checkbox" />
                                <span class="material-symbols-outlined absolute text-white opacity-0 peer-checked:opacity-100 pointer-events-none text-[16px]">check</span>
                            </div>
                            <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Mantener sesión iniciada</span>
                        </label>
                    </div>

                    <div class="pt-4">
                        <button class="relative flex w-full justify-center items-center rounded-xl bg-primary px-4 py-4 text-[15px] font-bold text-white shadow-lg shadow-primary/30 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] outline-none overflow-hidden group" type="submit">
                            <span class="relative z-10 flex items-center gap-2">
                                Iniciar Sesión
                                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </span>
                            <!-- Button Shine Effect -->
                            <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 z-0"></div>
                        </button>
                    </div>
                </form>

                <!-- Creación de cuentas fue centralizada. Por políticas de seguridad ya no existe autoregistro. -->

                <!-- Copyright Footer -->
                <div class="mt-16 text-center lg:text-left">
                    <p class="text-xs text-slate-400 font-medium">
                        &copy; <?php echo date('Y'); ?> SENA. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password_input');
            const icon = document.getElementById('toggle_icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerText = 'visibility_off';
                icon.classList.add('text-primary');
            } else {
                input.type = 'password';
                icon.innerText = 'visibility';
                icon.classList.remove('text-primary');
            }
        }
    </script>
</body>

</html>