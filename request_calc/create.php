<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машины
const ZBS = "zbs";
const COMIFLEX = "comiflex";

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Валюты
const USD = "usd";
const EURO = "euro";

// Краски
const CMYK = "cmyk";
const CYAN = "cyan";
const MAGENTA = "magenta";
const YELLOW = "yellow";
const KONTUR = "kontur";
const PANTON = "panton";
const WHITE = "white";
const LACQUER = "lacquer";

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$customer_id_valid = '';
$name_valid = '';
$work_type_valid = '';
$brand_name_valid = '';
$thickness_valid = '';
$quantity_valid = '';

$individual_brand_name_valid = '';
$individual_price_valid = '';
$individual_thickness_valid = '';
$individual_density_valid = '';

$stream_width_valid = '';
$stream_width_valid_message = "Ширина ручья обязательно";
$streams_count_valid = '';
$streams_count_valid_message = "Количество ручьёв обязательно";

$length_valid = '';
$length_message = "Длина этикетки вдоль рапорта вала обязательно";
$number_on_raport_valid = '';
$number_on_raport_message = "Количество этикеток на ручье обязательно";
$raport_valid = '';
$raport_message = "Рапорт обязательно";

// Переменные для валидации цвета, CMYK и процента
for($i=1; $i<=8; $i++) {
    $color_valid_var = 'color_'.$i.'_valid';
    $$color_valid_var = '';
    
    $cmyk_valid_var = 'cmyk_'.$i.'_valid';
    $$cmyk_valid_var = '';
    
    $percent_valid_var = 'percent_'.$i.'_valid';
    $$percent_valid_var = '';
}

