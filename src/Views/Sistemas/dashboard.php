<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }
?>

<div class="space-y-6">
    <div>
        <a href="/sistemas" class="text-teal-600 hover:text-teal-700 text-sm font-medium mb-2 inline-block">
            ← Sistemas
        </a>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($sistema['nombre']) ?></h1>
        <p class="text-gray-600 mt-1">Opciones del sistema</p>
    </div>

    <?php if (!empty($opciones)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($opciones as $opcion): ?>
                <?php
                    $url   = $opcion['url'] ?? null;
                    $color = $opcion['color'] ?? 'teal';
                ?>
                <?php if ($url): ?>
                    <a href="<?= htmlspecialchars($url) ?>"
                       class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 border-<?= $color ?>-500 p-5 min-h-32 flex flex-col justify-between group">
                        <div>
                            <h3 class="text-base font-bold text-gray-800 group-hover:text-<?= $color ?>-600"><?= htmlspecialchars($opcion['nombre']) ?></h3>
                            <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($opcion['descripcion'] ?? '') ?></p>
                        </div>
                        <span class="text-xs font-semibold text-<?= $color ?>-600 mt-3">Ingresar →</span>
                    </a>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition border-l-4 border-teal-500 p-5 min-h-32 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-800"><?= htmlspecialchars($opcion['nombre']) ?></h3>
                            <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($opcion['descripcion'] ?? '') ?></p>
                        </div>
                        <span class="text-xs text-gray-400 mt-3">Próximamente</span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">Sin opciones disponibles</p>
        </div>
    <?php endif; ?>
</div>
