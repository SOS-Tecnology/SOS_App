<?php $title = "Consulta de Pedido de Venta PV # " . $pedido['documento']; ?>

<div class="max-w-7xl mx-auto my-4 px-4">
    <div class="flex items-center justify-between mb-3">
        <a href="/pedido-venta" class="flex items-center text-xs font-bold text-blue-600 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            VOLVER AL LISTADO
        </a>
        <div class="flex gap-2">
            <a href="/pedido-venta/pdf/<?= $pedido['documento'] ?>" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded text-xs font-black shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                GENERAR PDF
            </a>

            <a href="/pedido-venta/edit/<?= $pedido['documento'] ?>" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-1.5 rounded text-xs font-black shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                EDITAR PEDIDO
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">

        <div class="bg-gray-800 p-4 text-white">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Documento</span>
                    <div class="text-lg font-black text-white">OP-<?= $pedido['documento'] ?></div>
                </div>
                <div class="md:col-span-2">
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Cliente</span>
                    <div class="text-sm font-bold truncate"><?= $pedido['codcp'] ?> - <?= $pedido['cliente'] ?></div>
                    <div class="text-[10px] text-gray-300"><?= $pedido['direccioncli'] ?? '' ?> - <?= $pedido['nombreciu'] ?? '' ?></div>
                    <div class="text-[10px] text-purple-300">Segmento: <?= $pedido['codsegmentocli'] ?? '—' ?></div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Sucursal</span>
                    <div class="text-sm font-bold"><?= $pedido['codsuc'] ?> - Principal</div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Estado</span>
                    <span class="inline-block mt-1 text-[10px] bg-blue-500 px-2 py-0.5 rounded font-black uppercase"><?= $pedido['estado'] == ' ' ? 'PENDIENTE' : $pedido['estado'] ?></span>
                </div>

                <div>
                    <span class="block text-[9px] font-bold text-blue-300 uppercase">Fecha OP</span>
                    <div class="text-xs font-bold"><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-green-400 uppercase">Fecha Entrega</span>
                    <div class="text-xs font-bold"><?= date('d/m/Y', strtotime($pedido['fechent'])) ?></div>
                </div>
                <div class="md:col-span-3">
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Comentario General</span>
                    <div class="text-xs italic text-gray-200"><?= $pedido['comen'] ?: 'Sin observaciones' ?></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b text-[9px] font-black text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2">Referencia / Producto</th>
                        <th class="px-2 py-2 text-center">Talla</th>
                        <th class="px-2 py-2 text-center">Color</th>
                        <th class="px-2 py-2 text-center">Tabla Precio</th>
                        <th class="px-2 py-2 text-center">Cant.</th>
                        <th class="px-2 py-2 text-right">Precio Un.</th>
                        <th class="px-2 py-2 text-center">Desc. %</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $totalQty = 0;
                    $subtotalBruto = 0;
                    foreach ($detalles as $d):
                        $subtotal = $d['cantidad'] * $d['valor'];
                        $totalQty += $d['cantidad'];
                        $subtotalBruto += $subtotal;
                    endforeach;

                    $descuentoPct = $pedido['descuento'] ?? 0;
                    $descuentoItems = $pedido['descuento2'] ?? 0;
                    $descGlobal = ($subtotalBruto - $descuentoItems) * ($descuentoPct / 100);
                    $baseGravable = $subtotalBruto - $descuentoItems - $descGlobal;
                    $reteFuente = $baseGravable * (($pedido['retencion'] ?? 0) / 100);
                    $reteIca = $baseGravable * (($pedido['reteica'] ?? 0) / 100);
                    ?>

                    <?php foreach ($detalles as $d):
                        $subtotal = $d['cantidad'] * $d['valor'];
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-2 px-3">
                                <div class="text-xs font-bold text-gray-800"><?= $d['codr'] ?></div>
                                <div class="text-[10px] text-gray-500 uppercase"><?= $d['producto_nombre'] ?></div>
                            </td>
                            <td class="p-2 text-center text-xs font-bold"><?= $d['codtalla'] ?></td>
                            <td class="p-2 text-center text-xs"><?= $d['codcolor'] ?></td>
                            <td class="p-2 text-center text-xs font-bold text-gray-600"><?= $d['tabpreitem'] ?? '' ?></td>
                            <td class="p-2 text-center text-xs font-black text-blue-600"><?= number_format($d['cantidad'], 0) ?></td>
                            <td class="p-2 text-right text-xs">$ <?= number_format($d['valor'], 2) ?></td>
                            <td class="p-2 text-center text-xs font-bold text-red-500"><?= number_format($d['descto'] ?? 0, 2) ?>%</td>
                            <td class="p-2 px-3 text-right text-xs font-bold text-gray-700">$ <?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php if (!empty($d['comencpo'])): ?>
                            <tr class="bg-gray-50/30">
                                <td colspan="8" class="px-3 py-1 text-[9px] text-blue-500 italic border-b border-gray-100">
                                    Nota ítem: <?= $d['comencpo'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="border-t">
            <div class="flex justify-end p-4">
                <div class="w-full max-w-sm">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">Subtotal bruto</td>
                                <td class="py-1.5 text-right font-mono text-gray-700">$ <?= number_format($subtotalBruto ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">Desc. global (<?= number_format($pedido['descuento'] ?? 0, 2) ?>%)</td>
                                <td class="py-1.5 text-right font-mono text-red-500">- $ <?= number_format($descGlobal ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">Desc. en ítems</td>
                                <td class="py-1.5 text-right font-mono text-red-500">- $ <?= number_format($pedido['descuento2'] ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-1.5 text-gray-600 font-semibold">Base gravable</td>
                                <td class="py-1.5 text-right font-mono font-semibold text-gray-700">$ <?= number_format($baseGravable ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">IVA</td>
                                <td class="py-1.5 text-right font-mono text-blue-600">$ <?= number_format($pedido['vriva'] ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">Ret. Fuente <?= number_format($pedido['retencion'] ?? 0, 2) ?>%</td>
                                <td class="py-1.5 text-right font-mono text-orange-500">- $ <?= number_format($reteFuente ?? 0, 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-1.5 text-gray-500 font-medium">Rete ICA <?= number_format($pedido['reteica'] ?? 0, 2) ?>%</td>
                                <td class="py-1.5 text-right font-mono text-orange-500">- $ <?= number_format($reteIca ?? 0, 2) ?></td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td class="py-2.5 pl-2 text-blue-800 font-black text-base">GRAN TOTAL</td>
                                <td class="py-2.5 pr-2 text-right font-black text-blue-800 text-base font-mono">$ <?= number_format($pedido['valortotal'] ?? 0, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border-t border-blue-100 px-6 py-3 flex justify-between items-center text-blue-900">
            <div class="flex gap-8">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase text-blue-400">Items</span>
                    <span class="text-sm font-black"><?= count($detalles) ?></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase text-blue-400">Total Unidades</span>
                    <span class="text-sm font-black"><?= number_format($totalQty, 0) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>