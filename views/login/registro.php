<?php
$pageTitle = 'Registro Coordinador - Programaciones';
if (session_status() === PHP_SESSION_NONE) {
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
    <title>Registro Coordinador - Programaciones SENA</title>
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

        <div class="relative w-full max-w-[440px] z-10 flex flex-col items-center">

            <!-- Main White Card -->
            <div class="w-full bg-white shadow-2xl rounded-2xl px-8 py-10 w-full relative z-10 border border-slate-200/60">
                <div class="mb-6 flex justify-center">
                    <img src="assets/imagenes/LOGOsena.png" alt="Logo SENA" class="h-16 w-auto object-contain">
                </div>
                <div class="mb-8 text-center">
                    <h2 class="text-[28px] leading-tight font-bold tracking-tight text-slate-800 mb-2">
                        Registro de Coordinador
                    </h2>
                    <p class="text-[15px] text-slate-500">
                        Vincúlate a tu Centro de Formación.
                    </p>
                </div>

                <?php if ($error_message): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
                        <span class="material-symbols-outlined text-xl">error</span>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (empty($coordinaciones)): ?>
                    <div class="bg-amber-50 border border-amber-200 text-amber-600 text-sm p-6 rounded-xl text-center shadow-sm">
                        <span class="material-symbols-outlined text-4xl mb-2">warning</span>
                        <h3 class="font-bold text-lg mb-1">Sin Vacantes</h3>
                        <p>No hay posiciones de coordinación disponibles en este momento. Todas las plazas están ocupadas.</p>
                        <a href="routing.php?controller=login&action=showLogin" class="inline-block mt-4 text-[#39A900] font-medium hover:underline">Volver al inicio</a>
                    </div>
                <?php else: ?>

                    <form action="routing.php?controller=login&action=guardarCoordinador" class="space-y-4" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                        <div class="flex flex-col gap-1.5">
                            <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="coordinacion_id">
                                Selecciona tu Coordinación / Centro
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">domain</span>
                                <select name="coordinacion_id" id="coordinacion_id" required class="block w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors outline-none appearance-none">
                                    <option value="" disabled selected>-- Elige una coordinación disponible --</option>
                                    <?php foreach ($coordinaciones as $coord): ?>
                                        <option value="<?php echo htmlspecialchars($coord['coord_id']); ?>">
                                            <?php echo htmlspecialchars($coord['coord_descripcion']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="nombre">
                                Nombre Completo
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                                <input class="block w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required="" type="text" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="correo">
                                Correo Institucional (@sena.edu.co)
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                                <input class="block w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="correo" name="correo" placeholder="ejemplo@sena.edu.co" required="" type="email" pattern="^[a-zA-Z0-9._%+-]+@sena\.edu\.co$" title="Debe ser un correo válido terminado en @sena.edu.co" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="password">
                                    Contraseña
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock</span>
                                    <input class="block w-full rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-9 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="password_input" name="password" placeholder="••••••••" required="" type="password" minlength="8" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" onclick="togglePassword('password_input', 'toggle_icon')">
                                        <span class="material-symbols-outlined text-[20px]" id="toggle_icon">visibility</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="block text-[13px] font-semibold text-slate-700 ml-0.5" for="password_confirm">
                                    Confirmar Contraseña
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock_outline</span>
                                    <input class="block w-full rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-9 text-slate-900 shadow-sm focus:border-[#39A900] focus:ring-1 focus:ring-[#39A900] sm:text-sm transition-colors placeholder:text-slate-400 outline-none" id="password_confirm_input" name="password_confirm" placeholder="••••••••" required="" type="password" minlength="8" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" onclick="togglePassword('password_confirm_input', 'toggle_icon_confirm')">
                                        <span class="material-symbols-outlined text-[20px]" id="toggle_icon_confirm">visibility</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button class="flex w-full justify-center items-center gap-2 rounded-xl bg-[#39A900] px-4 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-[#39A900]/90 transition-all duration-200 active:scale-[0.99] outline-none" type="submit">
                                <span class="material-symbols-outlined text-lg">how_to_reg</span>
                                Completar Registro
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <p class="mt-8 text-center text-[13px] text-slate-500">
                    ¿Ya tienes cuenta?
                    <a class="font-medium text-[#39A900] hover:text-[#39A900]/80 hover:underline underline-offset-2 transition-all" href="routing.php?controller=login&action=showLogin">
                        Inicia Sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
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