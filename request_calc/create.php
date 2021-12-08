<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

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

$other_brand_name_valid = '';
$other_price_valid = '';
$other_thickness_valid = '';
$other_weight_valid = '';

// Переменные для валидации цвета, CMYK и процента
for($i=1; $i<=8; $i++) {
    $color_valid_var = 'color_'.$i.'_valid';
    $$color_valid_var = '';
    
    $cmyk_valid_var = 'cmyk_'.$i.'_valid';
    $$cmyk_valid_var = '';
    
    $percent_valid_var = 'percent_'.$i.'_valid';
    $$percent_valid_var = '';
}

// Значение марки плёнки "другая"
const OTHER = "other";

// Сохранение в базу расчёта
if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    if(empty(filter_input(INPUT_POST, "customer_id"))) {
        $customer_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, "name"))) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'work_type_id'))) {
        $work_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'brand_name'))) {
        $brand_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty(filter_input(INPUT_POST, 'quantity'))) {
        $quantity_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'brand_name') == OTHER) {
        // Проверка валидности параметров, введённых вручную при выборе марки плёнки "Другая"
        if(empty(filter_input(INPUT_POST, 'other_brand_name'))) {
            $other_brand_name_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(filter_input(INPUT_POST, 'customers_material') != 'on' && empty(filter_input(INPUT_POST, 'other_price'))) {
            $other_price_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty(filter_input(INPUT_POST, 'other_thickness'))) {
            $other_thickness_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty(filter_input(INPUT_POST, 'other_weight'))) {
            $other_weight_valid = ISINVALID;
            $form_valid = false;
        }
    }
    else {
        // Проверка валидности параметров стандартных плёнок
        if(empty(filter_input(INPUT_POST, 'thickness'))) {
            $thickness_valid = ISINVALID;
            $form_valid = false;
        }
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
            
            if(empty($$percent_var)) {
                $percent_valid_var = 'percent_'.$i.'_valid';
                $$percent_valid_var = ISINVALID;
                $form_valid = false;
            }
            
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
    
    if($form_valid) {
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $brand_name = addslashes(filter_input(INPUT_POST, 'brand_name'));
        $thickness = filter_input(INPUT_POST, 'thickness');
        if(empty($thickness)) $thickness = "NULL";
        $other_brand_name = filter_input(INPUT_POST, 'other_brand_name');
        $other_price = filter_input(INPUT_POST, 'other_price');
        if(empty($other_price)) $other_price = "NULL";
        $other_thickness = filter_input(INPUT_POST, 'other_thickness');
        if(empty($other_thickness)) $other_thickness = "NULL";
        $other_weight = filter_input(INPUT_POST, 'other_weight');
        if(empty($other_weight)) $other_weight = "NULL";
        $customers_material = 0;
        if(filter_input(INPUT_POST, 'customers_material') == 'on') {
            $customers_material = 1;
        }
        
        $unit = filter_input(INPUT_POST, 'unit');
        $machine_id = filter_input(INPUT_POST, 'machine_id');
        if(empty($machine_id)) $machine_id = "NULL";
        
        $lamination1_brand_name = addslashes(filter_input(INPUT_POST, 'lamination1_brand_name'));
        $lamination1_thickness = filter_input(INPUT_POST, 'lamination1_thickness');
        if(empty($lamination1_thickness)) $lamination1_thickness = "NULL";
        $lamination1_other_brand_name = filter_input(INPUT_POST, 'lamination1_other_brand_name');
        $lamination1_other_price = filter_input(INPUT_POST, 'lamination1_other_price');
        if(empty($lamination1_other_price)) $lamination1_other_price = "NULL";
        $lamination1_other_thickness = filter_input(INPUT_POST, 'lamination1_other_thickness');
        if(empty($lamination1_other_thickness)) $lamination1_other_thickness = "NULL";
        $lamination1_other_weight = filter_input(INPUT_POST, 'lamination1_other_weight');
        if(empty($lamination1_other_weight)) $lamination1_other_weight = "NULL";
        $lamination1_customers_material = 0;
        if(filter_input(INPUT_POST, 'lamination1_customers_material') == 'on') {
            $lamination1_customers_material = 1;
        }
        
        $lamination2_brand_name = addslashes(filter_input(INPUT_POST, 'lamination2_brand_name'));
        $lamination2_thickness = filter_input(INPUT_POST, 'lamination2_thickness');
        if(empty($lamination2_thickness)) $lamination2_thickness = "NULL";
        $lamination2_other_brand_name = filter_input(INPUT_POST, 'lamination2_other_brand_name');
        $lamination2_other_price = filter_input(INPUT_POST, 'lamination2_other_price');
        if(empty($lamination2_other_price)) $lamination2_other_price = "NULL";
        $lamination2_other_thickness = filter_input(INPUT_POST, 'lamination2_other_thickness');
        if(empty($lamination2_other_thickness)) $lamination2_other_thickness = "NULL";
        $lamination2_other_weight = filter_input(INPUT_POST, 'lamination2_other_weight');
        if(empty($lamination2_other_weight)) $lamination2_other_weight = "NULL";
        $lamination2_customers_material = 0;
        if(filter_input(INPUT_POST, 'lamination2_customers_material') == 'on') {
            $lamination2_customers_material = 1;
        }
        
        $quantity = preg_replace("/\D/", "", filter_input(INPUT_POST, 'quantity'));
        $width = filter_input(INPUT_POST, 'width');
        if(empty($width)) $width = "NULL";
        $length = filter_input(INPUT_POST, 'length');
        if(empty($length)) $length = "NULL";
        $stream_width = filter_input(INPUT_POST, 'stream_width');
        if(empty($stream_width)) $stream_width = "NULL";
        $streams_count = filter_input(INPUT_POST, 'streams_count');
        if(empty($streams_count)) $streams_count = "NULL";
        $raport = filter_input(INPUT_POST, 'raport');
        if(empty($raport)) $raport = "NULL";
        $paints_count = filter_input(INPUT_POST, 'paints_count');
        if(empty($paints_count)) $paints_count = "NULL";
        
        $no_ski = 0;
        if(filter_input(INPUT_POST, 'no_ski') == 'on') {
            $no_ski = 1;
        }
        
        $manager_id = GetUserId();
        $status_id = 1; // Статус "Расчёт"
        
        $extracharge = filter_input(INPUT_POST, 'h_extracharge');
        if(empty($extracharge)) $extracharge = 35; // Наценка по умолчанию 35
        
        // Данные о цвете
        for($i=1; $i<=8; $i++) {
            $paint_var = "paint_$i";
            $$paint_var = filter_input(INPUT_POST, "paint_$i");
            
            $color_var = "color_$i";
            $$color_var = filter_input(INPUT_POST, "color_$i");
            if(empty($$color_var)) $$color_var = "NULL";
            
            $cmyk_var = "cmyk_$i";
            $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
            
            $percent_var = "percent_$i";
            $$percent_var = filter_input(INPUT_POST, "percent_$i");
            if(empty($$percent_var)) $$percent_var = "NULL";
            
            $form_var = "form_$i";
            $$form_var = filter_input(INPUT_POST, "form_$i");
        }
        
        $sql = "insert into request_calc (customer_id, name, work_type_id, unit, machine_id, "
                . "brand_name, thickness, other_brand_name, other_price, other_thickness, other_weight, customers_material, "
                . "lamination1_brand_name, lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, lamination1_customers_material, "
                . "lamination2_brand_name, lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, lamination2_customers_material, "
                . "width, quantity, streams_count, length, stream_width, raport, paints_count, manager_id, status_id, extracharge, no_ski, "
                . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
                . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
                . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
                . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
                . "form_1, form_2, form_3, form_4, form_5, form_6, form_7, form_8) "
                . "values($customer_id, '$name', $work_type_id, '$unit', $machine_id, "
                . "'$brand_name', $thickness, '$other_brand_name', $other_price, $other_thickness, $other_weight, $customers_material, "
                . "'$lamination1_brand_name', $lamination1_thickness, '$lamination1_other_brand_name', $lamination1_other_price, $lamination1_other_thickness, $lamination1_other_weight, $lamination1_customers_material, "
                . "'$lamination2_brand_name', $lamination2_thickness, '$lamination2_other_brand_name', $lamination2_other_price, $lamination2_other_thickness, $lamination2_other_weight, $lamination2_customers_material, "
                . "$width, $quantity, $streams_count, $length, $stream_width, $raport, $paints_count, $manager_id, $status_id, $extracharge, $no_ski, "
                . "'$paint_1', '$paint_2', '$paint_3', '$paint_4', '$paint_5', '$paint_6', '$paint_7', '$paint_8', "
                . "'$color_1', '$color_2', '$color_3', '$color_4', '$color_5', '$color_6', '$color_7', '$color_8', "
                . "'$cmyk_1', '$cmyk_2', '$cmyk_3', '$cmyk_4', '$cmyk_5', '$cmyk_6', '$cmyk_7', '$cmyk_8', "
                . "'$percent_1', '$percent_2', '$percent_3', '$percent_4', '$percent_5', '$percent_6', '$percent_7', '$percent_8', "
                . "'$form_1', '$form_2', '$form_3', '$form_4', '$form_5', '$form_6', '$form_7', '$form_8')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $insert_id = $executer->insert_id;
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/request_calc/create.php?id='.$insert_id);
        }
    }
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change_status_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    if(empty($extracharge)) {
        $sql = "update request_calc set status_id=$status_id where id=$id";
    }
    else {
        $sql = "update request_calc set status_id=$status_id, extracharge=$extracharge where id=$id";
    }
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/request_calc/request_calc.php'. BuildQuery('id', $id));
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

if(!empty($id)) {
    $sql = "select date, customer_id, name, work_type_id, unit, machine_id, "
            . "brand_name, thickness, other_brand_name, other_price, other_thickness, other_weight, customers_material, "
            . "lamination1_brand_name, lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, lamination1_customers_material, "
            . "lamination2_brand_name, lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, lamination2_customers_material, "
            . "quantity, width, streams_count, length, stream_width, raport, paints_count, status_id, extracharge, no_ski, "
            . "(select count(id) from techmap where request_calc_id = $id) techmaps_count, "
            . "paint_1, paint_2, paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
            . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
            . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "form_1, form_2, form_3, form_4, form_5, form_6, form_7, form_8 "
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

$other_brand_name = filter_input(INPUT_POST, 'other_brand_name');
if(null === $other_brand_name) {
    if(isset($row['other_brand_name'])) $other_brand_name = $row['other_brand_name'];
    else $other_brand_name = null;
}

$other_price = filter_input(INPUT_POST, 'other_price');
if(null === $other_price) {
    if(isset($row['other_price'])) $other_price = $row['other_price'];
    else $other_price = null;
}

$other_thickness = filter_input(INPUT_POST, 'other_thickness');
if(null === $other_thickness) {
    if(isset($row['other_thickness'])) $other_thickness = $row['other_thickness'];
    else $other_thickness = null;
}

$other_weight = filter_input(INPUT_POST, 'other_weight');
if(null === $other_weight) {
    if(isset($row['other_weight'])) $other_weight = $row['other_weight'];
    else $other_weight = null;
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

$machine_id = filter_input(INPUT_POST, 'machine_id');
if(null === $machine_id) {
    if(isset($row['machine_id'])) $machine_id = $row['machine_id'];
    else $machine_id = null;
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

$lamination1_other_brand_name = filter_input(INPUT_POST, 'lamination1_other_brand_name');
if(null === $lamination1_other_brand_name) {
    if(isset($row['lamination1_other_brand_name'])) $lamination1_other_brand_name = $row['lamination1_other_brand_name'];
    else $lamination1_other_brand_name = null;
}

$lamination1_other_price = filter_input(INPUT_POST, 'lamination1_other_price');
if(null === $lamination1_other_price) {
    if(isset($row['lamination1_other_price'])) $lamination1_other_price = $row['lamination1_other_price'];
    else $lamination1_other_price = null;
}

$lamination1_other_thickness = filter_input(INPUT_POST, 'lamination1_other_thickness');
if(null === $lamination1_other_thickness) {
    if(isset($row['lamination1_other_thickness'])) $lamination1_other_thickness = $row['lamination1_other_thickness'];
    else $lamination1_other_thickness = null;
}

$lamination1_other_weight = filter_input(INPUT_POST, 'lamination1_other_weight');
if(null === $lamination1_other_weight) {
    if(isset($row['lamination1_other_weight'])) $lamination1_other_weight = $row['lamination1_other_weight'];
    else $lamination1_other_weight = null;
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

$lamination2_other_brand_name = filter_input(INPUT_POST, 'lamination2_other_brand_name');
if(null === $lamination2_other_brand_name) {
    if(isset($row['lamination2_other_brand_name'])) $lamination2_other_brand_name = $row['lamination2_other_brand_name'];
    else $lamination2_other_brand_name = null;
}

$lamination2_other_price = filter_input(INPUT_POST, 'lamination2_other_price');
if(null === $lamination2_other_price) {
    if(isset($row['lamination2_other_price'])) $lamination2_other_price = $row['lamination2_other_price'];
    else $lamination2_other_price = null;
}

$lamination2_other_thickness = filter_input(INPUT_POST, 'lamination2_other_thickness');
if(null === $lamination2_other_thickness) {
    if(isset($row['lamination2_other_thickness'])) $lamination2_other_thickness = $row['lamination2_other_thickness'];
    else $lamination2_other_thickness = null;
}

$lamination2_other_weight = filter_input(INPUT_POST, 'lamination2_other_weight');
if(null === $lamination2_other_weight) {
    if(isset($row['lamination2_other_weight'])) $lamination2_other_weight = $row['lamination2_other_weight'];
    else $lamination2_other_weight = null;
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

$width = filter_input(INPUT_POST, 'width');
if(null === $width) {
    if(isset($row['width'])) $width = $row['width'];
    else $width = null;
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

$paints_count = filter_input(INPUT_POST, 'paints_count');
if(null === $paints_count) {
    if(isset($row['paints_count'])) $paints_count = $row['paints_count'];
    else $paints_count = null;
}

if(null !== filter_input(INPUT_POST, 'create_request_calc_submit')) {
    $no_ski = filter_input(INPUT_POST, 'no_ski') == 'on' ? 1 : 0;
}
else {
    if(isset($row['no_ski'])) $no_ski = $row['no_ski'];
    else $no_ski = null;
}

if(isset($row['techmaps_count'])) $techmaps_count = $row['techmaps_count'];
else $techmaps_count = null;

if(isset($row['status_id'])) $status_id = $row['status_id'];
else $status_id = null;

if(isset($row['extracharge'])) $extracharge = $row['extracharge'];
else $extracharge = 0;

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
    
    $form_var = "form_$i";
    $$form_var = filter_input(INPUT_POST, "form_$i");
    if(null === $$form_var) {
        if(isset($row["form_$i"])) $$form_var = $row["form_$i"];
        else $$form_var = null;
    }
}

// Расчёт скрываем:
// 1. При создании нового заказчика
// 2. При создании нового расчёта
// 3. При невалидной форме
// Если показываем рассчёт, то не показываем кнопку отправки.
// И наоборот.
$create_request_calc_submit_class = " d-none";

if(null !== filter_input(INPUT_POST, 'create_customer_submit') || 
        null === filter_input(INPUT_GET, 'id') ||
        !$form_valid) {
    $create_request_calc_submit_class = "";
}

// Список красочностей каждой машины
$colorfulnesses = array();
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
        include './right_panel.php';
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/<?= filter_input(INPUT_GET, "mode") == "recalc" ? "request_calc.php".BuildQueryRemove("mode") : "" ?>">Назад</a>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-5" id="left_side">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="h_extracharge" name="h_extracharge" class="extracharge" value="<?=$extracharge ?>" />
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
                                        $sql = "select id, name from customer order by name";
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
                        $thing_checked = $unit == "thing" ? " checked='checked'" : "";
                        ?>
                        <div class="print-only justify-content-start mt-2 mb-1 d-none">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="unit" value="kg"<?=$kg_checked ?> />Килограммы
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="unit" value="thing"<?=$thing_checked ?> />Штуки
                                </label>
                            </div>
                        </div>
                        <!-- Печатная машина -->
                        <div class="print-only d-none">
                        <div class="form-group w-100">
                            <label for="machine_id">Печатная машина</label>
                            <select id="machine_id" name="machine_id" class="form-control print-only d-none">
                                <option value="" hidden="hidden" selected="selected">Печатная машина...</option>
                                <?php
                                $sql = "select id, name, colorfulness from machine where colorfulness > 0";
                                $fetcher = new Fetcher($sql);
                                
                                while ($row = $fetcher->Fetch()):
                                $selected = '';
                                if($row['id'] == $machine_id) {
                                    $selected = " selected='selected'";
                                }
                                ?>
                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'].' ('.$row['colorfulness'].' красок)' ?></option>
                                <?php
                                // Заполняем список красочностей, чтобы при выборе машины установить нужное количество элементов списка
                                $colorfulnesses[$row['id']] = $row['colorfulness'];
                                endwhile;
                                ?>
                            </select>
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
                                            
                                            $other_selected = '';
                                            if(!empty($other_brand_name)) {
                                                $other_selected = " selected='selected'";
                                            }
                                            ?>
                                        <option value="<?=OTHER ?>"<?=$other_selected ?>>Другая</option>
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
                        <div class="row other_only">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="other_brand_name">Название пленки</label>
                                    <input type="text" 
                                           id="other_brand_name" 
                                           name="other_brand_name" 
                                           class="form-control" 
                                           placeholder="Название пленки" 
                                           value="<?=$other_brand_name ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'other_brand_name'); $(this).attr('name', 'other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'other_brand_name'); $(this).attr('name', 'other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onfocusout="javascript: $(this).attr('id', 'other_brand_name'); $(this).attr('name', 'other_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                    <div class="invalid-feedback">Название пленки обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="other_price">Цена</label>
                                    <input type="text" 
                                           id="other_price" 
                                           name="other_price" 
                                           class="form-control float-only" 
                                           placeholder="Цена" 
                                           value="<?= empty($other_price) ? '' : floatval($other_price) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'other_price'); $(this).attr('name', 'other_price'); $(this).attr('placeholder', 'Цена')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'other_price'); $(this).attr('name', 'other_price'); $(this).attr('placeholder', 'Цена')" 
                                           onfocusout="javascript: $(this).attr('id', 'other_price'); $(this).attr('name', 'other_price'); $(this).attr('placeholder', 'Цена')" />
                                    <div class="invalid-feedback">Цена обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="other_thickness">Толщина, мкм</label>
                                    <input type="text" 
                                           id="other_thickness" 
                                           name="other_thickness" 
                                           class="form-control int-only" 
                                           placeholder="Толщина" 
                                           value="<?= $other_thickness ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'other_thickness'); $(this).attr('name', 'other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'other_thickness'); $(this).attr('name', 'other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                           onfocusout="javascript: $(this).attr('id', 'other_thickness'); $(this).attr('name', 'other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                    <div class="invalid-feedback">Толщина обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="other_weight">Удельный вес</label>
                                    <input type="text" 
                                           id="other_weight" 
                                           name="other_weight" 
                                           class="form-control float-only" 
                                           placeholder="Удельный вес" 
                                           value="<?= empty($other_weight) ? '' : floatval($other_weight) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'other_weight'); $(this).attr('name', 'other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'other_weight'); $(this).attr('name', 'other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onfocusout="javascript: $(this).attr('id', 'other_weight'); $(this).attr('name', 'other_weight'); $(this).attr('placeholder', 'Удельный вес')" />
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
                                                
                                                $lamination1_other_selected = '';
                                                if(!empty($lamination1_other_brand_name)) {
                                                    $lamination1_other_selected = " selected='selected'";
                                                }
                                                ?>
                                            <option value="<?=OTHER ?>"<?=$lamination1_other_selected ?>>Другая</option>
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
                            <div class="row lamination1_other_only">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_other_brand_name">Название пленки</label>
                                        <input type="text" 
                                               id="lamination1_other_brand_name" 
                                               name="lamination1_other_brand_name" 
                                               class="form-control" 
                                               placeholder="Название пленки" 
                                               value="<?=$lamination1_other_brand_name ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_other_brand_name'); $(this).attr('name', 'lamination1_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_other_brand_name'); $(this).attr('name', 'lamination1_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_other_brand_name'); $(this).attr('name', 'lamination1_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                        <div class="invalid-feedback">Название пленки обязательно</div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="lamination1_other_price">Цена</label>
                                        <input type="text" 
                                               id="lamination1_other_price" 
                                               name="lamination1_other_price" 
                                               class="form-control float-only" 
                                               placeholder="Цена" 
                                               value="<?= empty($lamination1_other_price) ? '' : floatval($lamination1_other_price) ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_other_price'); $(this).attr('name', 'lamination1_other_price'); $(this).attr('placeholder', 'Цена')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_other_price'); $(this).attr('name', 'lamination1_other_price'); $(this).attr('placeholder', 'Цена')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_other_price'); $(this).attr('name', 'lamination1_other_price'); $(this).attr('placeholder', 'Цена')" />
                                        <div class="invalid-feedback">Цена обязательно</div>
                                    </div>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_other_thickness">Толщина, мкм</label>
                                        <input type="text" 
                                               id="lamination1_other_thickness" 
                                               name="lamination1_other_thickness" 
                                               class="form-control int-only" 
                                               placeholder="Толщина" 
                                               value="<?= $lamination1_other_thickness ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_other_thickness'); $(this).attr('name', 'lamination1_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_other_thickness'); $(this).attr('name', 'lamination1_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_other_thickness'); $(this).attr('name', 'lamination1_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                        <div class="invalid-feedback">Толщина обязательно</div>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <label for="lamination1_other_weight">Удельный вес</label>
                                        <input type="text" 
                                               id="lamination1_other_weight" 
                                               name="lamination1_other_weight" 
                                               class="form-control float-only" 
                                               placeholder="Удельный вес" 
                                               value="<?= empty($lamination1_other_weight) ? '' : floatval($lamination1_other_weight) ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_other_weight'); $(this).attr('name', 'lamination1_other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_other_weight'); $(this).attr('name', 'lamination1_other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_other_weight'); $(this).attr('name', 'lamination1_other_weight'); $(this).attr('placeholder', 'Удельный вес')" />
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
                                                    
                                                    $lamination2_other_selected = '';
                                                    if(!empty($lamination2_other_brand_name)) {
                                                        $lamination2_other_selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=OTHER ?>"<?=$lamination2_other_selected ?>>Другая</option>
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
                                <div class="row lamination2_other_only">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_other_brand_name">Название пленки</label>
                                            <input type="text" 
                                                   id="lamination2_other_brand_name" 
                                                   name="lamination2_other_brand_name" 
                                                   class="form-control" 
                                                   placeholder="Название пленки" 
                                                   value="<?=$lamination2_other_brand_name ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_other_brand_name'); $(this).attr('name', 'lamination2_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_other_brand_name'); $(this).attr('name', 'lamination2_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_other_brand_name'); $(this).attr('name', 'lamination2_other_brand_name'); $(this).attr('placeholder', 'Название пленки')" />
                                            <div class="invalid-feedback">Название пленки обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="lamination2_other_price">Цена</label>
                                            <input type="text" 
                                                   id="lamination2_other_price" 
                                                   name="lamination2_other_price" 
                                                   class="form-control float-only" 
                                                   placeholder="Цена" 
                                                   value="<?= empty($lamination2_other_price) ? '' : floatval($lamination2_other_price) ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_other_price'); $(this).attr('name', 'lamination2_other_price'); $(this).attr('placeholder', 'Цена')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_other_price'); $(this).attr('name', 'lamination2_other_price'); $(this).attr('placeholder', 'Цена')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_other_price'); $(this).attr('name', 'lamination2_other_price'); $(this).attr('placeholder', 'Цена')" />
                                            <div class="invalid-feedback">Цена обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-1"></div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_other_thickness">Толщина, мкм</label>
                                            <input type="text" 
                                                   id="lamination2_other_thickness" 
                                                   name="lamination2_other_thickness" 
                                                   class="form-control int-only" 
                                                   placeholder="Толщина" 
                                                   value="<?= $lamination2_other_thickness ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_other_thickness'); $(this).attr('name', 'lamination2_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_other_thickness'); $(this).attr('name', 'lamination2_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_other_thickness'); $(this).attr('name', 'lamination2_other_thickness'); $(this).attr('placeholder', 'Толщина, мкм')" />
                                            <div class="invalid-feedback">Толщина обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="lamination2_other_weight">Удельный вес</label>
                                            <input type="text" 
                                                   id="lamination2_other_weight" 
                                                   name="lamination2_other_weight" 
                                                   class="form-control float-only" 
                                                   placeholder="Удельный вес" 
                                                   value="<?= empty($lamination2_other_weight) ? '' : floatval($lamination2_other_weight) ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_other_weight'); $(this).attr('name', 'lamination2_other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_other_weight'); $(this).attr('name', 'lamination2_other_weight'); $(this).attr('placeholder', 'Удельный вес')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_other_weight'); $(this).attr('name', 'lamination2_other_weight'); $(this).attr('placeholder', 'Удельный вес')" />
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
                            <!-- Обрезная ширина -->
                            <div class="col-6 lam-only no-print-only d-none">
                                <div class="form-group">
                                    <label for="width">Обрезная ширина</label>
                                    <input type="text" 
                                           id="width" 
                                           name="width" 
                                           class="form-control int-only lam-only no-print-only d-none" 
                                           placeholder="Обрезная ширина, мм" 
                                           value="<?=$width ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'width'); $(this).attr('name', 'width'); $(this).attr('placeholder', 'Обрезная ширина, мм')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'width'); $(this).attr('name', 'width'); $(this).attr('placeholder', 'Обрезная ширина, мм')" 
                                           onfocusout="javascript: $(this).attr('id', 'width'); $(this).attr('name', 'width'); $(this).attr('placeholder', 'Обрезная ширина, мм')" />
                                    <div class="invalid-feedback">Обрезная ширина обязательно</div>
                                </div>
                            </div>
                            <!-- Длина от метки до метки -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="length">Длина от метки до метки, мм</label>
                                    <input type="text" 
                                           id="length" 
                                           name="length" 
                                           class="form-control print-only d-none" 
                                           placeholder="Длина от метки до метки, мм" 
                                           value="<?= empty($length) ? "" : floatval($length) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина от метки до метки, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина от метки до метки, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'length'); $(this).attr('name', 'length'); $(this).attr('placeholder', 'Длина от метки до метки, мм');" />
                                    <div class="invalid-feedback">Длина от метки до метки обязательно</div>
                                </div>
                            </div>
                            <!-- Ширина ручья -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="stream_width">Ширина ручья, мм</label>
                                    <input type="text" 
                                           id="stream_width" 
                                           name="stream_width" 
                                           class="form-control print-only d-none" 
                                           placeholder="Ширина ручья, мм" 
                                           value="<?= empty($stream_width) ? "" : floatval($stream_width) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'stream_width'); $(this).attr('name', 'stream_width'); $(this).attr('placeholder', 'Ширина ручья, мм');" />
                                    <div class="invalid-feedback">Ширина ручья обязательно</div>
                                </div>
                            </div>
                            <!-- Количество ручьёв -->
                            <div class="col-6 lam-only print-no-print d-none">
                                <div class="form-group">
                                    <label for="streams_count">Количество ручьев</label>
                                    <input type="text" 
                                           id="streams_count" 
                                           name="streams_count" 
                                           class="form-control int-only lam-only print-no-print d-none" 
                                           placeholder="Количество ручьев" 
                                           value="<?=$streams_count ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onfocusout="javascript: $(this).attr('id', 'streams_count'); $(this).attr('name', 'streams_count'); $(this).attr('placeholder', 'Количество ручьев');" />
                                    <div class="invalid-feedback">Количество ручьев обязательно</div>
                                </div>
                            </div>
                            <!-- Рапорт -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="raport">Рапорт</label>
                                    <select id="raport" name="raport" class="form-control print-only d-none">
                                        <option value="" hidden="hidden" selected="selected">Рапорт...</option>
                                        <?php
                                        if(!empty($machine_id)) {
                                            $sql = "select name, value from raport where machine_id = $machine_id order by value";
                                            $fetcher = new Fetcher($sql);
                                            
                                            while($row = $fetcher->Fetch()) {
                                                $raport_name = $row['name'];
                                                $raport_value = floatval($row['value']);
                                                $display_value = (empty($raport_name) ? "" : $raport_name." ").$raport_value;
                                                $selected = "";
                                                if($raport_value == $raport) $selected = " selected='selected'";
                                                echo "<option value='$raport_value'$selected>$display_value</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Печать без лыж -->
                        <div class="form-check mb-2 print-only d-none">
                            <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                <?php
                                $checked = $no_ski == 1 ? " checked='checked'" : "";
                                ?>
                                <input type="checkbox" class="form-check-input" id="no_ski" name="no_ski" value="on"<?=$checked ?>>Печать без лыж
                            </label>
                        </div>
                        <!-- Количество красок -->
                        <div class="print-only d-none">
                            <div class="form-group">
                                <label for="paints_count">Количество красок</label>
                                <select id="paints_count" name="paints_count" class="form-control print-only d-none">
                                    <option value="" hidden="hidden">Количество красок...</option>
                                        <?php
                                        if(!empty($paints_count) || !empty($machine_id)):
                                        for($i = 1; $i <= $colorfulnesses[$machine_id]; $i++):
                                            $selected = "";
                                        if($paints_count == $i) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                    <option<?=$selected ?>><?=$i ?></option>
                                        <?php
                                        endfor;
                                        endif;
                                        ?>
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
                                <div class="form-group<?=$form_class ?>" id="form_group_<?=$i ?>">
                                    <label for="form_<?=$i ?>">Форма</label>
                                    <select id="form_<?=$i ?>" name="form_<?=$i ?>" class="form-control form">
                                        <?php
                                        $old_selected = "";
                                        $flint_selected = "";
                                        $kodak_selected = "";
                                        $tver_selected = "";
                                    
                                        $form_var = "form_$i";
                                        $form_selected_var = $$form_var."_selected";
                                        $$form_selected_var = " selected='selected'";
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
                        <button type="submit" id="create_request_calc_submit" name="create_request_calc_submit" class="btn btn-dark mt-3<?=$create_request_calc_submit_class ?>">Рассчитать</button>
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
                SetFieldsVisibility($(this).val());
            });
            
            // Показываем или скрываем поля в зависимости от работы с печатью / без печати и наличия / отсутствия ламинации
            function SetFieldsVisibility(work_type_id) {
                if (work_type_id == 2) {
                    // Скрываем поля "только без печати"
                    $('.no-print-only').addClass('d-none');
                    $('.no-print-only').removeAttr('required');
                    
                    // Показываем поля "только с печатью"
                    $('.print-only').removeClass('d-none');
                    $('.print-only').attr('required', 'required');
                
                    // Показываем поля "с печатью и без печати"
                    $('.print-no-print').removeClass('d-none');
                    $('.print-no-print').attr('required', 'required');
                }
                else {
                    // Скрываем поля "только с печатью"
                    $('.print-only').addClass('d-none');
                    $('.print-only').removeAttr('required');
                    
                    // Показываем поля "только без печати"
                    $('.no-print-only').removeClass('d-none');
                    $('.no-print-only').attr('required', 'required');
                
                    // Показываем поля "с печатью и без печати"
                    $('.print-no-print').removeClass('d-none');
                    $('.print-no-print').attr('required', 'required');
                
                    // Скрываем поля "только с ламинацией"
                    $('.lam-only').addClass('d-none');
                    $('.lam-only').removeAttr('required');
                
                    // Если видима ламинация, то показываем поля "только с ламинацией"
                    if($('#form_lamination_1').is(':visible')) {
                        $('.lam-only').not('.print-only').removeClass('d-none');
                        $('.lam-only').not('.print-only').attr('required', 'required');
                    }
                }
            }
            
            SetFieldsVisibility($('#work_type_id').val());
            
            // Если единица объёма - кг, то в поле "Объём" пишем "Объём, кг", иначе "Объем, шт"
            if($('input[value=kg]').is(':checked')) {
                $('#label_quantity').text('Объем заказа, кг');
            }
            
            if($('input[value=thing]').is(':checked')) {
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
            
            // Заполняем список красочностей
            var colorfulnesses = {};
            <?php foreach (array_keys($colorfulnesses) as $key): ?>
                colorfulnesses[<?=$key ?>] = <?=$colorfulnesses[$key] ?>;
            <?php endforeach; ?>
            
            // Обработка выбора машины, заполнение списка рапортов
            $('#machine_id').change(function(){
                if($(this).val() == "") {
                    $('#raport').html("<option value=''>Рапорт...</option>")
                }
                else {
                    // Заполняем список количеств цветов
                    $('.paint_block').addClass('d-none');
                    $('.paint').removeAttr('required');
                                
                    colorfulness = parseInt(colorfulnesses[$(this).val()]);
                    var colorfulness_list = "<option value='' hidden='hidden'>Количество красок...</option>";
                    for(var i=1; i<=colorfulness; i++) {
                        colorfulness_list = colorfulness_list + "<option>" + i + "</option>";
                    }
                    $('#paints_count').html(colorfulness_list);
                    
                    // Заполняем список рапортов
                    $.ajax({ url: "../ajax/raport.php?machine_id=" + $(this).val() })
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
                    $('#' + prefix + 'other_price').val('');
                    $('#' + prefix + 'other_price').attr('disabled', 'disabled');
                }
                else {
                    $('#' + prefix + 'other_price').removeAttr('disabled');
                }
                
                if(value == '<?=OTHER ?>') {
                    $('#' + prefix + 'thickness').removeAttr('required');
                    $('#' + prefix + 'thickness').addClass('d-none');
                    $('#' + prefix + 'thickness').prev('label').addClass('d-none');
                    $('.' + prefix + 'other_only').removeClass('d-none');
                    $('.' + prefix + 'other_only input').attr('required', 'required');
                }
                else {
                    $('#' + prefix + 'thickness').attr('required', 'required');
                    $('#' + prefix + 'thickness').removeClass('d-none');
                    $('#' + prefix + 'thickness').prev('label').removeClass('d-none');
                    $('.' + prefix + 'other_only').addClass('d-none');
                    $('.' + prefix + 'other_only input').removeAttr('required');
                }
                
                if($('#' + prefix + 'other_price').attr('disabled') == 'disabled') {
                    $('#' + prefix + 'other_price').removeAttr('required');
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
                
                $('#form_group_' + data_id).removeClass('col-3');
                $('#form_group_' + data_id).addClass('d-none');
                
                // Снимаем атрибут required с кода цвета, CMYK и процента
                $('#color_' + data_id).removeAttr('required');
                $('#cmyk_' + data_id).removeAttr('required');
                $('#percent_' + data_id).removeAttr('required');
                
                // Затем, в зависимости от выбранного значения, устанавливаем видимость нужного элемента для этого значения
                if(paint == 'lacquer')  {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(paint == 'white') {
                    $('#paint_group_' + data_id).addClass('col-6');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(paint == 'cmyk') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                    $('#cmyk_' + data_id).attr('required', 'required');
                }
                else if(paint == 'panton') {
                    $('#paint_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).removeClass('d-none');
                    $('#percent_group_' + data_id).addClass('col-3');
                    $('#percent_group_' + data_id).removeClass('d-none');
                    $('#form_group_' + data_id).addClass('col-3');
                    $('#form_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
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
            
            // Ограничение значения поля "Длина от метки до метки" до 3 цифр
            $('input#length').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('input#length').change(function(){
                ChangeLimitIntValue($(this), 999);
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