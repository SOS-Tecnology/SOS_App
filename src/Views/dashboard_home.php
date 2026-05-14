<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
?>

<div>
    <p class="text-gray-500 text-sm mb-6">
        Bienvenido, <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></strong>.
        Selecciona un módulo para comenzar.
    </p>

    <div class="dashboard-grid">

        <!-- Empleados -->
        <a href="/empleados"
            class="bg-teal-700 hover:bg-teal-800 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="text-xl font-bold">Empleados</h3>
            <p class="text-sm opacity-80 mt-2">Gestión de empleados y contratos.</p>
        </a>

        <!-- Novedades -->
        <a href="/novedades"
            class="bg-blue-700 hover:bg-blue-800 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                       m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <h3 class="text-xl font-bold">Novedades</h3>
            <p class="text-sm opacity-80 mt-2">Registro de novedades de nómina.</p>
        </a>

        <!-- Informes -->
        <a href="/informes"
            class="bg-indigo-700 hover:bg-indigo-800 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9
                       a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0
                       012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="text-xl font-bold">Informes</h3>
            <p class="text-sm opacity-80 mt-2">Reportes y consultas de nómina.</p>
        </a>

        <!-- Nómina Electrónica -->
        <a href="/nomina-electronica"
            class="bg-emerald-700 hover:bg-emerald-800 text-white rounded-2xl shadow-lg p-8
                   flex flex-col items-center justify-center text-center
                   transition transform hover:-translate-y-1 min-h-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mb-4 opacity-90"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                       a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-bold">Nómina Electrónica</h3>
            <p class="text-sm opacity-80 mt-2">Generación y transmisión DIAN.</p>
        </a>

    </div>
</div>
