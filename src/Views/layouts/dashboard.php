<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SOS-Nómina' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/dashboard.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        button, a, [role="button"], input, select, textarea { touch-action: manipulation; }

        /* ── Select2 + Tailwind ──────────────────────────────────── */
        .select2-container--default .select2-selection--single {
            border-color: #D1D5DB; height: 38px; padding: 5px; border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container { width: 100% !important; }

        /* ── SIDEBAR ─────────────────────────────────────────────── */
        #sidebar {
            width: 240px;
            min-height: calc(100vh - 56px);
            background: #1a2d3a;
            transition: width 0.28s ease;
            overflow: hidden;
            flex-shrink: 0;
        }
        #sidebar.collapsed { width: 64px; }

        .sb-label {
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s, max-width 0.25s;
            max-width: 160px;
            opacity: 1;
        }
        #sidebar.collapsed .sb-label { opacity: 0; max-width: 0; }

        .sb-chevron { transition: transform 0.25s; flex-shrink: 0; }
        #sidebar.collapsed .sb-chevron { display: none; }

        .sb-item.active, .sb-item:hover { background: rgba(255,255,255,0.08); }

        .sb-tooltip {
            display: none;
            position: absolute;
            left: 64px;
            top: 50%;
            transform: translateY(-50%);
            background: #1a2d3a;
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 9999;
            pointer-events: none;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.4);
        }
        #sidebar.collapsed .sb-item { position: relative; }
        #sidebar.collapsed .sb-item:hover .sb-tooltip { display: block; }

        @media (max-width: 1024px) { main.flex-1 { padding: 0.65rem !important; } }

        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                top: 56px;
                left: 0;
                height: calc(100vh - 56px);
                z-index: 8000;
                width: 240px !important;
                transform: translateX(-100%);
                transition: transform 0.26s ease;
                display: block !important;
                box-shadow: 4px 0 16px rgba(0,0,0,0.35);
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #mobileOverlay {
                display: none;
                position: fixed;
                inset: 56px 0 0 0;
                background: rgba(0,0,0,0.45);
                z-index: 7999;
            }
            #mobileOverlay.visible { display: block; }
            #sidebarToggle { display: flex !important; }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-100 text-gray-800">

    <!-- ══ HEADER ══════════════════════════════════════════════════ -->
    <header class="bg-gray-800 text-white shadow-sm" style="height:56px;">
        <div class="px-4 h-full flex items-center justify-between">

            <div class="flex items-center gap-3">
                <button id="sidebarToggle"
                    class="text-gray-300 hover:text-white focus:outline-none p-1 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="/sistemas" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-teal-600 rounded flex items-center justify-center
                                font-bold text-sm hover:bg-teal-700 transition text-white">
                        SN
                    </div>
                    <div class="leading-tight">
                        <div class="font-semibold text-sm">SOS-Nómina</div>
                        <div class="text-xs text-gray-400">Sistema de Nómina</div>
                    </div>
                </a>
            </div>

            <?php if (isset($_SESSION['user'])): ?>
            <div class="flex items-center gap-3 relative">
                <a href="/sistemas" title="Inicio" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.75z"/>
                    </svg>
                </a>
                <button onclick="toggleUserMenu()"
                    class="flex items-center gap-2 text-sm hover:text-gray-300 focus:outline-none">
                    <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuario') ?></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="userMenu"
                    class="hidden absolute right-0 top-10 w-52 bg-white border rounded-lg shadow-lg z-50">
                    <a href="/usuarios/create"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Crear usuario
                    </a>
                    <a href="/permisos"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Gestionar permisos
                    </a>
                    <a href="/perfiles"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Gestionar perfiles
                    </a>
                    <div class="border-t"></div>
                    <a href="/logout"
                        class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        Cerrar sesión
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- ══ BODY: sidebar + contenido ══════════════════════════════ -->
    <div class="flex flex-1">

        <!-- ── SIDEBAR ───────────────────────────────────────────── -->
        <aside id="sidebar">
            <nav class="py-3">
                <?php
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                function sbItem(string $href, string $label, string $svgPath, string $current): void {
                    $active = ($href === '/sistemas')
                        ? ($current === '/sistemas' ? 'active' : '')
                        : (str_starts_with($current, $href) ? 'active' : '');
                    echo <<<HTML
                    <a href="{$href}" class="sb-item {$active} flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white cursor-pointer text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {$svgPath}
                        </svg>
                        <span class="sb-label">{$label}</span>
                        <span class="sb-tooltip">{$label}</span>
                    </a>
                    HTML;
                }
                ?>

                <!-- Inicio -->
                <?php sbItem(
                    '/sistemas', 'Inicio',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 3h7v7H3zm11 0h7v7h-7zM3 14h7v7H3zm11 0h7v7h-7z"/>',
                    $currentPath
                ); ?>

                <div class="border-t border-gray-700 mx-4 my-2"></div>

                <!-- Nómina -->
                <?php sbItem(
                    '/sistemas/nomina', 'Nómina',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    $currentPath
                ); ?>

                <!-- Comercial -->
                <?php sbItem(
                    '/sistemas/comercial', 'Comercial',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
                    $currentPath
                ); ?>

                <!-- Contable -->
                <?php sbItem(
                    '/sistemas/contable', 'Contable',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                    $currentPath
                ); ?>

                <!-- Administrativo -->
                <?php sbItem(
                    '/sistemas/administrativo', 'Administrativo',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
                    $currentPath
                ); ?>

                <!-- Archivos Generales -->
                <?php sbItem(
                    '/sistemas/archivos', 'Archivos Generales',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
                    $currentPath
                ); ?>

            </nav>
        </aside>

        <!-- ── ÁREA DE TRABAJO ──────────────────────────────────────── -->
        <main class="flex-1 overflow-auto p-6 pb-20"
              onclick="if(typeof closeMobileMenu==='function' && document.getElementById('mobileOverlay') && document.getElementById('mobileOverlay').classList.contains('visible')) closeMobileMenu()">
            <?= $content ?>
        </main>

    </div>

    <footer class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t text-center py-3 text-xs text-gray-400 shadow-sm">
        &copy; <?= date('Y') ?> SOS Technology | SOS-Nómina
    </footer>

    <script>
        const sidebar = document.getElementById('sidebar');
        const savedSidebar = localStorage.getItem('snSidebarCollapsed');
        if (savedSidebar === '1' || (savedSidebar === null && window.innerWidth <= 1024)) {
            sidebar.classList.add('collapsed');
        }

        document.getElementById('sidebarToggle').addEventListener('click', function () {
            if (isMobile()) {
                const isOpen = sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('visible', isOpen);
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('snSidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            }
        });

        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const menu = document.getElementById('userMenu');
            if (menu && !menu.contains(e.target) && !e.target.closest('button[onclick="toggleUserMenu()"]')) {
                menu.classList.add('hidden');
            }
        });

        const overlay = document.createElement('div');
        overlay.id = 'mobileOverlay';
        overlay.onclick = closeMobileMenu;
        document.body.appendChild(overlay);

        function isMobile() { return window.innerWidth <= 768; }

        function closeMobileMenu() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('visible');
        }
    </script>

</body>
</html>
