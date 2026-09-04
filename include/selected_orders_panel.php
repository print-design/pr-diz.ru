<style>
    /* Небольшая выезжающая справа панель -- НЕ модальное окно (нет фона-затемнения,
       нет перехвата фокуса), поэтому остальная страница остаётся кликабельной,
       пока панель открыта. Визуально повторяет .modal-dialog-aside. */
    #selected_orders_panel {
        position: fixed;
        top: 0;
        right: 0;
        height: 100%;
        width: 440px;
        max-width: 90vw;
        background: #fff;
        box-shadow: -2px 0 12px rgba(0, 0, 0, 0.2);
        transform: translateX(100%);
        transition: transform 0.25s ease-in-out;
        z-index: 1041;
        overflow-y: auto;
        padding: 40px 30px;
    }
    #selected_orders_panel.open {
        transform: translateX(0);
    }
    #selected_orders_breakdown_table th, #selected_orders_breakdown_table td {
        font-size: 14px;
        padding: 4px 8px;
    }
</style>
<div id="selected_orders_panel">
    <h4 style="font-weight: 600; margin-bottom: 25px;">Выбрано заказов: <span id="selected_orders_count">0</span></h4>
    <table class="table">
        <tr>
            <td>Вес нетто</td>
            <td class="text-right" id="selected_orders_net_weight">0 кг</td>
        </tr>
        <tr>
            <td>Объём</td>
            <td class="text-right" id="selected_orders_volume">&asymp;0 м<sup>3</sup></td>
        </tr>
    </table>
    
    <p class="text-muted" style="font-size: 13px; margin-top: 20px;">
        Заказы отсортированы по весу брутто. Если у нескольких заказов подряд вес брутто совпадает --
        скорее всего, они физически находятся в одном паллете. Снимите флажок у всех, кроме одного из них,
        чтобы не посчитать один и тот же паллет несколько раз.
    </p>
    <table class="table table-sm" id="selected_orders_breakdown_table">
        <thead>
            <tr>
                <th></th>
                <th>№ заказа</th>
                <th class="text-right">Вес брутто</th>
                <th class="text-right">Паллетов</th>
            </tr>
        </thead>
        <tbody id="selected_orders_breakdown_body">
        </tbody>
    </table>
    
    <table class="table" style="margin-top: 10px;">
        <tr>
            <td>Вес брутто (отмеченные)</td>
            <td class="text-right" id="selected_orders_gross_weight_sum">0 кг</td>
        </tr>
        <tr>
            <td>Паллетов (отмеченные)</td>
            <td class="text-right" id="selected_orders_pallet_count_sum">0</td>
        </tr>
        <tr>
            <td>Максимальный (отмеченные)</td>
            <td class="text-right" id="selected_orders_max_volume_order">&mdash;</td>
        </tr>
    </table>
</div>
<script>
    function FormatNumberRu(value, decimals) {
        return parseFloat(value || 0).toLocaleString('ru-RU', {minimumFractionDigits: decimals, maximumFractionDigits: decimals});
    }
    
    // Пересчёт нижней таблицы (суммы и габариты самого крупного паллета) по тем строкам разбивки,
    // у которых флажок остался отмеченным
    function RecalculateSelectedOrdersBreakdown() {
        var grossWeightSum = 0;
        var palletCountSum = 0;
        var maxVolume = -1;
        var maxVolumeDimensions = null;
        
        $('.selected_orders_breakdown_checkbox:checked').each(function() {
            grossWeightSum += parseFloat($(this).data('gross-weight')) || 0;
            palletCountSum += parseInt($(this).data('pallet-count')) || 0;
            
            var volume = parseFloat($(this).data('pallet-volume')) || 0;
            if(volume > maxVolume) {
                maxVolume = volume;
                var length = parseFloat($(this).data('pallet-length')) || 0;
                var width = parseFloat($(this).data('pallet-width')) || 0;
                var height = parseFloat($(this).data('pallet-height')) || 0;
                maxVolumeDimensions = FormatNumberRu(length, 2) + '\u00d7' + FormatNumberRu(width, 2) + '\u00d7' + FormatNumberRu(height, 2) + ' м';
            }
        });
        
        $('#selected_orders_gross_weight_sum').text(FormatNumberRu(grossWeightSum, 0) + ' кг');
        $('#selected_orders_pallet_count_sum').text(palletCountSum);
        $('#selected_orders_max_volume_order').text(maxVolumeDimensions !== null ? maxVolumeDimensions : '\u2014');
    }
    
    function UpdateSelectedOrdersPanel() {
        var ids = $('.order-select-checkbox:checked').map(function() { return $(this).val(); }).get();
        
        if(ids.length === 0) {
            $('#selected_orders_panel').removeClass('open');
            return;
        }
        
        $.ajax({ url: "<?=APPLICATION ?>/include/_selected_calculations_summary.php?ids=" + ids.join(',') })
                .done(function(data) {
                    var result = JSON.parse(data);
                    $('#selected_orders_count').text(result.count);
                    $('#selected_orders_net_weight').text(FormatNumberRu(result.net_weight, 0) + ' кг');
                    $('#selected_orders_volume').html('&asymp;' + FormatNumberRu(result.volume, 2) + ' м<sup>3</sup> <small class="text-muted">(' + FormatNumberRu(result.volume_min, 2) + '&ndash;' + FormatNumberRu(result.volume_max, 2) + ' м<sup>3</sup>)</small>');
                    
                    var rowsHtml = '';
                    result.orders.forEach(function(order) {
                        var grossWeightText = order.gross_weight !== null ? FormatNumberRu(order.gross_weight, 0) + ' кг' : '&mdash;';
                        var palletCountText = order.pallet_count !== null ? order.pallet_count : '&mdash;';
                        rowsHtml += '<tr>'
                                + '<td><input type="checkbox" class="selected_orders_breakdown_checkbox" checked="checked" data-gross-weight="' + (order.gross_weight || 0) + '" data-pallet-count="' + (order.pallet_count || 0) + '" data-pallet-volume="' + (order.pallet_volume || 0) + '" data-pallet-length="' + (order.pallet_length || 0) + '" data-pallet-width="' + (order.pallet_width || 0) + '" data-pallet-height="' + (order.pallet_height || 0) + '" /></td>'
                                + '<td>' + order.customer_id + '-' + order.num_for_customer + '</td>'
                                + '<td class="text-right">' + grossWeightText + '</td>'
                                + '<td class="text-right">' + palletCountText + '</td>'
                                + '</tr>';
                    });
                    $('#selected_orders_breakdown_body').html(rowsHtml);
                    
                    RecalculateSelectedOrdersBreakdown();
                    $('#selected_orders_panel').addClass('open');
                })
                .fail(function() {
                    alert('Ошибка при подсчёте итогов по выбранным заказам');
                });
    }
    
    $(document).on('change', '.order-select-checkbox', function() {
        UpdateSelectedOrdersPanel();
    });
    
    $(document).on('change', '.selected_orders_breakdown_checkbox', function() {
        RecalculateSelectedOrdersBreakdown();
    });
</script>
