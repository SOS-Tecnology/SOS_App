<?php if (!empty($_SESSION['errors'])): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
        <?php foreach ($_SESSION['errors'] as $e): ?>
            <p>⚠ <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<div class="max-w-4xl mx-auto mt-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Editar novedad</h1>
            <p class="text-sm text-gray-500 mt-0.5">Actualiza los datos de la novedad seleccionada.</p>
        </div>
        <a href="/novedades" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al listado
        </a>
    </div>

    <form method="POST" action="/novedades/<?= $novedad['id'] ?>/update"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-50">

        <div class="grid gap-6 p-6 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Código</label>
                <input type="text" name="codigo" required value="<?= htmlspecialchars($novedad['codigo'] ?? '') ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                              placeholder-gray-300 transition">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Nombre</label>
                <input type="text" name="nombre" required value="<?= htmlspecialchars($novedad['nombre'] ?? '') ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                              placeholder-gray-300 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tipo</label>
                <select name="tipo"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    <option value="Devengado" <?= ($novedad['tipo'] ?? '') === 'Devengado' ? 'selected' : '' ?>>Devengado</option>
                    <option value="Deducido" <?= ($novedad['tipo'] ?? '') === 'Deducido' ? 'selected' : '' ?>>Deducido</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Pagar en</label>
                <select name="pagar_en"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    <option value="1ra Quincena" <?= ($novedad['pagar_en'] ?? '') === '1ra Quincena' ? 'selected' : '' ?>>1ra Quincena</option>
                    <option value="2da Quincena" <?= ($novedad['pagar_en'] ?? '') === '2da Quincena' ? 'selected' : '' ?>>2da Quincena</option>
                    <option value="Quincenal" <?= ($novedad['pagar_en'] ?? '') === 'Quincenal' ? 'selected' : '' ?>>Quincenal</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Cantidad</label>
                <input type="number" step="0.01" min="0" name="cantidad" value="<?= htmlspecialchars($novedad['cantidad'] ?? '0') ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">% H.E.</label>
                <input type="number" step="0.01" min="0" name="porcentaje_he" value="<?= htmlspecialchars($novedad['porcentaje_he'] ?? '') ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Fórmula</label>
                <input type="text" name="formula" value="<?= htmlspecialchars($novedad['formula'] ?? '') ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                              placeholder-gray-300 transition">
            </div>
        </div>

        <div class="px-6 py-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <label class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3">
                <input type="checkbox" name="novedad_fija" <?= !empty($novedad['novedad_fija']) ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 rounded border-gray-300">
                <span class="text-sm text-gray-600">Novedad fija</span>
            </label>
            <label class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3">
                <input type="checkbox" name="salario" <?= !empty($novedad['salario']) ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 rounded border-gray-300">
                <span class="text-sm text-gray-600">Salario</span>
            </label>
            <label class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3">
                <input type="checkbox" name="hora_extra" <?= !empty($novedad['hora_extra']) ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 rounded border-gray-300">
                <span class="text-sm text-gray-600">Hora extra</span>
            </label>
            <label class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3">
                <input type="checkbox" name="renta_exclu" <?= !empty($novedad['renta_exclu']) ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 rounded border-gray-300">
                <span class="text-sm text-gray-600">Renta excluida</span>
            </label>
        </div>

        <div class="px-6 py-4 flex flex-col sm:flex-row justify-end gap-3 bg-gray-50 rounded-b-2xl">
            <a href="/novedades"
               class="px-5 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 text-sm font-semibold text-white bg-blue-600
                           rounded-lg hover:bg-blue-700 transition shadow-sm">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
