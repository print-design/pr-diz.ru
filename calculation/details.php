<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Атрибут "поле неактивно"
$disabled_attr = " disabled='disabled'";

// Редактирование включения ПФ в стоимость
if(null !== filter_input(INPUT_POST, 'cliche_in_price_submit')) {
    $cliche_in_price = 0; if(filter_input(INPUT_POST, 'cliche_in_price') == 'on') $cliche_in_price = 1;
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update calculation set cliche_in_price = $cliche_in_price where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Если ПФ включены в себестоимость, то заказчик всегда платит за ПФ
    if(empty($error_message) && $cliche_in_price == 1) {
        $sql = "update calculation set customer_pays_for_cliche = 1 where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "delete from calculation_result where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование стороны, которая платит за ПФ
if(null !== filter_input(INPUT_POST, 'customer_pays_for_cliche_submit')) {
    $customer_pays_for_cliche = 0; if(filter_input(INPUT_POST, 'customer_pays_for_cliche') == 'on') $customer_pays_for_cliche = 1;
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update calculation set customer_pays_for_cliche = $customer_pays_for_cliche where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Если заказчик не платит за ПФ, то ПФ не включены в себестоимость
    if(empty($error_message) && $customer_pays_for_cliche == 0) {
        $sql = "update calculation set cliche_in_price = 0 where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "delete from calculation_result where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование включения ножа в стоимость
if(null !== filter_input(INPUT_POST, 'knife_in_price_submit')) {
    $knife_in_price = 0; if(filter_input(INPUT_POST, 'knife_in_price') == 'on') $knife_in_price = 1;
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update calculation set knife_in_price = $knife_in_price where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Если нож включены в себестоимость, то заказчик всегда платит за нож
    if(empty($error_message) && $knife_in_price == 1) {
        $sql = "update calculation set customer_pays_for_knife = 1 where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "delete from calculation_result where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование стороны, которая платит за нож
if(null !== filter_input(INPUT_POST, 'customer_pays_for_knife_submit')) {
    $customer_pays_for_knife = 0; if(filter_input(INPUT_POST, 'customer_pays_for_knife') == 'on') $customer_pays_for_knife = 1;
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "update calculation set customer_pays_for_knife = $customer_pays_for_knife where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    // Если заказчик не платит за нож, то нож не включены в себестоимость
    if(empty($error_message) && $customer_pays_for_knife == 0) {
        $sql = "update calculation set knife_in_price = 0 where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "delete from calculation_result where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    
    $sql = "update calculation set status_id = $status_id, status_date = now() where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

$new_forms_number = 0;

for($i=1; $i<=$calculation->ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $calculation->$ink_var;
    
    $color_var = "color_$i";
    $$color_var = $calculation->$color_var;
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $calculation->$cmyk_var;
    
    $lacquer_var = "lacquer_$i";
    $$lacquer_var = $calculation->$lacquer_var;
    
    $percent_var = "percent_$i";
    $$percent_var = $calculation->$percent_var;
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $calculation->$cliche_var;
    
    if($calculation->work_type_id == WORK_TYPE_PRINT) {
        if(!empty($$cliche_var) && $$cliche_var != CLICHE_OLD) {
            $new_forms_number++;
        }
    }
}

if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    $new_forms_number += ($calculation->cliches_count_flint + $calculation->cliches_count_kodak);
}

// Если статус - "Черновик" или "Сделан  расчёт", то все чекбосы и поля наценки активны
if($calculation->status_id == ORDER_STATUS_DRAFT || $calculation->status_id == ORDER_STATUS_CALCULATION) {
    $disabled_attr = "";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.calculation-table tr th, table.calculation-table tr td {
                padding-top: 5px;
                padding-right: 5px;
                padding-bottom: 5px;
                padding-left: 5px;
                vertical-align: top;
            }
            
            table.calculation-table tr td {
                white-space: nowrap;
            }
            
            #left_side {
                width: 45%;
            }
            
            #calculation {
                width: 50%;
            }
            
            .btn-outline-dark.draft {
                color: gray;
                background-color: white;
                border-color: gray;
                border-radius: 8px;
            }
            
            .btn-outline-dark.draft:hover, .btn-outline-dark.draft:active {
                color: white;
                background-color: gray;
                border-color: gray;
            }
            
            p {
                margin-bottom: 0;
                margin-top: .3rem;
            }
            
            #status {
                width: 100%;
                padding: 12px;
                margin-top: 40p;
                margin-bottom: 40px;
                border-radius: 10px;
                font-weight: bold;
                text-align: center; 
            }
            
            .form_button {
                width: 200px;
            }
        </style>
    </head>
    <body>
        <?php
        if(!empty($calculation->work_type_id) && $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
            include './right_panel_self_adhesive.php';
        }
        else {
            include './right_panel.php';
        }
        
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <!-- Левая половина -->
            <div id="left_side">
                <div class="text-nowrap nav2">
                    <a href="details.php?<?= http_build_query($_GET) ?>" class="mr-4 active">Расчёт</a>
                    <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
                    <a href="techmap.php?<?= http_build_query($_GET) ?>" class="mr-4">Тех. карта</a>
                    <?php endif; ?>
                    <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER])) && in_array($calculation->status_id, ORDER_STATUSES_IN_CUT)): ?>
                    <a href="cut.php?<?= http_build_query($_GET) ?>" class="mr-4">Результаты</a>
                    <?php endif; ?>
                </div>
                <hr />
                <?php
                if(!empty($error_message)) {
                    echo "<div class='alert alert-danger'>$error_message</div>";
                }
            
                $backlink_get = '';
            
                if(in_array($calculation->status_id, ORDER_STATUSES_NOT_IN_WORK)) {
                    $backlink_get = BuildQueryAddRemove('status', ORDER_STATUS_NOT_IN_WORK, 'id');
                }
                elseif(in_array($calculation->status_id, ORDER_STATUSES_IN_PRODUCTION)) {
                    $backlink_get = BuildQueryAddRemove('status', ORDER_STATUS_IN_PRODUCTION, 'id');
                }
                elseif(in_array ($calculation->status_id, array(ORDER_STATUS_DRAFT, ORDER_STATUS_TRASH, ORDER_STATUS_SHIP_READY, ORDER_STATUS_SHIPPED))) {
                    $backlink_get = BuildQueryAddRemove('status', $calculation->status_id, 'id');
                }
                else {
                    $backlink_get = BuildQueryRemoveArray(array('status', 'id'));
                }
                ?>
                <a class="btn btn-light backlink" href="<?=APPLICATION ?>/calculation/<?= $backlink_get ?>">К списку</a>
                <h1><?= $calculation->name ?></h1>
                <h2>№<?=$calculation->customer_id."-".$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></h2>
                <?php
                include '../include/order_status_details.php';
                include './left_panel.php';
                ?>
                <a href="create.php<?= BuildQuery("mode", "recalc") ?>" class="btn btn-dark mt-5 mr-2 form_button">Пересчитать</a>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
                AdjustFixedBlock($('#calculation'));
            }
            
            // Ограничение значений наценки
            $('#extracharge').keydown(function(e) {
                if(($(e.target).val() === 0 || $(e.target).val() === '' || $(e.target).prop('selectionStart') !== $(e.target).prop('selectionEnd')) && e.key === 0) {
                    return true;
                }
                else if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge_cliche').keydown(function(e) {
                if(($(e.target).val() === 0 || $(e.target).val() === '' || $(e.target).prop('selectionStart') !== $(e.target).prop('selectionEnd')) && e.key === 0) {
                    return true;
                }
                else if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge_knife').keydown(function(e) {
                if(($(e.target).val() === 0 || $(e.target).val() === '' || $(e.target).prop('selectionStart') !== $(e.target).prop('selectionEnd')) && e.key === 0) {
                    return true;
                }
                else if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge').change(function(){
                if($(this).val() !== '0') {
                    ChangeLimitIntValue($(this), 999);
                }
            });
            
            $('#extracharge_cliche').change(function(){
                if($(this).val() !== '0') {
                    ChangeLimitIntValue($(this), 999);
                }
            });
            
            $('#extracharge_knife').change(function(){
                if($(this).val() !== '0') {
                    ChangeLimitIntValue($(this), 999);
                }
            });
            
            // Редактируем наценку
            function SetExtracharge(param) {
                extracharge = parseInt(param);
                
                if(!isNaN(extracharge) && extracharge > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_extracharge.php?id=<?=$id ?>&extracharge=' + extracharge })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#shipping_cost').text(data.shipping_cost);
                                    $('#shipping_cost_per_unit').text(data.shipping_cost_per_unit);
                                    $('#input_shipping_cost_per_unit').val(data.input_shipping_cost_per_unit);
                                    $('#income').text(data.income);
                                    $('#income_per_unit').text(data.income_per_unit);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании наценки");
                            });
                }
            }
            
            $('#extracharge').keyup(function(){
                SetExtracharge($(this).val());
            });
            
            // Редактируем наценку на ПФ
            function SetExtrachargeCliche(param) {
                extracharge_cliche = parseInt(param);
                
                if(!isNaN(extracharge_cliche) && extracharge_cliche > -1) {
                    $.ajax({ dataType: 'JSON', url: "_set_extracharge_cliche.php?id=<?=$id ?>&extracharge_cliche=" + extracharge_cliche })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#shipping_cliche_cost').text(data.shipping_cliche_cost);
                                    $('#input_shipping_cliche_cost').val(data.input_shipping_cliche_cost);
                                    $('#income_cliche').text(data.income_cliche);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании наценки ПФ");
                            });
                }
            }
            
            $('#extracharge_cliche').keyup(function(){
                SetExtrachargeCliche($(this).val());
            });
            
            // Редактируем наценку на нож
            function SetExtrachargeKnife(param) {
                extracharge_knife = parseInt(param);
                
                if(!isNaN(extracharge_knife) && extracharge_knife > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_extracharge_knife.php?id=<?=$id ?>&extracharge_knife=' + extracharge_knife })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#shipping_knife_cost').text(data.shipping_knife_cost);
                                    $('#input_shipping_knife_cost').val(data.input_shipping_knife_cost);
                                    $('#income_knife').text(data.income_knife);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании наценки на нож");
                            });
                }
            }
            
            $('#extracharge_knife').keyup(function(){
                SetExtrachargeKnife($(this).val());
            });
            
            // Вычисляем наценку по отгрузочной стоимости за единицу
            function SetShippingCostPerUnit(param) {
                shipping_cost_per_unit = parseFloat(param.replace(',', '.'));
                
                if(!isNaN(shipping_cost_per_unit) && shipping_cost_per_unit > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_shipping_cost_per_unit.php?id=<?=$id ?>&shipping_cost_per_unit=' + shipping_cost_per_unit })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#extracharge').val(Math.round(data.extracharge));
                                    $('#shipping_cost').text(data.shipping_cost);
                                    $('#shipping_cost_per_unit').text(data.shipping_cost_per_unit);
                                    $('#income').text(data.income);
                                    $('#income_per_unit').text(data.income_per_unit);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании отгрузочной стоимость за единицу");
                            });
                }
            }
            
            $('#input_shipping_cost_per_unit').keyup(function() {
                SetShippingCostPerUnit($(this).val());
            });
            
            // Вычисляем наценку на ПФ по отгрузочной стоимости ПФ
            function SetShippingClicheCost(param) {
                shipping_cliche_cost = parseFloat(param.replace(',', '.'));
                
                if(!isNaN(shipping_cliche_cost) && shipping_cliche_cost > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_shipping_cliche_cost.php?id=<?=$id ?>&shipping_cliche_cost=' + shipping_cliche_cost })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#extracharge_cliche').val(Math.round(data.extracharge_cliche));
                                    $('#shipping_cliche_cost').text(data.shipping_cliche_cost);
                                    $('#income_cliche').text(data.income_cliche);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании отгрузочной стоимости ПФ");
                            });
                }
            }
            
            $('#input_shipping_cliche_cost').keyup(function() {
                SetShippingClicheCost($(this).val());
            });
            
            // Вычисляем наценку на нож по отгрузочной стоимости ножа
            function SetShippingKnifeCost(param) {
                shipping_knife_cost = parseFloat(param.replace(',', '.'));
                
                if(!isNaN(shipping_knife_cost) && shipping_knife_cost > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_shipping_knife_cost.php?id=<?=$id ?>&shipping_knife_cost=' + shipping_knife_cost })
                            .done(function(data) {
                                if(data.error !== '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#extracharge_knife').val(Math.round(data.extracharge_knife));
                                    $('#shipping_knife_cost').text(data.shipping_knife_cost);
                                    $('#income_knife').text(data.income_knife);
                                    $('#income_total').text(data.income_total);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при редактировании отгрузочной стоимости ножа");
                            });
                }
            }
            
            $('#input_shipping_knife_cost').keyup(function() {
                SetShippingKnifeCost($(this).val());
            });
            
            // Пересчитываем по новому значению "Включить ПФ в себестоимость" и "Заказчик платит за ПФ"
            function RecalculateByCliche() {
                if($('#calculation').hasClass('d-none')) {
                    return;
                }
                
                var cliche_in_price = $('#cliche_in_price').is(':checked') ? 1 : 0;
                var customer_pays_for_cliche = $('#customer_pays_for_cliche').is(':checked') ? 1 : 0;
                
                $.ajax({ dataType: 'JSON', url: '_recalculate_by_cliche.php?id=<?=$id ?>&cliche_in_price=' + cliche_in_price + '&customer_pays_for_cliche=' + customer_pays_for_cliche })
                        .done(function(data) {
                            if(data.error !== '') {
                                alert(data.error);
                            }
                            else {
                                if(data.cliche_in_price === 1) {
                                    $('#cliche_in_price_box').addClass('d-none');
                                }
                                else {
                                    $('#cliche_in_price_box').removeClass('d-none');
                                }
                                
                                $('#cost').text(data.cost);
                                $('#cost_per_unit').text(data.cost_per_unit);
                                $('#shipping_cost').text(data.shipping_cost);
                                $('#shipping_cost_per_unit').text(data.shipping_cost_per_unit);
                                $('#input_shipping_cost_per_unit').val(data.input_shipping_cost_per_unit);
                                $('#extracharge').val(Math.round(data.extracharge));
                                $('#income').text(data.income);
                                $('#income_per_unit').text(data.income_per_unit);
                                $('#shipping_cliche_cost').text(data.shipping_cliche_cost);
                                $('#input_shipping_cliche_cost').val(data.input_shipping_cliche_cost);
                                $('#income_cliche').text(data.income_cliche);
                                $('#income_total').text(data.income_total);                                
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при пересчёте по новым значениям Включать ПФ в себестоимость и Заказчик платит за ПФ');
                        });
            }
            
            // Пересчитываем по новому значению "Включить нож в себестоимость" и "Заказчик платит за нож"
            function RecalculateByKnife() {
                if($('#calculation').hasClass('d-none')) {
                    return;
                }
                
                var knife_in_price = $('#knife_in_price').is(':checked') ? 1 : 0;
                var customer_pays_for_knife = $('#customer_pays_for_knife').is(':checked') ? 1 : 0;
                
                $.ajax({ dataType: 'JSON', url: '_recalculate_by_knife.php?id=<?=$id ?>&knife_in_price=' + knife_in_price + '&customer_pays_for_knife=' + customer_pays_for_knife })
                        .done(function(data) {
                            if(data.error !== '') {
                                alert(data.error);
                            }
                            else {
                                if(data.knife_in_price === 1) {
                                    $('#knife_in_price_box').addClass('d-none');
                                }
                                else {
                                    $('#knife_in_price_box').removeClass('d-none');
                                }
                                
                                $('#cost').text(data.cost);
                                $('#cost_per_unit').text(data.cost_per_unit);
                                $('#shipping_cost').text(data.shipping_cost);
                                $('#shipping_cost_per_unit').text(data.shipping_cost_per_unit);
                                $('#input_shipping_cost_per_unit').val(data.input_shipping_cost_per_unit);
                                $('#extracharge').val(Math.round(data.extracharge));
                                $('#income').text(data.income);
                                $('#income_per_unit').text(data.income_per_unit);
                                $('#shipping_knife_cost').text(data.shipping_knife_cost);
                                $('#income_knife').text(data.income_knife);
                                $('#income_total').text(data.income_total);                                
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при пересчёте по новым значениям Включать ПФ в себестоимость и Заказчик платит за ПФ');
                        });
            }
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            AdjustFixedBlock($('#calculation'));
            
            $(window).on("scroll", function(){
                AdjustFixedBlock($('#calculation'));
            });
        </script>
    </body>
</html>