<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
?>

<div class="space-y-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Permisos - <?= htmlspecialchars($perfil['nombre']) ?></h1>
            <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($perfil['descripcion'] ?? '') ?></p>
        </div>
        <a href="/permisos" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
            ← Volver
        </a>
    </div>

    <form method="POST" action="/permisos/<?= $perfil['id'] ?>/update" class="space-y-6">

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 mb-6">
            <strong>Instrucciones:</strong> Selecciona los permisos que deseas asignar a este perfil.
            <ul class="mt-2 list-disc list-inside space-y-1">
                <li><strong>Consultar:</strong> Puede ver la opción</li>
                <li><strong>Crear:</strong> Puede crear nuevos registros</li>
                <li><strong>Modificar:</strong> Puede editar registros existentes</li>
                <li><strong>Cambiar Fecha:</strong> Puede modificar fechas en documentos</li>
                <li><strong>Permiso Especial:</strong> Permisos especiales (ej: cambiar vendedor)</li>
            </ul>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="border border-gray-600 px-4 py-3 text-left">Opción</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Consultar</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Crear</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Modificar</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Cambiar Fecha</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Especial</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opciones as $opcion): ?>
                        <?php
                        $permiso = $permisos[$opcion['id']] ?? null;
                        $prefix = "opcion_{$opcion['id']}";
                        ?>
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="border border-gray-300 px-4 py-3 font-medium">
                                <?= htmlspecialchars($opcion['nombre']) ?>
                                <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($opcion['descripcion'] ?? '') ?></div>
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <input type="checkbox" name="<?= $prefix ?>_consultar"
                                    class="form-checkbox h-4 w-4 text-blue-600"
                                    <?= ($permiso['puede_consultar'] ?? 0) ? 'checked' : '' ?>>
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <input type="checkbox" name="<?= $prefix ?>_crear"
                                    class="form-checkbox h-4 w-4 text-blue-600"
                                    <?= ($permiso['puede_crear'] ?? 0) ? 'checked' : '' ?>>
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <input type="checkbox" name="<?= $prefix ?>_modificar"
                                    class="form-checkbox h-4 w-4 text-blue-600"
                                    <?= ($permiso['puede_modificar'] ?? 0) ? 'checked' : '' ?>>
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <input type="checkbox" name="<?= $prefix ?>_cambiar_fecha"
                                    class="form-checkbox h-4 w-4 text-blue-600"
                                    <?= ($permiso['puede_cambiar_fecha'] ?? 0) ? 'checked' : '' ?>>
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">
                                <input type="checkbox" name="<?= $prefix ?>_especial"
                                    class="form-checkbox h-4 w-4 text-blue-600"
                                    <?= ($permiso['permiso_especial'] ?? 0) ? 'checked' : '' ?>>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex gap-3 pt-6">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition font-medium">
                ✓ Guardar Permisos
            </button>
            <a href="/permisos" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
                Cancelar
            </a>
        </div>

    </form>
</div>

<style>
    .form-checkbox {
        accent-color: #2563eb;
    }
</style>
