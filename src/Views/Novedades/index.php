<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
        ✔ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-800">Novedades</h1>
        <p class="text-sm text-gray-500 mt-0.5">Listado de novedades de nómina disponibles.</p>
    </div>
    <a href="/novedades/create"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
              text-white text-sm font-semibold rounded-lg shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva novedad
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-blue-700 to-blue-600 text-white text-xs uppercase tracking-wide">
                <th class="px-6 py-3.5 text-left font-semibold">Código</th>
                <th class="px-6 py-3.5 text-left font-semibold">Descripción</th>
                <th class="px-6 py-3.5 text-center font-semibold">Tipo</th>
                <th class="px-6 py-3.5 text-center font-semibold">Salario</th>
                <th class="px-6 py-3.5 text-center font-semibold">Suma días</th>
                <th class="px-6 py-3.5 text-center font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (!empty($novedades)): ?>
                <?php foreach ($novedades as $n): ?>
                <?php $tipo = $n['tipo'] ?? null; ?>
                <?php $tipoLabel = $tipo == 1 ? 'Ingreso' : ($tipo == 2 ? 'Deducción' : htmlspecialchars($tipo)); ?>
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-6 py-3 font-semibold text-gray-800"><?= htmlspecialchars($n['codigo'] ?? '—') ?></td>
                    <td class="px-6 py-3 text-gray-600"><?= htmlspecialchars($n['descripcion'] ?? $n['nombre'] ?? '—') ?></td>
                    <td class="px-6 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= $tipoLabel ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="<?= !empty($n['salario']) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?> text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= !empty($n['salario']) ? 'Sí' : 'No' ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="<?= !empty($n['actudia']) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?> text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= !empty($n['actudia']) ? 'Sí' : 'No' ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="/novedades/<?= $n['id'] ?>" class="text-blue-600 hover:text-blue-800" aria-label="Ver">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/novedades/<?= $n['id'] ?>/edit" class="text-blue-600 hover:text-blue-800" aria-label="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                        No hay novedades registradas. <a href="/novedades/create" class="text-blue-600 hover:underline">Crear una nueva novedad</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
