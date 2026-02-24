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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#38a800", // Adapted to SENA green from #195de6
                        "background-light": "#f6f6f8",
                        "background-dark": "#020617",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body class="font-display bg-slate-50 text-slate-800 antialiased selection:bg-[#39A900] selection:text-white">
    <!-- Full Background with Image and Light Overlay -->
    <div class="relative flex min-h-screen w-full flex-col justify-center items-center overflow-hidden bg-cover bg-center py-12 sm:px-6 lg:px-8" style="background-image: url('assets/imagenes/Foto-nota-2024-01-03T181318.611.webp');">
        <!-- Bright, semi-transparent overlay to keep it clear but readable -->
        <div class="absolute inset-0 bg-slate-100/60 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-[400px] z-10 flex flex-col items-center">

            <!-- Main White Card -->
            <div class="w-full bg-white shadow-2xl rounded-2xl px-8 py-10 w-full relative z-10 border border-slate-200/60">
                <div class="mb-6 flex justify-center">
                    <img src="assets/imagenes/LOGOsena.png" alt="Logo SENA" class="h-16 w-auto object-contain">
                </div>
                <div class="mb-8 text-center">
                    <h2 class="text-[28px] leading-tight font-bold tracking-tight text-slate-800 mb-2">
                        Bienvenido al sistema
                    </h2>
                    <p class="text-[15px] text-slate-500">
                        Ingresa a tu cuenta para continuar.
                    </p>
                </div>

                <?php if ($error_message): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-xl">error</span>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="bg-green-50 border border-green-200 text-[#39A900] text-sm p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                        <p><?php echo htmlspecialchars($success_message); ?></p>
                    </div>
                <?php endif; ?>

                <form action="routing.php?controller=login&action=login" class="space-y-5" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="email">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <input autocomplete="email" class="block w-full rounded-xl border border-slate-300 bg-white py-3.5 px-4 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="email" name="email" placeholder="ejemplo@sena.edu.co" required="" type="email" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="password">
                            Contraseña
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <input autocomplete="current-password" class="block w-full rounded-xl border border-slate-300 bg-white py-3.5 px-4 pr-11 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="password_input" name="password" placeholder="••••••••" required="" type="password" />
                            <div class="absolute inset-y-0 right-0 flex items-center justify-center w-11 cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" onclick="togglePassword()">
                                <span class="material-symbols-outlined text-lg" id="toggle_icon">visibility</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input class="w-4 h-4 rounded border-slate-300 text-[#39A900] focus:ring-[#39A900] bg-white" id="remember-me" name="remember-me" type="checkbox" />
                            <span class="text-[13px] text-slate-600 group-hover:text-slate-900 transition-colors">Recordarme</span>
                        </label>
                        <a class="text-[13px] text-[#39A900] hover:text-[#39A900]/80 transition-colors font-medium" href="#">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="pt-2">
                        <button class="flex w-full justify-center items-center rounded-xl bg-[#39A900] px-4 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-[#39A900]/90 transition-all duration-200 active:scale-[0.99] outline-none" type="submit">
                            Iniciar Sesión
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-[13px] text-slate-500">
                    ¿Eres Coordinador nuevo?
                    <a class="font-medium text-[#39A900] hover:text-[#39A900]/80 hover:underline underline-offset-2 transition-all" href="routing.php?controller=login&action=registroCoordinador">
                        Regístrate aquí
                    </a>
                </p>
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
            } else {
                input.type = 'password';
                icon.innerText = 'visibility';
            }
        }
    </script>
</body>

</html>