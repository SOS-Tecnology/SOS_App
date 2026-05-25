<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
?>

<div class="space-y-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestionar Permisos por Perfil</h1>
    </div>

    <?php if (!empty($perfiles)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($perfiles as $perfil): ?>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-t-lg">
                        <h3 class="font-bold text-lg"><?= htmlspecialchars($perfil['nombre']) ?></h3>
                        <p class="text-sm opacity-90"><?= htmlspecialchars($perfil['descripcion'] ?? '') ?></p>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-600 text-sm mb-4">
                            <?php
                            $usuariosCount = 0;
                            // Aquí irías a contar usuarios con este perfil
                            ?>
                            Configura los permisos de este perfil
                        </p>
                        <a href="/permisos/<?= $perfil['id'] ?>/edit"
                            class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
                            Editar Permisos →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
            No hay perfiles disponibles.
        </div>
    <?php endif; ?>
</div>
