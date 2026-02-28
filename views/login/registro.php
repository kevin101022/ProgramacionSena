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
                        Registro de <br />
                        <span class="text-green-300">Coordinador</span>
                    </h1>
                    <p class="text-lg text-slate-100/90 leading-relaxed font-medium">
                        Crea tu cuenta institucional y vincúlate inmediatamente a tu Centro de Formación correspondiente.
                    </p>

                    <div class="mt-10 flex items-center gap-4 text-sm font-medium text-white/80">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-400">shield_person</span>
                            Seguridad
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-white/40"></div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-400">admin_panel_settings</span>
                            Gestión Total
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Registration Form -->
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

                <div class="mb-8 lg:text-left text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">
                        Crea tu cuenta
                    </h2>
                    <p class="text-[15px] text-slate-500 font-medium">
                        Por favor, completa los siguientes datos para registrarte.
                    </p>
                </div>

                <!-- Messages -->
                <?php if ($error_message): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-8 flex items-start gap-3 shadow-sm animate-fade-in">
                        <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (empty($centros)): ?>
                    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-6 rounded-r-xl shadow-sm animate-fade-in relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl">warning</span>
                        </div>
                        <div class="flex items-start gap-4 relative z-10">
                            <span class="material-symbols-outlined text-amber-500 text-3xl">warning</span>
                            <div>
                                <h3 class="font-bold text-lg mb-1">Sin Centros de Formación Disponibles</h3>
                                <p class="text-sm font-medium mb-4">No hay centros de formación registrados en el sistema en este permiso.</p>
                                <a href="routing.php?controller=login&action=showLogin" class="inline-flex items-center gap-1 text-sm font-bold text-amber-600 hover:text-amber-800 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                    Volver al inicio de sesión
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>

                    <!-- Form -->
                    <form action="routing.php?controller=login&action=guardarCoordinador" method="POST" class="space-y-5">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                        <div class="space-y-1.5">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700" for="centro_id">
                                    Centro de Formación
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">domain</span>
                                    </div>
                                    <select name="centro_id" id="centro_id" required class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-10 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 outline-none appearance-none cursor-pointer" onchange="loadCoordinaciones()">
                                        <option value="" disabled selected>Seleccione su Centro de Formación</option>
                                        <?php foreach ($centros as $centro): ?>
                                            <option value="<?php echo htmlspecialchars($centro['cent_id']); ?>">
                                                <?php echo htmlspecialchars($centro['cent_nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700" for="coordinacion_id">
                                    Coordinación a la que aplica
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">work</span>
                                    </div>
                                    <select name="coordinacion_id" id="coordinacion_id" required disabled class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-10 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 outline-none appearance-none cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="" disabled selected>Primero seleccione un Centro de Formación</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700" for="documento">
                                    Número de Documento
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">badge</span>
                                    </div>
                                    <input class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="documento" name="documento" placeholder="Ej: 1090123456" required="" type="text" />
                                </div>
                            </div>



                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700" for="nombre">
                                    Nombre Completo
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">person</span>
                                    </div>
                                    <input class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required="" type="text" />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-semibold text-slate-700" for="correo">
                                    Correo Institucional
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">mail</span>
                                    </div>
                                    <input class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 sm:text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="correo" name="correo" placeholder="ejemplo@correo.com" required="" type="email" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-semibold text-slate-700" for="password">
                                        Contraseña
                                    </label>
                                    <div class="relative group rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">lock</span>
                                        </div>
                                        <input class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-10 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="password_input" name="password" placeholder="••••••••" required="" type="password" minlength="8" />
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" onclick="togglePassword('password_input', 'toggle_icon')">
                                            <span class="material-symbols-outlined text-[18px]" id="toggle_icon">visibility</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-sm font-semibold text-slate-700" for="password_confirm">
                                        Confirmar
                                    </label>
                                    <div class="relative group rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">lock_outline</span>
                                        </div>
                                        <input class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3 pl-10 pr-10 text-slate-900 shadow-sm focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 text-sm transition-all duration-200 placeholder:text-slate-400 outline-none" id="password_confirm_input" name="password_confirm" placeholder="••••••••" required="" type="password" minlength="8" />
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" onclick="togglePassword('password_confirm_input', 'toggle_icon_confirm')">
                                            <span class="material-symbols-outlined text-[18px]" id="toggle_icon_confirm">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-5">
                                <button class="relative flex w-full justify-center items-center rounded-xl bg-primary px-4 py-4 text-[15px] font-bold text-white shadow-lg shadow-primary/30 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300 active:scale-[0.98] outline-none overflow-hidden group" type="submit">
                                    <span class="relative z-10 flex items-center gap-2">
                                        Completar Registro
                                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">how_to_reg</span>
                                    </span>
                                    <!-- Button Shine Effect -->
                                    <div class="absolute inset-0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 z-0"></div>
                                </button>
                            </div>
                    </form>
                <?php endif; ?>

                <div class="mt-10 text-center lg:text-left">
                    <p class="text-sm font-medium text-slate-500">
                        ¿Ya tienes cuenta?
                        <a class="font-bold text-primary hover:text-primary-dark ml-1 hover:underline underline-offset-4 transition-all" href="routing.php?controller=login&action=showLogin">
                            Inicia Sesión aquí
                        </a>
                    </p>
                </div>

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
                icon.classList.add('text-primary');
            } else {
                input.type = 'password';
                icon.innerText = 'visibility';
                icon.classList.remove('text-primary');
            }
        }

        async function loadCoordinaciones() {
            const centroSelect = document.getElementById('centro_id');
            const coordSelect = document.getElementById('coordinacion_id');
            const centroId = centroSelect.value;

            coordSelect.innerHTML = '<option value="" disabled selected>Cargando coordinaciones...</option>';
            coordSelect.disabled = true;

            if (centroId) {
                try {
                    const response = await fetch(`routing.php?controller=login&action=getCoordinacionesByCentro&centro_id=${centroId}`);
                    const coordinaciones = await response.json();

                    coordSelect.innerHTML = '<option value="" disabled selected>Seleccione la coordinación...</option>';

                    if (coordinaciones.length > 0) {
                        coordinaciones.forEach(coord => {
                            const option = document.createElement('option');
                            option.value = coord.coord_id;
                            option.textContent = coord.coord_descripcion;
                            coordSelect.appendChild(option);
                        });
                        coordSelect.disabled = false;
                    } else {
                        coordSelect.innerHTML = '<option value="" disabled selected>No hay coordinaciones disponibles</option>';
                    }
                } catch (error) {
                    console.error('Error al cargar coordinaciones:', error);
                    coordSelect.innerHTML = '<option value="" disabled selected>Error de conexión</option>';
                }
            }
        }
    </script>
</body>

</html>