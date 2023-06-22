<?php
include '../include/topscripts.php';
include './calculation.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))) {
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

$sql = "select rc.date, rc.customer_id, rc.name, rc.unit, rc.quantity, rc.work_type_id, "
        . "rc.film_variation_id, f.name film_name, fv.thickness thickness, fv.weight weight, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, rc.customers_material, rc.ski, rc.width_ski, "
        . "rc.lamination1_film_variation_id, lam1f.name lamination1_film_name, lam1fv.thickness lamination1_thickness, lam1fv.weight lamination1_weight, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
        . "rc.lamination2_film_variation_id, lam2f.name lamination2_film_name, lam2fv.thickness lamination2_thickness, lam2fv.weight lamination2_weight, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
        . "rc.machine_id, rc.laminator_id, rc.streams_number, rc.length, rc.stream_width, rc.raport, rc.number_in_raport, rc.lamination_roller_width, rc.ink_number, u.first_name, u.last_name, rc.status_id, "
        . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
        . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
        . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
        . "rc.lacquer_1, rc.lacquer_2, rc.lacquer_3, rc.lacquer_4, rc.lacquer_5, rc.lacquer_6, rc.lacquer_7, rc.lacquer_8, "
        . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
        . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
        . "rc.cliche_in_price, rc.cliches_count_flint, rc.cliches_count_kodak, rc.cliches_count_old, rc.extracharge, rc.extracharge_cliche, rc.customer_pays_for_cliche, "
        . "rc.knife, rc.extracharge_knife, rc.knife_in_price, rc.customer_pays_for_knife, extra_expense, "
        . "cus.name customer, cus.phone customer_phone, cus.extension customer_extension, cus.email customer_email, cus.person customer_person, "
        . "(select count(id) from calculation where customer_id = rc.customer_id and id <= rc.id) num_for_customer,"
        . "(select gap from calculation_result where calculation_id = rc.id) gap, tm.id techmap_id, pe.id plan_edition_id "
        . "from calculation rc "
        . "left join film_variation fv on rc.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_variation lam1fv on rc.lamination1_film_variation_id = lam1fv.id "
        . "left join film lam1f on lam1fv.film_id = lam1f.id "
        . "left join film_variation lam2fv on rc.lamination2_film_variation_id = lam2fv.id "
        . "left join film lam2f on lam2fv.film_id = lam2f.id "
        . "left join user u on rc.manager_id = u.id "
        . "left join customer cus on rc.customer_id = cus.id "
        . "left join techmap tm on tm.calculation_id = rc.id "
        . "left join plan_edition pe on pe.calculation_id = rc.id "
        . "where rc.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$unit = $row['unit'];
$quantity = $row['quantity'];
$work_type_id = $row['work_type_id'];

$film_variation_id = $row['film_variation_id'];
$film_name = $row['film_name'];
$thickness = $row['thickness'];
$weight = $row['weight'];
$price = $row['price'];
$currency = $row['currency'];
$individual_film_name = $row['individual_film_name'];
$individual_thickness = $row['individual_thickness'];
$individual_density = $row['individual_density'];
$customers_material = $row['customers_material'];
$ski = $row['ski'];
$width_ski = $row['width_ski'];

