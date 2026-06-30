<?php $title = "Nuevo Pedido de Venta"; ?>

<form action="/pedido-venta/store" method="POST" id="orderForm" class="max-w-7xl mx-auto my-8">

    <div class="max-w-7xl mx-auto mb-4">
        <a href="/pedido-venta" class="flex items-center text-sm font-bold text-gray-400 hover:text-blue-600 transition-colors w-fit">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Regresar al Listado
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">

        <!-- ═══════════ CABECERA ═══════════ -->
        <div class="bg-gray-50 border-b p-6">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-black tracking-widest uppercase">Pedido de Venta</span>
                </div>
                <h1 class="text-2xl font-black text-gray-800">OP # <span class="text-gray-400">NUEVO</span></h1>
            </div>

            <!-- Fila 1: Cliente | Sucursal | Fecha Solicitud | Fecha Entrega -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Cliente</label>
                    <select name="codcp" id="codcli" class="select2-cliente w-full" required>
                        <option value="">Buscar cliente...</option>
                        <?php foreach ($clientes as $c): ?>
                        <option value="<?= htmlspecialchars($c['codcli']) ?>"><?= htmlspecialchars($c['nombrecli']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Sucursal</label>
                    <select name="codsuc" id="codsuc" class="w-full">
                        <option value="">Seleccione cliente...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Fecha Solicitud</label>
                    <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5">
                </div>
            </div>

            <!-- Fila 2: Forma Pago | Días Crédito | Otro Doc | Fecha Entrega -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Forma de Pago</label>
                    <select name="formapago" id="formapago" class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5 bg-white">
                        <option value="0">Contado / Anticipado</option>
                        <option value="credito">Crédito</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Días Crédito</label>
                    <input type="number" name="plazo" id="plazo" min="0" value="0"
                        class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5 bg-gray-100" readonly placeholder="0">
                    <p id="plazoHint" class="text-[10px] text-blue-500 mt-0.5 hidden">Plazo cliente: <span id="plazoCli">—</span> días</p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Otro Documento</label>
                    <input type="text" name="otrodoc" maxlength="10" class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5" placeholder="Ref. externa">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Fecha Entrega</label>
                    <input type="date" name="fechent" class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5">
                </div>
            </div>

            <!-- Fila 3: Observaciones + botón Info Cliente -->
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase">Observaciones Generales</label>
                    <button type="button" id="btnInfoCliente"
                        class="hidden items-center gap-1 bg-teal-600 text-white px-2 py-0.5 rounded text-[10px] font-bold hover:bg-teal-700 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información de Cliente
                    </button>
                </div>
                <input type="text" name="comen" class="w-full border border-gray-300 rounded-lg text-sm px-2 py-1.5" placeholder="Notas del documento...">
            </div>
        </div>

        <!-- ═══════════ CUERPO: TABLA ÍTEMS ═══════════ -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="itemsTable">
                <thead class="bg-gray-100 border-b border-gray-200 text-[10px] font-bold text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2">Producto / Color / Talla</th>
                        <th class="px-3 py-2 w-24 text-right">Subtotal</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="itemsTbody"></tbody>
            </table>
            <div class="p-4 bg-gray-50 border-t flex justify-end gap-8 text-sm font-bold">
                <span>Total Ítems: <span id="totalItems">0</span></span>
                <span>Total Cantidades: <span id="totalQty">0</span></span>
                <span>Gran Total: <span id="grandTotal">$ 0.00</span></span>
            </div>
        </div>

        <!-- ═══════════ PIE: BOTONES AGREGAR ═══════════ -->
        <div class="p-4 bg-white border-t flex flex-wrap gap-2">
            <button type="button" id="btnAddRow"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 shadow flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                AGREGAR ÍTEM
            </button>
            <button type="button" id="btnBuscarCodigo"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 shadow flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                BUSCAR POR CÓDIGO
            </button>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-4">
        <a href="/pedido-venta" class="bg-gray-100 text-gray-600 px-8 py-3 rounded-xl font-black hover:bg-gray-200 transition">CANCELAR</a>
        <button type="submit" class="bg-green-600 text-white px-10 py-3 rounded-xl font-black shadow-lg hover:bg-green-700 transition">GUARDAR PEDIDO</button>
    </div>
</form>

<!-- ═══════════ MODAL: INFO CLIENTE ═══════════ -->
<div id="modalInfoCliente" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-teal-700 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-black text-base">Información del Cliente</h3>
            <button type="button" id="cerrarInfoCliente" class="text-teal-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6" id="infoClienteBody">
            <div class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ MODAL: BÚSQUEDA POR CÓDIGO ═══════════ -->
<div id="modalBuscarCodigo" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden flex flex-col" style="max-height:88vh">

        <div class="bg-indigo-700 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <h3 class="text-white font-black text-base">Buscar Producto por Código</h3>
            <button type="button" id="cerrarBuscarCodigo" class="text-indigo-200 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Tabla + Búsqueda visibles al mismo tiempo -->
        <div class="p-4 flex-shrink-0 border-b bg-gray-50">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Tabla de Precios</label>
                    <select id="tablaModalSelect"
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">— Seleccione —</option>
                        <?php foreach ($tablasPrecios as $t): ?>
                        <option value="<?= htmlspecialchars($t['codigo']) ?>"><?= htmlspecialchars($t['codigo']) ?> — <?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Código / Descripción</label>
                    <input type="text" id="inputBuscarCodigo" placeholder="Escriba para filtrar..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="flex-1 overflow-y-auto min-h-0">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b sticky top-0">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-black text-gray-400 uppercase">Código</th>
                        <th class="px-3 py-2 text-left text-[10px] font-black text-gray-400 uppercase">Descripción</th>
                        <th class="px-3 py-2 text-right text-[10px] font-black text-gray-400 uppercase w-20">Exist.</th>
                        <th class="px-3 py-2 text-center text-[10px] font-black text-gray-400 uppercase w-20">Cantidad</th>
                    </tr>
                </thead>
                <tbody id="resultadosBusqueda" class="divide-y divide-gray-100">
                    <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">Escriba para buscar...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Pie -->
        <div class="px-4 py-3 border-t bg-gray-50 flex justify-between items-center flex-shrink-0">
            <span class="text-xs text-gray-400">Solo se agregan ítems con cantidad > 0</span>
            <div class="flex gap-2">
                <button type="button" id="btnCancelarModal"
                    class="bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="button" id="btnAgregarTodos"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-xs font-black hover:bg-indigo-700 transition flex items-center gap-1 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Agregar Seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JSON para JS -->
<script>
const PRODUCTOS      = <?= json_encode(array_map(fn($p) => ['codr'=>$p['codr'],'descr'=>$p['descr']], $productos)) ?>;
const TABLAS_PRECIO  = <?= json_encode(array_map(fn($t) => ['codigo'=>$t['codigo'],'nombre'=>$t['nombre']], $tablasPrecios)) ?>;
const TABPRELIMIT_DATA = <?= json_encode(array_map(fn($r) => ['codigotp'=>trim($r['codigotp']),'codigoseg'=>trim($r['codigoseg'])], $tablasControladas ?? [])) ?>;
const COLORES        = <?= json_encode(array_map(fn($c) => ['codigo'=>$c['codigo'],'nombre'=>$c['nombre']], $colores)) ?>;
const TALLAS         = <?= json_encode(array_map(fn($t) => ['codigo'=>$t['codigo'],'nombre'=>$t['nombre']], $tallas)) ?>;
const COMEN_X_ITEM   = <?= (int)($tmConfig['comenxitem'] ?? 0) ?>;
const BODEGA_DEFAULT = '01';

// ── Control tabprelimit ──────────────────────────────────────────────
const TABLAS_CTRL = new Set(TABPRELIMIT_DATA.map(r => r.codigotp));
const TABLA_SEGS  = {};
TABPRELIMIT_DATA.forEach(r => {
    if (!TABLA_SEGS[r.codigotp]) TABLA_SEGS[r.codigotp] = [];
    TABLA_SEGS[r.codigotp].push(r.codigoseg);
});
let _clienteSegmento = '';
</script>

<script>
$(document).ready(function() {

    // ── Select2 cabecera ─────────────────────────────────────────
    $('.select2-cliente').select2({ placeholder: 'Buscar cliente...' });
    $('#codsuc').select2({ placeholder: 'Seleccione cliente...' });

    // ── Sucursales + info cliente AJAX ───────────────────────────
    $('#codcli').on('change', function() {
        const codcli = $(this).val();
        if (!codcli) return;
        $.getJSON('/pedido-venta/sucursales/' + codcli, function(data) {
            const opts = data.length
                ? data.map(s => `<option value="${s.codsuc}">${s.codsuc} - ${s.nombresuc}</option>`).join('')
                : '<option value="01">01 - SEDE PRINCIPAL</option>';
            $('#codsuc').html(opts).trigger('change.select2');
        });
        $.getJSON('/pedido-venta/cliente-info/' + codcli, function(d) {
            $('#plazoCli').text(d.plazocli ?? '—');
            if (d.plazocli > 0) {
                $('#formapago').val('credito').trigger('change');
                $('#plazo').val(d.plazocli);
            }
            $('#btnInfoCliente').addClass('flex').removeClass('hidden');
            _infoClienteData = d;
            _clienteSegmento = (d.codsegmentocli || '').trim();
        });
    });

    // ── Forma de pago ────────────────────────────────────────────
    $('#formapago').on('change', function() {
        if ($(this).val() === 'credito') {
            $('#plazoHint').removeClass('hidden');
            $('#plazo').prop('readonly', false).removeClass('bg-gray-100').addClass('bg-white');
        } else {
            $('#plazoHint').addClass('hidden');
            $('#plazo').val(0).prop('readonly', true).removeClass('bg-white').addClass('bg-gray-100');
        }
    }).trigger('change');

    // ── Modal Info Cliente ───────────────────────────────────────
    let _infoClienteData = null;

    $('#btnInfoCliente').on('click', function() {
        if (!_infoClienteData) return;
        const d = _infoClienteData;
        $('#infoClienteBody').html(`
            <dl class="space-y-3 text-sm">
                <div class="flex gap-2"><dt class="text-[10px] font-black text-gray-400 uppercase w-24 flex-shrink-0 pt-0.5">Nombre</dt><dd class="font-bold text-gray-800">${d.nombrecli ?? '—'}</dd></div>
                <div class="flex gap-2"><dt class="text-[10px] font-black text-gray-400 uppercase w-24 flex-shrink-0 pt-0.5">Dirección</dt><dd class="text-gray-600">${d.direccioncli ?? '—'}</dd></div>
                <div class="flex gap-2"><dt class="text-[10px] font-black text-gray-400 uppercase w-24 flex-shrink-0 pt-0.5">Ciudad</dt><dd class="text-gray-600">${d.nombreciu ?? '—'}</dd></div>
                <div class="flex gap-2"><dt class="text-[10px] font-black text-gray-400 uppercase w-24 flex-shrink-0 pt-0.5">Plazo</dt><dd class="font-bold text-teal-700">${d.plazocli > 0 ? d.plazocli + ' días' : 'Contado'}</dd></div>
                <div class="flex gap-2"><dt class="text-[10px] font-black text-gray-400 uppercase w-24 flex-shrink-0 pt-0.5">Cupo</dt><dd class="font-bold text-gray-700">${d.cupocli ? '$ ' + parseFloat(d.cupocli).toLocaleString('es-CO') : '—'}</dd></div>
            </dl>`);
        $('#modalInfoCliente').removeClass('hidden');
    });

    $('#cerrarInfoCliente').on('click', function() { $('#modalInfoCliente').addClass('hidden'); });

    // ── Helpers options ──────────────────────────────────────────
    const mkOpts = (arr, sel, lbl) =>
        arr.map(x => `<option value="${x.codigo}" ${x.codigo==sel?'selected':''}>${lbl(x)}</option>`).join('');

    // Tabla precio: dropdown muestra "codigo — nombre", seleccionado muestra solo código
    const buildTablaOpts  = s => '<option value="">—</option>'
        + TABLAS_PRECIO.map(x => `<option value="${x.codigo}" ${x.codigo==s?'selected':''}>${x.codigo} — ${x.nombre}</option>`).join('');
    const buildColorOpts  = s => '<option value="">—</option>'        + mkOpts(COLORES, s, x => x.codigo);
    const buildTallaOpts  = s => '<option value="">—</option>'        + mkOpts(TALLAS,  s, x => x.codigo);
    // Producto muestra codr - descr
    const buildProdOpts   = s => '<option value="">Buscar ref...</option>'
        + PRODUCTOS.map(x => `<option value="${x.codr}" ${x.codr==s?'selected':''}>${x.codr} - ${x.descr}</option>`).join('');

    // ── addRow ───────────────────────────────────────────────────
    let rowIdx = 0;

    function addRow(codr, tabpre, codcolor, codtalla, cantidad, precio) {
        codr     = codr     || '';  tabpre   = tabpre   || '';
        codcolor = codcolor || '';  codtalla = codtalla || '';
        cantidad = cantidad || 1;   precio   = precio   || 0;

        const comentFila = COMEN_X_ITEM
            ? `<tr class="comment-row border-b border-gray-200" data-index="${rowIdx}">
                <td colspan="3" class="px-3 pb-2 pt-0">
                    <input type="text" name="items[${rowIdx}][comencpo]" placeholder="Observación del ítem..."
                        class="w-full bg-transparent border-b border-dashed border-gray-300 text-[11px] text-blue-500 focus:ring-0 italic p-1">
                </td></tr>`
            : `<tr class="comment-row" data-index="${rowIdx}" style="display:none"><td colspan="3"><input type="hidden" name="items[${rowIdx}][comencpo]" value=""></td></tr>`;

        // Línea 1: producto (descripción) | color | talla
        // Línea 2: tabla precio | existencia | cantidad | precio | subtotal | [botón eliminar rowspan=2]
        $('#itemsTbody').append(`
        <tr class="item-row-a bg-white border-t border-gray-200" data-index="${rowIdx}">
            <td class="px-3 pt-2 pb-0" colspan="2">
                <div class="flex gap-2 items-end">
                    <div class="flex-1 min-w-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Producto</label>
                        <select name="items[${rowIdx}][codr]" class="product-select w-full" required>${buildProdOpts(codr)}</select>
                    </div>
                    <div class="w-24 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Color</label>
                        <select name="items[${rowIdx}][codcolor]" class="color-select w-full">${buildColorOpts(codcolor)}</select>
                    </div>
                    <div class="w-24 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Talla</label>
                        <select name="items[${rowIdx}][codtalla]" class="talla-select w-full">${buildTallaOpts(codtalla)}</select>
                    </div>
                </div>
            </td>
            <td class="px-2 pt-2 pb-0 text-center align-bottom" rowspan="2">
                <button type="button" class="remove-row text-red-300 hover:text-red-500 mt-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
        </tr>
        <tr class="item-row-b bg-white" data-index="${rowIdx}">
            <td class="px-3 pt-1 pb-2">
                <div class="flex gap-4 items-end">
                    <div class="w-28 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Tbl. Prec.</label>
                        <select name="items[${rowIdx}][tabpreitem]" class="tabpre-select w-full">${buildTablaOpts(tabpre)}</select>
                    </div>
                    <div class="w-20 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Exist.</label>
                        <div class="exis-val border border-gray-200 rounded bg-gray-50 text-sm text-right px-2 py-1 font-mono text-gray-500">—</div>
                    </div>
                    <div class="w-24 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Cant.</label>
                        <input type="number" name="items[${rowIdx}][cantidad]" class="qty w-full border border-gray-300 rounded text-sm text-center font-bold px-1 py-1" value="${cantidad}" min="1">
                    </div>
                    <div class="w-32 flex-shrink-0">
                        <label class="text-[9px] font-black text-gray-400 uppercase">Precio</label>
                        <input type="number" name="items[${rowIdx}][valor]" class="price w-full border border-gray-300 rounded text-sm text-right px-1 py-1" value="${precio}" placeholder="0" step="0.01">
                    </div>
                </div>
            </td>
            <td class="px-3 pt-1 pb-2 text-right align-bottom">
                <span class="text-xs font-black text-gray-500 uppercase block mb-0.5">Subtotal</span>
                <span class="font-black text-gray-700 line-total whitespace-nowrap">$ ${(cantidad * precio).toLocaleString('en-US', {minimumFractionDigits:2})}</span>
            </td>
        </tr>
        ${comentFila}`);

        // Inicializar Select2
        const s2 = { width: '100%', minimumResultsForSearch: Infinity };
        $(`[data-index="${rowIdx}"] .product-select`).select2({ placeholder: 'Buscar ref...', width: '100%' });
        $(`[data-index="${rowIdx}"] .tabpre-select`).select2({
            ...s2,
            templateSelection: s => s.id ? $(`<span>${s.id}</span>`) : s.text,
        });
        $(`[data-index="${rowIdx}"] .color-select`).select2(s2);
        $(`[data-index="${rowIdx}"] .talla-select`).select2(s2);

        // Precio y existencia automáticos si vienen con datos
        if (codr) cargarExistencia(rowIdx, codr);
        if (codr && tabpre) cargarPrecio(rowIdx, codr, tabpre);

        rowIdx++;
        updateTotals();
    }

    // ── AJAX: existencia ─────────────────────────────────────────
    function cargarExistencia(idx, codr) {
        $.getJSON('/pedido-venta/existencia', { codr, bodega: BODEGA_DEFAULT }, function(d) {
            const val = d.existen != null ? parseFloat(d.existen).toLocaleString('en-US', {minimumFractionDigits:0, maximumFractionDigits:2}) : '0';
            $(`[data-index="${idx}"] .exis-val`).text(val);
        });
    }

    // ── AJAX: precio ─────────────────────────────────────────────
    function cargarPrecio(idx, codr, tabpre) {
        $.getJSON('/pedido-venta/precio', { codr, tabpre }, function(d) {
            if (d.precio) {
                $(`tr.item-row-b[data-index="${idx}"] .price`).val(parseFloat(d.precio).toFixed(2)).trigger('keyup');
            }
        });
    }

    // ── Cambio de producto → existencia ──────────────────────────
    $(document).on('change', '.product-select', function() {
        const $rowA = $(this).closest('tr');
        const idx   = $rowA.data('index');
        const codr  = $(this).val();
        if (!codr) { $(`[data-index="${idx}"] .exis-val`).text('—'); return; }
        cargarExistencia(idx, codr);
        const tabpre = $(`tr.item-row-b[data-index="${idx}"] .tabpre-select`).val();
        if (tabpre) cargarPrecio(idx, codr, tabpre);
    });

    // ── Cambio de tabla precio → precio ──────────────────────────
    $(document).on('change', '.tabpre-select', function() {
        const $sel   = $(this);
        const idx    = $sel.closest('tr').data('index');
        const tabpre = $sel.val();
        if (!validarTablaPrecios(tabpre, $sel)) return;
        const codr   = $(`[data-index="${idx}"] .product-select`).val();
        if (!codr || !tabpre) return;
        cargarPrecio(idx, codr, tabpre);
    });

    // ── Subtotales ───────────────────────────────────────────────
    $(document).on('change keyup', '.qty, .price', function() {
        const idx = $(this).closest('tr').data('index');
        const qty   = parseFloat($(`tr.item-row-b[data-index="${idx}"] .qty`).val())   || 0;
        const price = parseFloat($(`tr.item-row-b[data-index="${idx}"] .price`).val()) || 0;
        $(`tr.item-row-b[data-index="${idx}"] .line-total`).text('$ ' + (qty * price).toLocaleString('en-US', {minimumFractionDigits:2}));
        updateTotals();
    });

    function updateTotals() {
        let total = 0, qty = 0, items = 0;
        $('tr.item-row-a').each(function() {
            const idx = $(this).data('index');
            qty   += parseFloat($(`tr.item-row-b[data-index="${idx}"] .qty`).val())  || 0;
            total += parseFloat($(`tr.item-row-b[data-index="${idx}"] .line-total`).text().replace(/[$,]/g,'')) || 0;
            items++;
        });
        $('#grandTotal').text('$ ' + total.toLocaleString('en-US', {minimumFractionDigits:2}));
        $('#totalQty').text(qty);
        $('#totalItems').text(items);
    }

    // ── Eliminar fila ────────────────────────────────────────────
    $(document).on('click', '.remove-row', function() {
        const idx = $(this).closest('tr').data('index');
        $(`tr[data-index="${idx}"]`).remove();
        updateTotals();
    });

    // ── Validación tabla de precios por segmento ──────────────────
    function validarTablaPrecios(tabpre, $select) {
        if (!tabpre) return true;
        if (!TABLAS_CTRL.has(tabpre)) return true; // No controlada: libre
        const seg = _clienteSegmento;
        const allowed = TABLA_SEGS[tabpre] || [];
        if (seg && allowed.includes(seg)) return true;
        const msg = !seg
            ? `La tabla de precios "${tabpre}" está controlada por segmento.\nEl cliente seleccionado no tiene segmento asignado.\nSeleccione otro cliente o cambie la tabla de precios.`
            : `La tabla de precios "${tabpre}" no está habilitada para el segmento de cliente "${seg}".\nSeleccione una tabla de precios permitida para este cliente.`;
        alert(msg);
        if ($select) $select.val('').trigger('change');
        return false;
    }

    // ── Botón 1: agregar fila directa ────────────────────────────
    $('#btnAddRow').on('click', function() { addRow(); });

    // ── Modal Búsqueda por Código ─────────────────────────────────
    function resetModal() {
        $('#tablaModalSelect').val('');
        $('#inputBuscarCodigo').val('');
        $('#resultadosBusqueda').html('<tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">Escriba para buscar...</td></tr>');
    }

    $('#btnBuscarCodigo').on('click', function() {
        resetModal();
        $('#modalBuscarCodigo').removeClass('hidden');
        setTimeout(() => $('#inputBuscarCodigo').focus(), 80);
    });

    $('#btnCancelarModal, #cerrarBuscarCodigo').on('click', function() {
        $('#modalBuscarCodigo').addClass('hidden');
    });

    $('#inputBuscarCodigo').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('#resultadosBusqueda').html('<tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">Escriba para buscar...</td></tr>');
            return;
        }
        const matches = PRODUCTOS.filter(p =>
            p.codr.toLowerCase().includes(q) || p.descr.toLowerCase().includes(q)
        ).slice(0, 80);

        if (!matches.length) {
            $('#resultadosBusqueda').html('<tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">Sin resultados.</td></tr>');
            return;
        }
        $('#resultadosBusqueda').html(matches.map(p => `
            <tr class="hover:bg-indigo-50 resultado-item" data-codr="${p.codr}">
                <td class="px-3 py-1.5 font-mono text-xs text-gray-600 whitespace-nowrap">${p.codr}</td>
                <td class="px-3 py-1.5 text-sm text-gray-800">${p.descr}</td>
                <td class="px-3 py-1 text-right font-mono text-xs text-gray-500 exis-modal" data-codr="${p.codr}">—</td>
                <td class="px-3 py-1 text-center">
                    <input type="number" class="cant-modal w-16 border border-gray-300 rounded text-sm text-center font-bold px-1 py-0.5" value="0" min="0">
                </td>
            </tr>`).join(''));

        // Cargar existencias para los resultados visibles
        matches.forEach(p => {
            $.getJSON('/pedido-venta/existencia', { codr: p.codr, bodega: BODEGA_DEFAULT }, function(d) {
                const val = d.existen != null
                    ? parseFloat(d.existen).toLocaleString('en-US', {minimumFractionDigits:0, maximumFractionDigits:2})
                    : '0';
                $(`.exis-modal[data-codr="${p.codr}"]`).text(val);
            });
        });
    });

    $('#btnAgregarTodos').on('click', function() {
        const tabla = $('#tablaModalSelect').val();
        if (tabla && !validarTablaPrecios(tabla, null)) {
            alert('La tabla de precios seleccionada en el modal no está habilitada para este cliente. Elija otra tabla.');
            return;
        }
        let n = 0;
        $('#resultadosBusqueda .resultado-item').each(function() {
            const qty = parseInt($(this).find('.cant-modal').val()) || 0;
            if (qty > 0) {
                addRow($(this).data('codr'), tabla, '', '', qty, 0);
                n++;
            }
        });
        if (n > 0) $('#modalBuscarCodigo').addClass('hidden');
    });

    // Cerrar al click fuera
    $('#modalBuscarCodigo, #modalInfoCliente').on('click', function(e) {
        if ($(e.target).is($(this))) $(this).addClass('hidden');
    });
});
</script>