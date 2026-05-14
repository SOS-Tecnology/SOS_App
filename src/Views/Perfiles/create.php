<?php if (!empty($_SESSION['errors'])): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
        <?php foreach ($_SESSION['errors'] as $e): ?>
            <p>⚠ <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>

<div class="max-w-lg mx-auto mt-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Nuevo perfil</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define nombre y descripción del perfil de acceso.</p>
        </div>
        <a href="/perfiles" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
    </div>

    <form method="POST" action="/perfiles/store"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-50">

        <!-- Nombre -->
        <div class="px-6 py-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Nombre del perfil</label>
            <input type="text" name="nombre" required placeholder="Ej. Recursos Humanos"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                          focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent
                          placeholder-gray-300 transition">
        </div>

        <!-- Descripción -->
        <div class="px-6 py-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Descripción</label>
            <textarea name="descripcion" rows="3" placeholder="Describe los accesos y permisos de este perfil…"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                             focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent
                             placeholder-gray-300 transition resize-none"></textarea>
        </div>

        <!-- Estado -->
        <div class="px-6 py-5">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Estado</label>
            <select name="activo"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition">
                <option value="1" selected>Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <!-- Botones -->
        <div class="px-6 py-4 flex justify-end gap-3 bg-gray-50 rounded-b-2xl">
            <a href="/perfiles"
               class="px-5 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 text-sm font-semibold text-white bg-teal-600
                           rounded-lg hover:bg-teal-700 transition shadow-sm">
                Crear perfil
            </button>
        </div>

    </form>
</div>
