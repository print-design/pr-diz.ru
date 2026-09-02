<style>
    /* Небольшая выезжающая справа панель -- НЕ модальное окно (нет фона-затемнения,
       нет перехвата фокуса), поэтому остальная страница остаётся кликабельной,
       пока панель открыта. Визуально повторяет .modal-dialog-aside. */
    #selected_orders_panel {
        position: fixed;
        top: 0;
        right: 0;
        height: 100%;
        width: 360px;
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
</style>
<div id="selected_orders_panel">
    <h4 style="font-weight: 600; margin-bottom: 25px;">Выбрано заказов: <span id="selected_orders_count">0</span></h4>
    <table class="table">
        <tr>
            <td>Вес нетто</td>
            <td class="text-right" id="selected_orders_net_weight">0 кг</td>
        </tr>
        <tr>
            <td>Вес брутто</td>
            <td class="text-right" id="selected_orders_gross_weight">0 кг</td>
        </tr>
        <tr>
            <td>Количество паллетов</td>
            <td class="text-right" id="selected_orders_pallet_count">0</td>
        </tr>
        <tr>
            <td>Объём</td>
            <td class="text-right" id="selected_orders_volume">&asymp;0 м<sup>3</sup></td>
        </tr>
    </table>
</div>
<script>
    function FormatNumberRu(value, decimals) {
        return parseFloat(value || 0).toLocaleString('ru-RU', {minimumFractionDigits: decimals, maximumFractionDigits: decimals});
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
                    var sharedNote = result.has_shared_pallet_orders ? ' <small class="text-muted">(с другим заказом)</small>' : '';
                    $('#selected_orders_count').text(result.count);
                    $('#selected_orders_net_weight').text(FormatNumberRu(result.net_weight, 0) + ' кг');
                    $('#selected_orders_gross_weight').html(FormatNumberRu(result.gross_weight, 0) + ' кг' + sharedNote);
                    $('#selected_orders_pallet_count').html(result.pallet_count + sharedNote);
                    $('#selected_orders_volume').html('&asymp;' + FormatNumberRu(result.volume, 2) + ' м<sup>3</sup> <small class="text-muted">(' + FormatNumberRu(result.volume_min, 2) + '&ndash;' + FormatNumberRu(result.volume_max, 2) + ' м<sup>3</sup>)</small>');
                    $('#selected_orders_panel').addClass('open');
                })
                .fail(function() {
                    alert('Ошибка при подсчёте итогов по выбранным заказам');
                });
    }
    
    $(document).on('change', '.order-select-checkbox', function() {
        UpdateSelectedOrdersPanel();
    });
</script>
