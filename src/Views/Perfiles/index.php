<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
        ✔ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <a href="/usuarios" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Usuarios
    </a>
    <div>
        <h1 class="text-xl font-semibold text-gray-800 text-right">Perfiles de acceso</h1>
        <p class="text-sm text-gray-500 mt-0.5 text-right">Define los roles y permisos del sistema.</p>
    </div>
    <a href="/perfiles/create"
       class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700
              text-white text-sm font-semibold rounded-lg shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo perfil
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-teal-700 to-teal-600 text-white text-xs uppercase tracking-wide">
                <th class="px-6 py-3.5 text-left font-semibold">Nombre</th>
                <th class="px-6 py-3.5 text-left font-semibold">Descripción</th>
                <th class="px-6 py-3.5 text-center font-semibold">Usuarios</th>
                <th class="px-6 py-3.5 text-center font-semibold">Estado</th>
                <th class="px-6 py-3.5 text-center font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (!empty($perfiles)): ?>
                <?php foreach ($perfiles as $p): ?>
                <tr class="hover:bg-teal-50 transition">
                    <td class="px-6 py-3 font-semibold text-gray-800">
                        <?= htmlspecialchars($p['nombre'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-gray-500">
                        <?= htmlspecialchars($p['descripcion'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                            <?= (int)($p['total_usuarios'] ?? 0) ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <?php $activo = (int)($p['activo'] ?? 1); ?>
                        <span class="<?= $activo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?> text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= $activo ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="/perfiles/<?= $p['id'] ?>/edit"
                               class="text-teal-600 hover:text-teal-800 font-medium text-xs hover:underline">
                                Editar
                            </a>
                            <?php if ((int)($p['total_usuarios'] ?? 0) === 0): ?>
                            <form method="POST" action="/perfiles/<?= $p['id'] ?>/delete"
                                  onsubmit="return confirm('¿Eliminar perfil «<?= htmlspecialchars(addslashes($p['nombre'] ?? '')) ?>»?')">
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 font-medium text-xs hover:underline">
                                    Eliminar
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                        No hay perfiles registrados. <a href="/perfiles/create" class="text-teal-600 hover:underline">Crear el primero</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
