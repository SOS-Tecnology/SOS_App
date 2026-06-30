<?php $title = "Pedidos de Venta"; ?>

<?php
$total_registros = count($pedidos);
?>

<div class="mb-5">

    <!-- ── Encabezado ─────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="/sistemas/comercial" class="flex items-center text-sm font-semibold text-gray-500 hover:text-blue-600 transition-colors flex-shrink-0">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Volver
            </a>
            <div class="min-w-0">
                <h2 class="text-xl font-extrabold text-gray-800 truncate">Pedidos de Venta</h2>
                <p class="text-xs text-gray-400">Mostrando <?= $total_registros ?> registro(s)</p>
            </div>
        </div>
        <a href="/pedido-venta/create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition flex items-center shadow-md flex-shrink-0 self-start sm:self-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo PV
        </a>
    </div>

    <!-- ── Panel de Filtros ───────────────────────────────────────── -->
    <form method="GET" action="/pedido-venta" id="formFiltros">

        <!-- Pestañas de estado y límite -->
        <div class="flex flex-col gap-2 mb-3">
            <!-- Tabs estado -->
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
                        class="px-3 py-1.5 text-xs font-bold rounded-md transition <?= $cls ?>">
                        <?= $tab['label'] ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Límite registros -->
            <div class="flex items-center gap-2 bg-white border rounded-lg px-3 py-1.5 shadow-sm self-start">
                <span class="text-xs text-gray-400 whitespace-nowrap">Mostrar</span>
                <?php foreach ([200 => '200', 500 => '500', 1000 => '1000', 0 => 'Todos'] as $val => $label): ?>
                    <button type="submit" name="limite" value="<?= $val ?>"
                        class="px-2 py-1 text-xs font-bold rounded transition <?= $limiteActual == $val ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">
                        <?= $label ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Filtros de cliente y fecha -->
        <div class="flex flex-col gap-2">
            <div>
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

            <div class="flex flex-wrap gap-2 items-end">
                <div class="flex-1 min-w-[130px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Desde</label>
                    <input type="date" name="fecha_desde" value="<?= htmlspecialchars($fechaDesde) ?>"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="flex-1 min-w-[130px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($fechaHasta) ?>"
                        class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button type="submit" onclick="return validateDates();"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                        Filtrar
                    </button>
                    <a href="/pedido-venta"
                        class="bg-gray-100 text-gray-600 px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-gray-200 transition whitespace-nowrap">
                        Limpiar
                    </a>
                </div>
            </div>
        </div>

    </form>

    <script>
        function validateDates() {
            var desde = document.querySelector('input[name="fecha_desde"]').value;
            var hasta = document.querySelector('input[name="fecha_hasta"]').value;
            if (desde && hasta && new Date(desde) > new Date(hasta)) {
                alert("La fecha desde no puede ser superior a la fecha hasta.");
                return false;
            }
            return true;
        }
    </script>
</div>

