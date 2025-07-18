<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// ID заказчика
$new_customer_id = null;

// Создание заказчика
if(null !== filter_input(INPUT_POST, 'create_customer_submit')) {
    if(!empty(filter_input(INPUT_POST, 'customer_name'))) {
        $id = filter_input(INPUT_POST, 'id');
        $customer_name = addslashes(filter_input(INPUT_POST, 'customer_name'));
        $customer_person = addslashes(filter_input(INPUT_POST, 'customer_person'));
        $customer_phone = filter_input(INPUT_POST, 'customer_phone');
        $customer_extension = filter_input(INPUT_POST, 'customer_extension');
        $customer_email = filter_input(INPUT_POST, 'customer_email');
        $customer_manager_id = GetUserId();
        
        // Если такой заказчик уже есть, просто получаем его ID
        $sql = "select id from customer where name = '$customer_name' and manager_id = $customer_manager_id limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $new_customer_id = $row[0];
        }
        
        // Если такого заказчика нет, создаём его
        if(empty($customer_id)) {
            $sql = "insert into customer (name, person, phone, extension, email, manager_id) values ('$customer_name', '$customer_person', '$customer_phone', '$customer_extension', '$customer_email', $customer_manager_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $new_customer_id = $executer->insert_id;
        }
    }
}

// Получение норм
$gap_raport = null;

$sql = "select gap_raport from norm_gap order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $gap_raport = $row['gap_raport'];
}

$min_percent = null;

$sql = "select min_percent from norm_ink order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $min_percent = $row['min_percent'];
}

$machine_width = null;

$machine_id = filter_input(INPUT_POST, 'machine_id');
if(!empty($machine_id)) {
    $sql = "select width from norm_machine where machine_id = $machine_id order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $machine_width = $row['width'];
    }
}

// Значение марки плёнки "другая"
const INDIVIDUAL = -1;

// Атрибут "поле неактивно"
$disabled_attr = "";

// Валидация формы
$form_valid = true;
$error_message = '';

$customer_id_valid = '';
$name_valid = '';
$work_type_valid = '';
$film_id_valid = '';
$film_variation_id_valid = '';
$price_valid = '';
$currency_valid = '';
$quantity_valid = '';
$printings_number_valid = '';

$individual_film_name_valid = '';
$individual_thickness_valid = '';
$individual_density_valid = '';

$width_ski_valid = '';
$width_machine_valid = '';

$lamination1_price_valid = '';
$lamination1_width_ski_valid = '';
$lamination1_width_machine_valid = '';

$lamination2_price_valid = '';
$lamination2_width_ski_valid = '';
$lamination2_width_machine_valid = '';

$raport_valid = '';

// Переменные для валидации цвета, CMYK и процента
for($i=1; $i<=8; $i++) {
    $ink_valid_var = 'ink_'.$i.'_valid';
    $$ink_valid_var = '';
    
    $cmyk_valid_var = 'cmyk_'.$i.'_valid';
    $$cmyk_valid_var = '';
    
    $lacquer_valid_var = 'lacquer_'.$i.'_valid';
    $$lacquer_valid_var = '';
    
    $color_valid_var = 'color_'.$i.'_valid';
    $$color_valid_var = '';
    
    $percent_valid_var = 'percent_'.$i.'_valid';
    $$percent_valid_var = '';
}

