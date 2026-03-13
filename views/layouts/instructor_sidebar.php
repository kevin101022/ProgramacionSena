<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'asignaciones';
$instructorNombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Instructor';
?>
<aside class="sidebar glass-container">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img">
            <div class="logo-divider"></div>
            <span class="logo-text">Programaciones</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section">Mi Espacio</p>
        <a href="../asignacion/instructor_index.php" class="nav-item <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/home-outline.svg"></ion-icon>
            Dashboard
        </a>
        <a href="../asignacion/instructor_calendario.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
            Asignaciones
        </a>
        <a href="../instructor/competencias.php" class="nav-item <?php echo ($activeNavItem === 'mis_competencias') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
            Competencias
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="profile-img bg-[#39A900] text-white flex items-center justify-center font-bold text-xl rounded-full h-10 w-10">
                <?php echo strtoupper(substr($instructorNombre, 0, 1)); ?>
            </div>
            <div class="profile-info">
                <p class="profile-name"><?php echo htmlspecialchars($instructorNombre); ?></p>
                <p class="profile-role text-xs text-slate-500">Instructor SENA</p>
            </div>
            <a href="../../routing.php?controller=login&action=logout" class="text-slate-500 hover:text-red-500 transition-colors" title="Cerrar Sesión">
                <ion-icon src="../../assets/ionicons/log-out-outline.svg"></ion-icon>
            </a>
        </div>
    </div>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Mobile sidebar toggle ---
        const btn = document.getElementById('mobileMenuBtn');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (btn && sidebar && overlay) {
            function toggleMenu() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            }
            btn.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);
        }

        // --- Custom table scroll indicator for mobile ---
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function setupTableScroll(container) {
            if (container.dataset.scrollSetup) return;
            container.dataset.scrollSetup = '1';

            // Wrap in a scroll-wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'table-scroll-wrapper';
            container.parentNode.insertBefore(wrapper, container);
            wrapper.appendChild(container);

            // Create track
            const track = document.createElement('div');
            track.className = 'table-scroll-track';
            const thumb = document.createElement('div');
            thumb.className = 'table-scroll-thumb';
            track.appendChild(thumb);
            wrapper.appendChild(track);

            // Hint text
            const hint = document.createElement('div');
            hint.className = 'table-scroll-hint';
            hint.innerHTML = '<ion-icon src="../../assets/ionicons/swap-horizontal-outline.svg"></ion-icon> Deslizar para ver más';
            wrapper.appendChild(hint);

            function updateThumb() {
                const scrollWidth = container.scrollWidth;
                const clientWidth = container.clientWidth;
                if (scrollWidth <= clientWidth) {
                    track.style.display = 'none';
                    hint.style.display = 'none';
                    if (wrapper.querySelector('.table-scroll-wrapper::after'))
                        wrapper.classList.add('scrolled-end');
                    return;
                }
                track.style.display = 'block';
                hint.style.display = 'flex';

                const ratio = clientWidth / scrollWidth;
                const thumbWidth = Math.max(ratio * 100, 15);
                const scrollLeft = container.scrollLeft;
                const maxScroll = scrollWidth - clientWidth;
                const thumbPos = (scrollLeft / maxScroll) * (100 - thumbWidth);

                thumb.style.width = thumbWidth + '%';
                thumb.style.left = thumbPos + '%';

                // Update fade gradient
                if (scrollLeft >= maxScroll - 5) {
                    wrapper.classList.add('scrolled-end');
                } else {
                    wrapper.classList.remove('scrolled-end');
                }
            }

            container.addEventListener('scroll', updateThumb);
            window.addEventListener('resize', updateThumb);

            // Drag thumb to scroll
            let dragging = false;
            let startX = 0;
            let startScrollLeft = 0;

            function onDragStart(e) {
                dragging = true;
                startX = (e.touches ? e.touches[0].clientX : e.clientX);
                startScrollLeft = container.scrollLeft;
                e.preventDefault();
            }

            function onDragMove(e) {
                if (!dragging) return;
                const x = (e.touches ? e.touches[0].clientX : e.clientX);
                const dx = x - startX;
                const trackWidth = track.clientWidth;
                const scrollWidth = container.scrollWidth - container.clientWidth;
                const scrollDelta = (dx / trackWidth) * container.scrollWidth;
                container.scrollLeft = startScrollLeft + scrollDelta;
                e.preventDefault();
            }

            function onDragEnd() {
                dragging = false;
            }

            thumb.addEventListener('mousedown', onDragStart);
            thumb.addEventListener('touchstart', onDragStart, { passive: false });
            document.addEventListener('mousemove', onDragMove);
            document.addEventListener('touchmove', onDragMove, { passive: false });
            document.addEventListener('mouseup', onDragEnd);
            document.addEventListener('touchend', onDragEnd);

            // Click on track to jump
            track.addEventListener('click', (e) => {
                if (e.target === thumb) return;
                const rect = track.getBoundingClientRect();
                const clickRatio = (e.clientX - rect.left) / rect.width;
                container.scrollLeft = clickRatio * (container.scrollWidth - container.clientWidth);
            });

            // Initial update
            setTimeout(updateThumb, 300);
            setTimeout(updateThumb, 1000);
        }

        function initAllTables() {
            if (!isMobile()) return;
            document.querySelectorAll('.table-container').forEach(setupTableScroll);
        }

        initAllTables();
        // Re-init on dynamic content load
        const observer = new MutationObserver(() => {
            if (isMobile()) initAllTables();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>