<!-- ── VISTA TARJETAS (móvil < md) ────────────────────────────────── -->
<div class="md:hidden space-y-2 mt-4">
    <?php if (empty($pedidos)): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-sm text-gray-400">
            No se encontraron registros con los filtros aplicados.
        </div>
    <?php endif; ?>
    <?php foreach ($pedidos as $p): ?>
        <?php
        if ($p['estado'] == 'C')     { $badgeCls = 'bg-green-100 text-green-700'; $badgeTxt = 'CERRADA'; }
        elseif ($p['estado'] == 'A') { $badgeCls = 'bg-red-100 text-red-700';     $badgeTxt = 'ANULADA'; }
        else                         { $badgeCls = 'bg-blue-100 text-blue-700';    $badgeTxt = 'PENDIENTE'; }
        ?>
        <div class="bg-white rounded-xl border shadow-sm p-3">
            <!-- Fila superior: doc + estado + acciones -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="min-w-0">
                    <span class="font-mono font-bold text-gray-700 text-sm"><?= htmlspecialchars($p['prefijo'] ?? '') ?>-<?= $p['documento'] ?></span>
                    <span class="ml-2 text-xs text-gray-400"><?= date('d/m/Y', strtotime($p['fecha'])) ?></span>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <span class="px-2 py-0.5 rounded text-[9px] font-black <?= $badgeCls ?>"><?= $badgeTxt ?></span>
                    <!-- Acciones siempre visibles -->
                    <a href="/pedido-venta/show/<?= $p['documento'] ?>"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-100 transition" title="Ver">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <?php if ($p['estado'] != 'C' && $p['estado'] != 'A'): ?>
                    <a href="/pedido-venta/edit/<?= $p['documento'] ?>"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-500 hover:bg-amber-100 transition" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Datos del cliente -->
            <div class="font-bold text-gray-800 text-sm truncate"><?= htmlspecialchars($p['cliente'] ?? '') ?></div>
            <?php if (!empty($p['ciudad'])): ?>
            <div class="text-xs text-gray-400 truncate"><?= htmlspecialchars($p['ciudad']) ?></div>
            <?php endif; ?>
            <!-- Fila inferior: total + entrega -->
            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                <span class="text-xs text-gray-400">Entrega: <?= $p['fechent'] ?></span>
                <span class="font-black text-gray-700 text-sm font-mono">$ <?= number_format($p['valortotal'] ?? 0, 2) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ── VISTA TABLA (tablet y desktop ≥ md) ───────────────────────── -->
<div class="hidden md:block bg-white rounded-xl border shadow-sm overflow-x-auto mt-4">
    <table class="text-left border-collapse text-sm" style="min-width:700px;width:100%">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">Documento</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">Fecha</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Cliente</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase hidden lg:table-cell">Vendedor</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase hidden lg:table-cell">Suc.</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase">Ciudad</th>
                <th class="p-3 text-[10px] font-black text-gray-400 uppercase text-right whitespace-nowrap">Total</th>
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
                <?php
                if ($p['estado'] == 'C')     { $c = 'bg-green-100 text-green-700'; $t = 'CERRADA'; }
                elseif ($p['estado'] == 'A') { $c = 'bg-red-100 text-red-700';     $t = 'ANULADA'; }
                else                         { $c = 'bg-blue-100 text-blue-700';    $t = 'PENDIENTE'; }
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-3 font-mono text-gray-600 whitespace-nowrap"><?= htmlspecialchars($p['prefijo'] ?? '') ?>-<?= $p['documento'] ?></td>
                    <td class="p-3 text-gray-600 whitespace-nowrap"><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                    <td class="p-3">
                        <div class="font-bold text-gray-800"><?= htmlspecialchars($p['cliente'] ?? '') ?></div>
                        <div class="text-[10px] text-gray-400 italic whitespace-nowrap">Entrega: <?= $p['fechent'] ?></div>
                    </td>
                    <td class="p-3 text-gray-500 hidden lg:table-cell"><?= htmlspecialchars($p['vendedor'] ?? '') ?></td>
                    <td class="p-3 text-gray-500 hidden lg:table-cell"><?= htmlspecialchars($p['codsuc'] ?? '') ?></td>
                    <td class="p-3 text-gray-600"><?= htmlspecialchars($p['ciudad'] ?? '') ?></td>
                    <td class="p-3 font-bold text-right text-gray-700 whitespace-nowrap font-mono">$ <?= number_format($p['valortotal'] ?? 0, 2) ?></td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <span class="px-2 py-1 rounded text-[9px] font-black <?= $c ?>"><?= $t ?></span>
                    </td>
                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-1">
                            <a href="/pedido-venta/show/<?= $p['documento'] ?>"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-500 hover:bg-blue-50 transition" title="Ver">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <?php if ($p['estado'] != 'C' && $p['estado'] != 'A'): ?>
                            <a href="/pedido-venta/edit/<?= $p['documento'] ?>"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-500 hover:bg-amber-50 transition" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
    $(document).ready(function() {
        $('#selectCliente').select2({
            placeholder: '— Todos los clientes —',
            allowClear: true,
            width: '100%'
        });
    });
</script>
