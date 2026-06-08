<?php $title = "Pedidos de Venta"; ?>

<?php
// Totales para el contador
$total_registros = count($pedidos);
?>

<div class="mb-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <div class="flex items-center gap-3">
            <a href="/sistemas/comercial" class="flex items-center text-sm font-semibold text-gray-500 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Volver
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-gray-800">Pedidos de Venta</h2>
                <p class="text-xs text-gray-400">Mostrando <?= $total_registros ?> registro(s)</p>
            </div>
        </div>
        <a href="/pedido-venta/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition flex items-center shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo PV
        </a>
    </div>

    <!-- ── Panel de Filtros ───────────────────────────────────── -->
    <form method="GET" action="/pedido-venta" id="formFiltros">

        <!-- Fila 1: Estado (tabs) + Límite -->
        <div class="flex flex-col sm:flex-row gap-3 mb-3">
            <div class="flex gap-1 bg-white p-1 rounded-lg border shadow-sm flex-wrap">
                <?php
                $tabs = [
                    'PENDIENTE' => ['label' => 'Pendientes',  'active' => 'bg-blue-600 text-white',  'inactive' => 'text-gray-500 hover:bg-gray-100'],
                    'C'         => ['label' => 'Cerradas',    'active' => 'bg-green-600 text-white', 'inactive' => 'text-gray-500 hover:bg-gray-100'],
                    'A'         => ['label' => 'Anuladas',    'active' => 'bg-red-600 text-white',   'inactive' => 'text-gray-500 hover:bg-gray-100'],
                    'ALL'       => ['label' => 'Todas',       'active' => 'bg-gray-800 text-white',  'inactive' => 'text-gray-500 hover:bg-gray-100'],
                ];
                foreach ($tabs as $val => $tab):
                    $cls = ($filtroActual == $val) ? $tab['active'] : $tab['inactive'];
                ?>
                <button type="submit" name="estado" value="<?= $val ?>"
                    class="px-3 py-1.5 text-xs font-bold rounded-md transition <?= $cls ?>"
                    onclick="this.form.elements['estado'].value='<?= $val ?>'">
                    <?= $tab['label'] ?>
                </button>
                <?php endforeach; ?>
                <input type="hidden" name="estado" value="<?= htmlspecialchars($filtroActual) ?>">
            </div>

            <!-- Límite de registros -->
            <div class="flex items-center gap-2 bg-white border rounded-lg px-3 shadow-sm">
                <span class="text-xs text-gray-400 whitespace-nowrap">Mostrar</span>
                <?php foreach ([200 => '200', 500 => '500', 1000 => '1000', 0 => 'Todos'] as $val => $label): ?>
                <button type="submit" name="limite" value="<?= $val ?>"
                    class="px-2 py-1 text-xs font-bold rounded transition <?= $limiteActual == $val ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Fila 2: Cliente + Fechas + Limpiar -->
        <div class="flex flex-col sm:flex-row gap-2 items-end">
            <!-- Cliente -->
            <div class="flex-1 min-w-0">
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Cliente</label>
                <select name="codcli" id="selectCliente"
                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— Todos los clientes —</option>
                    <?php foreach ($clientes as $c): ?>
                    <option value="<?= htmlspecialchars($c['codcli']) ?>"
                        <?= $codcliActual == $c['codcli'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombrecli']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Fecha desde -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="<?= htmlspecialchars($fechaDesde) ?>"
                    class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Fecha hasta -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($fechaHasta) ?>"
                    class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Botones -->
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                    Filtrar
                </button>
                <a href="/pedido-venta"
                    class="bg-gray-100 text-gray-600 px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition">
                    Limpiar
                </a>
            </div>
        </div>

    </form>
</div>

<!-- ── Tabla ──────────────────────────────────────────────────── -->
<div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Documento</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Fecha</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Cliente</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Vendedor</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Suc.</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Ciudad</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase text-right">Total</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase text-center">Estado</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($pedidos)): ?>
            <tr>
                <td colspan="9" class="p-8 text-center text-sm text-gray-400">No se encontraron registros con los filtros aplicados.</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($pedidos as $p): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="p-3 font-mono text-gray-600"><?= $p['prefijo'] ?>-<?= $p['documento'] ?></td>
                <td class="p-3 text-gray-600 whitespace-nowrap"><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                <td class="p-3">
                    <div class="font-bold text-gray-800"><?= htmlspecialchars($p['cliente']) ?></div>
                    <div class="text-[10px] text-gray-400 italic">Entrega: <?= $p['fechent'] ?></div>
                </td>
                <td class="p-3 text-gray-500"><?= htmlspecialchars($p['vendedor'] ?? '') ?></td>
                <td class="p-3 text-gray-500"><?= htmlspecialchars($p['codsuc'] ?? '') ?></td>
                <td class="p-3 text-gray-600"><?= htmlspecialchars($p['ciudad'] ?? '') ?></td>
                <td class="p-3 font-bold text-right text-gray-700 whitespace-nowrap">$ <?= number_format($p['valortotal'] ?? 0, 2) ?></td>
                <td class="p-3 text-center">
                    <?php
                    if ($p['estado'] == 'C') {
                        $c = 'bg-green-100 text-green-700'; $t = 'CERRADA';
                    } elseif ($p['estado'] == 'A') {
                        $c = 'bg-red-100 text-red-700'; $t = 'ANULADA';
                    } else {
                        $c = 'bg-blue-100 text-blue-700'; $t = 'PENDIENTE';
                    }
                    ?>
                    <span class="px-2 py-1 rounded text-[9px] font-black <?= $c ?>"><?= $t ?></span>
                </td>
                <td class="p-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/pedido-venta/show/<?= $p['documento'] ?>" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded-lg" title="Ver">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <?php if ($p['estado'] != 'C' && $p['estado'] != 'A'): ?>
                        <a href="/pedido-venta/edit/<?= $p['documento'] ?>" class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Select2 para el filtro de cliente
$(document).ready(function() {
    $('#selectCliente').select2({
        placeholder: '— Todos los clientes —',
        allowClear: true,
        width: '100%'
    });
});
</script>