// Сохранение в базу расчёта
if(null !== filter_input(INPUT_POST, 'create_calculation_submit')) {
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
    
    if(empty(filter_input(INPUT_POST, 'film_id'))) {
        $film_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'work_type_id') != WORK_TYPE_SELF_ADHESIVE && empty(filter_input(INPUT_POST, 'quantity'))) {
        $quantity_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(filter_input(INPUT_POST, 'work_type_id') == WORK_TYPE_SELF_ADHESIVE && empty(filter_input(INPUT_POST, 'printings_number'))) {
        $printings_number_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Если тип "Самоклеящийся материал", то должен быть добавлен хотя бы один тираж
    // Иначе поле "Размер тиража" не должно быть пустое
    if(filter_input(INPUT_POST, 'work_type_id') == WORK_TYPE_SELF_ADHESIVE && empty(filter_input(INPUT_POST, 'quantity_1'))) {
        $printings_number_valid = ISINVALID;
        $form_valid = false;
    }
    elseif(filter_input(INPUT_POST, 'work_type_id') != WORK_TYPE_SELF_ADHESIVE && empty (filter_input(INPUT_POST, 'quantity'))) {
        $quantity_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Валидация цен - они должны быть не меньше минимальных
    $price_min = filter_input(INPUT_POST, 'price_min');
    $price = filter_input(INPUT_POST, 'price');
    
    if(!empty($price_min) && !empty($price)) {
        $fPriceMin = floatval($price_min);
        $fPrice = floatval($price);
        
        if($fPrice < $fPriceMin) {
            $price_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(filter_input(INPUT_POST, 'film_id') == INDIVIDUAL) {
        // Проверка валидности параметров, введённых вручную при выборе марки плёнки "Другая"
        if(empty(filter_input(INPUT_POST, 'individual_film_name'))) {
            $individual_film_name_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty(filter_input(INPUT_POST, 'individual_thickness'))) {
            $individual_thickness_valid = ISINVALID;
            $form_valid = false;
        }
        
        if(empty(filter_input(INPUT_POST, 'individual_density'))) {
            $individual_density_valid = ISINVALID;
            $form_valid = false;
        }
    }
    else {
        // Проверка валидности параметров стандартных плёнок
        if(empty(filter_input(INPUT_POST, 'film_variation_id'))) {
            $film_variation_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    $work_type_id = filter_input(INPUT_POST, 'work_type_id');
    $stream_width = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'stream_width_2') : filter_input(INPUT_POST, 'stream_width');
    $streams_number = filter_input(INPUT_POST, 'streams_number');
    $stream_widths = array();
    
    // Если тип работы - не самоклейка, а ширина ручья пустая, то должен быть список ширин ручьёв каждого ручья
    if($work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) {
        $sci = 1;
        $stream_width_var = "stream_width_$sci";
        
        while (filter_input(INPUT_POST, $stream_width_var) !== null) {
            $stream_widths[$sci] = filter_input(INPUT_POST, $stream_width_var);
            $sci++;
            $stream_width_var = "stream_width_$sci";
        }
    }
    
    if(filter_input(INPUT_POST, 'ski') == SKI_NONSTANDARD && !empty(filter_input(INPUT_POST, 'width_ski')) && !empty(filter_input(INPUT_POST, 'streams_number'))) {
        $width_ski = filter_input(INPUT_POST, 'width_ski');
        
        if($work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) {
            // Если ширина плёнки меньше, чем суммарная ширина ручьёв, то плёнка слишком узкая
            if($width_ski < array_sum($stream_widths)) {
                $width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        else {
            // Если ширина плёнки меньше, чем ширина ручья * кол-во ручьёв, то плёнка слишком узкая
            if($width_ski < $stream_width * $streams_number) {
                $width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        
        // Если ширина плёнки больше, чем ширина машины, то плёнка слишком широкая
        if(!empty($machine_width) && $width_ski > $machine_width) {
            $width_machine_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // ЛАМИНАЦИЯ 1
    // Валидация цен ламинации 1 - они должны быть не меньше минимальных
    $lamination1_price_min = filter_input(INPUT_POST, 'lamination1_price_min');
    $lamination1_price = filter_input(INPUT_POST, 'lamination1_price');
    
    if(!empty($lamination1_price_min) && !empty($lamination1_price)) {
        $fPriceMin = floatval($lamination1_price_min);
        $fPrice = floatval($lamination1_price);
        
        if($fPrice < $fPriceMin) {
            $lamination1_price_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(filter_input(INPUT_POST, 'lamination1_ski') == SKI_NONSTANDARD && !empty(filter_input(INPUT_POST, 'lamination1_width_ski')) && !empty(filter_input(INPUT_POST, 'streams_number'))) {
        $lamination1_width_ski = filter_input(INPUT_POST, 'lamination1_width_ski');
        
        if($work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) {
            // Если ширина плёнки меньше, чем суммарная ширина ручьёв, то плёнка слишком узкая
            if($lamination1_width_ski < array_sum($stream_widths)) {
                $lamination1_width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        else {
            // Если ширина плёнки меньше, чем ширина ручья * кол-во ручьёв, то плёнка слишком узкая
            if($lamination1_width_ski < $stream_width * $streams_number) {
                $lamination1_width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        
        // Если ширина плёнки больше, чем ширина машины, то плёнка слишком широкая
        if(!empty($machine_width) && $lamination1_width_ski > $machine_width) {
            $lamination1_width_machine_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // ЛАМИНАЦИЯ 2
    // Валидация цен ламинации 2 - они должны быть не меньше минимальных
    $lamination2_price_min = filter_input(INPUT_POST, 'lamination2_price_min');
    $lamination2_price = filter_input(INPUT_POST, 'lamination2_price');
    
    if(!empty($lamination2_price_min) && !empty($lamination2_price)) {
        $fPriceMin = floatval($lamination2_price_min);
        $fPrice = floatval($lamination2_price);
        
        if($fPrice < $fPriceMin) {
            $lamination2_price_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    if(filter_input(INPUT_POST, 'lamination2_ski') == SKI_NONSTANDARD && !empty(filter_input(INPUT_POST, 'lamination2_width_ski')) && !empty(filter_input(INPUT_POST, 'streams_number'))) {
        $lamination2_width_ski = filter_input(INPUT_POST, 'lamination2_width_ski');
        
        if($work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) {
            // Если ширина плёнки меньше, чем суммарная ширина ручьёв, то плёнка слишком узкая
            if($lamination2_width_ski < array_sum($stream_widths)) {
                $lamination2_width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        else {
            // Если ширина плёнки меньше, чем ширина ручья * кол-во ручьёв, то плёнка слишком узкая
            if($lamination2_width_ski < $stream_width * $streams_number) {
                $lamination2_width_ski_valid = ISINVALID;
                $form_valid = false;
            }
        }
        
        // Если ширина плёнки больше, чем ширина машины, то плёнка слишком широкая
        if(!empty($machine_width) && $lamination2_width_ski > $machine_width) {
            $lamination2_width_machine_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Проверка валидности рапорта
    if($work_type_id != WORK_TYPE_NOPRINT) {
        if(null == filter_input(INPUT_POST, 'raport')) {
            $raport_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Проверка валидности цвета, CMYK и процента
    $ink_number = filter_input(INPUT_POST, 'ink_number');
    
    for($i = 1; $i <= 8; $i++) {
        if(!empty($ink_number) && is_numeric($ink_number) && $i <= $ink_number) {
            $ink_var = "ink_".$i;
            $$ink_var = filter_input(INPUT_POST, 'ink_'.$i);
            
            $color_var = "color_".$i;
            $$color_var = filter_input(INPUT_POST, 'color_'.$i);
            
            $cmyk_var = "cmyk_".$i;
            $$cmyk_var = filter_input(INPUT_POST, 'cmyk_'.$i);
            
            $lacquer_var = "lacquer_".$i;
            $$lacquer_var = filter_input(INPUT_POST, 'lacquer_'.$i);
            
            $percent_var = "percent_".$i;
            $$percent_var = filter_input(INPUT_POST, 'percent_'.$i);
            
            if(empty($$percent_var) || (intval($$percent_var) < intval($min_percent))) {
                $percent_valid_var = 'percent_'.$i.'_valid';
                $$percent_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if($$ink_var == 'panton' && empty($$color_var)) {
                $color_valid_var = 'color_'.$i.'_valid';
                $$color_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if($$ink_var == 'cmyk' && empty($$cmyk_var)) {
                $cmyk_valid_var = 'cmyk_'.$i.'_valid';
                $$cmyk_valid_var = ISINVALID;
                $form_valid = false;
            }
            
            if(filter_input(INPUT_POST, 'work_type_id') == WORK_TYPE_PRINT) {
                if($$ink_var == 'lacquer' && empty($$lacquer_var)) {
                    $lacquer_valid_var = 'lacquer_'.$i.'_valid';
                    $$lacquer_valid_var = ISINVALID;
                    $form_valid = false;
                }
            }
        }
    }
    
    if($form_valid) {
        $customer_id = filter_input(INPUT_POST, 'customer_id');
        $name = addslashes(filter_input(INPUT_POST, 'name'));
        $work_type_id = filter_input(INPUT_POST, 'work_type_id');
        $unit = filter_input(INPUT_POST, 'unit');
        $machine_id = filter_input(INPUT_POST, 'machine_id'); if(empty($machine_id)) $machine_id = "NULL"; if($work_type_id == WORK_TYPE_NOPRINT) $machine_id = "NULL";
        $quantity = preg_replace("/\D/", "", filter_input(INPUT_POST, 'quantity')); if(empty($quantity)) $quantity = "NULL";
        $film_id = filter_input(INPUT_POST, 'film_id');
        $film_variation_id = filter_input(INPUT_POST, 'film_variation_id'); if($film_id == INDIVIDUAL) $film_variation_id = "NULL";
        $price = filter_input(INPUT_POST, 'price'); if(empty($price)) $price = "NULL";
        $currency = filter_input(INPUT_POST, 'currency');
        $individual_film_name = addslashes(filter_input(INPUT_POST, 'individual_film_name')); if($film_id != INDIVIDUAL) $individual_film_name = "";
        $individual_thickness = filter_input(INPUT_POST, 'individual_thickness'); if(empty($individual_thickness)) $individual_thickness = "NULL"; if($film_id != INDIVIDUAL) $individual_thickness = "NULL";
        $individual_density = filter_input(INPUT_POST, 'individual_density'); if(empty($individual_density)) $individual_density = "NULL"; if($film_id != INDIVIDUAL) $individual_density = "NULL";
        $customers_material = 0; if(filter_input(INPUT_POST, 'customers_material') == 'on') $customers_material = 1;
        $ski = filter_input(INPUT_POST, 'ski'); if(empty($ski)) $ski = "NULL"; if(empty($film_id)) $ski = "NULL";
        $width_ski = filter_input(INPUT_POST, 'width_ski'); if(empty($width_ski)) $width_ski = "NULL"; if($ski != SKI_NONSTANDARD) $width_ski = "NULL";
        
        // Если currency пустой, то получаем значение валюты из справочника цен на плёнку
        if(empty($currency)) {
            $sql = "select currency from film_price where film_variation_id = $film_variation_id order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $currency = $row['currency'];
            }
        }
        
        $lamination1_film_id = filter_input(INPUT_POST, 'lamination1_film_id');
        $lamination1_film_variation_id = filter_input(INPUT_POST, 'lamination1_film_variation_id'); if(empty($lamination1_film_variation_id)) $lamination1_film_variation_id = "NULL"; if($lamination1_film_id == INDIVIDUAL) $lamination1_film_variation_id = "NULL";
        $lamination1_price = filter_input(INPUT_POST, 'lamination1_price'); if(empty($lamination1_price)) $lamination1_price = "NULL";
        $lamination1_currency = filter_input(INPUT_POST, 'lamination1_currency');
        $lamination1_individual_film_name = addslashes(filter_input(INPUT_POST, 'lamination1_individual_film_name')); if($lamination1_film_id != INDIVIDUAL) $lamination1_individual_film_name = "";
        $lamination1_individual_thickness = filter_input(INPUT_POST, 'lamination1_individual_thickness'); if(empty($lamination1_individual_thickness)) $lamination1_individual_thickness = "NULL"; if($lamination1_film_id != INDIVIDUAL) $lamination1_individual_thickness = "NULL";
        $lamination1_individual_density = filter_input(INPUT_POST, 'lamination1_individual_density'); if(empty($lamination1_individual_density)) $lamination1_individual_density = "NULL"; if($lamination1_film_id != INDIVIDUAL) $lamination1_individual_density = "NULL";
        $lamination1_customers_material = 0; if(filter_input(INPUT_POST, 'lamination1_customers_material') == 'on') $lamination1_customers_material = 1;
        $lamination1_ski = filter_input(INPUT_POST, 'lamination1_ski'); if(empty($lamination1_ski)) $lamination1_ski = "NULL"; if(empty($lamination1_film_id)) $lamination1_ski = "NULL";
        $lamination1_width_ski = filter_input(INPUT_POST, 'lamination1_width_ski'); if(empty($lamination1_width_ski)) $lamination1_width_ski = "NULL"; if($lamination1_ski != SKI_NONSTANDARD) $lamination1_width_ski = "NULL";
        
        // Если lamination1_currency пустой, то получаем значение валюты из справочника цен на плёнку
        if(empty($lamination1_currency)) {
            $sql = "select currency from film_price where film_variation_id = $lamination1_film_variation_id order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $lamination1_currency = $row['currency'];
            }
        }
        
        $lamination2_film_id = filter_input(INPUT_POST, 'lamination2_film_id');
        $lamination2_film_variation_id = filter_input(INPUT_POST, 'lamination2_film_variation_id'); if(empty($lamination2_film_variation_id)) $lamination2_film_variation_id = "NULL"; if($lamination2_film_id == INDIVIDUAL) $lamination2_film_variation_id = "NULL";
        $lamination2_price = filter_input(INPUT_POST, 'lamination2_price'); if(empty($lamination2_price)) $lamination2_price = "NULL";
        $lamination2_currency = filter_input(INPUT_POST, 'lamination2_currency');
        $lamination2_individual_film_name = addslashes(filter_input(INPUT_POST, 'lamination2_individual_film_name')); if($lamination2_film_id != INDIVIDUAL) $lamination2_individual_film_name = "";
        $lamination2_individual_thickness = filter_input(INPUT_POST, 'lamination2_individual_thickness'); if(empty($lamination2_individual_thickness)) $lamination2_individual_thickness = "NULL"; if($lamination2_film_id != INDIVIDUAL) $lamination2_individual_thickness = "NULL";
        $lamination2_individual_density = filter_input(INPUT_POST, 'lamination2_individual_density'); if(empty($lamination2_individual_density)) $lamination2_individual_density = "NULL"; if($lamination2_film_id != INDIVIDUAL) $lamination2_individual_density = "NULL";
        $lamination2_customers_material = 0; if(filter_input(INPUT_POST, 'lamination2_customers_material') == 'on') $lamination2_customers_material = 1;
        $lamination2_ski = filter_input(INPUT_POST, 'lamination2_ski'); if(empty($lamination2_ski)) $lamination2_ski = "NULL"; if(empty($lamination2_film_id)) $lamination2_ski = "NULL";
        $lamination2_width_ski = filter_input(INPUT_POST, 'lamination2_width_ski'); if(empty($lamination2_width_ski)) $lamination2_width_ski = "NULL"; if($lamination2_ski != SKI_NONSTANDARD) $lamination2_width_ski = "NULL";
        
        // Если lamination2_currency пустой, то получаем значение валюты из справочника цен на плёнку
        if(empty($lamination2_currency)) {
            $sql = "select currency from film_price where film_variation_id = $lamination2_film_variation_id order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $lamination2_currency = $row['currency'];
            }
        }
        
        $laminator_id = filter_input(INPUT_POST, 'laminator_id'); if(empty($laminator_id)) $laminator_id = "NULL";
        $length = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'length_2') : filter_input(INPUT_POST, 'length'); if(empty($length)) $length = "NULL";
        $stream_width = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'stream_width_2') : filter_input(INPUT_POST, 'stream_width'); if(empty($stream_width)) $stream_width = "NULL";
        $streams_number = filter_input(INPUT_POST, 'streams_number'); if(empty($streams_number)) $streams_number = "NULL";
        $raport = filter_input(INPUT_POST, 'raport'); if(empty($raport)) $raport = "NULL";
        $number_in_raport = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'number_in_raport_2') : filter_input(INPUT_POST, 'number_in_raport'); if(empty($number_in_raport)) $number_in_raport = "NULL";
        $lamination_roller_width = filter_input(INPUT_POST, 'lamination_roller_width'); if(empty($lamination_roller_width)) $lamination_roller_width = "NULL";
        $ink_number = filter_input(INPUT_POST, 'ink_number'); if(null == $ink_number) $ink_number = "NULL";
        
        $manager_id = GetUserId();
        $status_id = ORDER_STATUS_DRAFT; // Статус "Черновик"
        
        // Данные о цвете
        for($i=1; $i<=8; $i++) {
            $ink_var = "ink_$i";
            $$ink_var = filter_input(INPUT_POST, "ink_$i");
            
            $color_var = "color_$i";
            $$color_var = filter_input(INPUT_POST, "color_$i");
            
            $cmyk_var = "cmyk_$i";
            $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
            
            $lacquer_var = "lacquer_$i";
            $$lacquer_var = filter_input(INPUT_POST, "lacquer_$i");
            
            $percent_var = "percent_$i";
            $$percent_var = filter_input(INPUT_POST, "percent_$i");
            if(empty($$percent_var)) $$percent_var = "NULL";
            
            $cliche_var = "cliche_$i";
            $$cliche_var = filter_input(INPUT_POST, "cliche_$i");
        }
        
        $cliche_in_price = 0; if(filter_input(INPUT_POST, 'cliche_in_price') == 'on') $cliche_in_price = 1;
        $cliches_count_flint = filter_input(INPUT_POST, 'cliches_count_flint'); if($cliches_count_flint === null) $cliches_count_flint = "NULL";
        $cliches_count_kodak = filter_input(INPUT_POST, 'cliches_count_kodak'); if($cliches_count_kodak === null) $cliches_count_kodak = "NULL";
        $cliches_count_old = filter_input(INPUT_POST, 'cliches_count_old'); if($cliches_count_old === null) $cliches_count_old = "NULL";
        $customer_pays_for_cliche = 0; if(filter_input(INPUT_POST, 'customer_pays_for_cliche') == 'on') $customer_pays_for_cliche = 1;
        
        $knife = filter_input(INPUT_POST, 'knife'); if(empty($knife)) $knife = 0;
        $knife_in_price = 0; if(filter_input(INPUT_POST, 'knife_in_price') == 'on') $knife_in_price = 1;
        $customer_pays_for_knife = 0; if(filter_input(INPUT_POST, 'customer_pays_for_knife') == 'on') $customer_pays_for_knife = 1;
        $extra_expense = filter_input(INPUT_POST, 'extra_expense'); if(empty($extra_expense)) $extra_expense = 0;
        
        $sql = "insert into calculation (customer_id, name, unit, quantity, work_type_id, "
                . "film_variation_id, price, currency, individual_film_name, individual_thickness, individual_density, customers_material, ski, width_ski, "
                . "lamination1_film_variation_id, lamination1_price, lamination1_currency, lamination1_individual_film_name, lamination1_individual_thickness, lamination1_individual_density, lamination1_customers_material, lamination1_ski, lamination1_width_ski, "
                . "lamination2_film_variation_id, lamination2_price, lamination2_currency, lamination2_individual_film_name, lamination2_individual_thickness, lamination2_individual_density, lamination2_customers_material, lamination2_ski, lamination2_width_ski, "
                . "laminator_id, streams_number, machine_id, length, stream_width, raport, number_in_raport, lamination_roller_width, ink_number, manager_id, status_id, "
                . "ink_1, ink_2, ink_3, ink_4, ink_5, ink_6, ink_7, ink_8, "
                . "color_1, color_2, color_3, color_4, color_5, color_6, color_7, color_8, "
                . "cmyk_1, cmyk_2, cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
                . "lacquer_1, lacquer_2, lacquer_3, lacquer_4, lacquer_5, lacquer_6, lacquer_7, lacquer_8, "
                . "percent_1, percent_2, percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
                . "cliche_1, cliche_2, cliche_3, cliche_4, cliche_5, cliche_6, cliche_7, cliche_8, "
                . "cliche_in_price, cliches_count_flint, cliches_count_kodak, cliches_count_old, customer_pays_for_cliche, "
                . "knife, knife_in_price, customer_pays_for_knife, extra_expense) "
                . "values($customer_id, '$name', '$unit', $quantity, $work_type_id, "
                . "$film_variation_id, $price, '$currency', '$individual_film_name', $individual_thickness, $individual_density, $customers_material, $ski, $width_ski, "
                . "$lamination1_film_variation_id, $lamination1_price, '$lamination1_currency', '$lamination1_individual_film_name', $lamination1_individual_thickness, $lamination1_individual_density, $lamination1_customers_material, $lamination1_ski, $lamination1_width_ski, "
                . "$lamination2_film_variation_id, $lamination2_price, '$lamination2_currency', '$lamination2_individual_film_name', $lamination2_individual_thickness, $lamination2_individual_density, $lamination2_customers_material, $lamination2_ski, $lamination2_width_ski, "
                . "$laminator_id, $streams_number, $machine_id, $length, $stream_width, $raport, $number_in_raport, $lamination_roller_width, $ink_number, $manager_id, $status_id, "
                . "'$ink_1', '$ink_2', '$ink_3', '$ink_4', '$ink_5', '$ink_6', '$ink_7', '$ink_8', "
                . "'$color_1', '$color_2', '$color_3', '$color_4', '$color_5', '$color_6', '$color_7', '$color_8', "
                . "'$cmyk_1', '$cmyk_2', '$cmyk_3', '$cmyk_4', '$cmyk_5', '$cmyk_6', '$cmyk_7', '$cmyk_8', "
                . "'$lacquer_1', '$lacquer_2', '$lacquer_3', '$lacquer_4', '$lacquer_5', '$lacquer_6', '$lacquer_7', '$lacquer_8', "
                . "'$percent_1', '$percent_2', '$percent_3', '$percent_4', '$percent_5', '$percent_6', '$percent_7', '$percent_8', "
                . "'$cliche_1', '$cliche_2', '$cliche_3', '$cliche_4', '$cliche_5', '$cliche_6', '$cliche_7', '$cliche_8', "
                . "$cliche_in_price, $cliches_count_flint, $cliches_count_kodak, $cliches_count_old, $customer_pays_for_cliche, "
                . "$knife, $knife_in_price, $customer_pays_for_knife, $extra_expense)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $insert_id = $executer->insert_id;
        
        // Для самоклеящейся бумаги заполняем список тиражей
        if(empty($error_message) && $work_type_id == WORK_TYPE_SELF_ADHESIVE) {
            $qi = 1;
            $quantity_var = "quantity_$qi";
            
            while(filter_input(INPUT_POST, $quantity_var) !== null) {
                $$quantity_var = filter_input(INPUT_POST, $quantity_var);
                
                $sql = "insert into calculation_quantity (calculation_id, quantity) values($insert_id, ".$$quantity_var.")";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                
                $qi++;
                $quantity_var = "quantity_$qi";
            }
        }
        
        // Если разная ширина ручьёв, заполняем таблицу ширин ручьёв
        if(empty($error_message) && $work_type_id != WORK_TYPE_SELF_ADHESIVE && $stream_width == "NULL") {
            $sci = 1;
            $stream_width_var = "stream_width_$sci";
            
            while (filter_input(INPUT_POST, $stream_width_var) !== null) {
                $$stream_width_var = filter_input(INPUT_POST, $stream_width_var);
                
                $sql = "insert into calculation_stream_width (calculation_id, stream_number, width) values($insert_id, $sci, ".$$stream_width_var.")";
                $executer = new Executer($sql);
                $error_message = $executer->error;
                
                $sci++;
                $stream_width_var = "stream_width_$sci";
            }
        }
        
        if(empty($error_message)) {
            header('Location: create.php?id='.$insert_id);
        }
    }
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$calculation_result = null;

if(!empty($id)) {
    $calculation_result = CalculationResult::Create($id);
}

$row = array();

if(!empty($id)) {
    $sql = "select c.date, c.customer_id, c.name, c.unit, c.quantity, c.work_type_id, "
            . "c.film_variation_id, c.price, c.currency, c.individual_film_name, c.individual_thickness, c.individual_density, c.customers_material, c.ski, c.width_ski, "
            . "(select film_id from film_variation where id = c.film_variation_id) film_id, "
            . "c.lamination1_film_variation_id, c.lamination1_price, c.lamination1_currency, c.lamination1_individual_film_name, c.lamination1_individual_thickness, c.lamination1_individual_density, c.lamination1_customers_material, c.lamination1_ski, c.lamination1_width_ski, "
            . "(select film_id from film_variation where id = c.lamination1_film_variation_id) lamination1_film_id, "
            . "c.lamination2_film_variation_id, c.lamination2_price, c.lamination2_currency, c.lamination2_individual_film_name, c.lamination2_individual_thickness, c.lamination2_individual_density, c.lamination2_customers_material, c.lamination2_ski, c.lamination2_width_ski, "
            . "(select film_id from film_variation where id = c.lamination2_film_variation_id) lamination2_film_id, "
            . "c.laminator_id, c.streams_number, c.machine_id, c.length, c.stream_width, c.raport, c.number_in_raport, c.lamination_roller_width, c.ink_number, c.manager_id, c.status_id, "
            . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
            . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
            . "c.lacquer_1, c.lacquer_2, c.lacquer_3, c.lacquer_4, c.lacquer_5, c.lacquer_6, c.lacquer_7, c.lacquer_8, "
            . "c.percent_1, c.percent_2, c.percent_3, c.percent_4, c.percent_5, c.percent_6, c.percent_7, c.percent_8, "
            . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
            . "cliche_in_price, cliches_count_flint, cliches_count_kodak, cliches_count_old, extracharge, extracharge_cliche, customer_pays_for_cliche, "
            . "knife, extracharge_knife, knife_in_price, customer_pays_for_knife, extra_expense, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
            . "from calculation c where c.id = $id";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    $error_message = $fetcher->error;
}

$date = null;
if(isset($row['date'])) $date = $row['date'];

$customer_id = filter_input(INPUT_POST, 'customer_id');
if($customer_id === null && isset($row['customer_id'])) {
    $customer_id = $row['customer_id'];
}
else if(!empty($new_customer_id)) {
    $customer_id = $new_customer_id;
}

$name = filter_input(INPUT_POST, 'name');
if($name === null && isset($row['name'])) {
    $name = $row['name'];
}

$unit = filter_input(INPUT_POST, 'unit');
if($unit === null && isset($row['unit'])) {
    $unit = $row['unit'];
}

$quantity = filter_input(INPUT_POST, 'quantity');
if($quantity === null && isset($row['quantity'])) {
    $quantity = $row['quantity'];
}
else {
    $quantity = preg_replace("/\D/", "", $quantity);
}

$work_type_id = filter_input(INPUT_POST, 'work_type_id');
if($work_type_id === null && isset($row['work_type_id'])) {
    $work_type_id = $row['work_type_id'];
}

$film_id = filter_input(INPUT_POST, 'film_id');
if($film_id === null && isset($row['film_id'])) {
    $film_id = $row['film_id'];
}

$film_variation_id = filter_input(INPUT_POST, 'film_variation_id');
if($film_variation_id === null && isset($row['film_variation_id'])) {
    $film_variation_id = $row['film_variation_id'];
}

$price = filter_input(INPUT_POST, 'price');
if($price === null && isset($row['price'])) {
    $price = $row['price'];
}

$currency = filter_input(INPUT_POST, 'currency');
if($currency === null && isset($row['currency'])) {
    $currency = $row['currency'];
}

$individual_film_name = filter_input(INPUT_POST, 'individual_film_name');
if($individual_film_name === null && isset($row['individual_film_name'])) {
    $individual_film_name = $row['individual_film_name'];
}

$individual_thickness = filter_input(INPUT_POST, 'individual_thickness');
if($individual_thickness === null && isset($row['individual_thickness'])) {
    $individual_thickness = $row['individual_thickness'];
}

$individual_density = filter_input(INPUT_POST, 'individual_density');
if($individual_density === null && isset($row['individual_density'])) {
    $individual_density = $row['individual_density'];
}

$customers_material = filter_input(INPUT_POST, 'customers_material');
if($customers_material === null && isset($row['customers_material'])) {
    $customers_material = $row['customers_material'];
}

$ski = filter_input(INPUT_POST, 'ski');
if($ski === null && isset($row['ski'])) {
    $ski = $row['ski'];
}
if($ski === null) {
    $ski = SKI_STANDARD; // По умолчанию значение должно быть "Стандартные лыиж".
}

$width_ski = filter_input(INPUT_POST, 'width_ski');
if($width_ski === null && isset($row['width_ski'])) {
    $width_ski = $row['width_ski'];
}

$lamination1_film_id = filter_input(INPUT_POST, 'lamination1_film_id');
if($lamination1_film_id === null && isset($row['lamination1_film_id'])) {
    $lamination1_film_id = $row['lamination1_film_id'];
}

$lamination1_film_variation_id = filter_input(INPUT_POST, 'lamination1_film_variation_id');
if($lamination1_film_variation_id === null && isset($row['lamination1_film_variation_id'])) {
    $lamination1_film_variation_id = $row['lamination1_film_variation_id'];
}

$lamination1_price = filter_input(INPUT_POST, 'lamination1_price');
if($lamination1_price === null && isset($row['lamination1_price'])) {
    $lamination1_price = $row['lamination1_price'];
}

$lamination1_currency = filter_input(INPUT_POST, 'lamination1_currency');
if($lamination1_currency === null && isset($row['lamination1_currency'])) {
    $lamination1_currency = $row['lamination1_currency'];
}

$lamination1_individual_film_name = filter_input(INPUT_POST, 'lamination1_individual_film_name');
if($lamination1_individual_film_name === null && isset($row['lamination1_individual_film_name'])) {
    $lamination1_individual_film_name = $row['lamination1_individual_film_name'];
}

$lamination1_individual_thickness = filter_input(INPUT_POST, 'lamination1_individual_thickness');
if($lamination1_individual_thickness === null && isset($row['lamination1_individual_thickness'])) {
    $lamination1_individual_thickness = $row['lamination1_individual_thickness'];
}

$lamination1_individual_density = filter_input(INPUT_POST, 'lamination1_individual_density');
if($lamination1_individual_density === null && isset($row['lamination1_individual_density'])) {
    $lamination1_individual_density = $row['lamination1_individual_density'];
}

$lamination1_customers_material = filter_input(INPUT_POST, 'lamination1_customers_material');
if($lamination1_customers_material === null && isset($row['lamination1_customers_material'])) {
    $lamination1_customers_material = $row['lamination1_customers_material'];
}

$lamination1_ski = filter_input(INPUT_POST, 'lamination1_ski');
if($lamination1_ski === null && isset($row['lamination1_ski'])) {
    $lamination1_ski = $row['lamination1_ski'];
}

$lamination1_width_ski = filter_input(INPUT_POST, 'lamination1_width_ski');
if($lamination1_width_ski === null && isset($row['lamination1_width_ski'])) {
    $lamination1_width_ski = $row['lamination1_width_ski'];
}

$lamination2_film_id = filter_input(INPUT_POST, 'lamination2_film_id');
if($lamination2_film_id === null && isset($row['lamination2_film_id'])) {
    $lamination2_film_id = $row['lamination2_film_id'];
}

$lamination2_film_variation_id = filter_input(INPUT_POST, 'lamination2_film_variation_id');
if($lamination2_film_variation_id === null && isset($row['lamination2_film_variation_id'])) {
    $lamination2_film_variation_id = $row['lamination2_film_variation_id'];
}

$lamination2_price = filter_input(INPUT_POST, 'lamination2_price');
if($lamination2_price === null && isset($row['lamination2_price'])) {
    $lamination2_price = $row['lamination2_price'];
}

$lamination2_currency = filter_input(INPUT_POST, 'lamination2_currency');
if($lamination2_currency === null && isset($row['lamination2_currency'])) {
    $lamination2_currency = $row['lamination2_currency'];
}

$lamination2_individual_film_name = filter_input(INPUT_POST, 'lamination2_individual_film_name');
if($lamination2_individual_film_name === null && isset($row['lamination2_individual_film_name'])) {
    $lamination2_individual_film_name = $row['lamination2_individual_film_name'];
}

$lamination2_individual_thickness = filter_input(INPUT_POST, 'lamination2_individual_thickness');
if($lamination2_individual_thickness === null && isset($row['lamination2_individual_thickness'])) {
    $lamination2_individual_thickness = $row['lamination2_individual_thickness'];
}

$lamination2_individual_density = filter_input(INPUT_POST, 'lamination2_individual_density');
if($lamination2_individual_density === null && isset($row['lamination2_individual_density'])) {
    $lamination2_individual_density = $row['lamination2_individual_density'];
}

$lamination2_customers_material = filter_input(INPUT_POST, 'lamination2_customers_material');
if($lamination2_customers_material === null && isset($row['lamination2_customers_material'])) {
    $lamination2_customers_material = $row['lamination2_customers_material'];
}

$lamination2_ski = filter_input(INPUT_POST, 'lamination2_ski');
if($lamination2_ski === null && isset($row['lamination2_ski'])) {
    $lamination2_ski = $row['lamination2_ski'];
}

$lamination2_width_ski = filter_input(INPUT_POST, 'lamination2_width_ski');
if($lamination2_width_ski === null && isset($row['lamination2_width_ski'])) {
    $lamination2_width_ski = $row['lamination2_width_ski'];
}

$laminator_id = filter_input(INPUT_POST, 'laminator_id');
if($laminator_id === null && isset($row['laminator_id'])) {
    $laminator_id = $row['laminator_id'];
}

$streams_number = filter_input(INPUT_POST, 'streams_number');
if($streams_number === null && isset($row['streams_number'])) {
    $streams_number = $row['streams_number'];
}

$machine_id = filter_input(INPUT_POST, 'machine_id');
if($machine_id === null && isset($row['machine_id'])) {
    $machine_id = $row['machine_id'];
}

$length = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'length_2') : filter_input(INPUT_POST, 'length');
if(empty($length) && isset($row['length'])) {
    $length = $row['length'];
}

$stream_width = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'stream_width_2') : filter_input(INPUT_POST, 'stream_width');
if(empty($stream_width) && isset($row['stream_width'])) {
    $stream_width = $row['stream_width'];
}

$stream_widths = array();

if($work_type_id != WORK_TYPE_SELF_ADHESIVE && !empty($streams_number)) {
    for($i = 1; $i <= $streams_number; $i++) {
        $stream_width_var = "stream_width_$i";
        $w = filter_input(INPUT_POST, $stream_width_var);
        if(!empty($w)) {
            $stream_widths[$i] = $w;
        }
    }
    
    if(count($stream_widths) == 0) {
        $sql = "select stream_number, width from calculation_stream_width where calculation_id = $id";
        $fetcher = new Fetcher($sql);
        while($stream_widths_row = $fetcher->Fetch()) {
            $stream_widths[intval($stream_widths_row['stream_number'])] = intval($stream_widths_row['width']);
        }
    }
}

$raport = filter_input(INPUT_POST, 'raport');
if($raport === null && isset($row['raport'])) {
    $raport = $row['raport'];
}

$number_in_raport = $work_type_id == WORK_TYPE_SELF_ADHESIVE ? filter_input (INPUT_POST, 'number_in_raport_2') : filter_input(INPUT_POST, 'number_in_raport');
if(empty($number_in_raport) && isset($row['number_in_raport'])) {
    $number_in_raport = $row['number_in_raport'];
}

$lamination_roller_width = filter_input(INPUT_POST, 'lamination_roller_width');
if($lamination_roller_width === null && isset($row['lamination_roller_width'])) {
    $lamination_roller_width = $row['lamination_roller_width'];
}

$ink_number = filter_input(INPUT_POST, 'ink_number');
if($ink_number === null && isset($row['ink_number'])) {
    $ink_number = $row['ink_number'];
}

$manager_id = filter_input(INPUT_POST, 'manager_id');
if($manager_id === null && isset($row['manager_id'])) {
    $manager_id = $row['manager_id'];
}

$status_id = filter_input(INPUT_POST, 'status_id');
if($status_id === null && isset($row['status_id'])) {
    $status_id = $row['status_id'];
}

// Количество новых форм
$new_forms_number = 0;

// Данные о цветах
$ink_1 = null; $ink_2 = null; $ink_3 = null; $ink_4 = null; $ink_5 = null; $ink_6 = null; $ink_7 = null; $ink_8 = null;
$color_1 = null; $color_2 = null; $color_3 = null; $color_4 = null; $color_5 = null; $color_6 = null; $color_7 = null; $color_8 = null;
$cmyk_1 = null; $cmyk_2 = null; $cmyk_3 = null; $cmyk_4 = null; $cmyk_5 = null; $cmyk_6 = null; $cmyk_7 = null; $cmyk_8 = null;
$lacquer_1 = null; $lacquer_2 = null; $lacquer_3 = null; $lacquer_4 = null; $lacquer_5 = null; $lacquer_6 = null; $lacquer_7 = null; $lacquer_8 = null;
$percent_1 = null; $percent_2 = null; $percent_3 = null; $percent_4 = null; $percent_5 = null; $percent_6 = null; $percent_7 = null; $percent_8 = null;
$cliche_1 = null; $cliche_2 = null; $cliche_3 = null; $cliche_4 = null; $cliche_5 = null; $cliche_6 = null; $cliche_7 = null; $cliche_8 = null;

for ($i=1; $i<=$ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = filter_input(INPUT_POST, "ink_$i");
    if(null === $$ink_var && isset($row["ink_$i"])) {
        $$ink_var = $row["ink_$i"];
    }
    
    $color_var = "color_$i";
    $$color_var = filter_input(INPUT_POST, "color_$i");
    if(null === $$color_var && isset($row["color_$i"])) {
        $$color_var = $row["color_$i"];
    }
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = filter_input(INPUT_POST, "cmyk_$i");
    if(null === $$cmyk_var && isset($row["cmyk_$i"])) {
        $$cmyk_var = $row["cmyk_$i"];
    }
    
    $lacquer_var = "lacquer_$i";
    $$lacquer_var = filter_input(INPUT_POST, "lacquer_$i");
    if(null === $$lacquer_var && isset($row["lacquer_$i"])) {
        $$lacquer_var = $row["lacquer_$i"];
    }
    
    $percent_var = "percent_$i";
    $$percent_var = filter_input(INPUT_POST, "percent_$i");
    if(null === $$percent_var && isset($row["percent_$i"])) {
        $$percent_var = $row["percent_$i"];
    }
    
    $cliche_var = "cliche_$i";
    $$cliche_var = filter_input(INPUT_POST, "cliche_$i");
    if(null === $$cliche_var && isset($row["cliche_$i"])) {
        $$cliche_var = $row["cliche_$i"];
    }
    
    if($work_type_id == WORK_TYPE_PRINT) {
        if($$cliche_var != CLICHE_OLD) {
            $new_forms_number++;
        }
    }
}

$cliche_in_price = null;
if(filter_input(INPUT_POST, 'create_calculation_submit') !== null) $cliche_in_price = 0;
if(filter_input(INPUT_POST, 'cliche_in_price') == 'on') $cliche_in_price = 1;
if($cliche_in_price === null && isset($row['cliche_in_price'])) {
    $cliche_in_price = $row['cliche_in_price'];
}

$cliches_count_flint = filter_input(INPUT_POST, 'cliches_count_flint');
if($cliches_count_flint === null && isset($row['cliches_count_flint'])) {
    $cliches_count_flint = $row['cliches_count_flint'];
}
if($cliches_count_flint === null) {
    $cliches_count_flint = 0;
}

$cliches_count_kodak = filter_input(INPUT_POST, 'cliches_count_kodak');
if($cliches_count_kodak === null && isset($row['cliches_count_kodak'])) {
    $cliches_count_kodak = $row['cliches_count_kodak'];
}
if($cliches_count_kodak === null) {
    $cliches_count_kodak = 0;
}

$cliches_count_old = filter_input(INPUT_POST, 'cliches_count_old');
if($cliches_count_old === null && isset($row['cliches_count_old'])) {
    $cliches_count_old = $row['cliches_count_old'];
}
if($cliches_count_old === null) {
    $cliches_count_old = 0;
}

$extracharge = null;
if(isset($row['extracharge'])) {
    $extracharge = $row['extracharge'];
}

$extracharge_cliche = null;
if(isset($row['extracharge_cliche'])) {
    $extracharge_cliche = $row['extracharge_cliche'];
}

$extracharge_knife = null;
if(isset($row['extracharge_knife'])) {
    $extracharge_knife = $row['extracharge_knife'];
}

$customer_pays_for_cliche = null;
if(filter_input(INPUT_POST, 'create_calculation_submit') !== null) $customer_pays_for_cliche = 0; 
if(filter_input(INPUT_POST, 'customer_pays_for_cliche') == 'on') $customer_pays_for_cliche = 1;
if($customer_pays_for_cliche === null && isset($row['customer_pays_for_cliche'])) {
    $customer_pays_for_cliche = $row['customer_pays_for_cliche'];
}

$knife = filter_input(INPUT_POST, 'knife');
if($knife === null && isset($row['knife'])) {
    $knife = $row['knife'];
}

$extracharge_knife = null;
if(isset($row['extracharge_knife'])) {
    $extracharge_knife = $row['extracharge_knife'];
}

$knife_in_price = null;
if(filter_input(INPUT_POST, 'create_calculation_submit') !== null) $knife_in_price = 0;
if(filter_input(INPUT_POST, 'knife_in_price') == 'on') $knife_in_price = 1;
if($knife_in_price === null && isset($row['knife_in_price'])) {
    $knife_in_price = $row['knife_in_price'];
}

$customer_pays_for_knife = null;
if(filter_input(INPUT_POST, 'create_calculation_submit') !== null) $customer_pays_for_knife = 0;
if(filter_input(INPUT_POST, 'customer_pays_for_knife') == 'on') $customer_pays_for_knife = 1;
if($customer_pays_for_knife === null && isset($row['customer_pays_for_knife'])) {
    $customer_pays_for_knife = $row['customer_pays_for_knife'];
}

$extra_expense = filter_input(INPUT_POST, 'extra_expense');
if($extra_expense === null && isset($row['extra_expense'])) {
    $extra_expense = $row['extra_expense'];
}

$num_for_customer = null;
if(isset($row['num_for_customer'])) {
    $num_for_customer = $row['num_for_customer'];
}

// Общее количество новых форм
if($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    $new_forms_number += ($cliches_count_flint + $cliches_count_kodak);
}

// Данные об объёмах заказов
if(!empty(filter_input(INPUT_POST, 'quantity_1'))) {
    $qi = 1;
    $quantity_var = "quantity_$qi";

    while(filter_input(INPUT_POST, $quantity_var) !== null) {
        $$quantity_var = filter_input(INPUT_POST, $quantity_var);
        $qi++;
        $quantity_var = "quantity_$qi";
    }
}
elseif(filter_input(INPUT_GET, 'id') !== null) {
    $sql_quantity = "select quantity from calculation_quantity where calculation_id = $id";
    $fetcher_quantity = new Fetcher($sql_quantity);
    $error_message = $fetcher_quantity->error;

    $qi = 1;
    $quantity_var = "quantity_$qi";

    while($row_quantity = $fetcher_quantity->Fetch()) {
        $$quantity_var = $row_quantity['quantity'];
        $qi++;
        $quantity_var = "quantity_$qi";
    }
}

// Расчёт скрываем:
// 1. При создании нового заказчика
// 2. При создании нового расчёта
// 3. При невалидной форме
// Если показываем рассчёт, то не показываем кнопку отправки.
// И наоборот.
$create_calculation_submit_class = " d-none";

if(null !== filter_input(INPUT_POST, 'create_customer_submit') || 
        null === filter_input(INPUT_GET, 'id') || 
        !$form_valid) {
    $create_calculation_submit_class = "";
}

// Если есть ламинация, а ламинатор пустой, то присваиваем ему значение "Сольвент".
// (В старых расчётах ламинатор может быть не указан, поскольку тогда бессольвента не было.)
if((!empty($lamination1_film_id) || !empty($lamination1_individual_film_name)) && empty($laminator_id)) {
    $laminator_id = LAMINATOR_SOLVENT;
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
            
            .select2 {
                width:100%!important;
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
                background: gray;
                border-color: gray;
            }
            
            ul.ui-autocomplete {
                z-index: 1100;
            }
        </style>
    </head>
    <body>
        <?php
        if(!empty($work_type_id) && $work_type_id == WORK_TYPE_SELF_ADHESIVE && $form_valid) {
            include './right_panel_self_adhesive.php';
        }
        elseif($form_valid) {
            include './right_panel.php';
        }
        
        include '../include/header_zakaz.php';
        ?>
        <!-- Форма создания заказчика -->
        <div id="new_customer" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <div class="modal-header">
                            <i class="fas fa-user"></i>&nbsp;&nbsp;Новый заказчик
                            <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-danger d-none" id="customer_exists" style="font-size: x-large;">Такой заказчик есть</div>
                            <div class="form-group">
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       class="form-control customer_names" 
                                       placeholder="Название компании" 
                                       required="required" />
                                <div class="invalid-feedback">Название компании обязательно</div>
                            </div>
                            <div class="form-group">
                                <input type="text" 
                                       id="customer_person" 
                                       name="customer_person" 
                                       class="form-control" 
                                       placeholder="Имя представителя" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');"
                                       onfocusout="javascript: $(this).attr('id', 'customer_person'); $(this).attr('name', 'customer_person'); $(this).attr('placeholder', 'Имя представителя');" />
                                <div class="invalid-feedback">Имя представителя обязательно</div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-group">
                                        <input type="tel" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               class="form-control" 
                                               placeholder="Номер телефона" 
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" 
                                               onfocusout="javascript: $(this).attr('id', 'customer_phone'); $(this).attr('name', 'customer_phone'); $(this).attr('placeholder', 'Номер телефона');" />
                                        <div class="invalid-feedback">Номер телефона обязательно</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <input type="tel" 
                                               id="customer_extension" 
                                               name="customer_extension" 
                                               class="form-control" 
                                               placeholder="Добавочный" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" 
                                               onfocusout="javascript: $(this).attr('id', 'customer_extension'); $(this).attr('name', 'customer_extension'); $(this).attr('placeholder', 'Добавочный');" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       class="form-control" 
                                       placeholder="E-Mail" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                       onmouseup="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" 
                                       onfocusout="javascript: $(this).attr('id', 'customer_email'); $(this).attr('name', 'customer_email'); $(this).attr('placeholder', 'E-Mail');" />
                                <div class="invalid-feedback">E-Mail обязательно</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark mt-3" data-dismiss="modal">Отмена</button>
                            <button type="submit" id="create_customer_submit" name="create_customer_submit" class="btn btn-dark mt-3">Создать</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Форма ввода нескольких объёмов заказа -->
        <div id="quantities" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Объем заказа
                        <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body" id="quantities_form_body" style="max-height: 80vh; overflow-y: scroll;"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark mt-3" data-dismiss="modal" style="width: 150px;">Отмена</button>
                        <button type="button" id="quantities_submit" name="quantities_submit" class="btn btn-dark mt-3" style="width: 150px;">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            
            $backlink_get = '';
            
            if(filter_input(INPUT_GET, "mode") == "recalc") {
                $backlink_get = "details.php".BuildQueryRemove("mode");
            }
            ?>
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/calculation/<?= $backlink_get ?>">Назад</a>
            <div>
                <!-- Левая половина -->
                <div id="left_side">
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="scroll" name="scroll" />
                        <?php if(null === filter_input(INPUT_GET, 'id') || filter_input(INPUT_GET, 'mode') == 'recalc'): ?>
                        <h1>Новый расчет</h1>
                        <?php else: ?>
                        <h1><?= htmlentities($name) ?></h1>
                        <h2>№<?=$customer_id ?>-<?=$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                        <?php endif; ?>
                        <!-- Заказчик -->
                        <div class="form-group">
                            <label for="customer_id" class="d-block">Заказчик</label>
                            <div class="d-flex justify-content-between">
                                <div class="w-75">
                                    <select id="customer_id" name="customer_id" class="form-control<?=$customer_id_valid ?>" multiple="multiple" required="required">
                                        <option value="">Заказчик...</option>
                                        <?php
                                        $sql = "select id, name from customer order by name";
                                        $fetcher = new Fetcher($sql);
                                        
                                        while ($row = $fetcher->Fetch()):
                                        $selected = '';
                                        if(!empty($customer_id) && $row['id'] == $customer_id) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                        <?php
                                        endwhile;
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-dark d-inline" data-toggle="modal" data-target="#new_customer"><i class="fas fa-plus"></i>&nbsp;Создать нового</button>
                                </div>
                            </div>
                            <div class="invalid-feedback">Заказчик обязательно</div>
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
                                foreach(WORK_TYPES as $item):
                                    $selected = '';
                                    if($item == $work_type_id) {
                                        $selected = " selected='selected'";
                                    }
                                ?>
                                <option value ="<?=$item ?>"<?=$selected ?>><?=WORK_TYPE_NAMES[$item] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Единица заказа -->
                        <?php
                        $kg_checked = ($unit == "kg" || empty($unit)) ? " checked='checked'" : "";
                        $pieces_checked = $unit == "pieces" ? " checked='checked'" : "";
                        ?>
                        <div id="units" class="justify-content-start mt-2 mb-1 d-none">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" id="unit_kg" name="unit" value="kg"<?=$kg_checked ?> />Килограммы
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" id="unit_pieces" name="unit" value="pieces"<?=$pieces_checked ?> />Штуки
                                </label>
                            </div>
                        </div>
                        <!-- Печатная машина -->
                        <div class="print-only self-adhesive-only d-none">
                            <div class="form-group w-100">
                                <label for="machine_id">Печатная машина</label>
                                <select id="machine_id" name="machine_id" class="form-control print-only self-adhesive-only d-none">
                                    <option value="" hidden="hidden" selected="selected">Печатная машина...</option>
                                    <?php
                                    if(!empty($work_type_id)):
                                    foreach(WORK_TYPE_PRINTERS[$work_type_id] as $item):
                                        $selected = '';
                                    if($item == $machine_id) {
                                        $selected = " selected='selected'";
                                    }
                                    ?>
                                    <option value="<?=$item ?>"<?=$selected ?>><?=PRINTER_NAMES[$item].' ('.PRINTER_COLORFULLNESSES[$item].' красок)' ?></option>
                                    <?php
                                    endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Объем заказа -->
                        <div class="row no-print-only print-only d-none">
                            <!-- Объем заказа -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="quantity" id="label_quantity">Объем заказа, кг</label>
                                    <input type="text" 
                                           id="quantity" 
                                           name="quantity" 
                                           class="form-control int-only int-format no-print-only print-only" 
                                           placeholder="Объем заказа" 
                                           value="<?= empty($quantity) ? "" : number_format($quantity, 0, ",", " ") ?>" 
                                           required="required" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" 
                                           onfocusout="javascript: $(this).attr('id', 'quantity'); $(this).attr('name', 'quantity'); $(this).attr('placeholder', 'Объем заказа');" />
                                    <div class="invalid-feedback">Объем заказа обязательно</div>
                                </div>
                            </div>
                        </div>
                        <!-- Количество тиражей -->
                        <div class="form-group self-adhesive-only d-none">
                            <label for="printings_number" class="d-block">Количество тиражей</label>
                            <div class="d-flex justify-content-between">
                                <div class="w-75">
                                    <?php
                                    $printings_number = filter_input(INPUT_POST, 'printings_number');
                                    if(empty($printings_number)) {
                                        $printings_number = 0;
                                        $qi = 1;
                                        $quantity_var = "quantity_$qi";
                                        while(!empty($$quantity_var)) {
                                            $printings_number++;
                                            $qi++;
                                            $quantity_var = "quantity_$qi";
                                        }
                                    }
                                    ?>
                                    <input type="text" 
                                           id="printings_number" 
                                           name="printings_number" 
                                           class="form-control int-only self-adhesive-only<?=$printings_number_valid ?>" 
                                           placeholder="Количество тиражей" 
                                           value="<?= empty($printings_number) ? '' : $printings_number ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'printings_number'); $(this).attr('name', 'printings_number'); $(this).attr('placeholder', 'Количество тиражей');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'printings_number'); $(this).attr('name', 'printings_number'); $(this).attr('placeholder', 'Количество тиражей');" 
                                           onfocusout="javascript: $(this).attr('id', 'printings_number'); $(this).attr('name', 'printings_number'); $(this).attr('placeholder', 'Количество тиражей');" />
                                    <div class="invalid-feedback">Укажите тиражи</div>
                                </div>
                                <div>
                                    <button type="button" id="btn_quantities" class="btn btn-outline-dark d-inline" data-toggle="modal" data-target="#quantities" disabled="disabled">Объем заказов</button>
                                </div>
                            </div>
                        </div>
                        <div class="self-adhesive-only d-none">
                            <label>Тиражи</label>
                            <div id="quantities_list">
                                <div class="row mb-3">
                                    <div class="col-3">
                                    <?php
                                    $qi = 1;
                                    $quantity_var = "quantity_$qi";
                                    while (!empty($$quantity_var)) {
                                        if($qi > 1 && ($qi - 1) % 20 == 0) {
                                            echo "</div>";
                                            echo "</div>";
                                            echo "<div class='row mb_3'>";
                                            echo "<div class='col-3'>";
                                        }
                                        elseif($qi > 1 && ($qi - 1) % 5 == 0) {
                                            echo "</div>";
                                            echo "<div class='col-3'>";
                                        }
                                        echo "<p style='font-size: larger;'>$qi.&nbsp;&nbsp;&nbsp;".DisplayNumber(intval($$quantity_var), 0)." шт</p>";
                                        echo "<input type='hidden' id='quantity_$qi' name='quantity_$qi' value='".$$quantity_var."' />";
                                        $qi++;
                                        $quantity_var = "quantity_$qi";
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Основная плёнка -->
                        <p id="film_title"><span class="font-weight-bold">Пленка</span></p>
                        <p id="main_film_title" class="d-none"><span class="font-weight-bold">Основная пленка</span></p>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="film_id">Марка пленки</label>
                                    <select id="film_id" name="film_id" class="form-control" required="required">
                                        <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                            <?php
                                            $sql = "select id, name from film order by name";
                                            $film_ids = (new Grabber($sql))->result;
                                            
                                            foreach ($film_ids as $row):
                                            $selected = '';
                                            if($row['id'] == $film_id) {
                                                $selected = " selected='selected'";
                                            }
                                            ?>
                                        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                            <?php
                                            endforeach;
                                            
                                            $individual_selected = '';
                                            if(!empty($individual_film_name)) {
                                                $individual_selected = " selected='selected'";
                                            }
                                            ?>
                                        <option disabled="disabled">  </option>
                                        <option value="<?=INDIVIDUAL ?>"<?=$individual_selected ?>>Другая</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="film_variation_id">Толщина, мкм</label>
                                    <select id="film_variation_id" name="film_variation_id" class="form-control" required="required">
                                        <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                            <?php
                                            if(!empty($film_id)) {
                                                $sql = "select id, thickness, weight from film_variation where film_id='$film_id' order by thickness";
                                                $thicknesses = (new Grabber($sql))->result;
                                            
                                                foreach ($thicknesses as $row):
                                                $selected = '';
                                                if($row['id'] == $film_variation_id) {
                                                    $selected = " selected='selected'";
                                                }
                                            ?>
                                        <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                            <?php
                                            endforeach;
                                            }
                                            ?>
                                    </select>
                                </div>
                                <div class="form-group individual_only">
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
                            <div class="col-6 individual_only">
                                <div class="form-group">
                                    <label for="individual_film_name">Название пленки</label>
                                    <input type="text" 
                                           id="individual_film_name" 
                                           name="individual_film_name" 
                                           class="form-control" 
                                           placeholder="Название пленки" 
                                           value="<?=$individual_film_name ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_film_name'); $(this).attr('name', 'individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_film_name'); $(this).attr('name', 'individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_film_name'); $(this).attr('name', 'individual_film_name'); $(this).attr('placeholder', 'Название пленки')" />
                                    <div class="invalid-feedback">Название пленки обязательно</div>
                                </div>
                            </div>
                            <div class="col-6 individual_only">
                                <div class="form-group">
                                    <label for="individual_density">Удельный вес</label>
                                    <input type="text" 
                                           id="individual_density" 
                                           name="individual_density" 
                                           class="form-control float-only" 
                                           placeholder="Удельный вес" 
                                           value="<?= empty($individual_density) ? '' : floatval($individual_density) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" 
                                           onfocusout="javascript: $(this).attr('id', 'individual_density'); $(this).attr('name', 'individual_density'); $(this).attr('placeholder', 'Удельный вес')" />
                                    <div class="invalid-feedback">Удельный вес обязательно</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="price" id="for_price">Цена</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               id="price" 
                                               name="price" 
                                               class="form-control float-only film-price<?=$price_valid ?>" 
                                               placeholder="Цена" 
                                               value="<?= empty($price) ? "" : round($price, 2) ?>"
                                               required="required" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" 
                                               onfocusout="javascript: $(this).attr('id', 'price'); $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" />
                                        <div class="input-group-append">
                                            <select id="currency" name="currency" class="film-currency">
                                                <option value="" hidden="">...</option>
                                                <option value="rub"<?=$currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                <option value="usd"<?=$currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                <option value="euro"<?=$currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                            </select>
                                            <div class="input-group-text d-none" id="currency_text"></div>
                                        </div>
                                        <div class="invalid-feedback">Цена ниже минимальной</div>
                                    </div>
                                </div>
                                <input type="hidden" id="price_min" name="price_min" />
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="ski" id="for_ski">Лыжи</label>
                                    <select name="ski" id="ski" class="form-control">
                                        <?php
                                        $no_ski_class = "";
                                        ?>
                                        <option id="no_ski_option" value="<?= SKI_NO ?>"<?=$no_ski_class ?><?=($ski == SKI_NO ? " selected='selected'" : "") ?>>Без лыж</option>
                                        <option value="<?= SKI_STANDARD ?>"<?=($ski == SKI_STANDARD ? " selected='selected'" : "") ?>>Стандартные лыжи</option>
                                        <option value="<?= SKI_NONSTANDARD ?>"<?=($ski == SKI_NONSTANDARD ? " selected='selected'" : "") ?>>Нестандартные лыжи</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <label class="form-check-label text-nowrap mt-2 mb-2" style="line-height: 25px;">
                                        <?php
                                        $checked = $customers_material == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="customers_material" name="customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="width_ski" id="for_width_ski">Ширина пленки, мм</label>
                                    <input name="width_ski" id="width_ski" type="text" class="form-control int-only" value="<?=$width_ski ?>" placeholder="Ширина пленки" onkeydown="javascript: $('#width_ski_message').hide(); $('#width_machine_message').hide();" />
                                    <div class="invalid-feedback">Ширина пленки обязательно</div>
                                </div>
                                <?php if(!empty($width_ski_valid)): ?>
                                <div class="text-danger" id="width_ski_message">Узкая плёнка</div>
                                <?php
                                endif;
                                if(!empty($width_machine_valid)):
                                ?>
                                <div class="text-danger" id="width_machine_message">Материал шире оборудования</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row no-print-only print-only d-none">
                            <div class="col-6">
                                <div id="show_lamination_1">
                                    <button type="button" class="btn btn-light" onclick="javascript: event.preventDefault(); ShowLamination1();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                                </div>
                            </div>
                            <div class="col-6">
                                
                            </div>
                        </div>
                        <!-- Ламинация 1 -->
                        <div id="form_lamination_1" class="d-none">
                            <div class="d-flex justify-content-between">
                                <div class="p-1">
                                    <p class="font-weight-bold">Ламинация 1</p>
                                </div>
                                <div class="p-0">
                                    <?php
                                    $hide_lamination1_class = "d-block";
                                    if(!empty($lamination2_film_id)) {
                                        $hide_lamination1_class = "d-none";
                                    }
                                    ?>
                                    <div class="<?=$hide_lamination1_class ?>" id="hide_lamination_1">
                                        <button type="button" class="btn btn-link font-weight-bold" onclick="javascript: event.preventDefault(); HideLamination1();"><img src="../images/icons/trash2.svg" />&nbsp;&nbsp;&nbsp;Удалить</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_film_id">Марка пленки</label>
                                        <select id="lamination1_film_id" name="lamination1_film_id" class="form-control">
                                            <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                                <?php
                                                foreach ($film_ids as $row):
                                                $selected = '';
                                                if($row['id'] == $lamination1_film_id) {
                                                    $selected = " selected='selected'";
                                                }
                                                ?>
                                            <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                <?php
                                                endforeach;
                                                
                                                $lamination1_individual_selected = '';
                                                if(!empty($lamination1_individual_film_name)) {
                                                    $lamination1_individual_selected = " selected='selected'";
                                                }
                                                ?>
                                            <option disabled="disabled">  </option>
                                            <option value="<?=INDIVIDUAL ?>"<?=$lamination1_individual_selected ?>>Другая</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_film_variation_id">Толщина, мкм</label>
                                        <select id="lamination1_film_variation_id" name="lamination1_film_variation_id" class="form-control">
                                            <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                                <?php
                                                if(!empty($lamination1_film_id)) {
                                                    $sql = "select id, thickness, weight from film_variation where film_id='$lamination1_film_id' order by thickness";
                                                    $thicknesses = (new Grabber($sql))->result;
                                                
                                                    foreach ($thicknesses as $row):
                                                    $selected = '';
                                                    if($row['id'] == $lamination1_film_variation_id) {
                                                        $selected = " selected='selected'";
                                                    }
                                                ?>
                                            <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                                <?php
                                                endforeach;
                                                }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="form-group lamination1_individual_only">
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
                                <div class="col-6 lamination1_individual_only">
                                    <div class="form-group">
                                        <label for="lamination1_individual_film_name">Название пленки</label>
                                        <input type="text" 
                                               id="lamination1_individual_film_name" 
                                               name="lamination1_individual_film_name" 
                                               class="form-control" 
                                               placeholder="Название пленки" 
                                               value="<?=$lamination1_individual_film_name ?>" 
                                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                               onmouseup="javascript: $(this).attr('id', 'lamination1_individual_film_name'); $(this).attr('name', 'lamination1_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                               onkeyup="javascript: $(this).attr('id', 'lamination1_individual_film_name'); $(this).attr('name', 'lamination1_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                               onfocusout="javascript: $(this).attr('id', 'lamination1_individual_film_name'); $(this).attr('name', 'lamination1_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" />
                                        <div class="invalid-feedback">Название пленки обязательно</div>
                                    </div>
                                </div>
                                <div class="col-6 lamination1_individual_only">
                                    <div class="form-group">
                                        <label for="lamination1_individual_density">Удельный вес</label>
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
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_price" id="for_lamination1_price">Цена</label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   id="lamination1_price" 
                                                   name="lamination1_price" 
                                                   class="form-control float-only film-price<?=$lamination1_price_valid ?>" 
                                                   placeholder="Цена" 
                                                   value="<?= empty($lamination1_price) ? "" : round($lamination1_price, 2) ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination1_price'); $(this).attr('name', 'lamination1_price'); $(this).attr('placeholder', 'Цена');" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination1_price'); $(this).attr('name', 'lamination1_price'); $(this).attr('placeholder', 'Цена');" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination1_price'); $(this).attr('name', 'lamination1_price'); $(this).attr('placeholder', 'Цена');" />
                                            <div class="input-group-append">
                                                <select id="lamination1_currency" name="lamination1_currency" class="film-currency">
                                                    <option value="" hidden="">...</option>
                                                    <option value="rub"<?=$lamination1_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                    <option value="usd"<?=$lamination1_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                    <option value="euro"<?=$lamination1_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                                </select>
                                                <div class="input-group-text d-none" id="lamination1_currency_text"></div>
                                            </div>
                                            <div class="invalid-feedback">Цена ниже минимальной</div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="lamination1_price_min" name="lamination1_price_min" />
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_ski" id="for_lamination1_ski">Лыжи</label>
                                        <select name="lamination1_ski" id="lamination1_ski" class="form-control">
                                            <option value="<?= SKI_STANDARD ?>"<?=($lamination1_ski == SKI_STANDARD ? " selected='selected'" : "") ?>>Стандартные лыжи</option>
                                            <option value="<?= SKI_NONSTANDARD ?>"<?=($lamination1_ski == SKI_NONSTANDARD ? " selected='selected'" : "") ?>>Нестандартные лыжи</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-check">
                                                <label class="form-check-label text-nowrap mt-2 mb-2" style="line-height: 25px;">
                                                    <?php
                                                    $checked = $lamination1_customers_material == 1 ? " checked='checked'" : "";
                                                    ?>
                                                    <input type="checkbox" class="form-check-input" id="lamination1_customers_material" name="lamination1_customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                        $solvent_yes_checked = "";
                                        $solvent_no_checked = "";
                                        if($laminator_id == LAMINATOR_SOLVENT) {
                                            $solvent_yes_checked = " checked='checked'";
                                        }
                                        elseif($laminator_id == LAMINATOR_SOLVENTLESS) {
                                            $solvent_no_checked = " checked='checked'";
                                        }
                                        ?>
                                        <div class="col-8">
                                            <div class="form-check-inline">
                                                <label class="form-check-label mt-3">
                                                    <input type="radio" class="form-check-input" id="solvent_yes" name="laminator_id" value="<?= LAMINATOR_SOLVENT ?>"<?=$solvent_yes_checked ?> />Сольвент
                                                </label>
                                            </div>
                                            <div class="form-check-inline">
                                                <label class="form-check-label mt-3">
                                                    <input type="radio" class="form-check-input" id="solvent_no" name="laminator_id" value="<?= LAMINATOR_SOLVENTLESS ?>"<?=$solvent_no_checked ?> />Бессольвент
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lamination1_width_ski" id="for_lamination1_width_ski">Ширина пленки, мм</label>
                                        <input name="lamination1_width_ski" id="lamination1_width_ski" type="text" class="form-control int-only" value="<?=$lamination1_width_ski ?>" placeholder="Ширина пленки" onkeydown="javascript: $('#lamination1_width_ski_message').hide(); $('#lamination1_width_machine_message').hide();" />
                                        <div class="invalid-feedback">Ширина пленки обязательно</div>
                                    </div>
                                    <?php if(!empty($lamination1_width_ski_valid)): ?>
                                    <div class="text-danger" id="lamination1_width_ski_message">Узкая плёнка</div>
                                    <?php
                                    endif;
                                    if(!empty($lamination1_width_machine_valid)):
                                    ?>
                                    <div class="text-danger" id="lamination1_width_machine_message">Материал шире оборудования</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div id="show_lamination_2">
                                        <button type="button" class="btn btn-light" onclick="javascript: event.preventDefault(); ShowLamination2();"><i class="fas fa-plus"></i>&nbsp;Добавить ламинацию</button>
                                    </div> 
                                </div>
                                <div class="col-6"></div>
                            </div>
                            <!-- Ламинация 2 -->
                            <div id="form_lamination_2" class="d-none">
                                <div class="d-flex justify-content-between">
                                    <div class="p-1">
                                        <p class="font-weight-bold">Ламинация 2</p>
                                    </div>
                                    <div class="p-0">
                                        <button type="button" class="btn btn-link font-weight-bold" onclick="javascript: event.preventDefault(); HideLamination2();"><img src="../images/icons/trash2.svg" />&nbsp;&nbsp;&nbsp;Удалить</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_film_id">Марка пленки</label>
                                            <select id="lamination2_film_id" name="lamination2_film_id" class="form-control">
                                                <option value="" hidden="hidden" selected="selected">Марка пленки...</option>
                                                    <?php
                                                    foreach ($film_ids as $row):
                                                    $selected = '';
                                                    if($row['id'] == $lamination2_film_id) {
                                                        $selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['name'] ?></option>
                                                    <?php
                                                    endforeach;
                                                    
                                                    $lamination2_individual_selected = '';
                                                    if(!empty($lamination2_individual_film_name)) {
                                                        $lamination2_individual_selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option disabled="disabled">  </option>
                                                <option value="<?=INDIVIDUAL ?>"<?=$lamination2_individual_selected ?>>Другая</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_film_variation_id">Толщина, мкм</label>
                                            <select id="lamination2_film_variation_id" name="lamination2_film_variation_id" class="form-control">
                                                <option value="" hidden="hidden" selected="selected">Толщина...</option>
                                                    <?php
                                                    if(!empty($lamination2_film_id)):
                                                    $sql = "select id, thickness, weight from film_variation where film_id='$lamination2_film_id' order by thickness";
                                                    $variations = (new Grabber($sql))->result;
                                                    
                                                    foreach ($variations as $row):
                                                    $selected = "";
                                                    if($row['id'] == $lamination2_film_variation_id) {
                                                        $selected = " selected='selected'";
                                                    }
                                                    ?>
                                                <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['thickness'] ?> мкм <?=$row['weight'] ?> г/м<sup>2</sup></option>
                                                    <?php
                                                    endforeach;
                                                    endif;
                                                    ?>
                                            </select>
                                        </div>
                                        <div class="form-group lamination2_individual_only">
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
                                    <div class="col-6 lamination2_individual_only">
                                        <div class="form-group">
                                            <label for="lamination2_individual_film_name">Название пленки</label>
                                            <input type="text" 
                                                   id="lamination2_individual_film_name" 
                                                   name="lamination2_individual_film_name" 
                                                   class="form-control" 
                                                   placeholder="Название пленки" 
                                                   value="<?=$lamination2_individual_film_name ?>" 
                                                   onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                   onmouseup="javascript: $(this).attr('id', 'lamination2_individual_film_name'); $(this).attr('name', 'lamination2_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                   onkeyup="javascript: $(this).attr('id', 'lamination2_individual_film_name'); $(this).attr('name', 'lamination2_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" 
                                                   onfocusout="javascript: $(this).attr('id', 'lamination2_individual_film_name'); $(this).attr('name', 'lamination2_individual_film_name'); $(this).attr('placeholder', 'Название пленки')" />
                                            <div class="invalid-feedback">Название пленки обязательно</div>
                                        </div>
                                    </div>
                                    <div class="col-6 lamination2_individual_only">
                                        <div class="form-group">
                                            <label for="lamination2_individual_density">Удельный вес</label>
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
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_price" id="for_lamination2_price">Цена</label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       id="lamination2_price" 
                                                       name="lamination2_price" 
                                                       class="form-control float-only film-price<?=$lamination2_price_valid ?>" 
                                                       placeholder="Цена" 
                                                       value="<?= empty($lamination2_price) ? "" : round($lamination2_price, 2) ?>" 
                                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                       onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                                       onmouseup="javascript: $(this).attr('id', 'lamination2_price'); $(this).attr('name', 'lamination2_price'); $(this).attr('placeholder', 'Цена');" 
                                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                                       onkeyup="javascript: $(this).attr('id', 'lamination2_price'); $(this).attr('name', 'lamination2_price'); $(this).attr('placeholder', 'Цена');" 
                                                       onfocusout="javascript: $(this).attr('id', 'lamination2_price'); $(this).attr('name', 'lamination2_price'); $(this).attr('placeholder', 'Цена');" />
                                                <div class="input-group-append">
                                                    <select id="lamination2_currency" name="lamination2_currency" class="film-currency">
                                                        <option value="" hidden="">...</option>
                                                        <option value="rub"<?=$lamination2_currency == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                                        <option value="usd"<?=$lamination2_currency == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                                        <option value="euro"<?=$lamination2_currency == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                                    </select>
                                                    <div class="input-group-text d-none" id="lamination2_currency_text"></div>
                                                </div>
                                                <div class="invalid-feedback">Цена ниже минимальной</div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="lamination2_price_min" name="lamination2_price_min" />
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_ski" id="for_lamination2_ski">Лыжи</label>
                                            <select name="lamination2_ski" id="lamination2_ski" class="form-control">
                                                <option value="<?= SKI_STANDARD ?>"<?=($lamination2_ski == SKI_STANDARD ? " selected='selected'" : "") ?>>Стандартные лыжи</option>
                                                <option value="<?= SKI_NONSTANDARD ?>"<?=($lamination2_ski == SKI_NONSTANDARD ? " selected='selected'" : "") ?>>Нестандартные лыжи</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <label class="form-check-label text-nowrap mt-2 mb-2" style="line-height: 25px;">
                                                <?php
                                                $checked = $lamination2_customers_material == 1 ? " checked='checked'" : "";
                                                ?>
                                                <input type="checkbox" class="form-check-input" id="lamination2_customers_material" name="lamination2_customers_material" value="on"<?=$checked ?>>Сырьё заказчика
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lamination2_width_ski" id="for_lamination2_width_ski">Ширина пленки, мм</label>
                                            <input name="lamination2_width_ski" id="lamination2_width_ski" type="text" class="form-control int-only" value="<?=$lamination2_width_ski ?>" placeholder="Ширина пленки" onkeydown="javascript: $('#lamination2_width_ski_message').hide(); $('#lamination2_width_machine_message').hide();" />
                                            <div class="invalid-feedback">Ширина пленки обязательно</div>
                                        </div>
                                        <?php if(!empty($lamination2_width_ski_valid)): ?>
                                        <div class="text-danger" id="lamination2_width_ski_message">Узкая плёнка</div>
                                        <?php
                                        endif;
                                        if(!empty($lamination2_width_machine_valid)):
                                        ?>
                                        <div class="text-danger" id="lamination2_width_machine_message">Материал шире оборудования</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p id="film_title" class="d-none no-print-only print-only self-adhesive-only"><span class="font-weight-bold">Ручьи</span></p>
                        <div class="row">
                            <!-- Ширина этикетки -->
                            <div class="col-6 self-adhesive-only d-none">
                                <div class="form-group">
                                    <label for="stream_width_2">Ширина этикетки, мм</label>
                                    <input type="text" 
                                           id="stream_width_2" 
                                           name="stream_width_2" 
                                           class="form-control float-only self-adhesive-only d-none" 
                                           required="required" 
                                           placeholder="Ширина этикетки, мм" 
                                           value="<?= empty($stream_width) ? "" : floatval($stream_width) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'stream_width_2'); $(this).attr('name', 'stream_width_2'); $(this).attr('placeholder', 'Ширина этикетки, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'stream_width_2'); $(this).attr('name', 'stream_width_2'); $(this).attr('placeholder', 'Ширина этикетки, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'stream_width_2'); $(this).attr('name', 'stream_width_2'); $(this).attr('placeholder', 'Ширина этикетки, мм');" />
                                </div>
                            </div>
                            <!-- Длина этикетки -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="length">Длина этикетки, мм</label>
                                    <input type="text" 
                                           readonly="readonly" 
                                           id="length" 
                                           name="length" 
                                           class="form-control print-only d-none" 
                                           placeholder="Длина этикетки, мм" 
                                           value="<?= empty($length) ? "" : floatval($length) ?>" />
                                    <div class="invalid-feedback">Длина этикетки обязательно</div>
                                </div>
                            </div>
                            <!-- Длина этикетки (для самоклеящейся бумаги) -->
                            <div class="col-6 self-adhesive-only d-none">
                                <div class="form-group">
                                    <label for="length_2">Длина этикетки, мм</label>
                                    <input type="text" 
                                           id="length_2" 
                                           name="length_2" 
                                           class="form-control float-only self-adhesive-only d-none" 
                                           required="required" 
                                           placeholder="Длина этикетки, мм" 
                                           value="<?= empty($length) ? "" : floatval($length) ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'length_2'); $(this).attr('name', 'length_2'); $(this).attr('placeholder', 'Длина этикетки, мм');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'length_2'); $(this).attr('name', 'length_2'); $(this).attr('placeholder', 'Длина этикетки, мм');" 
                                           onfocusout="javascript: $(this).attr('id', 'length_2'); $(this).attr('name', 'length_2'); $(this).attr('placeholder', 'Длина этикетки, мм');" />
                                    <div class="invalid-feedback">Длина этикетки обязательно</div>
                                </div>
                            </div>
                            <!-- Количество ручьёв -->
                            <div class="col-6 no-print-only print-only self-adhesive-only d-none">
                                <div class="form-group">
                                    <label for="streams_number">Количество ручьев</label>
                                    <input type="text" 
                                           id="streams_number" 
                                           name="streams_number" 
                                           class="form-control int-only no-print-only print-only self-adhesive-only d-none" 
                                           required="required"
                                           placeholder="Количество ручьев" 
                                           value="<?=$streams_number ?>" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                           onmouseup="javascript: $(this).attr('id', 'streams_number'); $(this).attr('name', 'streams_number'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'streams_number'); $(this).attr('name', 'streams_number'); $(this).attr('placeholder', 'Количество ручьев');" 
                                           onfocusout="javascript: $(this).attr('id', 'streams_number'); $(this).attr('name', 'streams_number'); $(this).attr('placeholder', 'Количество ручьев');" />
                                    <div class="invalid-feedback">Количество ручьев обязательно</div>
                                </div>
                            </div>
                            <!-- Рапорт -->
                            <div class="col-6 print-only self-adhesive-only d-none">
                                <div class="form-group">
                                    <label for="raport">Рапорт</label>
                                    <div id="raport_control">
                                        <?php
                                        if(!empty($machine_id)):
                                            $sql = "select value from raport where active = 1 and machine_id = $machine_id ";
                                            if(!empty($raport)) {
                                                $sql .= "union select value from raport where active = 0 and machine_id = $machine_id and value = $raport ";
                                            }
                                            $sql .= "order by value";
                                            $grabber = new Grabber($sql);
                                            $raports = $grabber->result;
                                            $in_list = false;
                                            
                                            if(!empty($raport)) {
                                                foreach($raports as $row) {
                                                    if($row['value'] == $raport) {
                                                        $in_list = true;
                                                    }
                                                }
                                            }
                                            
                                            if($in_list || empty($raport)):
                                            ?>
                                        <select id="raport" name="raport" class="form-control print-only self-adhesive-only<?=$raport_valid ?>">
                                            <option value="" hidden="hidden">Рапорт...</option>
                                            <?php
                                            if(!empty($raports)):
                                                foreach($raports as $row):
                                                $selected = "";
                                            if(!empty($raport) && $row['value'] == $raport) {
                                                $selected = " selected='selected'";
                                            }
                                            ?>
                                            <option<?=$selected ?>><?= floatval($row['value']) ?></option>
                                            <?php
                                            endforeach;
                                            ?>
                                            <option disabled="disabled">-</option>
                                            <option value="-1">Добавить вручную...</option>
                                            <?php endif; ?>
                                        </select>
                                        <?php else: ?>
                                        <input type="text" id="raport" name="raport" placeholder="Рапорт, мм" value="<?=$raport ?>" class="form-control print-only self-adhesive-only" required="required" />
                                            <?php
                                            endif;
                                            else:
                                            ?>
                                        <select id="raport" name="raport" class="form-control print-only self-adhesive-only d-none">
                                            <option value="" hidden="hidden">Рапорт...</option>
                                        </select>
                                        <?php endif; ?>
                                        <div class="invalid-feedback">Рапорт обязательно</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Количество этикеток в рапорте -->
                            <div class="col-6 print-only d-none">
                                <div class="form-group">
                                    <label for="number_in_raport">Количество этикеток в рапорте</label>
                                    <select id="number_in_raport" name="number_in_raport" class="form-control print-only d-none">
                                        <option value="" hidden="hidden" selected="selected">Кол-во эт. в рапорте...</option>
                                        <?php
                                        for($i = 1; $i <= 10; $i++):
                                        $selected = "";
                                        if($i == $number_in_raport) $selected = " selected='selected'";
                                        ?>
                                        <option<?=$selected ?>><?=$i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Количество этикеток в рапорте (для самоклейки) -->
                            <div class="col-6 self-adhesive-only d-none">
                                <div class="form-group">
                                    <label for="number_in_raport_2">Количество этикеток в рапорте</label>
                                    <input type="text" 
                                           readonly="readonly" 
                                           id="number_in_raport_2" 
                                           name="number_in_raport_2" 
                                           class="form-control self-adhesive-only d-none" 
                                           placeholder="Количество этикеток в рапорте" 
                                           value="<?= empty($number_in_raport) ? "" : intval($number_in_raport) ?>" />
                                    <div class="invalid-feedback">Количество этикеток в рапорте обязательно</div>
                                </div>
                            </div>
                            <div class="col-6 self-adhesive-only">
                                <div class="form-group">
                                    <label id="gap_fact">
                                        <?php
                                        if(!empty($raport) && !empty($length) && !empty($number_in_raport)) {
                                            $f_raport = floatval($raport);
                                            $f_length = floatval($length);
                                            $f_number_in_raport = floatval($number_in_raport);
                                            $gap_fact = ($f_raport - ($f_length * $f_number_in_raport)) / $f_number_in_raport;
                                            $s_gap_fact = DisplayNumber($gap_fact, 2);
                                            echo "Зазор между этикетками $s_gap_fact мм";
                                        }
                                        ?>
                                    </label>
                                </div>
                            </div>
                            <!-- Ширина ламинирующего вала -->
                            <div class="col-6 lam-only d-none">
                                <div class="form-group">
                                    <label for="lamination_roller_width">Ширина ламинирующего вала, мм</label>
                                    <div id="lamination_roller_width_control">
                                        <?php
                                        if(!empty($laminator_id)):
                                            $sql = "select value from norm_laminator_roller where laminator_id = $laminator_id and active = 1 ";
                                            if(!empty($stream_width) && !empty($streams_number)) {
                                                $min_width = $stream_width * $streams_number;
                                                $sql .= "and value >= $min_width + 5 and value <= $min_width + 12 ";
                                            }
                                            if(!empty($lamination_roller_width)) {
                                                $sql .= "union select value from norm_laminator_roller where laminator_id = $laminator_id and active = 0 and value = $lamination_roller_width ";
                                            }
                                            $sql .= "order by value";
                                            $grabber = new Grabber($sql);
                                            $lamination_roller_widths = $grabber->result;
                                            $in_list = false;
                                            
                                            if(!empty($lamination_roller_width)) {
                                                foreach($lamination_roller_widths as $row) {
                                                    if($row['value'] == $lamination_roller_width) {
                                                        $in_list = true;
                                                    }
                                                }
                                            }
                                        
                                            if($in_list || empty($lamination_roller_width)):
                                            ?>
                                            <select id="lamination_roller_width" name="lamination_roller_width" class="form-control lam-only d-none">
                                                <?php
                                                if(!empty($lamination_roller_widths) && count($lamination_roller_widths) > 0):
                                                ?>
                                                <option value='' hidden='hidden'>Ширина ламинирующего вала...</option>
                                                <?php
                                                foreach($lamination_roller_widths as $row):
                                                    $selected = "";
                                                    if($row['value'] == $lamination_roller_width) { 
                                                        $selected = " selected='selected'";
                                                    }
                                                ?>
                                                <option<?=$selected ?>><?=$row['value'] ?></option>
                                                <?php
                                                endforeach;
                                                ?>
                                                <?php else: ?>
                                                <option value="" hidden="hidden">Нет вала</option>
                                                <?php endif; ?>
                                                <option disabled="disabled">-</option>
                                                <option value="-1">Добавить вручную...</option>
                                            </select>
                                            <?php else: ?>
                                            <input type='text' id='lamination_roller_width' name='lamination_roller_width' placeholder='Ширина ламинирующего вала, мм' value="<?=$lamination_roller_width ?>" class='form-control int-only lam-only' required='required' />
                                            <?php endif; ?>
                                        <?php else: ?>
                                        <select id="lamination_roller_width" name="lamination_roller_width" class="form-control lam-only d-none">
                                            <option value="" hidden="hidden">Ширина ламинирующего вала...</option>
                                        </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Ширина ручья -->
                            <div class="col-6 no-print-only print-only d-none">
                                <div class="form-group">
                                    <label for="stream_width">Ширина ручья, мм</label>
                                    <?php
                                    $disabled_attribute = '';
                                    if((null != filter_input(INPUT_GET, 'id') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) || 
                                            (null !== filter_input(INPUT_POST, 'create_calculation_submit') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width))) {
                                        $disabled_attribute = "disabled='disabled' ";
                                    }
                                    ?>
                                    <input type="text" <?=$disabled_attribute ?>
                                           id="stream_width" 
                                           name="stream_width" 
                                           class="form-control float-only no-print-only print-only d-none" 
                                           required="required" 
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
                            <!-- Разная ширина ручьёв -->
                            <?php
                            $stream_widths_many_visible_class = " d-none";
                            $stream_widths_many_checked = "";
                            if((null != filter_input(INPUT_GET, 'id') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) || 
                                    (null !== filter_input(INPUT_POST, 'create_calculation_submit') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width))) {
                                $stream_widths_many_visible_class = '';
                                $stream_widths_many_checked = " checked='checked'";
                            }
                            ?>
                            <div class="col-6<?=$stream_widths_many_visible_class ?>" id="stream_widths_many_wrapper">
                                <div class="form-check mt-4">
                                    <label class="form-check-label text-nowrap mt-2 mb-2" style="line-height: 25px;">
                                        <input type="checkbox" class="form-check-input" id="stream_widths_many" name="stream_widths_many" value="on"<?=$stream_widths_many_checked ?>>Разная ширина ручьёв
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Ширины ручьёв -->
                        <?php
                        $stream_widths_many_row_visible_class = " d-none";
                        if((null != filter_input(INPUT_GET, 'id') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width)) || 
                                (null !== filter_input(INPUT_POST, 'create_calculation_submit') && $work_type_id != WORK_TYPE_SELF_ADHESIVE && empty($stream_width))) {
                            $stream_widths_many_row_visible_class = '';
                        }
                        ?>
                        <div class="row<?=$stream_widths_many_row_visible_class ?>" id="stream_widths_many_row">
                            <?php foreach($stream_widths as $key => $value): ?>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="stream_width_<?=$key ?>">Ширина ручья <?=$key ?>, мм</label>
                                    <input type="text" class="form-control" id="stream_width_<?=$key ?>" name="stream_width_<?=$key ?>" value="<?=$value ?>" required="required" />
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Количество красок (для самоклейки возможно 0) -->
                        <p id="film_title" class="d-none print-only self-adhesive-only"><span class="font-weight-bold">Краска</span></p>
                        <div class="print-only self-adhesive-only d-none">
                            <div class="row">
                                <?php
                                $ink_number_class = " col-12";
                                if($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                    $ink_number_class = " col-3";
                                }
                                ?>
                                <div class="form-group<?=$ink_number_class ?>" id="ink-col-ink">
                                    <label for="ink_number">Количество красок</label>
                                    <select id="ink_number" name="ink_number" class="form-control print-only self-adhesive-only d-none">
                                        <option value="" hidden="hidden">Количество красок...</option>
                                        <?php
                                        if($work_type_id == WORK_TYPE_SELF_ADHESIVE): 
                                        $selected = "";
                                        if($ink_number == 0) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                    <option<?=$selected ?>>0</option>
                                        <?php
                                        endif;
                                        if(!empty($ink_number) || !empty($machine_id)):
                                        for($i = 1; $i <= PRINTER_COLORFULLNESSES[$machine_id]; $i++):
                                        $selected = "";
                                        if($ink_number == $i) {
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
                                <div class="form-group col-3 self-adhesive-only" id="ink-col-cliche-flint">
                                    <label for="cliches_count_flint">Кол-во новых Флинт</label>
                                    <input type="text" 
                                           id="cliches_count_flint" 
                                           name="cliches_count_flint" 
                                           value="<?=$cliches_count_flint ?>" 
                                           class="form-control int-only self-adhesive-only d-none" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                           onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                           onmouseup="javascript: $(this).attr('id', 'cliches_count_flint'); $(this).attr('name', 'cliches_count_flint');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'cliches_count_flint'); $(this).attr('name', 'cliches_count_flint');" 
                                           onfocusout="javascript: $(this).attr('id', 'cliches_count_flint'); $(this).attr('name', 'cliches_count_flint');" />
                                </div>
                                <div class="form-group col-3 self-adhesive-only" id="ink-col-cliche-kodak">
                                    <label for="cliches_count_kodak">Кол-во новых Кодак</label>
                                    <input type="text" 
                                           id="cliches_count_kodak" 
                                           name="cliches_count_kodak" 
                                           value="<?=$cliches_count_kodak ?>" 
                                           class="form-control int-only self-adhesive-only d-none" 
                                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                           onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                           onmouseup="javascript: $(this).attr('id', 'cliches_count_kodak'); $(this).attr('name', 'cliches_count_kodak');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                           onkeyup="javascript: $(this).attr('id', 'cliches_count_kodak'); $(this).attr('name', 'cliches_count_kodak');" 
                                           onfocusout="javascript: $(this).attr('id', 'cliches_count_kodak'); $(this).attr('name', 'cliches_count_kodak');" />
                                </div>
                                <div class="form-group col-3 self-adhesive-only" id="ink-col-cliche-old">
                                    <label for="cliches_count_old">Кол-во старых форм</label>
                                    <input type="text" id="cliches_count_old" name="cliches_count_old" value="<?=$cliches_count_old ?>" class="form-control int-only self-adhesive-only" readonly="readonly" />
                                </div>
                            </div>
                            <!-- Каждая краска -->
                            <?php
                            for($i=1; $i<=8; $i++):
                            $block_class = " d-none";
                            $ink_required = "";

                            if(!empty($ink_number) && is_numeric($ink_number) && $i <= $ink_number) {
                                $block_class = "";
                                $ink_required = " required='required'";
                            }
                            ?>
                            <div class="row ink_block<?=$block_class ?>" id="ink_block_<?=$i ?>">
                                <?php
                                $ink_class = " col-12";
                                $cmyk_class = " d-none";
                                $lacquer_class = " d-none";
                                $color_class = " d-none";
                                $percent_class = " d-none";
                                $cliche_class = " d-none";
                            
                                $ink_var_name = "ink_$i";
                        
                                if($$ink_var_name == "white") {
                                    $ink_class = " col-6";
                                    
                                    if($work_type_id == WORK_TYPE_PRINT) {
                                        $percent_class = " col-3";
                                        $cliche_class = " col-3";
                                    }
                                    elseif ($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        $percent_class = " col-6";
                                    }
                                }
                                elseif($$ink_var_name == "lacquer") {
                                    if($work_type_id == WORK_TYPE_PRINT) {
                                        $ink_class = " col-3";
                                        $lacquer_class = " col-3";
                                    }
                                    elseif($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        $ink_class = " col-6";
                                    }
                                    
                                    if($work_type_id == WORK_TYPE_PRINT) {
                                        $percent_class = " col-3";
                                        $cliche_class = " col-3";
                                    }
                                    elseif ($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        $percent_class = " col-6";
                                    }
                                }
                                elseif($$ink_var_name == "panton") {
                                    $ink_class = " col-3";
                                    $color_class = " col-3";
                                    
                                    if($work_type_id == WORK_TYPE_PRINT) {
                                        $percent_class = " col-3";
                                        $cliche_class = " col-3";
                                    }
                                    elseif($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        $percent_class = " col-6";
                                    }
                                }
                                elseif($$ink_var_name == "cmyk") {
                                    $ink_class = " col-3";
                                    $cmyk_class = " col-3";
                                    
                                    if($work_type_id == WORK_TYPE_PRINT) {
                                        $percent_class = " col-3";
                                        $cliche_class = " col-3";
                                    }
                                    elseif($work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        $percent_class = " col-6";
                                    }
                                }
                                ?>
                                <div class="form-group<?=$ink_class ?>" id="ink_group_<?=$i ?>">
                                    <label for="ink_<?=$i ?>"><?=$i ?> цвет</label>
                                    <select id="ink_<?=$i ?>" name="ink_<?=$i ?>" class="form-control ink" data-id="<?=$i ?>"<?=$ink_required ?>>
                                        <option value="" hidden="hidden" selected="selected">Цвет...</option>
                                        <?php
                                        $cmyk_selected = "";
                                        $panton_selected = "";
                                        $white_selected = "";
                                        $lacquer_selected = "";
                                    
                                        $selected_var_name = $$ink_var_name."_selected";
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
                                <div class="form-group<?=$lacquer_class ?>" id="lacquer_group_<?=$i ?>">
                                    <?php
                                    $lacquer_var = "lacquer_$i";
                                    $lacquer_var_valid = 'lacquer_'.$i.'_valid';
                                    ?>
                                    <label for="lacquer_<?=$i ?>">Лак</label>
                                    <select id="lacquer_<?=$i ?>" name="lacquer_<?=$i ?>" class="form-control lacquer<?=$$lacquer_var_valid ?>" data-id="<?=$i ?>">
                                        <option value="" hidden="hidden" selected="selected">Лак...</option>
                                        <?php
                                        $glossy_selected = "";
                                        $matte_selected = "";
                                        
                                        $lacquer_var_selected = $$lacquer_var.'_selected';
                                        $$lacquer_var_selected = " selected='selected'";
                                        ?>
                                        <option value="glossy"<?=$glossy_selected ?>>Глянцевый</option>
                                        <option value="matte"<?=$matte_selected ?>>Матовый</option>
                                    </select>
                                    <div class="invalid-feedback">Выберите лак</div>
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
                                        <div class="invalid-feedback">Не менее <?=$min_percent ?></div>
                                    </div>
                                </div>
                                <div class="form-group<?=$cliche_class ?>" id="cliche_group_<?=$i ?>">
                                    <label for="cliche_<?=$i ?>">Форма</label>
                                    <select id="cliche_<?=$i ?>" name="cliche_<?=$i ?>" class="form-control cliche">
                                        <?php
                                        $old_selected = "";
                                        $flint_selected = "";
                                        $kodak_selected = "";
                                    
                                        $cliche_var = "cliche_$i";
                                        $cliche_selected_var = $$cliche_var."_selected";
                                        $$cliche_selected_var = " selected='selected'";
                                        ?>
                                        <option value="<?= CLICHE_FLINT ?>"<?=$flint_selected ?>>Новая Флинт</option>
                                        <option value="<?= CLICHE_KODAK ?>"<?=$kodak_selected ?>>Новая Кодак</option>
                                        <option value="<?= CLICHE_OLD ?>"<?=$old_selected ?>>Старая</option>
                                    </select>
                                </div>
                            </div>
                            <?php
                            endfor;
                            ?>
                        </div>
                        <!-- Самая нижняя часть формы -->
                        <p id="film_title"><span class="font-weight-bold">Дополнительно</span></p>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="extra_expense" id="label_extra_expense">Дополнительные расходы с шт, руб</label>
                                    <input type="text" 
                                            id="extra_expense" 
                                            name="extra_expense" 
                                            class="form-control float-only" 
                                            value="<?= round($extra_expense, 2) ?>" 
                                            placeholder="Дополнительные расходы, руб" 
                                            onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                            onmouseup="javascript: $(this).attr('id', 'extra_expense'); $(this).attr('name', 'extra_expense'); $(this).attr('placeholder', 'Дополнительные расходы, руб');" 
                                            onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                            onkeyup="javascript: $(this).attr('id', 'extra_expense'); $(this).attr('name', 'extra_expense'); $(this).attr('placeholder', 'Дополнительные расходы, руб');" 
                                            onfocusout="javascript: $(this).attr('id', 'extra_expense'); $(this).attr('name', 'extra_expense'); $(this).attr('palceholder', 'Дополнительные расходы, руб');" />
                                </div>
                            </div>
                            <div class="col-6 print-only self-adhesive-only d-none">
                                <div class="form-check">
                                    <label class="form-check-label text-nowrap mt-3" style="line-height: 25px;">
                                        <?php
                                        $checked = $cliche_in_price == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="cliche_in_price" name="cliche_in_price" value="on"<?=$checked ?> onchange="javascript: if($(this).is(':checked')) { $('#customer_pays_for_cliche').prop('checked', true); } RecalculateByCliche();" />Включить ПФ в себестоимость
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                        <?php
                                        $checked = $customer_pays_for_cliche == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="customer_pays_for_cliche" name="customer_pays_for_cliche" value="on"<?=$checked ?> onchange="javascript: if(!$(this).is(':checked')) { $('#cliche_in_price').prop('checked', false); } RecalculateByCliche();" />Заказчик платит за ПФ
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 self-adhesive-only d-none">
                                <div class="form-group" style="border-top: solid 2px lightgray; margin-top: 12px;">
                                    <label for="knife">Стоимость ножа, руб</label>
                                    <input type="text"
                                            id="knife"
                                            name="knife"
                                            class="form-control float-only self-adhesive-only d-none"
                                            value="<?=round($knife, 2) ?>"
                                            placeholder="Стоимость ножа, руб"
                                            onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                            onmouseup="javascript: $(this).attr('id', 'knife'); $(this).attr('name', 'knife'); $(this).attr('placeholder', 'Стоимость ножа, руб');" 
                                            onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                            onkeyup="javascript: $(this).attr('id', 'knife'); $(this).attr('name', 'knife'); $(this).attr('placeholder', 'Стоимость ножа, руб');" 
                                            onfocusout="javascript: $(this).attr('id', 'knife'); $(this).attr('name', 'knife'); $(this).attr('placeholder', 'Стоимость ножи, руб');" />
                                    <div class="invalid-feedback">Стоимость ножа обязательно</div>
                                </div>
                            </div>
                            <div class="col-6 self-adhesive-only d-none">
                                <div class="form-check" style="border-top: solid 2px lightgray; margin-top: 12px;">
                                    <label class="form-check-label text-nowrap mt-3" style="line-height: 25px;">
                                        <?php
                                        $checked = $knife_in_price == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="knife_in_price" name="knife_in_price" value="on"<?=$checked ?> onchange="javascript: if($(this).is(':checked')) { $('#customer_pays_for_knife').prop('checked', true); } RecalculateByKnife();" />Включить нож в себестоимость
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label text-nowrap" style="line-height: 25px;">
                                        <?php
                                        $checked = $customer_pays_for_knife == 1 ? " checked='checked'" : "";
                                        ?>
                                        <input type="checkbox" class="form-check-input" id="customer_pays_for_knife" name="customer_pays_for_knife" value="on"<?=$checked ?> onchange="javascript: if(!$(this).is(':checked')) { $('#knife_in_price').prop('checked', false); } RecalculateByKnife();" />Заказчик платит за нож
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="create_calculation_submit" name="create_calculation_submit" class="btn btn-dark mt-3<?=$create_calculation_submit_class ?>">Рассчитать</button>
                        <button type="button" class="d-none" onclick="javascript: $('.d-none').removeClass('d-none');">Показать</button>
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
            // Валидация цены
            function ValidatePrice(this_input, min_value_input) {
                val = this_input.val();
                min_val = min_value_input.val();
                
                if(val == '' || min_val == '') {
                    this_input.removeClass('is-invalid');
                }
                else {
                    i_val = parseFloat(val);
                    i_min_val = parseFloat(min_val);
                    
                    if(i_val < i_min_val) {
                        this_input.addClass('is-invalid');
                    }
                    else {
                        this_input.removeClass('is-invalid');
                    }
                }
            }
            
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
            
            // При изменении количества тиражей, добавляем соответствующее количество полей "Объём заказа"
            $('#printings_number').keyup(function() {
                if($(this).val() == '') {
                    $('#btn_quantities').attr('disabled', 'disabled');
                }
                else {
                    $('#btn_quantities').removeAttr('disabled');
                }
            });
            
            // Ограничение количества тиражей до 20 и активация/деактивация кнопки вызова модального окна списка тиражей
            $('#printings_number').change(function(){
                if($(this).val() == '') {
                    $('#btn_quantities').attr('disabled', 'disabled');
                }
                else {
                    $('#btn_quantities').removeAttr('disabled');
                }
                
                ChangeLimitIntValue($(this), 20);
            });
            
            $('#printings_number').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 20)) {
                    return false;
                }
            });
            
            if($('#printings_number').val() == '') {
                $('#btn_quantities').attr('disabled', 'disabled');
            }
            else {
                $('#btn_quantities').removeAttr('disabled');
            }
            
            // Автозаполнение названий заказчика при открытии окна заказчика.
            function CustomerNamesAutocomplete() {
                var customer_names = [
                    <?php
                    $customer_names = array();
                    $sql = "select name from customer order by name";
                    $fetcher = new Fetcher($sql);
                    while($row = $fetcher->Fetch()) {
                        array_push($customer_names, "'". addslashes($row['name'])."'");
                    }
                    
                    echo implode(",", $customer_names);
                    ?>
                ];
                $("input.customer_names").autocomplete({
                    source: customer_names
                });
            }
            
            CustomerNamesAutocomplete();
            
            // При открытии окна создания заказчика устанавливаем фокус на первом поле.
            $('#new_customer').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            // При закрытии окна очищаем все текстовые поля.
            $('#new_customer').on('hidden.bs.modal', function() {
                $('input[name=customer_name]').val('');
                $('input[name=customer_person]').val('');
                $('input[name=customer_phone]').val('');
                $('input[name=customer_extension]').val('');
                $('input[name=customer_email]').val('');
            });
            
            // Открытие модального окна со списком заказов
            $('#btn_quantities').click(function(){
                num = $('#printings_number').val();
                $('#quantities_form_body').html('');
                
                quantities_html = '';
                
                for(i=1; i<=num; i++) {
                    quantities_html += "<div class='form-group mb-3'><input type='text' id='quantity_" + i + "' name='quantity_" + i + "' class='form-control int-format' placeholder='Тираж " + i + " (кол-во этикеток)' value='" + (!$('#quantity_' + i).val() ? '' : Intl.NumberFormat('ru-RU').format($('#quantity_' + i).val())) + "' required='required' /><div class='invalid-feedback'>Указать значение</div></div>";
                }
                
                $('#quantities_form_body').html(quantities_html);
                
                for(i=1; i<=num; i++) {
                    $('#quantity_' + i).keypress(function(e) {
                        $(this).removeClass('is-invalid');
                        if(/\D/.test(e.key)) {
                            return false;
                        }
                    });
                    
                    $('#quantity_' + i).keyup(function() {
                        $(this).removeClass('is-invalid');
                        var val = $(this).val();
                        val = val.replaceAll(/\D/g, '');
                        
                        if(val === '') {
                            $(this).val('');
                        }
                        else {
                            val = parseInt(val);
                            
                            if($(this).hasClass('int-format')) {
                                val = Intl.NumberFormat('ru-RU').format(val);
                            }
                            
                            $(this).val(val);
                        }
                    });
                    
                    $('#quantity_' + i).change(function() {
                        $('#quantity_' + i).keyup();
                    });
                    
                    $('#quantity_' + i).attr('onmousedown', "javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');");
                    $('#quantity_' + i).attr('onmouseup', "javascript: $(this).attr('id', 'quantity_" + i + "'); $(this).attr('name', 'quantity_" + i + "'); $(this).attr('placeholder', 'Тираж " + i + " (кол-во этикеток)');");
                    $('#quantity_' + i).attr('onkeydown', "javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');");
                    $('#quantity_' + i).attr('onkeyup', "javascript: $(this).attr('id', 'quantity_" + i + "'); $(this).attr('name', 'quantity_" + i + "'); $(this).attr('placeholder', 'Тираж " + i + " (кол-во этикеток)');");
                    $('#quantity_' + i).attr('onfocusout', "javascript: $(this).attr('id', 'quantity_" + i + "'); $(this).attr('name', 'quantity_" + i + "'); $(this).attr('placeholder', 'Тираж " + i + " (кол-во этикеток)');");
                }
            });
            
            // При открытии модального окна установка фокуса на первом поле
            $('#quantities').on('shown.bs.modal', function() {
                $('#quantity_1').focus();
            });
            
            // Обработка нажатия кнопки OK в модальной форме добавления размера тиража
            $('#quantities_submit').click(function() {
                is_valid = true;
                
                num = $('#printings_number').val();
                
                for(i=1; i<=num; i++) {
                    if($('#quantity_' + i).val() == '') {
                        $('#quantity_' + i).addClass('is-invalid');
                        is_valid = false;
                    }
                }
                
                if(is_valid) {
                    $('#quantities_list').html('');
                    quantities_list = "<div class='row mb-3'>";
                    quantities_list += "<div class='col-3'>";
                    
                    for(i=1; i<=num; i++) {
                        if(i > 1 && (i - 1) % 20 == 0) {
                            quantities_list += "</div>";
                            quantities_list += "</div>";
                            quantities_list += "<div class='row mb-3'>";
                            quantities_list += "<div class='col-3'>";
                        }
                        else if(i > 1 && (i - 1) % 5 == 0) {
                            quantities_list += "</div>";
                            quantities_list += "<div class='col-3'>";
                        }
                        quantities_list += "<p style='font-size: larger;'>" + i + ".&nbsp;&nbsp;&nbsp;" + $('#quantity_' + i).val() + " шт</p>"
                        quantities_list += "<input type='hidden' id='quantity_" + i + "' name='quantity_" + i + "' value='" + $('#quantity_' + i).val().replace(/\D/g, "") + "' />";
                    }
            
                    quantities_list += "</div>";
                    quantities_list += "</div>";
                    $('#quantities_list').html(quantities_list);
                    $('#quantities').modal('hide');
                    $('#printings_number').removeClass('is-invalid');
                    HideCalculation();
                }
                
                SetClichesCount();
            });
            
            // Установление количества форм: Флинт, Кодак и старых
            function SetClichesCount() {
                if($('#work_type_id').val() == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                    ink_number = $('#ink_number').val();
                    printings_number = $('#printings_number').val();
                    
                    //if(Number.isInteger(ink_number) && Number.isInteger(printings_number)) {
                    if(!isNaN(ink_number) && !isNaN(printings_number)) {
                        cliches_count = ink_number * printings_number;
                        $('#cliches_count_old').val(cliches_count);
                        $('#cliches_count_flint').val(0);
                        $('#cliches_count_kodak').val(0);
                    }
                }
            }
            
            // Пересчёт количества форм: Флинт, Кодак и старых
            function ReSetClichesCount() {
                ink_number = $('#ink_number').val();
                printings_number = $('#printings_number').val();
                cliches_count_flint = $('#cliches_count_flint').val();
                cliches_count_kodak = $('#cliches_count_kodak').val();
                cliches_count_old = $('#cliches_count_old').val();
                
                if(!isNaN(ink_number) && ink_number != '' && 
                        !isNaN(printings_number) && printings_number != '' && 
                        !isNaN(cliches_count_flint) && cliches_count_flint != '' && 
                        !isNaN(cliches_count_kodak) && cliches_count_kodak != '' && 
                        !isNaN(cliches_count_old) && cliches_count_old != '') {
                    cliches_count_old = (parseInt(ink_number) * parseInt(printings_number)) - (parseInt(cliches_count_flint) + parseInt(cliches_count_kodak));
                    if(cliches_count_old < 0) {
                        cliches_count_old = 0;
                    }
                    $('#cliches_count_old').val(cliches_count_old);
                }
                else {
                    $('#cliches_count_old').val(0);
                }
            }
            
            $('#cliches_count_flint').keyup(function() {
                ReSetClichesCount();
            });
            
            $('#cliches_count_flint').change(function() {
                ReSetClichesCount();
            });
            
            $('#cliches_count_kodak').keyup(function() {
                ReSetClichesCount();
            });
            
            $('#cliches_count_kodak').change(function() {
                ReSetClichesCount();
            });
            
            // Список заказчиков с поиском
            $('#customer_id').select2({
                placeholder: "Заказчик...",
                maximumSelectionLength: 1,
                language: "ru"
            });
            
            // Смена типа работы
            $('#work_type_id').change(function() {
                SetFieldsVisibility($(this).val());
                FillMachines($(this).val());
                
                // Для типа "Самоклеящийся материал" делаем список красок узким,
                // чтобы уместились поля для количества форм
                if($(this).val() == <?= WORK_TYPE_PRINT ?>) {
                    $('#ink-col-ink').removeClass('col-3');
                    $('#ink-col-ink').addClass('col-12');
                }
                else if($(this).val() == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                    $('#ink-col-ink').removeClass('col-12');
                    $('#ink-col-ink').addClass('col-3');
                }
                
                // Устанавливаем флажки
                // Для плёнки с печатью: вкл. ПФ в себес. 0, польз. пл. за ПФ 1
                // Для самоклейки: вкл. ПФ в себес. 1, польз. пл. за ПФ 1, вкл. нож в себес. 0, польз. пл. за нож 1
                if($(this).val() == <?= WORK_TYPE_PRINT ?>) {
                    $('#cliche_in_price').prop('checked', false);
                    $('#customer_pays_for_cliche').prop('checked', true);
                }
                else if($(this).val() == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                    $('#cliche_in_price').prop('checked', true);
                    $('#customer_pays_for_cliche').prop('checked', true);
                    $('#knife_in_price').prop('checked', false);
                    $('#customer_pays_for_knife').prop('checked', true);
                }
                
                // Изменяем видимость "Разная ширина ручьёв"
                if($(this).val() == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                    $('#stream_width').removeAttr('disabled');
                    $('#stream_widths_many').prop('checked', false);
                    $('#stream_widths_many_wrapper').addClass('d-none');
                    $('#stream_widths_many_row').html('');
                    $('#stream_widths_many_row').addClass('d-none');
                }
                else {
                    streams_number = Number($('#streams_number').val());
                    if(Number.isInteger(streams_number) && streams_number > 1) {
                        $('#stream_widths_many_wrapper').removeClass('d-none');
                        $('#stream_widths_many_row').removeClass('d-none');
                        ShowStreamWidthsMany();
                    }
                }
            });
            
            // Заполняем список машин
            function FillMachines(work_type_id) {
                $.ajax({ url: "_machine.php?work_type_id=" + work_type_id })
                        .done(function(data) {
                            $('#machine_id').html(data);
                            $('#machine_id').change();
                        })
                        .fail(function() {
                            alert('Ошибка при заполнении списка машин');
                        });
            }
            
            // Если единица объёма - кг, то в поле "Объём" пишем "Объём, кг", иначе "Объем, шт"
            // Если единица объёма - кг, то в поле "Дополнительные расходы" пишем "кг", иначе - "шт"
            if($('input[value=kg]').is(':checked')) {
                $('#label_quantity').text('Объем заказа, кг');
                $('#label_extra_expense').text('Дополнительные расходы с кг, руб');
            }
            
            if($('input[value=pieces]').is(':checked')) {
                $('#label_quantity').text('Объем заказа, шт');
                $('#label_extra_expense').text('Дополнительные расходы с шт, руб');
            }
                
            $('input[name=unit]').click(function(){
                if($(this).val() == 'kg') {
                    $('#label_quantity').text('Объем заказа, кг');
                    $('#label_extra_expense').text('Дополнительные расходы с кг, руб');
                }
                else {
                    $('#label_quantity').text('Объем заказа, шт');
                    $('#label_extra_expense').text('Дополнительные расходы с шт, руб');
                }
            });
            
            // Обработка выбора машины, заполнение списка рапортов
            $('#machine_id').change(function() {
                $('#raport_control').html("<select id='raport' name='raport' class='form-control print-only self-adhesive-only'><option value='' hidden='hidden'>Рапорт...</option></select>");
                
                if($('#work_type_id').val() != <?= WORK_TYPE_NOPRINT ?>) {
                    $('#raport').attr('required', 'required');
                }
                
                SetRaportOnChange();
                
                if($(this).val() == "") {
                    $('select#raport').html("<option value=''>Рапорт...</option>")
                    $('#ink_number').html("<option value='' hidden='hidden'>Количество красок...</option>");
                    $('#ink_number').change();
                }
                else {
                    // Заполняем список количеств цветов (Если тип "Самоклеящаяся бумага", то возможна красочность 0)
                    $('.ink_block').addClass('d-none');
                    $('.ink').removeAttr('required');
                    
                    colorfulness = parseInt(colorfulnesses[$(this).val()]);
                    var colorfulness_list = "<option value='' hidden='hidden'>Количество красок...</option>";
                    if($('#work_type_id').val() == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                        colorfulness_list = colorfulness_list + "<option>0</option>";
                    }
                    for(var i=1; i<=colorfulness; i++) {
                        colorfulness_list = colorfulness_list + "<option>" + i + "</option>";
                    }
                    $('#ink_number').html(colorfulness_list);
                    
                    // Заполняем список рапортов
                    $.ajax({ url: "_raport.php?machine_id=" + $(this).val() })
                            .done(function(data) {
                                $('#raport').html(data);
                                SetRaportOnChange();
                            })
                            .fail(function() {
                                alert('Ошибка при заполнении списка рапортов');
                            });
                }
            });
            
            // При выборе значения "Ввести вручную" в списке рапортов, скрываем список и показываем текстовое поле
            function SetRaportOnChange() {
                $('select#raport').change(function() {
                    CountLength();
                    CountNumberInRaport();
                    
                    if($(this).val() == -1) {
                        $('#raport_control').html("<input type='text' id='raport' name='raport' placeholder='Рапорт, мм' class='form-control float-only print-only self-adhesive-only' required='required' />");
                        $('input#raport').focus();
                        SetRaportHandler();
                    }
                });
            }
            
            SetRaportOnChange();
            
            // Обработка нажатия клавиш в текстовом поле "Рапорт"
            function SetRaportHandler() {
                $('input#raport').keydown(function(e) {
                    if(e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39) {
                        if(!/[\.\,\d]/.test(e.key)) {
                            return false;
                        }
                        
                        if(/[\.\,]/.test(e.key) && ($(this).val().includes('.') || $(this).val().includes(','))) {
                            return false;
                        }
                    }
                });
                
                $('input#raport').keyup(function(e) {
                    var val = $(this).val();
                    val = val.replaceAll(/[^\.\,\d]/g, '');
                    $(this).val(val);
                    
                    CountLength();
                    CountNumberInRaport();
                    
                    if(e.which == 8 && val == '') {
                        $('#raport_control').html("<select id='raport' name='raport' class='form-control print-only self-adhesive-only'><option value='' hidden='hidden'>Рапорт...</option></select>");
                        $('#machine_id').change();
                        SetRaportOnChange();
                    }
                });
                
                $('input#raport').change(function(e) {
                    var val = $(this).val();
                    val = val.replace(',', '.');
                    val = val.replace(/[^\.\d]/g, '');
                    $(this).val(val);
                    
                    CountLength();
                    CountNumberInRaport();
                });
            }
            
            SetRaportHandler()
            
            // Обработка выбора типа плёнки основной плёнки: перерисовка списка толщин и установка видимости полей
            $('#film_id').change(function(){
                $('label#for_price').text("Цена");
                $('#currency').val('');
                $('#currency').removeClass('d-none');
                $('#currency_text').addClass('d-none');
                $('#price_min').val('');
                $('#price').removeClass('is-invalid');
                SetFilmFieldsVisibility($(this).val(), $('#customers_material').is(':checked'), '');
                
                if($(this).val() == "") {
                    $('#film_variation_id').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../supplier/_thickness.php?film_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_variation_id').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Обработка выбора толщины основной плёнки: отображение цены
            $('#film_variation_id').change(function(){
                if($(this).val() != '') {
                    $.ajax({ dataType: 'JSON', url: "../supplier/_film_price.php?film_variation_id=" + $(this).val() })
                        .done(function(data) {
                            $('label#for_price').text("Цена (" + data.text + ")");
                            $('#price_min').val(data.price);
                            ValidatePrice($('#price'), $('#price_min'));
                            
                            if(data.currency_local != '') {
                                $('#currency').addClass('d-none');
                                $('#currency').removeAttr('required');
                                $('#currency_text').text(data.currency_local);
                                $('#currency_text').removeClass('d-none');
                            }
                            else {
                                $('#currency_text').addClass('d-none');
                                $('#currency').removeClass('d-none');
                                $('#currency').attr('required', 'required');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при выборе толщины пленки');
                        });
                }
            });
            
            // Валидация цены
            $('#price').keyup(function() {
                ValidatePrice($(this), $('#price_min'));
            });
            
            $('#price').change(function() {
                ValidatePrice($(this), $('#price_min'));
            });
            
            <?php if(!empty($film_id) && $film_id != INDIVIDUAL && $customers_material != 1): ?>
            $('#film_variation_id').change();
            <?php endif; ?>
            
            // Обработка выбора типа плёнки ламинации1: перерисовка списка толщин
            $('#lamination1_film_id').change(function(){
                $('label#for_lamination1_price').text("Цена");
                $('#lamination1_currency').val('');
                $('#lamination1_currency').removeClass('d-none');
                $('#lamination1_currency_text').addClass('d-none');
                $('#lamination1_price_min').val('');
                $('#lamination1_price').removeClass('is-invalid');
                SetFilmFieldsVisibility($(this).val(), $('#lamination1_customers_material').is(':checked'), 'lamination1_');
                
                if($(this).val() == "") {
                    $('#lamination1_film_variation_id').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../supplier/_thickness.php?film_id=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination1_film_variation_id').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Обработка выбора толщины ламинации 1: отображение цены
            $('#lamination1_film_variation_id').change(function(){
                if($(this).val() != '') {
                    $.ajax({ dataType: 'JSON', url: "../supplier/_film_price.php?film_variation_id=" + $(this).val() })
                        .done(function(data) {
                            $('label#for_lamination1_price').text("Цена (" + data.text + ")");
                            $('#lamination1_price_min').val(data.price);
                            ValidatePrice($('#lamination1_price'), $('#lamination1_price_min'));
                            
                            if(data.currency_local != '') {
                                $('#lamination1_currency').addClass('d-none');
                                $('#lamination1_currency').removeAttr('required');
                                $('#lamination1_currency_text').text(data.currency_local);
                                $('#lamination1_currency_text').removeClass('d-none');
                            }
                            else {
                                $('#lamination1_currency_text').addClass('d-none');
                                $('#lamination1_currency').removeClass('d-none');
                                $('#lamination1_currency').attr('required', 'required');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при выборе толщины пленки');
                        });
                }
            });
            
            // Валидация цены
            $('#lamination1_price').keyup(function() {
                ValidatePrice($(this), $('#lamination1_price_min'));
            });
            
            $('#lamination1_price').change(function() {
                ValidatePrice($(this), $('#lamination1_price_min'));
            });
            
            <?php if(!empty($lamination1_film_id) && $lamination1_film_id != INDIVIDUAL && $lamination1_customers_material != 1): ?>
            $('#lamination1_film_variation_id').change();
            <?php endif; ?>
            
            // Обработка выбора типа плёнки ламинации2: перерисовка списка толщин
            $('#lamination2_film_id').change(function(){
                $('label#for_lamination2_price').text("Цена");
                $('#lamination2_currency').val('');
                $('#lamination2_currency').removeClass('d-none');
                $('#lamination2_currency_text').addClass('d-none');
                $('#lamination2_price_min').val('');
                $('#lamination2_price').removeClass('is-invalid');
                SetFilmFieldsVisibility($(this).val(), $('#lamination2_customers_material').is(':checked'), 'lamination2_');
                
                if($(this).val() == "") {
                    $('#lamination2_film_variation_id').html("<option value=''>Толщина...</option>");
                }
                else {
                    $.ajax({ url: "../supplier/_thickness.php?film_id=" + $(this).val() })
                            .done(function(data) {
                                $('#lamination2_film_variation_id').html(data);
                    })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                    });
                }
            });
            
            // Обработка выбора толщины ламинации 2: отображение цены
            $('#lamination2_film_variation_id').change(function(){
                if($(this).val() != '') {
                    $.ajax({ dataType: 'JSON', url: "../supplier/_film_price.php?film_variation_id=" + $(this).val() })
                        .done(function(data) {
                            $('label#for_lamination2_price').text("Цена (" + data.text + ")");
                            $('#lamination2_price_min').val(data.price);
                            ValidatePrice($('#lamination2_price'), $('#lamination2_price_min'));
                            
                            if(data.currency_local.length != '') {
                                $('#lamination2_currency').addClass('d-none');
                                $('#lamination2_currency').removeAttr('required');
                                $('#lamination2_currency_text').text(data.currency_local);
                                $('#lamination2_currency_text').removeClass('d-none');
                            }
                            else {
                                $('#lamination2_currency_text').addClass('d-none');
                                $('#lamination2_currency').removeClass('d-none');
                                $('#lamination2_currency').attr('required', 'required');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при выборе толщины пленки');
                        });
                }
            });
            
            // Валидация цены
            $('#lamination2_price').keyup(function() {
                ValidatePrice($(this), $('#lamination2_price_min'));
            });
            
            $('#lamination2_price').change(function() {
                ValidatePrice($(this), $('#lamination2_price_min'));
            });
            
            <?php if(!empty($lamination2_film_id) && $lamination2_film_id != INDIVIDUAL && $lamination2_customers_material != 1): ?>
            $('#lamination2_film_variation_id').change();
            <?php endif; ?>
                            
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('.percent').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 100)) {
                    return false;
                }
            });
    
            $(".percent").change(function(){
                ChangeLimitIntValue($(this), 100);
            });
            
            // Показываем или скрываем поле "Ширина пленки" в зависимости от значения поля "Лыжи"
            $('#ski').change(SetWidthSkiVisibility);
            $('#lamination1_ski').change(SetWidthSkiVisibility);
            $('#lamination2_ski').change(SetWidthSkiVisibility);
            
            function SetWidthSkiVisibility() {
                if($('#ski').val() == <?= SKI_NONSTANDARD ?>) {
                    $('#width_ski').removeClass('d-none');
                    $('#width_ski').attr('required', 'required');
                    $('#for_width_ski').removeClass('d-none');
                }
                else {
                    $('#width_ski').addClass('d-none');
                    $('#width_ski').removeAttr('required');
                    $('#for_width_ski').addClass('d-none');
                }
                
                if($('#lamination1_ski').val() == <?= SKI_NONSTANDARD ?>) {
                    $('#lamination1_width_ski').removeClass('d-none');
                    $('#lamination1_width_ski').attr('required', 'required');
                    $('#for_lamination1_width_ski').removeClass('d-none');
                }
                else {
                    $('#lamination1_width_ski').addClass('d-none');
                    $('#lamination1_width_ski').removeAttr('required');
                    $('#for_lamination1_width_ski').addClass('d-none');
                }
                
                if($('#lamination2_ski').val() == <?= SKI_NONSTANDARD ?>) {
                    $('#lamination2_width_ski').removeClass('d-none');
                    $('#lamination2_width_ski').attr('required', 'required');
                    $('#for_lamination2_width_ski').removeClass('d-none');
                }
                else {
                    $('#lamination2_width_ski').addClass('d-none');
                    $('#lamination2_width_ski').removeAttr('required');
                    $('#for_lamination2_width_ski').addClass('d-none');
                }
            }
            
            SetWidthSkiVisibility();
            
            // Показываем или скрываем поля в зависимости от работы с печатью / без печати и наличия / отсутствия ламинации
            function SetFieldsVisibility(work_type_id) {
                if(work_type_id == <?= WORK_TYPE_PRINT ?>) {
                    // Если тип работы "Плёнка с печатью", то объём заказа и в килограммах и в штуках
                    $('#units').removeClass('d-none');
                    $('#unit_kg').parent().parent().removeClass('d-none');
                    $('#unit_pieces').parent().parent().removeClass('d-none');
                    
                    // Скрываем поля "только без печати"
                    $('.no-print-only').addClass('d-none');
                    $('.no-print-only').removeAttr('required');
                    
                    // Скрываем поля "только самоклеящиеся материалы"
                    $('.self-adhesive-only').addClass('d-none');
                    $('.self-adhesive-only').removeAttr('required');
                    
                    // Показываем поля "только с печатью"
                    $('.print-only').not('.lam-only').removeClass('d-none');
                    $('input.print-only').not('.lam-only').attr('required', 'required');
                    $('select.print-only').not('.lam-only').attr('required', 'required');
                    
                    if($('#form_lamination_1').is(':visible')) {
                        // Если есть ламинация, показываем поля "только с ламинацией"
                        $('.lam-only').not('.no-print-only').removeClass('d-none');
                        $('input.lam-only').not('.no-print-only').attr('required', 'required');
                        $('select.lam-only').not('.no-print-only').attr('required', 'required');
                        
                        // Скрываем поля "только без ламинации"
                        $('.no-lam-only').addClass('d-none');
                        $('.no-lam-only').removeAttr('required');
                    }
                    else {
                        // Показываем поля "только без ламинации"
                        $('.no-lam-only').not('.no-print-only').removeClass('d-none');
                        $('input.no-lam-only').not('.no-print-only').attr('required', 'required');
                        $('select.no-lam-only').not('.no-print-only').attr('required', 'required');
                        
                        // Скрываем поля "только с ламинацией"
                        $('.lam-only').addClass('d-none');
                        $('.lam-only').removeAttr('required');
                        
                        // Показываем пункт "без лыж"
                        $('#no_ski_option').removeClass('d-none');
                    }
                }
                else if(work_type_id == <?= WORK_TYPE_NOPRINT ?>) {
                    // Если тип работы "Плёнка без печати", то объём заказа всегда в килограммах
                    $('#units').removeClass('d-none');
                    $('#unit_kg').parent().parent().removeClass('d-none');
                    $('#unit_pieces').parent().parent().addClass('d-none');
                    $('#unit_kg').click();
                    
                    // Скрываем поля "только с печатью"
                    $('.print-only').addClass('d-none');
                    $('.print-only').removeAttr('required');
                    
                    // Скрываем поля "только самоклеящиеся материалы"
                    $('.self-adhesive-only').addClass('d-none');
                    $('.self-adhesive-only').removeAttr('required');
                    
                    // Показываем поля "только без печати"
                    $('.no-print-only').not('.lam-only').removeClass('d-none');
                    $('input.no-print-only').not('.lam-only').attr('required', 'required');
                    $('select.no-print-only').not('.lam-only').attr('required', 'required');
                    
                    if($('#form_lamination_1').is(':visible')) {
                        // Если есть ламинация, показываем поля "только с ламинацией"
                        $('.lam-only').not('.print-only').removeClass('d-none');
                        $('input.lam-only').not('.print-only').attr('required', 'required');
                        $('select.lam-only').not('.print-only').attr('required', 'required');
                        
                        // Скрываем поля "только без ламинации"
                        $('.no-lam-only').addClass('d-none');
                        $('.no-lam-only').removeAttr('required');
                    }
                    else {
                        // Показываем поля "только без ламинации"
                        $('.no-lam-only').not('.print-only').removeClass('d-none');
                        $('input.no-lam-only').not('.print-only').attr('required', 'required');
                        $('select.no-lam-only').not('.print-only').attr('required', 'required');
                        
                        // Скрываем поля "только с ламинацией"
                        $('.lam-only').addClass('d-none');
                        $('.lam-only').removeAttr('required');
                        
                        // Показываем пункт "без лыж"
                        $('#no_ski_option').removeClass('d-none');
                    }
                }
                else if(work_type_id == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                    // Если тип работы "Самоклеящиеся материалы", то объём заказа всегда в штуках
                    $('#units').removeClass('d-none');
                    $('#unit_kg').parent().parent().addClass('d-none');
                    $('#unit_pieces').parent().parent().removeClass('d-none');
                    $('#unit_pieces').click();
                    
                    // Скрываем поля "только с печатью"
                    $('.print-only').addClass('d-none');
                    $('.print-only').removeAttr('required');
                    
                    // Скрываем поля "только без печати"
                    $('.no-print-only').addClass('d-none');
                    $('.no-print-only').removeAttr('required');
                    
                    // Скрываем поля "только с ламинацией"
                    $('.lam-only').addClass('d-none');
                    $('.lam-only').removeAttr('required');
                    
                    // Скрываем все поля, касающиеся ламинации
                    $('#form_lamination_1 select').val('');
                    $('#form_lamination_1 input').val('');
                    $('#lamination1_film_id').change();
                    $('#lamination1_customers_material').prop("checked", false);
                
                    $('#form_lamination_1').addClass('d-none');
                    $('#show_lamination_1').removeClass('d-none');
                    $('#main_film_title').addClass('d-none');
                    $('#film_title').removeClass('d-none');
                
                    $('#form_lamination_1 input').removeAttr('required');
                    $('#form_lamination_1 select').removeAttr('required');
                    $('#form_lamination_1 input').removeAttr('disabled');
                    $('#form_lamination_1 select').removeAttr('disabled');
                
                    $('#lamination1_ski').val(<?= SKI_STANDARD ?>);
                    $('#lamination1_ski').change();
        
                    HideLamination2();
                    
                    // Скрываем пункт "без лыж"
                    $('#no_ski_option').addClass('d-none');
                    
                    // Показываем поля "только самоклеящиеся материалы"
                    $('.self-adhesive-only').removeClass('d-none');
                    $('.self-adhesive-only').attr('required', 'required');
                }
            }
            
            SetFieldsVisibility($('#work_type_id').val());
            
            // Установка видимости полей для ручного ввода при выборе марки плёнки "Другая"
            function SetFilmFieldsVisibility(value, isCustomers, prefix) {
                if(isCustomers) {
                    $('#' + prefix + 'price').val('');
                    $('#' + prefix + 'price').attr('disabled', 'disabled');
                    $('#' + prefix + 'currency').val('');
                    $('#' + prefix + 'currency').attr('disabled', 'disabled');
                }
                else {
                    $('#' + prefix + 'price').removeAttr('disabled');
                    $('#' + prefix + 'currency').removeAttr('disabled');
                    $('#' + prefix + 'thickness').change();
                }
                
                if(value == <?=INDIVIDUAL ?>) {
                    $('#' + prefix + 'film_variation_id').removeAttr('required');
                    $('#' + prefix + 'film_variation_id').addClass('d-none');
                    $('#' + prefix + 'film_variation_id').prev('label').addClass('d-none');
                    $('.' + prefix + 'individual_only').removeClass('d-none');
                    $('.' + prefix + 'individual_only input').attr('required', 'required');
                    $('.' + prefix + 'individual_only select').attr('required', 'required');
                }
                else {
                    $('#' + prefix + 'film_variation_id').attr('required', 'required');
                    $('#' + prefix + 'film_variation_id').removeClass('d-none');
                    $('#' + prefix + 'film_variation_id').prev('label').removeClass('d-none');
                    $('.' + prefix + 'individual_only').addClass('d-none');
                    $('.' + prefix + 'individual_only input').removeAttr('required');
                    $('.' + prefix + 'individual_only select').removeAttr('required');
                }
            }
            
            $('#customers_material').change(function(e) {
                isCustomers = $(e.target).is(':checked');
                SetFilmFieldsVisibility($('#film_id').val(), isCustomers, '');
                $('#price').removeClass('is-invalid');
                
                if(!isCustomers) {
                    $('#film_variation_id').change();
                }
            });
            
            $('#lamination1_customers_material').change(function(e) {
                isCustomers = $(e.target).is(':checked');
                SetFilmFieldsVisibility($('#lamination1_film_id').val(), isCustomers, 'lamination1_');
                $('#lamination1_price').removeClass('is-invalid');
                
                if(!isCustomers) {
                    $('#lamination1_film_variation_id').change();
                }
            });
            
            $('#lamination2_customers_material').change(function(e) {
                isCustomers = $(e.target).is(':checked');
                SetFilmFieldsVisibility($('#lamination2_film_id').val(), isCustomers, 'lamination2_');
                $('#lamination2_price').removeClass('is-invalid');
                
                if(!isCustomers) {
                    $('#lamination2_film_variation_id').change();
                }
            });
            
            SetFilmFieldsVisibility($('#film_id').val(), $('#customers_material').is(':checked'), '');
            
            // Показ марки плёнки и толщины для ламинации 1
            function ShowLamination1() {
                $('#form_lamination_1').removeClass('d-none');
                $('#show_lamination_1').addClass('d-none');
                $('#main_film_title').removeClass('d-none');
                $('#film_title').addClass('d-none');
                $('#lamination1_film_id').attr('required', 'required');
                $('#lamination1_film_variation_id').attr('required', 'required');
                $('#lamination1_price').attr('required', 'required');
                
                $('#no_ski_option').addClass('d-none');
                if($('#ski').val() == <?= SKI_NO ?>) {
                    $('#ski').val(<?= SKI_STANDARD ?>);
                }
                
                SetFieldsVisibility($('#work_type_id').val());
                SetFilmFieldsVisibility($('#lamination1_film_id').val(), $('#lamination1_customers_material').is(':checked'), 'lamination1_');
                
                <?php if(empty($laminator_id)): ?>
                // Устанавливаем по умолчанию выбранным радиобаттон "Сольвент"
                $('#solvent_yes').click();
                <?php endif; ?>
            }
            
            <?php if(!empty($lamination1_film_id) || !empty($lamination1_individual_film_name)): ?>
                ShowLamination1();
            <?php endif; ?>
            
            // Скрытие марки плёнки и толщины для ламинации 1
            function HideLamination1() {
                $('#form_lamination_1 select').val('');
                $('#form_lamination_1 input').val('');
                $('#lamination1_film_id').change();
                $('#lamination1_customers_material').prop("checked", false);
                
                $('#form_lamination_1').addClass('d-none');
                $('#show_lamination_1').removeClass('d-none');
                $('#main_film_title').addClass('d-none');
                $('#film_title').removeClass('d-none');
                
                $('#form_lamination_1 input').removeAttr('required');
                $('#form_lamination_1 select').removeAttr('required');
                $('#form_lamination_1 input').removeAttr('disabled');
                $('#form_lamination_1 select').removeAttr('disabled');
                
                $('#no_ski_option').removeClass('d-none');
                $('#lamination1_ski').val(<?= SKI_STANDARD ?>);
                $('#lamination1_ski').change();
        
                SetFieldsVisibility($('#work_type_id').val());
                HideLamination2();
            }
            
            // Показ марки плёнки и толщины для ламинации 2
            function ShowLamination2() {
                $('#form_lamination_2').removeClass('d-none');
                $('#show_lamination_2').addClass('d-none');
                $('#hide_lamination_1').addClass('d-none');
                $('#hide_lamination_1').removeClass('d-block');
                $('#lamination2_film_id').attr('required', 'required');
                $('#lamination2_film_variation_id').attr('required', 'required');
                $('#lamination2_price').attr('required', 'required');
                SetFilmFieldsVisibility($('#lamination2_film_id').val(), $('#lamination2_customers_material').is(':checked'), 'lamination2_');
                
                // Скрываем радиобаттон "бессольвент" и устанавливаем выбранным радиобаттон "Сольвент"
                $('#solvent_yes').click();
                $('#solvent_no').parent().parent().addClass('d-none');
            }
            
            <?php if(!empty($lamination2_film_id) || !empty($lamination2_individual_film_name)): ?>
                ShowLamination2();
            <?php endif; ?>
            
            // Скрытие марки плёнки и толщины для ламинации 2
            function HideLamination2() {
                $('#form_lamination_2 select').val('');
                $('#form_lamination_2 input').val('');
                $('#lamination2_film_id').change();
                $('#lamination2_customers_material').prop("checked", false);
                
                $('#form_lamination_2').addClass('d-none');
                $('#show_lamination_2').removeClass('d-none');
                $('#hide_lamination_1').removeClass('d-none');
                $('#hide_lamination_1').addClass('d-block');
                
                $('#form_lamination_2 input').removeAttr('required');
                $('#form_lamination_2 select').removeAttr('required');
                $('#form_lamination_2 input').removeAttr('disabled');
                $('#form_lamination_2 select').removeAttr('disabled');
                
                $('#lamination2_ski').val(<?= SKI_STANDARD ?>);
                $('#lamination2_ski').change();
                
                // Показываем радиобаттон "Бессольвент"
                $('#solvent_no').parent().parent().removeClass('d-none');
            }
            
            // Заполнение списка валов ламинации
            function GetLaminationRollers() {
                laminator_id = 0;
                if($('#solvent_yes').is(':checked')) {
                    laminator_id = <?=LAMINATOR_SOLVENT ?>;
                }
                else if($('#solvent_no').is(':checked')) {
                    laminator_id = <?=LAMINATOR_SOLVENTLESS ?>;
                }
                
                min_width = 0;
                stream_width = $('#stream_width').val();
                streams_number = $('#streams_number').val();
                if(stream_width !== "" && streams_number !== "" && stream_width !== 0 && streams_number !== 0) {
                    min_width = stream_width * streams_number;
                }
                
                $.ajax({ url: "_laminator_roller.php?laminator_id=" + laminator_id + "&min_width=" + min_width })
                        .done(function(data) {
                            // Если есть ламинация (то есть, скрыта кнопка "Добавить ламинацию 1")
                            if($('#show_lamination_1').hasClass('d-none')) {
                                $('#lamination_roller_width_control').html("<select id='lamination_roller_width' name='lamination_roller_width' class='form-control lam-only' required='required'><option value='' hidden='hidden'>Ширина ламинирующего вала...</option></select>");
                                $('#lamination_roller_width').html(data);
                                SetLaminationRollerWidthOnChange();
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при заполнении ширин ламинирующего вала');
                        });
            }
            
            // Обрабатываем выбор сольвентного или бессольвентного ламинатора
            $('#solvent_yes').click(function() {
                GetLaminationRollers();
            });
            
            $('#solvent_no').click(function() {
                GetLaminationRollers();
            });
            
            // При выборе значения "Ввести вручную" в списке ширин ламинирующего вала, скрываем список и показываем текстовое поле
            function SetLaminationRollerWidthOnChange() {
                $('select#lamination_roller_width').change(function() {
                    if($(this).val() == -1) {
                        $('#lamination_roller_width_control').html("<input type='text' id='lamination_roller_width' name='lamination_roller_width' placeholder='Ширина ламинирующего вала, мм' class='form-control int-only lam-only' required='required' />");
                        $('input#lamination_roller_width').focus();
                        SetLaminationRollerWidthHandler();
                    }
                });
            }
            
            SetLaminationRollerWidthOnChange();
            
            // Обработка нажатия клавиш в текстовом поле "Ширина ламинирующего вала"
            function SetLaminationRollerWidthHandler() {
                $('input#lamination_roller_width').keydown(function(e) {
                    if(e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39) {
                        if(/\D/.test(e.key)) {
                            return false;
                        }
                    }
                });
                
                $('input#lamination_roller_width').keyup(function(e) {
                    var val = $(this).val();
                    val = val.replaceAll(/\D/g, '');
                    $(this).val(val);
                    
                    if((e.which == 8 || e.which == 46) && val == '') {
                        $('#lamination_roller_width_control').html("<select id='lamination_roller_width' name='lamination_roller_width' class='form-control lam-only' required='required'><option value='' hidden='hidden'>Ширина ламинирующего вала...</option></select>");
                        if($('#solvent_yes').is(':checked')) {
                            $('#solvent_yes').click();
                        }
                        if($('#solvent_no').is(':checked')) {
                            $('#solvent_no').click();
                        }
                    }
                });
                
                $('input#lamination_roller_width').change(function(e) {
                    var val = $(this).val();
                    val = val.replace(/[^\d]/g, '');
                    $(this).val(val);
                });
            }
            
            SetLaminationRollerWidthHandler();
            
            // Обработка изменения значения флажка "Разная ширина ручьёв"
            $('#stream_widths_many').change(function(e) {
                if($(e.target).is(':checked')) {
                    $('#stream_width').attr('disabled', 'disabled');
                    $('#stream_widths_many_row').removeClass('d-none');
                    ShowStreamWidthsMany();
                }
                else {
                    $('#stream_width').removeAttr('disabled');
                    $('#stream_widths_many_row').html('');
                    $('#stream_widths_many_row').addClass('d-none');
                }
            });
            
            // Показ полей с разными ширинами ручьёв
            function ShowStreamWidthsMany() {
                if($('#stream_widths_many').is(':checked')) {
                    $('#stream_widths_many_row').html('');
                    var streams_number = parseInt($('#streams_number').val());
                    
                    <?php
                    if(count($stream_widths) > 0):
                        foreach($stream_widths as $key => $value):
                    ?>
                    stream_width = $("<div class='col-6'><div class='form-group'><label for='stream_width_<?=$key ?>'>Ширина ручья <?=$key ?>, мм</label><input type='text' class='form-control' id='stream_width_<?=$key ?>' name='stream_width_<?=$key ?>' value='<?=$value ?>' required='required' /></div></div>");
                    $('#stream_widths_many_row').append(stream_width);
                    <?php
                    endforeach;
                    else:
                    ?>
                    for(i = 1; i <= streams_number; i++) {
                        stream_width = $("<div class='col-6'><div class='form-group'><label for='stream_width_" + i + "'>Ширина ручья " + i + ", мм</label><input type='text' class='form-control' id='stream_width_" + i + "' name='stream_width_" + i + "' value='' required='required' /></div></div>");
                        $('#stream_widths_many_row').append(stream_width);
                    }
                    <?php endif; ?>
                }
            }
            
            // Считаем длину этикетки (рапорт / количество этикеток в рапорте)
            function CountLength() {
                var raport = $('#raport').val();
                var number_in_raport = $('#number_in_raport').val();
                if(raport != '' && raport != '-1' && number_in_raport != '') {
                    var f_raport = parseFloat(raport);
                    var i_number_in_raport = parseInt(number_in_raport);
                    var length = Math.floor(f_raport / i_number_in_raport * 10) / 10;
                    $('#length').val(length);
                }
                else {
                    $('#length').val('');
                }
            }
            
            // Считаем количество этикеток в рапорте (рапорт / длина этикетки грязная, округляем в меньшую сторону)
            // Считаем фактический зазор: (рапорт - (длина этикетки чистая * кол-во этикеток в рапорте чистое)) / кол-во этикеток в рапорте чистое
            function CountNumberInRaport() { //alert('works');
                var raport = $('#raport').val();
                var length = $('#length_2').val();
                var gap_raport = <?=$gap_raport ?>;
                if(raport != '' && raport != '-1' && length != '') {
                    var f_raport = parseFloat(raport);
                    var f_length = parseFloat(length);
                    var f_gap_raport = parseFloat(gap_raport);
                    var f_length_dirty = f_length + f_gap_raport;
                    var number_in_raport = Math.floor(f_raport / f_length_dirty);
                    $('#number_in_raport_2').val(number_in_raport);
                    
                    var gap_fact = (f_raport - (f_length * number_in_raport)) / number_in_raport;
                    var s_gap_fact = Intl.NumberFormat('ru', { maximumFractionDigits: 2 }).format(gap_fact);
                    $('#gap_fact').text('Зазор между этикетками ' + s_gap_fact + ' мм');
                }
                else {
                    $('#number_in_raport_2').val('');
                    $('#gap_fact').text('');
                }
            }
            
            $('#number_in_raport').change(function() {
                CountLength();
            });
            
            $('#length_2').change(function() {
                CountNumberInRaport();
            });
            
            $('#length_2').keyup(function() {
                CountNumberInRaport();
            });
            
            // Заполняем список красочностей
            var colorfulnesses = {};
            <?php foreach (array_keys(PRINTER_COLORFULLNESSES) as $key): ?>
                colorfulnesses[<?=$key ?>] = <?=PRINTER_COLORFULLNESSES[$key] ?>;
            <?php endforeach; ?>
            
            // Обработка выбора количества красок
            $('#ink_number').change(function(){
                var count = $(this).val();
                $('.ink_block').addClass('d-none');
                $('.ink').removeAttr('required');
                
                work_type_id = $('#work_type_id').val();
                
                if(count != '') {
                    iCount = parseInt(count);
                    
                    for(var i=1; i<=iCount; i++) {
                        $('#ink_block_' + i).removeClass('d-none');
                        $('#ink_' + i).attr('required', 'required');
                        
                        if(work_type_id == <?= WORK_TYPE_PRINT ?> && $('#percent_group_' + i).hasClass('col-6')) {
                            $('#percent_group_' + i).removeClass('col-6');
                            $('#percent_group_' + i).addClass('col-3');
                            $('#cliche_group_' + i).removeClass('d-none');
                            $('#cliche_group_' + i).addClass('col-3');
                        }
                        else if(work_type_id == <?= WORK_TYPE_SELF_ADHESIVE ?> && $('#percent_group_' + i).hasClass('col-3')) {
                            $('#percent_group_' + i).removeClass('col-3');
                            $('#percent_group_' + i).addClass('col-6');
                            $('#cliche_group_' + i).removeClass('col-3');
                            $('#cliche_group_' + i).addClass('d-none');
                        }
                    }
                }
                
                SetClichesCount();
            });
            
            // Обработка выбора краски
            $('.ink').change(function(){
                ink = $(this).val();
                var data_id = $(this).attr('data-id');
                work_type_id = $('#work_type_id').val();
                
                // Устанавливаем видимость всех элементов по умолчанию, как если бы выбрали пустое значение
                $('#ink_group_' + data_id).removeClass('col-12');
                $('#ink_group_' + data_id).removeClass('col-6');
                $('#ink_group_' + data_id).removeClass('col-3');
                
                $('#color_group_' + data_id).removeClass('col-3');
                $('#color_group_' + data_id).addClass('d-none');
                
                $('#cmyk_group_' + data_id).removeClass('col-3');
                $('#cmyk_group_' + data_id).addClass('d-none');
                
                $('#lacquer_group_' + data_id).removeClass('col-3');
                $('#lacquer_group_' + data_id).addClass('d-none');
                
                $('#percent_group_' + data_id).removeClass('col-3');
                $('#percent_group_' + data_id).removeClass('col-6');
                $('#percent_group_' + data_id).addClass('d-none');
                
                $('#cliche_group_' + data_id).removeClass('col-3');
                $('#cliche_group_' + data_id).removeClass('col-6');
                $('#cliche_group_' + data_id).addClass('d-none');
                
                // Снимаем атрибут required с кода цвета, CMYK, лака и процента
                $('#color_' + data_id).removeAttr('required');
                $('#cmyk_' + data_id).removeAttr('required');
                $('#lacquer_' + data_id).removeAttr('required');
                $('#percent_' + data_id).removeAttr('required');
                
                // Затем, в зависимости от выбранного значения, устанавливаем видимость нужного элемента для этого значения
                if(ink == 'lacquer' || ink == 'white' || ink == 'cmyk' || ink == 'panton') {
                    if(work_type_id == <?= WORK_TYPE_PRINT ?>) {
                        $('#percent_group_' + data_id).removeClass('col-6');
                        $('#percent_group_' + data_id).addClass('col-3');
                        $('#percent_group_' + data_id).removeClass('d-none');
                        $('#cliche_group_' + data_id).addClass('col-3');
                        $('#cliche_group_' + data_id).removeClass('d-none');
                    }
                    else if(work_type_id == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                        $('#percent_group_' + data_id).addClass('col-6');
                        $('#percent_group_' + data_id).removeClass('col-3');
                        $('#percent_group_' + data_id).removeClass('d-none');
                        $('#cliche_group_' + data_id).addClass('d-none');
                        $('#cliche_group_' + data_id).removeClass('col-3');
                    }
                }
                
                if(ink == 'lacquer')  {
                    if(work_type_id == <?= WORK_TYPE_PRINT ?>) {
                        $('#ink_group_' + data_id).addClass('col-3');
                        $('#lacquer_group_' + data_id).addClass('col-3');
                        $('#lacquer_group_' + data_id).removeClass('d-none');
                        $('#lacquer_' + data_id).attr('required', 'required');
                    }
                    else if(work_type_id == <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                        $('#ink_group_' + data_id).addClass('col-6');
                    }
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(ink == 'white') {
                    $('#ink_group_' + data_id).addClass('col-6');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                }
                else if(ink == 'cmyk') {
                    $('#ink_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).addClass('col-3');
                    $('#cmyk_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                    $('#cmyk_' + data_id).attr('required', 'required');
                }
                else if(ink == 'panton') {
                    $('#ink_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).addClass('col-3');
                    $('#color_group_' + data_id).removeClass('d-none');
                    
                    $('#percent_' + data_id).attr('required', 'required');
                    $('#color_' + data_id).attr('required', 'required');
                }
                else {
                    $('#ink_group_' + data_id).addClass('col-12');
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
                $("#create_calculation_submit").removeClass("d-none");
            }
            
            // Ограницение значений наценки
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
            
            // Редактируем наценку
            function SetExtracharge(param) {
                <?php if(!empty($id)): ?>
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
                <?php endif; ?>
            }
            
            $('#extracharge').keyup(function(){
                SetExtracharge($(this).val());
            });
            
            // Редактируем наценку на ПФ
            function SetExtrachargeCliche(param) {
                <?php if(!empty($id)): ?>
                extracharge_cliche = parseInt(param);
                
                if(!isNaN(extracharge_cliche) && extracharge_cliche > -1) {
                    $.ajax({ dataType: 'JSON', url: "_set_extracharge_cliche.php?id=<?=$id ?>&extracharge_cliche=" + extracharge_cliche })
                            .done(function(data) {
                                if(data.error != '') {
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
                <?php endif; ?>
            }
            
            $('#extracharge_cliche').keyup(function(){
                SetExtrachargeCliche($(this).val());
            });
            
            // Редактируем наценку на нож
            function SetExtrachargeKnife(param) {
                <?php if(!empty($id)): ?>
                extracharge_knife = parseInt(param);
                
                if(!isNaN(extracharge_knife) && extracharge_knife > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_extracharge_knife.php?id=<?=$id ?>&extracharge_knife=' + extracharge_knife })
                            .done(function(data) {
                                if(data.error != '') {
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
                <?php endif; ?>
            }
            
            $('#extracharge_knife').keyup(function(){
                SetExtrachargeKnife($(this).val());
            });
            
            // Вычисляем наценку по отгрузочной стоимости за единицу
            function SetShippingCostPerUnit(param) {
                <?php if(!empty($id)): ?>
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
                <?php endif; ?>
            }
            
            $('#input_shipping_cost_per_unit').keyup(function() {
                SetShippingCostPerUnit($(this).val());
            });
            
            // Вычисляем наценку на ПФ по отгрузочной стоимости ПФ
            function SetShippingClicheCost(param) {
                <?php if(!empty($id)): ?>
                shipping_cliche_cost = parseFloat(param.replace(',', '.'));
                
                if(!isNaN(shipping_cliche_cost) && shipping_cliche_cost > -1) {
                    $.ajax({ dataType: 'JSON', url: '_set_shipping_cliche_cost.php?id=<?=$id ?>&shipping_cliche_cost=' + shipping_cliche_cost })
                            .done(function(data) {
                                if(data.error != '') {
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
                <?php endif; ?>
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
                                if(data.error != '') {
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
                <?php if(!empty($id)): ?>
                if($('#calculation').hasClass('d-none')) {
                    return;
                }
                
                var cliche_in_price = $('#cliche_in_price').is(':checked') ? 1 : 0;
                var customer_pays_for_cliche = $('#customer_pays_for_cliche').is(':checked') ? 1 : 0;
                
                $.ajax({ dataType: 'JSON', url: '_recalculate_by_cliche.php?id=<?=$id ?>&cliche_in_price=' + cliche_in_price + '&customer_pays_for_cliche=' + customer_pays_for_cliche })
                        .done(function(data) {
                            if(data.error != '') {
                                alert(data.error);
                            }
                            else {
                                if(data.cliche_in_price == 1) {
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
                <?php endif; ?>
            }
            
            // Пересчитываем по новому значению "Включить нож в себестоимость" и "Заказчик платит за нож"
            function RecalculateByKnife() {
                <?php if(!empty($id)): ?>
                if($('#calculation').hasClass('d-none')) {
                    return;
                }
                
                var knife_in_price = $('#knife_in_price').is(':checked') ? 1 : 0;
                var customer_pays_for_knife = $('#customer_pays_for_knife').is(':checked') ? 1 : 0;
                
                $.ajax({ dataType: 'JSON', url: '_recalculate_by_knife.php?id=<?=$id ?>&knife_in_price=' + knife_in_price + '&customer_pays_for_knife=' + customer_pays_for_knife })
                        .done(function(data) {
                            if(data.error != '') {
                                alert(data.error);
                            }
                            else {
                                if(data.knife_in_price == 1) {
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
                <?php endif; ?>
            }
            
            // Ограничение значения поля "Обрезная ширина" до 1600
            $('input#width').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 1600)) {
                    return false;
                }
            });
            
            $('input#width').change(function(){
                ChangeLimitIntValue($(this), 1600);
            });
            
            // Ограничение значения поля "Длина этикетки" до 3 цифр
            $('input#length').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('input#length').change(function(){
                ChangeLimitIntValue($(this), 999);
            });
            
            // Автоматический выбор ламинирующего вала по следующему алгоритму:
            // Кол-во ручьев * ширину ручья +5, затем должен выбираться наиболее близкий из списка вал , но в большую сторону.
            function SelectLaminatorRoller() {
                if(!$('#lamination_roller_width').hasClass('d-none')) {
                    var streams_number = $('#streams_number').val();
                    var stream_width = $('#stream_width').val();
                    
                    if(!isNaN(streams_number) && !isNaN(stream_width) &&
                            streams_number != '' && stream_width != '' &&
                            streams_number != undefined && stream_width != undefined &&
                            ($('#solvent_yes').is(':checked') || $('#solvent_no').is(':checked'))) {
                        if($('input#lamination_roller_width').val() != undefined && $('input#lamination_roller_width').val() != '') { 
                            $('#lamination_roller_width_control').html("<select id='lamination_roller_width' name='lamination_roller_width' class='form-control lam-only'><option value='' hidden='hidden'>Ширина ламинирующего вала...</option></select>");
                            if($('#solvent_yes').is(':checked')) $('#solvent_yes').click();
                            else if($('#solvent_no').is(':checked')) $('#solvent_no').click();
                        }
                    
                        material_width = streams_number * stream_width + 5;
                        laminator_widths = $.map($('#lamination_roller_width option'), function(option) {
                            if(!isNaN(option.value) && option.value >= material_width) {
                               return option.value;
                            }
                        });

                        laminator_width = 0;
                        
                        if(laminator_widths.length > 0) {
                            laminator_width = Math.min.apply(null, laminator_widths);
                        }
                        
                        $('#lamination_roller_width').val(laminator_width);
                    }
                }
            }
            
            // В поле "количество ручьёв" ограничиваем значения: целые числа от 1 до 16
            $('input#streams_number').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 16)) {
                    return false;
                }
            });
            
            $('input#streams_number').keyup(function() {
                GetLaminationRollers();
                
                // Скрываем / показываем флажок "Разный размер ручьёв"
                streams_number = Number($(this).val());
                if(Number.isInteger(streams_number) && streams_number > 1) {
                    if($('#work_type_id').val() != <?= WORK_TYPE_SELF_ADHESIVE ?>) {
                        $('#stream_widths_many_wrapper').removeClass('d-none');
                        $('#stream_widths_many_row').removeClass('d-none');
                        ShowStreamWidthsMany();
                    }
                }
                else {
                    $('#stream_width').removeAttr('disabled');
                    $('#stream_widths_many').prop('checked', false);
                    $('#stream_widths_many_wrapper').addClass('d-none');
                    $('#stream_widths_many_row').html('');
                    $('#stream_widths_many_row').addClass('d-none');
                }
            });
    
            $('input#streams_number').change(function(){
                ChangeLimitIntValue($(this), 16);
            });
            
            // Ограничение значения поля "Ширина ручья" до 4 цифр
            $('input#stream_width').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 9999)) {
                    return false;
                }
            });
            
            $('input#stream_width').keyup(function() {
                GetLaminationRollers();
            });
            
            $('input#stream_width').change(function(){
                ChangeLimitIntValue($(this), 9999);
            });
            
            // Ограничение значения поля "пантон"
            $('input.panton').keypress(function(e) {
                if(/[^0-9a-zA-Zа-яА-Я]/.test(e.key)) {
                    return false;
                }
            });
            
            $('input.panton').keyup(function() {
                var val = $(this).val();
                val = val.replaceAll(/[^0-9a-zA-Zа-яА-Я]/g, '');
            });
    
            $('input.panton').change(function(e) {
                var val = $(this).val();
                val = val.replace(/[^0-9a-zA-Zа-яА-Я]/g, '');
            });
            
            // Скрытие расчёта при изменении значения полей
            $("input[id!=extracharge][id!=extracharge_cliche][id!=extracharge_knife][id!=input_shipping_cost_per_unit][id!=input_shipping_cliche_cost][id!=input_shipping_knife_cost][id!=cliche_in_price][id!=customer_pays_for_cliche][id!=knife_in_price][id!=customer_pays_for_knife]").change(function () {
                HideCalculation();
            });
            
            $('select').change(function () {
                HideCalculation();
            });
            
            $("input[id!=extracharge][id!=extracharge_cliche][id!=extracharge_knife][id!=input_shipping_cost_per_unit][id!=input_shipping_cliche_cost][id!=input_shipping_knife_cost][id!=cliche_in_price][id!=customer_pays_for_cliche][id!=knife_in_price][id!=customer_pays_for_knife]").keydown(function () {
                HideCalculation();
            });
            
            // Скрытие расчёта, если имеется параметр mode=recalc
            <?php if(filter_input(INPUT_GET, 'mode') == 'recalc'): ?>
                HideCalculation();
            <?php endif; ?>
            
            // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
            if($('#calculation').offset() != undefined) {
                AdjustFixedBlock($('#calculation'));
            }
            
            $(window).on("scroll", function(){
                if($('#calculation').offset() != undefined) {
                    AdjustFixedBlock($('#calculation'));
                }
            });
            
            // Повторное прокручивание страницы 
            // (так как после первого прокручивания внизу страницы нарисовались новые элементы, 
            // и текущее положение вертикальной полосы прокрутки уже не соответствует размеру страницы)
            <?php if(!empty($_REQUEST['scroll'])): ?>
                window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
            <?php endif; ?>
        </script>
    </body>
</html>