$lamination1_film_variation_id = $row['lamination1_film_variation_id'];
$lamination1_film_name = $row['lamination1_film_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_weight = $row['lamination1_weight'];
$lamination1_price = $row['lamination1_price'];
$lamination1_currency = $row['lamination1_currency'];
$lamination1_individual_film_name = $row['lamination1_individual_film_name'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination1_individual_density = $row['lamination1_individual_density'];
$lamination1_customers_material = $row['lamination1_customers_material'];
$lamination1_ski = $row['lamination1_ski'];
$lamination1_width_ski = $row['lamination1_width_ski'];

$lamination2_film_variation_id = $row['lamination2_film_variation_id'];
$lamination2_film_name = $row['lamination2_film_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_weight = $row['lamination2_weight'];
$lamination2_price = $row['lamination2_price'];
$lamination2_currency = $row['lamination2_currency'];
$lamination2_individual_film_name = $row['lamination2_individual_film_name'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];
$lamination2_individual_density = $row['lamination2_individual_density'];
$lamination2_customers_material = $row['lamination2_customers_material'];
$lamination2_ski = $row['lamination2_ski'];
$lamination2_width_ski = $row['lamination2_width_ski'];

$machine_id = $row['machine_id'];
$laminator_id = $row['laminator_id'];
$streams_number = $row['streams_number'];
$length = $row['length'];
$stream_width = $row['stream_width'];
$raport = $row['raport'];
$number_in_raport = $row['number_in_raport'];
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$status_id = $row['status_id'];

$new_forms_number = 0;

for($i=1; $i<=$ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $row[$ink_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $lacquer_var = "lacquer_$i";
    $$lacquer_var = $row[$lacquer_var];
    
    $percent_var = "percent_$i";
    $$percent_var = $row[$percent_var];
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $row[$cliche_var];
    
    if($work_type_id == CalculationBase::WORK_TYPE_PRINT) {
        if(!empty($$cliche_var) && $$cliche_var != CalculationBase::OLD) {
            $new_forms_number++;
        }
    }
}

$cliche_in_price = $row['cliche_in_price'];
$cliches_count_flint = $row['cliches_count_flint'];
$cliches_count_kodak = $row['cliches_count_kodak'];
$cliches_count_old = $row['cliches_count_old'];
$extracharge = $row['extracharge'];
$extracharge_cliche = $row['extracharge_cliche'];
$customer_pays_for_cliche = $row['customer_pays_for_cliche'];

$knife = $row['knife'];
$extracharge_knife = $row['extracharge_knife'];
$knife_in_price = $row['knife_in_price'];
$customer_pays_for_knife = $row['customer_pays_for_knife'];
$extra_expense = $row['extra_expense'];

if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
    $new_forms_number += ($cliches_count_flint + $cliches_count_kodak);
}

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$num_for_customer = $row['num_for_customer'];
$gap = $row['gap'];

$techmap_id = $row['techmap_id'];
$plan_edition_id = $row['plan_edition_id'];

// Если есть ламинация, а ламинатор пустой, то присваиваем ему значение "Сольвент".
// (В старых расчётах ламинатор может быть не указан, поскольку тогда бессольвента не было.)
if((!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) && empty($laminator_id)) {
    $laminator_id = CalculationBase::SOLVENT_YES;
}

// Если статус - "Черновик" или "Сделан  расчёт", то все чекбосы и поля наценки активны
if($status_id == ORDER_STATUS_DRAFT || $status_id == ORDER_STATUS_CALCULATION) {
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
            
            h1 {
                font-size: 32px;
                font-weight: 600;
            }
            
            h2 {
                font-size: 26px;
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
        if(!empty($work_type_id) && $work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE) {
            include './right_panel_self_adhesive.php';
        }
        else {
            include './right_panel.php';
        }
        
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $backlink_get = '';
            
            if(in_array($status_id, array(ORDER_STATUS_CALCULATION, ORDER_STATUS_TECHMAP))) {
                $backlink_get = BuildQueryAddRemove('status', ORDER_STATUS_NOT_IN_WORK, 'id');
            }
            elseif(in_array ($status_id, array(ORDER_STATUS_DRAFT, ORDER_STATUS_TRASH))) {
                $backlink_get = BuildQueryAddRemove('status', $status_id, 'id');
            }
            else {
                $backlink_get = BuildQueryRemoveArray(array('status', 'id'));
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/calculation/<?= $backlink_get ?>">Назад</a>
            <!-- Левая половина -->
            <div id="left_side">
                <h1><?= htmlentities($name) ?></h1>
                <h2>№<?=$customer_id."-".$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                <div id="status" style="border: solid 2px <?=ORDER_STATUS_COLORS[$status_id] ?>; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">
                    <i class="<?=ORDER_STATUS_ICONS[$status_id] ?>"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$status_id] ?>
                </div>
                <?php include './left_panel.php'; ?>
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
                if(($(e.target).val() == 0 || $(e.target).val() == '' || $(e.target).prop('selectionStart') != $(e.target).prop('selectionEnd')) && e.key == 0) {
                    return true;
                }
                else if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge_cliche').keydown(function(e) {
                if(($(e.target).val() == 0 || $(e.target).val() == '' || $(e.target).prop('selectionStart') != $(e.target).prop('selectionEnd')) && e.key == 0) {
                    return true;
                }
                else if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge_knife').keydown(function(e) {
                if(($(e.target).val() == 0 || $(e.target).val() == '' || $(e.target).prop('selectionStart') != $(e.target).prop('selectionEnd')) && e.key == 0) {
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
            
            // Вычисляем отгрузочную стоимость при других наценках
            function SetExtracharge(param) {
                extracharge = parseInt(param);
                
                if(!isNaN(extracharge) && extracharge > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_extracharge.php?id=<?=$id ?>&extracharge=' + extracharge })
                            .done(function(data) {
                                if(data.error != '') {
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
            
            function SetExtrachargeCliche(param) {
                extracharge_cliche = parseInt(param);
                
                if(!isNaN(extracharge_cliche) && extracharge_cliche > -1) {
                    $.ajax({ dataType: 'JSON', url: "_set_extracharge_cliche.php?id=<?=$id ?>&extracharge_cliche=" + extracharge_cliche })
                            .done(function(data) {
                                if(data.error != '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#shipping_cliche_cost').text(data.shipping_cliche_cost);
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
            
            // Вычисляем наценку по отгрузочной стоимости за единицу
            function SetShippingCostPerUnit(param) {
                shipping_cost_per_unit = parseFloat(param.replace(',', '.'));
                
                if(!isNaN(shipping_cost_per_unit) && shipping_cost_per_unit > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_shipping_cost_per_unit.php?id=<?=$id ?>&shipping_cost_per_unit=' + shipping_cost_per_unit })
                            .done(function(data) {
                                if(data.error != '') {
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
            
            // Вычисляем наценку на нож
            function SetExtrachargeKnife(param) {
                extracharge_knife = parseInt(param);
                
                if(!isNaN(extracharge_knife) && extracharge_knife > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_extracharge_knife.php?id=<?=$id ?>&extracharge_knife=' + extracharge_knife })
                            .done(function(data) {
                                if(data.error != '') {
                                    alert(data.error);
                                }
                                else {
                                    $('#shipping_knife_cost').text(data.shipping_knife_cost);
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
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            AdjustFixedBlock($('#calculation'));
            
            $(window).on("scroll", function(){
                AdjustFixedBlock($('#calculation'));
            });
        </script>
    </body>
</html>