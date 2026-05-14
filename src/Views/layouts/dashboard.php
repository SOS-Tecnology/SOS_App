<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SOS-Nómina' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/dashboard.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        button, a, [role="button"], input, select, textarea { touch-action: manipulation; }

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

        .sb-sub { overflow: hidden; max-height: 0; transition: max-height 0.3s ease; }
        .sb-sub.open { max-height: 500px; }
        #sidebar.collapsed .sb-sub { max-height: 0 !important; }

        .sb-item.active, .sb-item:hover { background: rgba(255,255,255,0.08); }
        .sb-subitem:hover { background: rgba(255,255,255,0.06); }
        .sb-subitem.active { background: rgba(255,255,255,0.10); }

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
                <a href="/dashboard_home" class="flex items-center gap-2">
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
                <a href="/dashboard_home" title="Inicio" class="text-gray-400 hover:text-white transition">
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
                    $active = (str_starts_with($current, $href) && $href !== '/dashboard_home')
                              || $current === $href ? 'active' : '';
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

                $archivosItems = [
                    ['tipos-documento',        'Tipos de Documento'],
                    ['periodos-liquidacion',    'Período de Liquidación'],
                    ['tipos-trabajador-pila',   'Tipos Trabajador PILA'],
                    ['subtipos-trabajador',     'Sub Tipo Trabajador'],
                    ['tipos-contrato',          'Tipo de Contrato'],
                    ['tipos-incapacidad',       'Tipo de Incapacidad'],
                    ['tabla-riesgos',           'Tabla Riesgos Prof.'],
                    ['fondos-solidaridad',      'Fondo de Solidaridad P.'],
                    ['eps',                     'Empresas Prestadoras EPS'],
                    ['fondos-cesantias',        'Fondos de Cesantías'],
                    ['entidades-riesgos',       'Entidades de Riesgos Prof.'],
                    ['cajas-compensacion',      'Cajas de Compensa. Fam.'],
                ];
                $enArchivos = str_starts_with($currentPath, '/archivos');
                ?>

                <!-- Panel -->
                <?php sbItem(
                    '/dashboard_home', 'Panel',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 3h7v7H3zm11 0h7v7h-7zM3 14h7v7H3zm11 0h7v7h-7z"/>',
                    $currentPath
                ); ?>

                <!-- Empleados -->
                <?php sbItem(
                    '/empleados', 'Empleados',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                    $currentPath
                ); ?>

                <!-- Novedades -->
                <?php sbItem(
                    '/novedades', 'Novedades',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                    $currentPath
                ); ?>

                <!-- Informes -->
                <?php sbItem(
                    '/informes', 'Informes',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9
                           a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0
                           012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                    $currentPath
                ); ?>

                <!-- Nómina Electrónica -->
                <?php sbItem(
                    '/nomina-electronica', 'Nómina Electrónica',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                           a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    $currentPath
                ); ?>

                <div class="border-t border-gray-700 mx-4 my-2"></div>

                <!-- Archivos (con submenú) -->
                <div class="sb-item <?= $enArchivos ? 'active' : '' ?> flex items-center gap-3 px-4 py-2.5 text-gray-300
                     hover:text-white cursor-pointer text-sm select-none"
                     onclick="toggleArchivos(this)">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1
                               M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="sb-label flex-1">Archivos</span>
                    <svg class="sb-chevron w-4 h-4 <?= $enArchivos ? 'rotate-90' : '' ?>"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="sb-tooltip">Archivos</span>
                </div>
                <div class="sb-sub <?= $enArchivos ? 'open' : '' ?>" id="sb-archivos">
                    <?php foreach ($archivosItems as [$slug, $label]):
                        $href   = '/archivos/' . $slug;
                        $active = $currentPath === $href ? 'active' : '';
                    ?>
                    <a href="<?= $href ?>"
                       class="sb-subitem <?= $active ?> flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <?= htmlspecialchars($label) ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-gray-700 mx-4 my-2"></div>

                <!-- Usuarios -->
                <?php sbItem(
                    '/usuarios', 'Usuarios',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87
                           M12 12a4 4 0 100-8 4 4 0 000 8z"/>',
                    $currentPath
                ); ?>

                <!-- Perfiles -->
                <?php sbItem(
                    '/perfiles', 'Perfiles',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066
                           c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35
                           a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065
                           c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37
                           a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573
                           c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
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

        function toggleArchivos(el) {
            if (sidebar.classList.contains('collapsed')) return;
            const sub     = document.getElementById('sb-archivos');
            const chevron = el.querySelector('.sb-chevron');
            sub.classList.toggle('open');
            chevron.classList.toggle('rotate-90');
        }

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
