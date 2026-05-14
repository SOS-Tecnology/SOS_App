<div class="max-w-3xl mx-auto mt-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Detalle de novedad</h1>
            <p class="text-sm text-gray-500 mt-0.5">Consulta la información completa de esta novedad.</p>
        </div>
        <a href="/novedades" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al listado
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Código</h3>
                <p class="mt-2 text-gray-800 font-semibold"><?= htmlspecialchars($novedad['codigo'] ?? '—') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre</h3>
                <p class="mt-2 text-gray-800 font-semibold"><?= htmlspecialchars($novedad['nombre'] ?? '—') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tipo</h3>
                <p class="mt-2 text-gray-600"><?= htmlspecialchars($novedad['tipo'] ?? '—') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pagar en</h3>
                <p class="mt-2 text-gray-600"><?= htmlspecialchars($novedad['pagar_en'] ?? '—') ?></p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Cantidad</h3>
                <p class="mt-2 text-gray-600"><?= htmlspecialchars($novedad['cantidad'] ?? '0') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">% H.E.</h3>
                <p class="mt-2 text-gray-600"><?= htmlspecialchars($novedad['porcentaje_he'] ?? '—') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Fórmula</h3>
                <p class="mt-2 text-gray-600"><?= htmlspecialchars($novedad['formula'] ?? '—') ?></p>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</h3>
                <span class="<?= !empty($novedad['activo']) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?> text-xs font-semibold px-2.5 py-1 rounded-full">
                    <?= !empty($novedad['activo']) ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-gray-50 rounded-2xl p-4">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Novedad fija</h3>
                <p class="mt-2 text-gray-700"><?= !empty($novedad['novedad_fija']) ? 'Sí' : 'No' ?></p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Salario</h3>
                <p class="mt-2 text-gray-700"><?= !empty($novedad['salario']) ? 'Sí' : 'No' ?></p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Hora extra</h3>
                <p class="mt-2 text-gray-700"><?= !empty($novedad['hora_extra']) ? 'Sí' : 'No' ?></p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Renta excluida</h3>
                <p class="mt-2 text-gray-700"><?= !empty($novedad['renta_exclu']) ? 'Sí' : 'No' ?></p>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 text-right">
            <a href="/novedades/<?= $novedad['id'] ?>/edit"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                Editar novedad
            </a>
        </div>
    </div>
</div>
