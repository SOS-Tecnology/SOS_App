<?php
if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }

$colores = [
    'teal'    => 'from-teal-600 to-teal-700 hover:to-teal-800',
    'blue'    => 'from-blue-600 to-blue-700 hover:to-blue-800',
    'indigo'  => 'from-indigo-600 to-indigo-700 hover:to-indigo-800',
    'emerald' => 'from-emerald-600 to-emerald-700 hover:to-emerald-800',
    'amber'   => 'from-amber-600 to-amber-700 hover:to-amber-800',
];
?>

<div class="space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bienvenido, <?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></h1>
        <p class="text-gray-600 mt-1">Selecciona un sistema para comenzar.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <?php foreach ($sistemas as $slug => $s): ?>
            <a href="/sistemas/<?= htmlspecialchars($slug) ?>"
               class="bg-gradient-to-br <?= $colores[$s['color']] ?? $colores['teal'] ?> text-white rounded-xl shadow-md hover:shadow-lg p-6
                      flex flex-col items-center justify-center text-center transition transform hover:-translate-y-1 min-h-40">
                <h3 class="font-bold text-base"><?= htmlspecialchars($s['nombre']) ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
</div>
