<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
        ✔ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['errors'])): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
        <?php foreach ($_SESSION['errors'] as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <a href="/dashboard_home" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Inicio
    </a>
    <div>
        <h1 class="text-xl font-semibold text-gray-800 text-right"><?= htmlspecialchars($titulo) ?></h1>
        <p class="text-sm text-gray-500 mt-0.5 text-right">Tabla de referencia del sistema de nómina.</p>
    </div>
    <button onclick="abrirModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700
                   text-white text-sm font-semibold rounded-lg shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo registro
    </button>
</div>

<!-- Tabla -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-teal-700 to-teal-600 text-white text-xs uppercase tracking-wide">
                <th class="px-6 py-3.5 text-left font-semibold">Código</th>
                <th class="px-6 py-3.5 text-left font-semibold">Descripción</th>
                <th class="px-6 py-3.5 text-center font-semibold">Estado</th>
                <th class="px-6 py-3.5 text-center font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100" id="tabla-body">
            <?php if (!empty($registros)): ?>
                <?php foreach ($registros as $r): ?>
                <tr class="hover:bg-teal-50 transition">
                    <td class="px-6 py-3 font-mono text-gray-700 font-medium">
                        <?= htmlspecialchars($r['codigo'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-gray-800">
                        <?= htmlspecialchars($r['descripcion'] ?? '—') ?>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <?php $activo = (int)($r['activo'] ?? 1); ?>
                        <span class="<?= $activo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' ?> text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= $activo ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button onclick='abrirModalEditar(<?= json_encode($r) ?>)'
                                    class="text-teal-600 hover:text-teal-800 font-medium text-xs hover:underline">
                                Editar
                            </button>
                            <form method="POST" action="/archivos/<?= htmlspecialchars($catalogo) ?>/<?= (int)$r['id'] ?>/delete"
                                  onsubmit="return confirm('¿Eliminar este registro?')">
                                <button type="submit"
                                        class="text-red-500 hover:text-red-700 font-medium text-xs hover:underline">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                        No hay registros. Crea el primero con el botón <strong>Nuevo registro</strong>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ══ MODAL CREAR/EDITAR ══════════════════════════════════════════ -->
<div id="modal-catalogo" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-700 to-teal-600 px-6 py-4 flex items-center justify-between">
            <h2 id="modal-title" class="text-white font-semibold text-sm">Nuevo registro</h2>
            <button onclick="cerrarModal()" class="text-white/70 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="modal-form" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="_method_id" id="modal-id" value="">
            <!-- Código -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Código
                </label>
                <input type="text" name="codigo" id="modal-codigo" required maxlength="20"
                       placeholder="Ej. 01"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
            </div>
            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Descripción
                </label>
                <input type="text" name="descripcion" id="modal-descripcion" required maxlength="200"
                       placeholder="Descripción completa"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
            </div>
            <!-- Estado -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Estado</label>
                <select name="activo" id="modal-activo"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="cerrarModal()"
                        class="px-5 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-2 text-sm font-semibold text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE_URL = '/archivos/<?= htmlspecialchars($catalogo) ?>';

function abrirModal() {
    document.getElementById('modal-title').textContent = 'Nuevo registro';
    document.getElementById('modal-form').action = BASE_URL + '/store';
    document.getElementById('modal-id').value = '';
    document.getElementById('modal-codigo').value = '';
    document.getElementById('modal-descripcion').value = '';
    document.getElementById('modal-activo').value = '1';
    document.getElementById('modal-catalogo').classList.remove('hidden');
    document.getElementById('modal-codigo').focus();
}

function abrirModalEditar(r) {
    document.getElementById('modal-title').textContent = 'Editar registro';
    document.getElementById('modal-form').action = BASE_URL + '/' + r.id + '/update';
    document.getElementById('modal-id').value = r.id;
    document.getElementById('modal-codigo').value = r.codigo || '';
    document.getElementById('modal-descripcion').value = r.descripcion || '';
    document.getElementById('modal-activo').value = String(r.activo ?? '1');
    document.getElementById('modal-catalogo').classList.remove('hidden');
    document.getElementById('modal-descripcion').focus();
}

function cerrarModal() {
    document.getElementById('modal-catalogo').classList.add('hidden');
}

document.getElementById('modal-catalogo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