// Обработка нажатия кнопки "Сохранить расчёт"
if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    // Если тип работы "Пленка без печати", то обязательно требуем добавить хотя бы одну ламинацию
    if(filter_input(INPUT_POST, 'work_type_id') == 1 && empty(filter_input(INPUT_POST, 'lamination1_brand_name'))) {
        $error_message = "Если тип работы 'Пленка без печати', то выберите хотя бы одну ламинацию";
        $form_valid = false;
    }
    
    // Валидация
    
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    
    if(empty($customer_id)) {
        $customer_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $name = addslashes(filter_input(INPUT_POST, 'name'));
    
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $work_type_id = filter_input(INPUT_POST, 'work_type_id');
    
    if(empty($work_type_id)) {
        $work_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    $unit = filter_input(INPUT_POST, 'unit');
    $quantity = preg_replace("/\D/", "", filter_input(INPUT_POST, 'quantity'));
    
    if(empty(filter_input(INPUT_POST, 'quantity'))) {
        $quantity_valid = ISINVALID;
        $form_valid = false;
    }
    
    $machine = filter_input(INPUT_POST, 'machine');
    $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
    
    if(empty($brand_name)) {
        $brand_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    $individual_brand_name = filter_input(INPUT_POST, 'individual_brand_name');
    $individual_price = filter_input(INPUT_POST, 'individual_price');
    $individual_thickness = filter_input(INPUT_POST, 'individual_thickness');
    $individual_density = filter_input(INPUT_POST, 'individual_density');
    
    if($brand_name == INDIVIDUAL) {
        // Проверка валидности параметров, введённых вручную при выборе марки плёнки "Другая"
        if(empty($individual_brand_name)) {
            $individual_brand_name_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(filter_input(INPUT_POST, 'customers_material') != 'on' && empty($individual_price)) {
            $individual_price_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty($individual_thickness)) {
            $individual_thickness_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty($individual_density)) {
            $individual_density_valid = ISINVALID;
            $form_valid = false;
        }
    }
    else {
        // Проверка валидности параметров стандартных плёнок
        if(empty($thickness)) {
            $thickness_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    $customers_material = 0;
    if(filter_input(INPUT_POST, 'customers_material') == 'on') {
        $customers_material = 1;
    }
    
    $stream_width = filter_input(INPUT_POST, 'stream_width');
    
    if(empty($stream_width)) {
        $stream_width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $streams_count = filter_input(INPUT_POST, 'streams_count');
    
    if(empty($streams_count)) {
        $streams_count_valid = ISINVALID;
        $form_valid = false;
    }
    
    $length = filter_input(INPUT_POST, 'length');
    
    // Если объём заказа в штуках, то длина этикетки вдоль рапорта вала обязательно, больше нуля
    if($unit == 'pieces' && empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $raport = filter_input(INPUT_POST, 'raport');
    $number_on_raport = filter_input(INPUT_POST, 'number_on_raport');
    $lamination_roller = filter_input(INPUT_POST, 'lamination_roller');
    $ski_width = filter_input(INPUT_POST, 'ski_width');
    
    $no_ski = 0;
    if(filter_input(INPUT_POST, 'no_ski') == 'on') {
        $no_ski = 1;
    }
    
    // Проверка валидности цвета, CMYK и процента
    $paints_count = filter_input(INPUT_POST, 'paints_count');
    
    for($i=1; $i<=8; $i++) {
        if(!empty($paints_count) && is_numeric($paints_count) && $i <= $paints_count) {
            $paint_var = "paint_".$i;
            $$paint_var = filter_input(INPUT_POST, 'paint_'.$i);
            
            $color_var = "color_".$i;
            $$color_var = filter_input(INPUT_POST, 'color_'.$i);
            
            $cmyk_var = "cmyk_".$i;
            $$cmyk_var = filter_input(INPUT_POST, 'cmyk_'.$i);
            
            $percent_var = "percent_".$i;
            $$percent_var = filter_input(INPUT_POST, 'percent_'.$i);
            
            // Проценты вводят художники, поэтому их не делаем обязательными
            if($$paint_var == 'panton' && empty($$color_var)) {
                $color_valid_var = 'color_'.$i.'_valid';
                $$color_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if($$paint_var == 'cmyk' && empty($$cmyk_var)) {
                $cmyk_valid_var = 'cmyk_'.$i.'_valid';
                $$cmyk_valid_var = ISINVALID;
                $form_valid = false;
            }
        }
    }
    
    // Номер машины
    $machine_ids = array();
    $machine_shortnames = array();
    
    $sql = "select id, shortname from machine";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $machine_ids[$row['shortname']] = $row['id'];
        $machine_shortnames[$row['id']] = $row['shortname'];
    }
    
    $machine_id = null;
    
    if(!empty($machine) && !empty($paints_count)) {
        if($machine == 'comiflex') {
            $machine_id = $machine_ids['comiflex'];
        }
        elseif($paints_count > 6) {
            $machine_id = $machine_ids['zbs3'];
        }
        else {
            $machine_id = $machine_ids['zbs1'];
        }
    }
    
    // Общая ширина материала (сумма ширин ручьёв) должна быть не больше, чем возможно для данной машины.
    // При этом, если печать с лыжами, то сравнивается ширина плюс лыжи.
    $sum_stream_widths = 0;
    
    if(!empty($stream_width) && !empty($streams_count)) {
        $sum_stream_widths = intval($stream_width) * intval($streams_count);
    }
    
    if(!empty($machine_id)) {
        $machine_max_width = 0;
        
        $sql = "select max_width from norm_machine where machine_id = $machine_id order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $machine_max_width = $row['max_width'];
        }
        
        if($no_ski && $sum_stream_widths > $machine_max_width) {
            $stream_width_valid_message = "Сумма ручьёв для печати не более $machine_max_width мм";
            $streams_count_valid_message = $stream_width_valid_message;
            $stream_width_valid = ISINVALID;
            $streams_count_valid = ISINVALID;
            $form_valid = false;
        }
        elseif(!$no_ski && ($sum_stream_widths + $ski_width) > $machine_max_width) {
            $stream_width_valid_message = "Сумма ручьёв для печати (минус лыжи) не более ".($machine_max_width - $ski_width)." мм";
            $streams_count_valid_message = $stream_width_valid_message;
            $stream_width_valid = ISINVALID;
            $streams_count_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Если печать с ламинацией, то проверяем ещё и максимальную ширину для ламинации
    if(!empty(filter_input(INPUT_POST, 'lamination1_brand_name'))) {
        $laminator_max_width = 0;
        
        $sql = "select max_width from norm_laminator order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $laminator_max_width = $row['max_width'];
        }
        
        if($sum_stream_widths > $laminator_max_width) {
            $stream_width_valid_message = "Сумма ручьёв для ламинации не более $laminator_max_width мм";
            $streams_count_valid_message = $stream_width_valid_message;
            $stream_width_valid = ISINVALID;
            $streams_count_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Длина этикетки вдоль рапорта, умноженная на количество этикеток на ручье
    // Должна соответствовать рапорту
    //if($length * $number_on_raport != $raport) {
    if(!empty($raport) && !empty($number_on_raport) && !empty($length) && round($raport / $number_on_raport, 4) != $length) {
        $raport_valid = ISINVALID;
        $length_valid = ISINVALID;
        $number_on_raport_valid = ISINVALID;
        $raport_message = "Сумма длин не соответствует рапорту";
        $length_message = $raport_message;
        $number_on_raport_message = $raport_message;
        $form_valid = false;
    }
    
    if($form_valid) {
        if(empty($thickness)) $thickness = "NULL";
        if(empty($individual_price)) $individual_price = "NULL";
        if(empty($individual_thickness)) $individual_thickness = "NULL";
        if(empty($individual_density)) $individual_density = "NULL";
        $unit = filter_input(INPUT_POST, 'unit');
        $machine = filter_input(INPUT_POST, 'machine');
        
        $lamination1_brand_name = addslashes(filter_input(INPUT_POST, 'lamination1_brand_name'));
        $lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
        if(empty($lamination1_thickness)) $lamination1_thickness = "NULL";
        $lamination1_individual_brand_name = filter_input(INPUT_POST, 'lamination1_individual_brand_name');
        $lamination1_individual_price = filter_input(INPUT_POST, 'lamination1_individual_price');
        if(empty($lamination1_individual_price)) $lamination1_individual_price = "NULL";
        $lamination1_individual_thickness = filter_input(INPUT_POST, 'lamination1_individual_thickness');
        if(empty($lamination1_individual_thickness)) $lamination1_individual_thickness = "NULL";
        $lamination1_individual_density = filter_input(INPUT_POST, 'lamination1_individual_density');
        if(empty($lamination1_individual_density)) $lamination1_individual_density = "NULL";
        $lamination1_customers_material = 0;
        if(filter_input(INPUT_POST, 'lamination1_customers_material') == 'on') {
            $lamination1_customers_material = 1;
        }
        
        $lamination2_brand_name = addslashes(filter_input(INPUT_POST, 'lamination2_brand_name'));
        $lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
        if(empty($lamination2_thickness)) $lamination2_thickness = "NULL";
        $lamination2_individual_brand_name = filter_input(INPUT_POST, 'lamination2_individual_brand_name');
        $lamination2_individual_price = filter_input(INPUT_POST, 'lamination2_individual_price');
        if(empty($lamination2_individual_price)) $lamination2_individual_price = "NULL";
        $lamination2_individual_thickness = filter_input(INPUT_POST, 'lamination2_individual_thickness');
        if(empty($lamination2_individual_thickness)) $lamination2_individual_thickness = "NULL";
        $lamination2_individual_density = filter_input(INPUT_POST, 'lamination2_individual_density');
        if(empty($lamination2_individual_density)) $lamination2_individual_density = "NULL";
        $lamination2_customers_material = 0;
        if(filter_input(INPUT_POST, 'lamination2_customers_material') == 'on') {
            $lamination2_customers_material = 1;
        }
        
        if($length === null || $length === '') $length = "NULL";
        if(empty($stream_width)) $stream_width = "NULL";
        if(empty($streams_count)) $streams_count = "NULL";
        if(empty($raport)) $raport = "NULL";
        if(empty($number_on_raport)) $number_on_raport = "NULL";
        if(empty($lamination_roller)) $lamination_roller = "NULL";
        
        if(empty($ski_width)) $ski_width = "NULL";
        $paints_count = filter_input(INPUT_POST, 'paints_count');
        if(empty($paints_count)) $paints_count = "NULL";
        
        $manager_id = GetUserId();
        
        $extracharge = filter_input(INPUT_POST, 'extracharge');
        
        if(empty($extracharge)) {
            // Если объём заказа в штуках, то наценка по умолчанию 35.
            // Если в килограммах, то наценку берём из таблицы extracharge.
            $extracharge = 35;
            
            if($unit == "kg") {
                // Тип наценки:
                $extracharge_type_id = 0;
                
                if($work_type_id == 1) {
                    // 1 - без печати
                    $extracharge_type_id = 1;
                }
                elseif(empty ($lamination1_brand_name)) {
                    // 2 - печать без ламинации
                    $extracharge_type_id = 2;
                }
                elseif(empty ($lamination2_brand_name)) {
                    // 3 - печать с одной ламинацией
                    $extracharge_type_id = 3;
                }
                else {
                    // 4 - печать с двумя ламинациями
                    $extracharge_type_id = 4;
                }
                
                $sql_ec = "select value from extracharge where from_weight <= $quantity and to_weight >= $quantity and extracharge_type_id = $extracharge_type_id order by id limit 1";
                $fetcher_ec = new Fetcher($sql_ec);
                
                if($row_ec = $fetcher_ec->Fetch()) {
                    $extracharge = $row_ec[0];
                }
            }
        }
        
        // Данные о цвете
        for($i=1; $i<=8; $i++) {
            $paint_var = "paint_$i";
            $color_var = "color_$i";
            $cmyk_var = "cmyk_$i";
            $percent_var = "percent_$i";
            $cliche_var = "cliche_$i";
            
            $$paint_var = null;
            $$color_var = "NULL";
            $$cmyk_var = null;
            $$percent_var = "NULL";
            $$cliche_var = null;
            
            if(!empty($paints_count) && $paints_count >= $i) {
                $$paint_var = filter_input(INPUT_POST, "paint_$i");
            
                $$color_var = filter_input(INPUT_POST, "color_$i");
                if(empty($$color_var)) $$color_var = "NULL";
            
                $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
            
                $$percent_var = filter_input(INPUT_POST, "percent_$i");
                if(empty($$percent_var)) $$percent_var = "NULL";
            
                $$cliche_var = filter_input(INPUT_POST, "cliche_$i");
            }
        }
       
        // Сохранение в базу
        if(empty($error_message)) {
            // Если mode = recalc или пустой id, то создаём новый объект
            if(filter_input(INPUT_GET, 'mode') == 'recalc' || empty(filter_input(INPUT_GET, 'id'))) {
                $sql = "insert into request_calc (customer_id, name, work_type_id, unit, machine, "
                        . "brand_name, thickness, individual_brand_name, individual_price, individual_thickness, individual_density, customers_material, "
                        . "lamination1_brand_name, lamination1_thickness, lamination1_individual_brand_name, lamination1_individual_price, lamination1_individual_thickness, lamination1_individual_density, lamination1_customers_material, "
                        . "lamination2_brand_name, lamination2_thickness, lamination2_individual_brand_name, lamination2_individual_price, lamination2_individual_thickness, lamination2_individual_density, lamination2_customers_material, "
                        . "quantity, streams_count, length, stream_width, raport, number_on_raport, lamination_roller, paints_count, manager_id, extracharge, ski_width, no_ski, "
                        . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
                        . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
                        . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
                        . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
                        . "cliche_1, cliche_2, cliche_3, cliche_4, cliche_5, cliche_6, cliche_7, cliche_8) "
                        . "values($customer_id, '$name', $work_type_id, '$unit', '$machine', "
                        . "'$brand_name', $thickness, '$individual_brand_name', $individual_price, $individual_thickness, $individual_density, $customers_material, "
                        . "'$lamination1_brand_name', $lamination1_thickness, '$lamination1_individual_brand_name', $lamination1_individual_price, $lamination1_individual_thickness, $lamination1_individual_density, $lamination1_customers_material, "
                        . "'$lamination2_brand_name', $lamination2_thickness, '$lamination2_individual_brand_name', $lamination2_individual_price, $lamination2_individual_thickness, $lamination2_individual_density, $lamination2_customers_material, "
                        . "$quantity, $streams_count, $length, $stream_width, $raport, $number_on_raport, $lamination_roller, $paints_count, $manager_id, $extracharge, $ski_width, $no_ski, "
                        . "'$paint_1', '$paint_2', '$paint_3', '$paint_4', '$paint_5', '$paint_6', '$paint_7', '$paint_8', "
                        . "'$color_1', '$color_2', '$color_3', '$color_4', '$color_5', '$color_6', '$color_7', '$color_8', "
                        . "'$cmyk_1', '$cmyk_2', '$cmyk_3', '$cmyk_4', '$cmyk_5', '$cmyk_6', '$cmyk_7', '$cmyk_8', "
                        . "'$percent_1', '$percent_2', '$percent_3', '$percent_4', '$percent_5', '$percent_6', '$percent_7', '$percent_8', "
                        . "'$cliche_1', '$cliche_2', '$cliche_3', '$cliche_4', '$cliche_5', '$cliche_6', '$cliche_7', '$cliche_8')";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                $id = $executer->insert_id;
            }
            else {
                $sql = "update request_calc "
                        . "set customer_id=$customer_id, name='$name', work_type_id=$work_type_id, unit='$unit', machine='$machine', "
                        . "brand_name='$brand_name', thickness=$thickness, individual_brand_name='$individual_brand_name', individual_price=$individual_price, "
                        . "individual_thickness=$individual_thickness, individual_density=$individual_density, customers_material=$customers_material, "
                        . "lamination1_brand_name='$lamination1_brand_name', lamination1_thickness=$lamination1_thickness, "
                        . "lamination1_individual_brand_name='$lamination1_individual_brand_name', lamination1_individual_price=$lamination1_individual_price, "
                        . "lamination1_individual_thickness=$lamination1_individual_thickness, lamination1_individual_density=$lamination1_individual_density, "
                        . "lamination1_customers_material=$lamination1_customers_material, "
                        . "lamination2_brand_name='$lamination2_brand_name', lamination2_thickness=$lamination2_thickness, "
                        . "lamination2_individual_brand_name='$lamination2_individual_brand_name', lamination2_individual_price=$lamination2_individual_price, "
                        . "lamination2_individual_thickness=$lamination2_individual_thickness, lamination2_individual_density=$lamination2_individual_density, "
                        . "lamination2_customers_material=$lamination2_customers_material, "
                        . "quantity=$quantity, streams_count=$streams_count, length=$length, stream_width=$stream_width, raport=$raport, "
                        . "number_on_raport=$number_on_raport, "
                        . "lamination_roller=$lamination_roller, paints_count=$paints_count, manager_id=$manager_id, "
                        . "extracharge=$extracharge, ski_width=$ski_width, no_ski=$no_ski, "
                        . "paint_1='$paint_1', paint_2='$paint_2', paint_3='$paint_3', paint_4='$paint_4', "
                        . "paint_5='$paint_5', paint_6='$paint_6', paint_7='$paint_7', paint_8='$paint_8', "
                        . "color_1='$color_1', color_2='$color_2', color_3='$color_3', color_4='$color_4', "
                        . "color_5='$color_5', color_6='$color_6', color_7='$color_7', color_8='$color_8', "
                        . "cmyk_1='$cmyk_1', cmyk_2='$cmyk_2', cmyk_3='$cmyk_3', cmyk_4='$cmyk_4', "
                        . "cmyk_5='$cmyk_5', cmyk_6='$cmyk_6', cmyk_7='$cmyk_7', cmyk_8='$cmyk_8', "
                        . "percent_1='$percent_1', percent_2='$percent_2', percent_3='$percent_3', percent_4='$percent_4', "
                        . "percent_5='$percent_5', percent_6='$percent_6', percent_7='$percent_7', percent_8='$percent_8', "
                        . "cliche_1='$cliche_1', cliche_2='$cliche_2', cliche_3='$cliche_3', cliche_4='$cliche_4', "
                        . "cliche_5='$cliche_5', cliche_6='$cliche_6', cliche_7='$cliche_7', cliche_8='$cliche_8' "
                        . "where id=$id";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/request_calc/request_calc.php?id='.$id);
        }
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

if(!empty($id)) {
    $sql = "select date, customer_id, name, work_type_id, unit, machine, "
            . "brand_name, thickness, individual_brand_name, individual_price, individual_thickness, individual_density, customers_material, "
            . "lamination1_brand_name, lamination1_thickness, lamination1_individual_brand_name, lamination1_individual_price, lamination1_individual_thickness, lamination1_individual_density, lamination1_customers_material, "
            . "lamination2_brand_name, lamination2_thickness, lamination2_individual_brand_name, lamination2_individual_price, lamination2_individual_thickness, lamination2_individual_density, lamination2_customers_material, "
            . "quantity, streams_count, length, stream_width, raport, number_on_raport, lamination_roller, paints_count, extracharge, ski_width, no_ski, "
            . "(select id from techmap where request_calc_id = $id limit 1) techmap_id, "
            . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
            . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
            . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "cliche_1, cliche_2, cliche_3, cliche_4, cliche_5, cliche_6, cliche_7, cliche_8 "
            . "from request_calc where id=$id";
    $row = (new Fetcher($sql))->Fetch();
}

if(isset($row['date'])) $date = $row['date'];
else $date = null;

$customer_id = filter_input(INPUT_POST, 'customer_id');
if(null === $customer_id) {
    if(isset($row['customer_id'])) $customer_id = $row['customer_id'];
    else $customer_id = null;
}

$name = filter_input(INPUT_POST, 'name');
if(null === $name) {
    if(isset($row['name'])) $name = $row['name'];
    else $name = null;
}

$work_type_id = filter_input(INPUT_POST, 'work_type_id');
if(null === $work_type_id) {
    if(isset($row['work_type_id'])) $work_type_id = $row['work_type_id'];
    else $work_type_id = null;
}

$brand_name = filter_input(INPUT_POST, 'brand_name');
if(null === $brand_name) {
    if(isset($row['brand_name'])) $brand_name = $row['brand_name'];
    else $brand_name = null;
}

$thickness = filter_input(INPUT_POST, 'thickness');
if(null === $thickness) {
    if(isset($row['thickness'])) $thickness = $row['thickness'];
    else $thickness = null;
}

$individual_brand_name = filter_input(INPUT_POST, 'individual_brand_name');
if(null === $individual_brand_name) {
    if(isset($row['individual_brand_name'])) $individual_brand_name = $row['individual_brand_name'];
    else $individual_brand_name = null;
}

$individual_price = filter_input(INPUT_POST, 'individual_price');
if(null === $individual_price) {
    if(isset($row['individual_price'])) $individual_price = $row['individual_price'];
    else $individual_price = null;
}

$individual_thickness = filter_input(INPUT_POST, 'individual_thickness');
if(null === $individual_thickness) {
    if(isset($row['individual_thickness'])) $individual_thickness = $row['individual_thickness'];
    else $individual_thickness = null;
}

$individual_density = filter_input(INPUT_POST, 'individual_density');
if(null === $individual_density) {
    if(isset($row['individual_density'])) $individual_density = $row['individual_density'];
    else $individual_density = null;
}

if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $customers_material = filter_input(INPUT_POST, 'customers_material') == 'on' ? 1 : 0;
}
else {
    if(isset($row['customers_material'])) $customers_material = $row['customers_material'];
    else $customers_material = null;
}

$unit = filter_input(INPUT_POST, "unit");
if(null === $unit) {
    if(isset($row['unit'])) $unit = $row['unit'];
    else $unit = null;
}

$machine = filter_input(INPUT_POST, 'machine');
if(null === $machine) {
    if(isset($row['machine'])) $machine = $row['machine'];
    else $machine = null;
}

$lamination1_brand_name = filter_input(INPUT_POST, 'lamination1_brand_name');
if(null === $lamination1_brand_name) {
    if(isset($row['lamination1_brand_name'])) $lamination1_brand_name = $row['lamination1_brand_name'];
    else $lamination1_brand_name = null;
}

$lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
if(null === $lamination1_thickness) {
    if(isset($row['lamination1_thickness'])) $lamination1_thickness = $row['lamination1_thickness'];
    else $lamination1_thickness = null;
}

$lamination1_individual_brand_name = filter_input(INPUT_POST, 'lamination1_individual_brand_name');
if(null === $lamination1_individual_brand_name) {
    if(isset($row['lamination1_individual_brand_name'])) $lamination1_individual_brand_name = $row['lamination1_individual_brand_name'];
    else $lamination1_individual_brand_name = null;
}

$lamination1_individual_price = filter_input(INPUT_POST, 'lamination1_individual_price');
if(null === $lamination1_individual_price) {
    if(isset($row['lamination1_individual_price'])) $lamination1_individual_price = $row['lamination1_individual_price'];
    else $lamination1_individual_price = null;
}

$lamination1_individual_thickness = filter_input(INPUT_POST, 'lamination1_individual_thickness');
if(null === $lamination1_individual_thickness) {
    if(isset($row['lamination1_individual_thickness'])) $lamination1_individual_thickness = $row['lamination1_individual_thickness'];
    else $lamination1_individual_thickness = null;
}

$lamination1_individual_density = filter_input(INPUT_POST, 'lamination1_individual_density');
if(null === $lamination1_individual_density) {
    if(isset($row['lamination1_individual_density'])) $lamination1_individual_density = $row['lamination1_individual_density'];
    else $lamination1_individual_density = null;
}

if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $lamination1_customers_material = filter_input(INPUT_POST, 'lamination1_customers_material') == 'on' ? 1 : 0;
}
else {
    if(isset($row['lamination1_customers_material'])) $lamination1_customers_material = $row['lamination1_customers_material'];
    else $lamination1_customers_material = null;
}

$lamination2_brand_name = filter_input(INPUT_POST, 'lamination2_brand_name');
if(null === $lamination2_brand_name) {
    if(isset($row['lamination2_brand_name'])) $lamination2_brand_name = $row['lamination2_brand_name'];
    else $lamination2_brand_name = null;
}

$lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
if(null === $lamination2_thickness) {
    if(isset($row['lamination2_thickness'])) $lamination2_thickness = $row['lamination2_thickness'];
    else $lamination2_thickness = null;
}

$lamination2_individual_brand_name = filter_input(INPUT_POST, 'lamination2_individual_brand_name');
if(null === $lamination2_individual_brand_name) {
    if(isset($row['lamination2_individual_brand_name'])) $lamination2_individual_brand_name = $row['lamination2_individual_brand_name'];
    else $lamination2_individual_brand_name = null;
}

$lamination2_individual_price = filter_input(INPUT_POST, 'lamination2_individual_price');
if(null === $lamination2_individual_price) {
    if(isset($row['lamination2_individual_price'])) $lamination2_individual_price = $row['lamination2_individual_price'];
    else $lamination2_individual_price = null;
}

$lamination2_individual_thickness = filter_input(INPUT_POST, 'lamination2_individual_thickness');
if(null === $lamination2_individual_thickness) {
    if(isset($row['lamination2_individual_thickness'])) $lamination2_individual_thickness = $row['lamination2_individual_thickness'];
    else $lamination2_individual_thickness = null;
}

$lamination2_individual_density = filter_input(INPUT_POST, 'lamination2_individual_density');
if(null === $lamination2_individual_density) {
    if(isset($row['lamination2_individual_density'])) $lamination2_individual_density = $row['lamination2_individual_density'];
    else $lamination2_individual_density = null;
}

if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $lamination2_customers_material = filter_input(INPUT_POST, 'lamination2_customers_material') == 'on' ? 1 : 0;
}
else {
    if(isset($row['lamination2_customers_material'])) $lamination2_customers_material = $row['lamination2_customers_material'];
    else $lamination2_customers_material = null;
}

$quantity = filter_input(INPUT_POST, 'quantity');
if(null === $quantity) {
    if(isset($row['quantity'])) $quantity = $row['quantity'];
    else $quantity = null;
}
else {
    $quantity = preg_replace("/\D/", "", $quantity);
}

$streams_count = filter_input(INPUT_POST, 'streams_count');
if(null === $streams_count) {
    if(isset($row['streams_count'])) $streams_count = $row['streams_count'];
    else $streams_count = null;
}

$length = filter_input(INPUT_POST, 'length');
if(null === $length) {
    if(isset($row['length'])) $length = $row['length'];
    else $length = null;
}

$stream_width = filter_input(INPUT_POST, 'stream_width');
if(null === $stream_width) {
    if(isset($row['stream_width'])) $stream_width = $row['stream_width'];
    else $stream_width = null;
}

$raport = filter_input(INPUT_POST, 'raport');
if(null === $raport) {
    if(isset($row['raport'])) $raport = $row['raport'];
    else $raport = null;
}

$number_on_raport = filter_input(INPUT_POST, 'number_on_raport');
if(null === $number_on_raport) {
    if(isset($row['number_on_raport'])) $number_on_raport = $row['number_on_raport'];
    else $number_on_raport = null;
}

$lamination_roller = filter_input(INPUT_POST, 'lamination_roller');
if(null === $lamination_roller) {
    if(isset($row['lamination_roller'])) $lamination_roller = $row['lamination_roller'];
    else $lamination_roller = null;
}

$paints_count = filter_input(INPUT_POST, 'paints_count');
if(null === $paints_count) {
    if(isset($row['paints_count'])) $paints_count = $row['paints_count'];
    else $paints_count = null;
}

$ski_width = filter_input(INPUT_POST, 'ski_width');
if(null == $ski_width) {
    if(isset($row['ski_width'])) $ski_width = $row['ski_width'];
    else $ski_width = null;
}

if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $no_ski = filter_input(INPUT_POST, 'no_ski') == 'on' ? 1 : 0;
}
else {
    if(isset($row['no_ski'])) $no_ski = $row['no_ski'];
    else $no_ski = null;
}

if(isset($row['techmap_id'])) $techmap_id = $row['techmap_id'];
else $techmap_id = null;

if(isset($row['extracharge'])) $extracharge = $row['extracharge'];
else $extracharge = null;

// Данные о цветах
for ($i=1; $i<=8; $i++) {
    $paint_var = "paint_$i";
    $$paint_var = filter_input(INPUT_POST, "paint_$i");
    if(null === $$paint_var) {
        if(isset($row["paint_$i"])) $$paint_var = $row["paint_$i"];
        else $$paint_var = null;
    }
    
    $color_var = "color_$i";
    $$color_var = filter_input(INPUT_POST, "color_$i");
    if(null === $$color_var) {
        if(isset($row["color_$i"])) $$color_var = $row["color_$i"];
        else $$color_var = null;
    }
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
    if(null === $$cmyk_var) {
        if(isset($row["cmyk_$i"])) $$cmyk_var = $row["cmyk_$i"];
        else $$cmyk_var = null;
    }
    
    $percent_var = "percent_$i";
    $$percent_var = filter_input(INPUT_POST, "percent_$i");
    if(null === $$percent_var) {
        if(isset($row["percent_$i"])) $$percent_var = $row["percent_$i"];
        else $$percent_var = null;
    }
    
    $cliche_var = "cliche_$i";
    $$cliche_var = filter_input(INPUT_POST, "cliche_$i");
    if(null === $$cliche_var) {
        if(isset($row["cliche_$i"])) $$cliche_var = $row["cliche_$i"];
        else $$cliche_var = null;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/select2.min.css" rel="stylesheet"/>
        <style>
            .form-group {
                margin-bottom: .2rem;
            }
            
            p {
                margin-bottom: 0;
                margin-top: .3rem;
            }
            
            label {
                margin-bottom: .2rem;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <?php
            if(null !== filter_input(INPUT_GET, 'id')):
            $id = filter_input(INPUT_GET, 'id');
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/request_calc.php?id=<?=$id ?>">Назад</a>
            <?php else: ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/<?= IsInRole('manager') ? BuildQueryAddRemove('manager', GetUserId(), 'id') : BuildQueryRemove('id') ?>">К списку</a>
            <?php endif; ?>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-5" id="left_side">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="extracharge" name="extracharge" class="extracharge" value="<?=$extracharge ?>" />
                        <input type="hidden" id="scroll" name="scroll" />
                        <?php if(null === filter_input(INPUT_GET, 'id') || filter_input(INPUT_GET, 'mode') == 'recalc'): ?>
                        <h1>Новый расчет</h1>
                        <?php else: ?>
                        <h1><?= htmlentities($name) ?></h1>
                        <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                        <?php endif; ?>
                        <!-- Заказчик -->
                        <div class="row">
                            <div class="col-8 form-group">
                                <label for="customer_id">Заказчик</label>
                                <select id="customer_id" name="customer_id" class="form-control<?=$customer_id_valid ?>" multiple="multiple" required="required">
                                    <option value="">Заказчик...</option>
                                        <?php
                                        $manager_id = GetUserId();
                                        $sql = "select id, name from customer where manager_id = $manager_id order by name";
                                        $fetcher = new Fetcher($sql);
                                        
                                        while ($row = $fetcher->Fetch()):
                                        $selected = '';
                                        if($row['id'] == $customer_id) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                    <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                        <?php
                                        endwhile;
                                        ?>
                                </select>
                                <div class="invalid-feedback">Заказчик обязательно</div>
                            </div>
                            <div class="col-4 form-group d-flex flex-column justify-content-end">
                                <button type="button" class="btn btn-outline-dark w-100" data-toggle="modal" data-target="#new_customer"><i class="fas fa-plus"></i>&nbsp;Создать нового</button>
                            </div>
                        </div>
                        <!-- Название заказа -->
                        <div class="form-group">
                            <label for="name">Название заказа</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control<?=$name_valid ?>" 
                                   placeholder="Название заказа" 
                                   value="<?= htmlentities($name) ?>" 
                                   required="required" 
                                   autocomplete="off" 
                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('id', 'name'); $(this).attr('name', 'name'); $(this).attr('placeholder', 'Название заказа');" 
                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('id', 'name'); $(this).attr('name', 'name'); $(this).attr('placeholder', 'Название заказа');" 
                                   onfocusout="javascript: $(this).attr('id', 'name'); $(this).attr('name', 'name'); $(this).attr('placeholder', 'Название заказа');" />
                            <div class="invalid-feedback">Название заказа обязательно</div>
                        </div>
                        <!-- Тип работы -->
                        <div class="form-group">
                            <label for="work_type_id">Тип работы</label>
                            <select id="work_type_id" name="work_type_id" class="form-control" required="required">
                                <option value="" hidden="hidden" selected="selected">Тип работы...</option>
                                <?php
                                $sql = "select id, name from work_type";
                                $fetcher = new Fetcher($sql);
                                
                                while ($row = $fetcher->Fetch()):
                                $selected = '';
                                if($row['id'] == $work_type_id) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                <?php
                                endwhile;
                                ?>
                            </select>
                        </div>
                        <!-- Единица заказа -->
                        <?php
                        $kg_checked = ($unit == "kg" || empty($unit)) ? " checked='checked'" : "";
                        $pieces_checked = $unit == "pieces" ? " checked='checked'" : "";
                        ?>
                        <div class="justify-content-start mt-2 mb-1">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="unit" id="unit_kg" value="kg"<?=$kg_checked ?> /><span id="unit_kg_label">Килограммы</span>
                                </label>
                            </div>
                            <?php
                            $unit_pieces_display_none = "";
                            if($work_type_id == 1) {
                                $unit_pieces_display_none = " d-none";
                            }
                            ?>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input<?=$unit_pieces_display_none ?>" name="unit" id="unit_pieces" value="pieces"<?=$pieces_checked ?> /><span id="unit_pieces_label" class="placeh<?=$unit_pieces_display_none ?>">Штуки</span>
                                </label>
                            </div>
                        </div>
                        <!-- Объем заказа -->
                        <div class="row">
                            <!-- Объем заказа -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="quantity" id="label_quantity">Объем заказа, кг</label>
                                    <input type="text" 
                                           id="quantity" 
                                           name="quantity" 
                                           class="form-control int-only int-format" 
                                           placeholder="Объем заказа" 
                                           value="<?= empty($quantity) ? "" : number_format($quantity, 0, ",", " ") ?>" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="avascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" 
                                           onfocusout="avascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" />
                                    <div class="invalid-feedback">Объем заказа обязательно</div>
                                </div>
                            </div>
                        </div>
                        <!-- Печатная машина -->
                        <div class="print-only d-none">
                            <div class="form-group w-100">
                                <label for="machine">Печатная машина</label>
                                <select id="machine" name="machine" class="form-control print-only d-none">
                                    <option value="" hidden="hidden" selected="selected">Печатная машина...</option>
                                    <?php
                                    $zbs_selected = "";
                                    if($machine == ZBS) {
                                        $zbs_selected = " selected='selected'";
                                    }
                                    ?>
                                    <option value="<?=ZBS ?>"<?=$zbs_selected ?>>ZBS</option>
                                    <?php
                                    $comiflex_selected = "";
                                    if($machine == COMIFLEX) {
                                        $comiflex_selected = " selected='selected'";
                                    }
                                    ?>
                                    <option value="<?=COMIFLEX ?>"<?=$comiflex_selected ?>>Comiflex</option>
                                </select>
                            </div>
                        </div>
                        <!-- Основная плёнка -->
                        <div id="film_title">
                            <p><span class="font-weight-bold">Пленка</span></p>
                        </div>
                        <div id="main_film_title" class="d-none">
                            <p><span class="font-weight-bold">Основная пленка</span></p>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="brand_name">Марка пленки</label>
                                    <select id="brand_name" name="brand_name" class="form-control" required="required">
                                        <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                            <?php
                                            $sql = "select distinct name from film_brand order by name";
                                            $brand_names = (new Grabber($sql))->result;
                                            
                                            foreach ($brand_names as $row):
                                            $selected = '';
                                            if($row['name'] == $brand_name) {
                                                $selected = " selected='selected'";
                                            }
                                            ?>
                                        <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                            <?php
                                            endforeach;
                                            
                                            $individual_selected = '';
                                            if(!empty($individual_brand_name)) {
                                                $individual_selected = " selected='selected'";
                                            }
                                            ?>
                                        <option value="<?=INDIVIDUAL ?>"<?=$individual_selected ?>>Другая</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="thickness">Толщина, мкм</label>
                                    <select id="thickness" name="thickness" class="form-control" required="required">
                                        <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                        <?php
                                        if(!empty($brand_name)) {
                                            $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' order by thickness";
                                            $thicknesses = (new Grabber($sql))->result;
                                            
                                            foreach ($thicknesses as $row):
                                            $selected = '';
                                            if($row['thickness'] == $thickness) {
                                                $selected = " selected='selected'";
                                            }
                                        ?>
                                        <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                        <?php
                                            endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row individual_only">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="individual_brand_name">Название пленки</label>
                                    <input type="text" 
                                           id="individual_brand_name" 
                                           name="individual_brand_name" 
                                           class="form-control" 
                                           placeholder="Название пленки" 
                                           value="<?=$individual_brand_name ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_brand_name'); $(this).attr('name', 'individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_brand_name'); $(this).attr('name', 'individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_brand_name'); $(this).attr('name', 'individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                    <div class="invalid-feedback">Название пленки обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="individual_price">Цена за 1 кг, руб</label>
                                    <input type="text" 
                                           id="individual_price" 
                                           name="individual_price" 
                                           class="form-control float-only" 
                                           placeholder="Цена" 
                                           value="<?= empty($individual_price) ? '' : floatval($individual_price) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_price'); $(this).attr('name', 'individual_price'); $(this).attr('placeholder', 'Цена')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_price'); $(this).attr('name', 'individual_price'); $(this).attr('placeholder', 'Цена')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_price'); $(this).attr('name', 'individual_price'); $(this).attr('placeholder', 'Цена')" />
                                    <div class="invalid-feedback">Цена обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="individual_thickness">Толщина, мкм</label>
                                    <input type="text" 
                                           id="individual_thickness" 
                                           name="individual_thickness" 
                                           class="form-control int-only" 
                                           placeholder="Толщина" 
                                           value="<?= $individual_thickness ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_thickness'); $(this).attr('name', 'individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_thickness'); $(this).attr('name', 'individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_thickness'); $(this).attr('name', 'individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                    <div class="invalid-feedback">Толщина обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="individual_density">Удельный вес, г/м<sup>2</sup></label>
                                    <input type="text" 
                                           id="individual_density" 
                                           name="individual_density" 
                                           class="form-control float-only" 
                                           placeholder="Удельный вес" 
                                           value="<?= empty($individual_density) ? '' : floatval($individual_density) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" />
                                    <div class="invalid-feedback">Удельный вес обязательно</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div id="show_lamination_1">
                                    <button type="button" class="btn btn-light" onclick="javascript: ShowLamination1();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                        <?php
                                        $checked = $customers_material == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="customers_material" name="customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Ламинация 1 -->
                        <div id="form_lamination_1" class="d-none">
                            <p><span class="font-weight-bold">Ламинация 1</span></p>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_brand_name">Марка пленки</label>
                                        <select id="lamination1_brand_name" name="lamination1_brand_name" class="form-control">
                                            <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                                <?php
                                                foreach ($brand_names as $row):
                                                $selected = '';
                                                if($row['name'] == $lamination1_brand_name) {
                                                    $selected = " selected='selected'";
                                                }
                                                ?>
                                            <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                <?php
                                                endforeach;
                                                
                                                $lamination1_individual_selected = '';
                                                if(!empty($lamination1_individual_brand_name)) {
                                                    $lamination1_individual_selected = " selected='selected'";
                                                }
                                                ?>
                                            <option value="<?=INDIVIDUAL ?>"<?=$lamination1_individual_selected ?>>Другая</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="lamination1_thickness">Толщина, мкм</label>
                                        <select id="lamination1_thickness" name="lamination1_thickness" class="form-control">
                                            <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                            <?php
                                            if(!empty($lamination1_brand_name)) {
                                                $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination1_brand_name' order by thickness";
                                                $thicknesses = (new Grabber($sql))->result;
                                                
                                                foreach ($thicknesses as $row):
                                                $selected = '';
                                                if($row['thickness'] == $lamination1_thickness) {
                                                    $selected = " selected='selected'";
                                                }
                                            ?>
                                            <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                            <?php
                                                endforeach;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                $hide_lamination1_class = "d-flex";
                                if(!empty($lamination2_brand_name)) {
                                    $hide_lamination1_class = "d-none";
                                }
                                ?>
                                <div class="col-1 <?=$hide_lamination1_class ?> flex-column justify-content-end" id="hide_lamination_1">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-light" onclick="javascript: HideLamination1();"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row lamination1_individual_only">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_individual_brand_name">Название пленки</label>
                                        <input type="text" 
                                               id="lamination1_individual_brand_name" 
                                               name="lamination1_individual_brand_name" 
                                               class="form-control" 
                                               placeholder="Название пленки" 
                                               value="<?=$lamination1_individual_brand_name ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_individual_brand_name'); $(this).attr('name', 'lamination1_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_individual_brand_name'); $(this).attr('name', 'lamination1_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_individual_brand_name'); $(this).attr('name', 'lamination1_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                        <div class="invalid-feedback">Название пленки обязательно</div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="lamination1_individual_price">Цена за 1 кг, руб</label>
                                        <input type="text" 
                                               id="lamination1_individual_price" 
                                               name="lamination1_individual_price" 
                                               class="form-control float-only" 
                                               placeholder="Цена" 
                                               value="<?= empty($lamination1_individual_price) ? '' : floatval($lamination1_individual_price) ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_individual_price'); $(this).attr('name', 'lamination1_individual_price'); $(this).attr('placeholder', 'Цена')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_individual_price'); $(this).attr('name', 'lamination1_individual_price'); $(this).attr('placeholder', 'Цена')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_individual_price'); $(this).attr('name', 'lamination1_individual_price'); $(this).attr('placeholder', 'Цена')" />
                                        <div class="invalid-feedback">Цена обязательно</div>
                                    </div>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_individual_thickness">Толщина, мкм</label>
                                        <input type="text" 
                                               id="lamination1_individual_thickness" 
                                               name="lamination1_individual_thickness" 
                                               class="form-control int-only" 
                                               placeholder="Толщина" 
                                               value="<?= $lamination1_individual_thickness ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_individual_thickness'); $(this).attr('name', 'lamination1_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_individual_thickness'); $(this).attr('name', 'lamination1_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_individual_thickness'); $(this).attr('name', 'lamination1_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                        <div class="invalid-feedback">Толщина обязательно</div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="lamination1_individual_density">Удельный вес, г/м<sup>2</sup></label>
                                        <input type="text" 
                                               id="lamination1_individual_density" 
                                               name="lamination1_individual_density" 
                                               class="form-control float-only" 
                                               placeholder="Удельный вес" 
                                               value="<?= empty($lamination1_individual_density) ? '' : floatval($lamination1_individual_density) ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_individual_density'); $(this).attr('name', 'lamination1_individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_individual_density'); $(this).attr('name', 'lamination1_individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_individual_density'); $(this).attr('name', 'lamination1_individual_density'); $(this).attr('placeholder', 'Удельный вес')" />
                                        <div class="invalid-feedback">Удельный вес обязательно</div>
                                    </div>
                                </div>
                                <div class="col-1"></div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div id="show_lamination_2">
                                        <button type="button" class="btn btn-light" onclick="javascript: ShowLamination2();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                            <?php
                                            $checked = $lamination1_customers_material == 1 ? " checked='checked'" : "";
                                            ?>
                                            <input type="checkbox" class="form-check-input" id="lamination1_customers_material" name="lamination1_customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Ламинация 2 -->
                            <div id="form_lamination_2" class="d-none">
                                <p><span class="font-weight-bold">Ламинация 2</span></p>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_brand_name">Марка пленки</label>
                                            <select id="lamination2_brand_name" name="lamination2_brand_name" class="form-control">
                                                <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                                    <?php
                                                    foreach ($brand_names as $row):
                                                    $selected = '';
                                                    if($row['name'] == $lamination2_brand_name) {
                                                        $selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=$row['name'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                    <?php
                                                    endforeach;
                                                    
                                                    $lamination2_individual_selected = '';
                                                    if(!empty($lamination2_individual_brand_name)) {
                                                        $lamination2_individual_selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=INDIVIDUAL ?>"<?=$lamination2_individual_selected ?>>Другая</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="lamination2_thickness">Толщина, мкм</label>
                                            <select id="lamination2_thickness" name="lamination2_thickness" class="form-control">
                                                <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                                <?php
                                                if(!empty($lamination2_brand_name)) {
                                                    $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$lamination2_brand_name' order by thickness";
                                                    $thicknesses = (new Grabber($sql))->result;
                                                    
                                                    foreach ($thicknesses as $row):
                                                    $selected = "";
                                                    if($row['thickness'] == $lamination2_thickness) {
                                                        $selected = " selected='selected'";
                                                    }
                                                ?>
                                                <option value="<?=$row['thickness'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                                <?php
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-1 d-flex flex-column justify-content-end" id="hide_lamination_2">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-light" onclick="javascript: HideLamination2();"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row lamination2_individual_only">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_individual_brand_name">Название пленки</label>
                                            <input type="text" 
                                                   id="lamination2_individual_brand_name" 
                                                   name="lamination2_individual_brand_name" 
                                                   class="form-control" 
                                                   placeholder="Название пленки" 
                                                   value="<?=$lamination2_individual_brand_name ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_individual_brand_name'); $(this).attr('name', 'lamination2_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_individual_brand_name'); $(this).attr('name', 'lamination2_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_individual_brand_name'); $(this).attr('name', 'lamination2_individual_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                            <div class="invalid-feedback">Название пленки обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="lamination2_individual_price">Цена за 1 кг, руб</label>
                                            <input type="text" 
                                                   id="lamination2_individual_price" 
                                                   name="lamination2_individual_price" 
                                                   class="form-control float-only" 
                                                   placeholder="Цена" 
                                                   value="<?= empty($lamination2_individual_price) ? '' : floatval($lamination2_individual_price) ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_individual_price'); $(this).attr('name', 'lamination2_individual_price'); $(this).attr('placeholder', 'Цена')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_individual_price'); $(this).attr('name', 'lamination2_individual_price'); $(this).attr('placeholder', 'Цена')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_individual_price'); $(this).attr('name', 'lamination2_individual_price'); $(this).attr('placeholder', 'Цена')" />
                                            <div class="invalid-feedback">Цена обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_individual_thickness">Толщина, мкм</label>
                                            <input type="text" 
                                                   id="lamination2_individual_thickness" 
                                                   name="lamination2_individual_thickness" 
                                                   class="form-control int-only" 
                                                   placeholder="Толщина" 
                                                   value="<?= $lamination2_individual_thickness ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_individual_thickness'); $(this).attr('name', 'lamination2_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_individual_thickness'); $(this).attr('name', 'lamination2_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_individual_thickness'); $(this).attr('name', 'lamination2_individual_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                            <div class="invalid-feedback">Толщина обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="lamination2_individual_density">Удельный вес, г/м<sup>2</sup></label>
                                            <input type="text" 
                                                   id="lamination2_individual_density" 
                                                   name="lamination2_individual_density" 
                                                   class="form-control float-only" 
                                                   placeholder="Удельный вес" 
                                                   value="<?= empty($lamination2_individual_density) ? '' : floatval($lamination2_individual_density) ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_individual_density'); $(this).attr('name', 'lamination2_individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_individual_density'); $(this).attr('name', 'lamination2_individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_individual_density'); $(this).attr('name', 'lamination2_individual_density'); $(this).attr('placeholder', 'Удельный вес')" />
                                            <div class="invalid-feedback">Удельный вес обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6"></div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                                <?php
                                                $checked = $lamination2_customers_material == 1 ? " checked='checked'" : "";
                                                ?>
                                                <input type="checkbox" class="form-check-input" id="lamination2_customers_material" name="lamination2_customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Ширина ручья -->
                            <div class="col-6 lam-only print-only d-none">
                                <div class="form-group">
                                    <label for="stream_width">Ширина ручья, мм</label>
                                    <input type="text" 
                                           id="stream_width" 
                                           name="stream_width" 
                                           class="form-control int-only lam-only print-only d-none<?=$stream_width_valid ?>" 
                                           placeholder="Ширина ручья, мм" 
                                           value="<?= empty($stream_width) ? "" : floatval($stream_width) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" />
                                    <div class="invalid-feedback"><?=$stream_width_valid_message ?></div>
                                </div>
                            </div>
                            <!-- Количество ручьёв -->
                            <div class="col-6 lam-only print-only d-none">
                                <div class="form-group">
                                    <label for="streams_count">Количество ручьев</label>
                                    <input type="text" 
                                           id="streams_count" 
                                           name="streams_count" 
                                           class="form-control int-only lam-only print-only d-none<?=$streams_count_valid ?>" 
                                           placeholder="Количество ручьев" 
                                           value="<?=$streams_count ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onfocusout="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" />
                                    <div class="invalid-feedback"><?=$streams_count_valid_message ?></div>
                                </div>
                            </div>
                            <!-- Количество этикеток на ручье -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="number_on_raport">Количество этикеток на ручье</label>
                                    <input type="text" 
                                           id="number_on_raport" 
                                           name="number_on_raport" 
                                           class="form-control int-only print-only d-none<?=$number_on_raport_valid ?>" 
                                           placeholder="Количество этикеток на ручье" 
                                           value="<?= $number_on_raport === null ? "" : intval($number_on_raport) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'number_on_raport'); $(this).attr('name', 'number_on_raport'); $(this).attr('placeholder', 'Количество этикеток на ручье');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'number_on_raport'); $(this).attr('name', 'number_on_raport'); $(this).attr('placeholder', 'Количество этикеток на ручье');" 
                                           onfocusout="javascript: $(this).attr('id', 'number_on_raport'); $(this).attr('name', 'number_on_raport'); $(this).attr('placeholder', 'Количество этикеток на ручье');" />
                                    <div class="invalid-feedback"><?=$number_on_raport_message ?></div>
                                </div>
                            </div>
                            <!-- Рапорт -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="raport">Рапорт, мм</label>
                                    <select id="raport" name="raport" class="form-control print-only d-none<?=$raport_valid ?>">
                                        <option value="" hidden="hidden" selected="selected">Рапорт...</option>
                                        <?php
                                        if(!empty($machine)) {
                                            $sql = "";
                                            
                                            if($machine == "comiflex") {
                                                $sql = "select r.value "
                                                        . "from raport r "
                                                        . "inner join machine m on r.machine_id = m.id "
                                                        . "where m.shortname = 'comiflex' "
                                                        . "order by r.value";
                                            }
                                            elseif($machine == "zbs") {
                                                $sql = "select distinct r.value "
                                                        . "from raport r "
                                                        . "inner join machine m on r.machine_id = m.id "
                                                        . "where m.shortname in ('zbs1', 'zbs2', 'zbs3') "
                                                        . "order by r.value";
                                            }
                                            
                                            $fetcher = new Fetcher($sql);
                                            
                                            while($row = $fetcher->Fetch()) {
                                                $raport_value = floatval($row['value']);
                                                $display_value = $raport_value;
                                                $selected = "";
                                                if($raport_value == $raport) $selected = " selected='selected'";
                                                echo "<option value='$raport_value'$selected>$display_value</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback"><?=$raport_message ?></div>
                                </div>
                            </div>
                            <!-- Длина этикетки вдоль рапорта вала -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="length">Длина этикетки вдоль рапорта вала, мм</label>
                                    <input type="text" 
                                           id="length" 
                                           name="length" 
                                           class="form-control float-only print-only d-none<?=$length_valid ?>" 
                                           placeholder="Длина этикетки вдоль рапорта вала, мм" 
                                           value="<?= $length === null ? "" : floatval($length) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина этикетки вдоль рапорта вала, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина этикетки вдоль рапорта вала, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина этикетки вдоль рапорта вала, мм');" />
                                    <div class="invalid-feedback"><?=$length_message ?></div>
                                </div>
                            </div>
                            <!-- Ширина вала ламинации -->
                            <div class="col-6 lam-only d-none">
                                <div class="form-group">
                                    <label for="lamination_roller">Ширина вала ламинации, мм</label>
                                    <select id="lamination_roller" name="lamination_roller" class="form-control lam-only d-none">
                                        <option value="" hidden="hidden" selected="selected">Ширина вала ламинации...</option>
                                            <?php
                                            $sql = "select value from norm_laminator_roller order by value"; echo $sql;
                                            $rollers = (new Grabber($sql))->result;
                                                
                                            foreach ($rollers as $row):
                                            $selected = '';
                                            if($row['value'] == $lamination_roller) {
                                                $selected = " selected='selected'";
                                            }
                                            ?>
                                        <option value="<?=$row['value'] ?>"<?=$selected ?>><?=$row['value'] ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Ширина лыж -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="raport">Ширина лыж, мм</label>
                                    <?php
                                    $disabled = $no_ski == 1 ? " disabled='disabled'" : "";
                                    ?>
                                    <input<?=$disabled ?> type="text" 
                                           id="ski_width" 
                                           name="ski_width" 
                                           class="form-control int-only print-only d-none" 
                                           placeholder="Ширина лыж, мм" 
                                           value="<?= empty($ski_width) ? 20 : intval($ski_width) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'ski_width'); $(this).attr('name', 'ski_width'); $(this).attr('placeholder', 'Ширина лыж, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'ski_width'); $(this).attr('name', 'ski_width'); $(this).attr('placeholder', 'Ширина лыж, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'ski_width'); $(this).attr('name', 'ski_width'); $(this).attr('placeholder', 'Ширина лыж, мм');" />
                                    <div class="invalid-feedback">Ширина лыж обязательно</div>
                                </div>
                            </div>
                            <!-- Печать без лыж -->
                            <div class="col-6 print-only d-none">
                                <div class="form-check pt-4">
                                    <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                        <?php
                                        $checked = $no_ski == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="no_ski" name="no_ski" value="on"<?=$checked ?>>Печать без лыж
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Количество красок -->
                        <div class="print-only d-none">
                            <div class="form-group">
                                <label for="paints_count">Количество красок</label>
                                <select id="paints_count" name="paints_count" class="form-control print-only d-none">
                                    <option value="" hidden="hidden">Количество красок...</option>
                                        <?php                                        
                                        for($i = 1; $i <= 8; $i++):
                                        $selected = "";
                                        if($paints_count == $i) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                    <option<?=$selected ?>><?=$i ?></option>
                                        <?php endfor; ?>
                                </select>
                            </div>
                            <!-- Каждая краска -->
                            <?php
                            for($i=1; $i<=8; $i++):
                            $block_class = " d-none";
                            $paint_required = "";

                            if(!empty($paints_count) && is_numeric($paints_count) && $i <= $paints_count) {
                                $block_class = "";
                                $paint_required = " required='required'";
                            }
                            ?>
                            <div class="row paint_block<?=$block_class ?>" id="paint_block_<?=$i ?>">
                                <?php
                                $paint_class = " col-12";
                                $cmyk_class = " d-none";
                                $color_class = " d-none";
                                $percent_class = " d-none";
                                $form_class = " d-none";
                            
                                $paint_var_name = "paint_$i";
                            
                                if($$paint_var_name == "white" || $$paint_var_name == "lacquer") {
                                    $paint_class = " col-6";
                                    $percent_class = " col-3";
                                    $form_class = " col-3";
                                }
                                else if($$paint_var_name == "panton") {
                                    $paint_class = " col-3";
                                    $color_class = " col-3";
                                    $percent_class = " col-3";
                                    $form_class = " col-3";
                                }
                                else if($$paint_var_name == "cmyk") {
                                    $paint_class = " col-3";
                                    $cmyk_class = " col-3";
                                    $percent_class = " col-3";
                                    $form_class = " col-3";
                                }
                                ?>
                                <div class="form-group<?=$paint_class ?>" id="paint_group_<?=$i ?>">
                                    <label for="paint_<?=$i ?>"><?=$i ?> цвет</label>
                                    <select id="paint_<?=$i ?>" name="paint_<?=$i ?>" class="form-control paint" data-id="<?=$i ?>"<?=$paint_required ?>>
                                        <option value="" hidden="hidden" selected="selected">Цвет...</option>
                                        <?php
                                        $cmyk_selected = "";
                                        $panton_selected = "";
                                        $white_selected = "";
                                        $lacquer_selected = "";
                                    
                                        $selected_var_name = $$paint_var_name."_selected";
                                        $$selected_var_name = " selected='selected'";
                                        ?>
                                        <option value="cmyk"<?=$cmyk_selected ?>>CMYK</option>
                                        <option value="panton"<?=$panton_selected ?>>Пантон</option>
                                        <option value="white"<?=$white_selected ?>>Белый</option>
                                        <option value="lacquer"<?=$lacquer_selected ?>>Лак</option>
                                    </select>
                                    <div class="invalid-feedback">Цвет обязательно</div>
                                </div>
                                <div class="form-group<?=$color_class ?>" id="color_group_<?=$i ?>">
                                    <?php
                                    $color_var = "color_$i"; 
                                    $color_var_valid = 'color_'.$i.'_valid'; 
                                    ?>
                                    <label for="color_<?=$i ?>">Номер пантона</label>
                                    <div class="input-group flex-nowrap">
                                        <div class="input-group-prepend"><span class="input-group-text">P</span></div>
                                        <input type="text" 
                                               id="color_<?=$i ?>" 
                                               name="color_<?=$i ?>" 
                                               class="form-control panton color<?=$$color_var_valid ?>" 
                                               placeholder="Номер пантона..." 
                                               value="<?= empty($$color_var) ? "" : $$color_var?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" 
                                               onfocusout="javascript: $(this).attr('id', 'color_<?=$i ?>'); $(this).attr('name', 'color_<?=$i ?>'); $(this).attr('placeholder', 'Номер пантона...');" />
                                    </div>
                                    <div class="invalid-feedback">Код цвета обязательно</div>
                                </div>
                                <div class="form-group<?=$cmyk_class ?>" id="cmyk_group_<?=$i ?>">
                                    <?php
                                    $cmyk_var = "cmyk_$i";
                                    $cmyk_var_valid = 'cmyk_'.$i.'_valid';
                                    ?>
                                    <label for="cmyk_<?=$i ?>">CMYK</label>
                                    <select id="cmyk_<?=$i ?>" name="cmyk_<?=$i ?>" class="form-control cmyk<?=$$cmyk_var_valid ?>" data-id="<?=$i ?>">
                                        <option value="" hidden="hidden" selected="selected">CMYK...</option>
                                        <?php
                                        $cyan_selected = "";
                                        $magenta_selected = "";
                                        $yellow_selected = "";
                                        $kontur_selected = "";
                                    
                                        $cmyk_var_selected = $$cmyk_var.'_selected';
                                        $$cmyk_var_selected = " selected='selected'";
                                        ?>
                                        <option value="cyan"<?=$cyan_selected ?>>Cyan</option>
                                        <option value="magenta"<?=$magenta_selected ?>>Magenta</option>
                                        <option value="yellow"<?=$yellow_selected ?>>Yellow</option>
                                        <option value="kontur"<?=$kontur_selected ?>>Kontur</option>
                                    </select>
                                    <div class="invalid-feedback">Выберите компонент цвета</div>
                                </div>
                                <div class="form-group<?=$percent_class ?>" id="percent_group_<?=$i ?>">
                                    <?php
                                    $percent_var = "percent_$i";
                                    $percent_var_valid = 'percent_'.$i.'_valid';
                                    ?>
                                    <label for="percent_<?=$i ?>">Процент<br /></label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" 
                                            id="percent_<?=$i ?>" 
                                            name="percent_<?=$i ?>" 
                                            class="form-control int-only percent<?=$$percent_var_valid ?>" 
                                            style="width: 80px;" 
                                            value="<?= empty($$percent_var) ? "" : $$percent_var ?>" 
                                            placeholder="Процент..." 
                                            onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                            onmouseup="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" 
                                            onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                            onkeyup="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" 
                                            onfocusout="javascript: $(this).attr('id', 'percent_<?=$i ?>'); $(this).attr('name', 'percent_<?=$i ?>'); $(this).attr('placeholder', 'Процент...');" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="invalid-feedback">Процент обязательно</div>
                                    </div>
                                </div>
                                <div class="form-group<?=$cliche_class ?>" id="cliche_group_<?=$i ?>">
                                    <label for="cliche_<?=$i ?>">Форма</label>
                                    <select id="cliche_<?=$i ?>" name="form_<?=$i ?>" class="form-control form">
                                        <?php
                                        $old_selected = "";
                                        $flint_selected = "";
                                        $kodak_selected = "";
                                        $tver_selected = "";
                                    
                                        $cliche_var = "cliche_$i";
                                        $cliche_selected_var = $$cliche_var."_selected";
                                        $$cliche_selected_var = " selected='selected'";
                                        ?>
                                        <option value="old"<?=$old_selected ?>>Старая</option>
                                        <option value="flint"<?=$flint_selected ?>>Новая Флинт</option>
                                        <option value="kodak"<?=$kodak_selected ?>>Новая Кодак</option>
                                        <option value="tver"<?=$tver_selected ?>>Новая Тверь</option>
                                    </select>
                                </div>
                            </div>
                            <?php
                            endfor;
                            ?>
                        </div>
                        <button type="submit" id="create_request_calc_submit" name="create_request_calc_submit" class="btn btn-dark mt-3">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/select2.min.js"></script>
        <script src="<?=APPLICATION ?>/js/i18n/ru.js"></script>
        <script>
            // Список с  поиском
            $('#customer_id').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            // Всплывающая подсказка
            $(function() {
                $("i.fa-info-circle").tooltip({
                    position: {
                        my: "left center",
                        at: "right+10 center"
                    }
                });
            });
    
            // Маска телефона заказчика
            $.mask.definitions['~'] = "[+-]";
            $("#customer_phone").mask("+7 (999) 999-99-99");
    
            // При щелчке в поле телефона, устанавливаем курсор в самое начало ввода телефонного номера.
            $("#customer_phone").click(function(){
                var maskposition = $(this).val().indexOf("_");
                if(Number.isInteger(maskposition)) {
                    $(this).prop("selectionStart", maskposition);
                    $(this).prop("selectionEnd", maskposition);
                }
            });
            
            // Проверка, что заказчик существует
            $("#customer_name").keyup(function() {
                if($(this).val() == "") {
                    $('#customer_exists').addClass('d-none');
                }
                else {
                    $.ajax({ url: "../ajax/customer_exists.php?name=" + $(this).val() })
                            .done(function(data) {
                                if(data == 0) {
                                    $('#customer_exists').addClass('d-none');
                                }
                                else {
                                    $('#customer_exists').removeClass('d-none');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при проверке, что заказчик существует');
                            });
                }
            });
            
            // В поле "количество ручьёв" ограничиваем значения: целые числа от 1 до 50
            $('#streams_count').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 50)) {
                    return false;
                }
            });
    
            $("#streams_count").change(function(){
                ChangeLimitIntValue($(this), 50);
            });
            
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('.percent').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $(".percent").change(function(){
                ChangeLimitIntValue($(this), 100);
            });
            
            // При смене типа работы: если тип работы "плёнка с печатью", показываем поля, предназначенные только для плёнки с печатью
            $('#work_type_id').change(function() {
                // При типе "Плёнка без печати" количество возможно только в килограммах
                if($(this).val() == 1) {
                    $('#unit_kg').prop('checked', true);
                    $('#unit_pieces').addClass('d-none');
                    $('#unit_pieces_label').addClass('d-none');
                }
                else {
                    $('#unit_pieces').removeClass('d-none');
                    $('#unit_pieces_label').removeClass('d-none');
                }
                
                SetFieldsVisibility($(this).val());
            });
            
            // При щелчке на флажке "Печать без лыж" делаем поле "ширина лыж" доступным или нет
            $('#no_ski').change(function() {
                no_ski_checked = $(this).is(':checked');
                
                if(no_ski_checked) {
                    $('#ski_width').attr('disabled', 'disabled');
                }
                else {
                    $('#ski_width').removeAttr('disabled');
                }
            });
            
            // Показываем или скрываем поля в зависимости от работы с печатью / без печати и наличия / отсутствия ламинации
            function SetFieldsVisibility(work_type_id) {
                if (work_type_id == 2) {
                    // Скрываем поля "только без печати"
                    $('.no-print-only').addClass('d-none');
                    $('.no-print-only').removeAttr('required');
                    
                    // Показываем поля "только с печатью"
                    $('.print-only').removeClass('d-none');
                    $('input.print-only').attr('required', 'required');
                    $('select.print-only').attr('required', 'required');
                }
                else {
                    // Скрываем поля "только с печатью"
                    $('.print-only').addClass('d-none');
                    $('.print-only').removeAttr('required');
                    
                    // Показываем поля "только без печати"
                    $('.no-print-only').removeClass('d-none');
                    $('input.no-print-only').attr('required', 'required');
                    $('select.no-print-only').attr('required', 'required');
                }
                
                // Если видима ламинация, то показываем поля "только с ламинацией"
                // Иначе скрываем эти поля
                if($('#form_lamination_1').is(':visible')) {
                    $('.lam-only').removeClass('d-none');
                    $('input.lam-only').attr('required', 'required');
                    $('select.lam-only').attr('required', 'required');
                }
                else {
                    if (work_type_id == 2) {
                        $('.lam-only').not('.print-only').addClass('d-none');
                        $('.lam-only').not('.print-only').removeAttr('required');
                    }
                    else {
                        $('.lam-only').addClass('d-none');
                        $('.lam-only').removeAttr('required');
                    }
                }
            }
            
            SetFieldsVisibility($('#work_type_id').val());
            
            // Если единица объёма - кг, то в поле "Объём" пишем "Объём, кг", иначе "Объем, шт"
            if($('input[value=kg]').is(':checked')) {
                $('#label_quantity').text('Объем заказа, кг');
            }
            
            if($('input[value=pieces]').is(':checked')) {
                $('#label_quantity').text('Объем заказа, шт');
            }
                
            $('input[name=unit]').click(function(){
                if($(this).val() == 'kg') {
                    $('#label_quantity').text('Объем заказа, кг');
                }
                else {
                    $('#label_quantity').text('Объем заказа, шт');
                }
            });
            
            // Обработка выбора машины, заполнение списка рапортов
            $('#machine').change(function(){
                if($(this).val() == "") {
                    $('#raport').html("<option value=''>Рапорт...</option>")
                }
                else {
                    // Заполняем список рапортов
                    $.ajax({ url: "../ajax/raport.php?machine=" + $(this).val() })
                            .done(function(data) {
                                $('#raport').html(data);
                            })
                            .fail(function() {
                                alert('Ошиибка при выборе машины');
                            });
                }
            });
            
            // Установка видимости полей для ручного ввода при выборе марки плёнки "Другая"
            function SetBrandFieldsVisibility(value, isCustomers, prefix) {
                if(isCustomers) {
                    $('#' + prefix + 'individual_price').val('');
                    $('#' + prefix + 'individual_price').attr('disabled', 'disabled');
                }
                else {
                    $('#' + prefix + 'individual_price').removeAttr('disabled');
                }
                
                if(value == '<?=INDIVIDUAL ?>') {
                    $('#' + prefix + 'thickness').removeAttr('required');
                    $('#' + prefix + 'thickness').addClass('d-none');
                    $('#' + prefix + 'thickness').prev('label').addClass('d-none');
                    $('.' + prefix + 'individual_only').removeClass('d-none');
                    $('.' + prefix + 'individual_only input').attr('required', 'required');
                }
                else {
                    $('#' + prefix + 'thickness').attr('required', 'required');
                    $('#' + prefix + 'thickness').removeClass('d-none');
                    $('#' + prefix + 'thickness').prev('label').removeClass('d-none');
                    $('.' + prefix + 'individual_only').addClass('d-none');
                    $('.' + prefix + 'individual_only input').removeAttr('required');
                }
                
                if($('#' + prefix + 'individual_price').attr('disabled') == 'disabled') {
                    $('#' + prefix + 'individual_price').removeAttr('required');
                }
            }
            
            $('#customers_material').change(function(e) {
                SetBrandFieldsVisibility($('#brand_name').val(), $(e.target).is(':checked'), '');
            });
            
            $('#lamination1_customers_material').change(function(e) {
                SetBrandFieldsVisibility($('#lamination1_brand_name').val(), $(e.target).is(':checked'), 'lamination1_');
            });
            
            $('#lamination2_customers_material').change(function(e) {
                SetBrandFieldsVisibility($('#lamination2_brand_name').val(), $(e.target).is(':checked'), 'lamination2_');
            });
            
            SetBrandFieldsVisibility($('#brand_name').val(), $('#customers_material').is(':checked'), '');
            
            // Обработка выбора типа плёнки основной плёнки: перерисовка списка толщин и установка видимости полей
            $('#brand_name').change(function(){
                SetBrandFieldsVisibility($(this).val(), $('#customers_material').is(':checked'), '');
                
                if($(this).val() == "") {
                    $('#thickness').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#thickness').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Обработка выбора типа плёнки ламинации1: перерисовка списка толщин
            $('#lamination1_brand_name').change(function(){
                SetBrandFieldsVisibility($(this).val(), $('#lamination1_customers_material').is(':checked'), 'lamination1_');
                
                if($(this).val() == "") {
                    $('#lamination1_thickness').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination1_thickness').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Обработка выбора типа плёнки ламинации2: перерисовка списка толщин
            $('#lamination2_brand_name').change(function(){
                SetBrandFieldsVisibility($(this).val(), $('#lamination2_customers_material').is(':checked'), 'lamination2_');
                
                if($(this).val() == "") {
                    $('#lamination2_thickness').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination2_thickness').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Показ марки плёнки и толщины для ламинации 1
            function ShowLamination1() {
                $('#form_lamination_1').removeClass('d-none');
                $('#show_lamination_1').addClass('d-none');
                $('#main_film_title').removeClass('d-none');
                $('#film_title').addClass('d-none');
                $('#lamination1_brand_name').attr('required', 'required');
                $('#lamination1_thickness').attr('required', 'required');
                SetFieldsVisibility($('#work_type_id').val());
                SetBrandFieldsVisibility($('#lamination1_brand_name').val(), $('#lamination1_customers_material').is(':checked'), 'lamination1_');
            }
            
            <?php if(!empty($lamination1_brand_name)): ?>
                ShowLamination1();
            <?php endif; ?>
            
            // Скрытие марки плёнки и толщины для ламинации 1
            function HideLamination1() {
                $('#form_lamination_1 select').val('');
                $('#form_lamination_1 input').val('');
                $('#lamination1_brand_name').change();
                $('#lamination1_customers_material').prop("checked", false);
                
                $('#form_lamination_1').addClass('d-none');
                $('#show_lamination_1').removeClass('d-none');
                $('#main_film_title').addClass('d-none');
                $('#film_title').removeClass('d-none');
                
                $('#form_lamination_1 input').removeAttr('required');
                $('#form_lamination_1 select').removeAttr('required');
                $('#form_lamination_1 input').removeAttr('disabled');
                $('#form_lamination_1 select').removeAttr('disabled');
        
                SetFieldsVisibility($('#work_type_id').val());
                HideLamination2();
            }
            
            // Показ марки плёнки и толщины для ламинации 2
            function ShowLamination2() {
                $('#form_lamination_2').removeClass('d-none');
                $('#show_lamination_2').addClass('d-none');
                $('#hide_lamination_1').addClass('d-none');
                $('#hide_lamination_1').removeClass('d-flex');
                $('#lamination2_brand_name').attr('required', 'required');
                $('#lamination2_thickness').attr('required', 'required');
                SetBrandFieldsVisibility($('#lamination2_brand_name').val(), $('#lamination2_customers_material').is(':checked'), 'lamination2_');
            }
            
            <?php if(!empty($lamination2_brand_name)): ?>
                ShowLamination2();
            <?php endif; ?>
            
            // Скрытие марки плёнки и толщины для ламинации 2
            function HideLamination2() {
                $('#form_lamination_2 select').val('');
                $('#form_lamination_2 input').val('');
                $('#lamination2_brand_name').change();
                $('#lamination2_customers_material').prop("checked", false);
                
                $('#form_lamination_2').addClass('d-none');
                $('#show_lamination_2').removeClass('d-none');
                $('#hide_lamination_1').removeClass('d-none');
                $('#hide_lamination_1').addClass('d-flex');
                
                $('#form_lamination_2 input').removeAttr('required');
                $('#form_lamination_2 select').removeAttr('required');
                $('#form_lamination_2 input').removeAttr('disabled');
                $('#form_lamination_2 select').removeAttr('disabled');
            }
            
            // При изменении значения рапорта или количества этикеток на ручье
            // автоматически вычисляем длину этикетки вдоль рапорта вала
            $('#raport').change(function() {
                CalculateLength()
            });
            
            $('#number_on_raport').change(function() {
                CalculateLength()
            });
            
            $('#number_on_raport').keyup(function() {
                CalculateLength()
            });
            
            function CalculateLength() {
                var raport = $('#raport').val();
                var number_on_raport = $('#number_on_raport').val();
                
                if(raport && number_on_raport) {
                    $('#length').val(Math.round(parseFloat(raport) / parseFloat(number_on_raport) * 10000, -4) / 10000);
                }
            }
            
            // Обработка выбора количества красок
            $('#paints_count').change(function(){
                var count = $(this).val();
                $('.paint_block').addClass('d-none');
                $('.paint').removeAttr('required');
                
                if(count != '') {
                    iCount = parseInt(count);
                    
                    for(var i=1; i<=iCount; i++) {
                        $('#paint_block_' + i).removeClass('d-none');
                        $('#paint_' + i).attr('required', 'required');
                    }
                }
            });
            
            // Обработка выбора краски
            $('.paint').change(function(){
                paint = $(this).val();
                var data_id = $(this).attr('data-id');
                
                // Устанавливаем видимость всех элементов по умолчанию, как если бы выбрали пустое значение
                $('#paint_group_' + data_id).removeClass('col-12');
                $('#paint_group_' + data_id).removeClass('col-6');
                $('#paint_group_' + data_id).removeClass('col-3');
                
                $('#color_group_' + data_id).removeClass('col-3');
                $('#color_group_' + data_id).addClass('d-none');
                
                $('#cmyk_group_' + data_id).removeClass('col-3');
                $('#cmyk_group_' + data_id).addClass('d-none');
                
                $('#percent_group_' + data_id).removeClass('col-3');
                $('#percent_group_' + data_id).addClass('d-none');
                
                $('#cliche_group_' + data_id).removeClass('col-3');
                $('#cliche_group_' + data_id).addClass('d-none');
                
                // Снимаем атрибут required с кода цвета, CMYK и процента
                // Проценты вводит художник, поэтому их не делаем обязательными
                $('#color_' + data_id).removeAttr('required');
                $('#cmyk_' + data_id).removeAttr('required');
                
                // Затем, в зависимости от выбранного значения, устанавливаем видимость нужного элемента для этого значения
                if(paint == 'lacquer')  {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#cliche_group_' + data_id).addClass('col-3');
                    $('#cliche_group_' + data_id).removeClass('d-none');
                }
                else if(paint == 'white') {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#cliche_group_' + data_id).addClass('col-3');
                    $('#cliche_group_' + data_id).removeClass('d-none');
                }
                else if(paint == 'cmyk') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#cliche_group_' + data_id).addClass('col-3');
                    $('#cliche_group_' + data_id).removeClass('d-none');
                    
                    $('#cmyk_' + data_id).attr('required', 'required');
                }
                else if(paint == 'panton') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#cliche_group_' + data_id).addClass('col-3');
                    $('#cliche_group_' + data_id).removeClass('d-none');
                    
                    $('#color_' + data_id).attr('required', 'required');
                }
                else {
                    $('#paint_group_' + data_id).addClass('col-12');
                }
            });
            
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
            
            // Скрытие расчёта
            function HideCalculation() {
                $("#calculation").addClass("d-none");
                $("#create_request_calc_submit").removeClass("d-none");
            }
            
            // Ограницение значений наценки
            $('#extracharge').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge').change(function(){
                ChangeLimitIntValue($(this), 999);
                
                // Сохранение значения в базе
                EditExtracharge($(this));
            });
            
            // Ограничение значения поля "пантон"
            $('input.panton').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 99999)) {
                    return false;
                }
            });
            
            $('input.panton').change(function(){
                ChangeLimitIntValue($(this), 99999);
            });
            
            // Ограничение значения поля "Ширина ручья" до 4 цифр
            $('input#stream_width').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 9999)) {
                    return false;
                }
            });
            
            $('input#stream_width').change(function(){
                ChangeLimitIntValue($(this), 9999);
            });
            
            // Скрытие расчёта при изменении значения полей
            $("input[id!=extracharge]").change(function () {
                HideCalculation();
            });
            
            $('select').change(function () {
                HideCalculation();
            });
            
            $("input[id!=extracharge]").keydown(function () {
                HideCalculation();
            });
            
            // Скрытие расчёта, если имеется параметр mode=recalc
            <?php if(filter_input(INPUT_GET, 'mode') == 'recalc'): ?>
                HideCalculation();
            <?php endif; ?>
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            AdjustFixedBlock($('#calculation'));
            
            $(window).on("scroll", function(){
                AdjustFixedBlock($('#calculation'));
            });
        </script>
    </body>
</html>