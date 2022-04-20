<?php
$calculation_class = "";
                        
if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}

// Типы наценки
const ET_NOPRINT = 1; // Пленка без печати
const ET_PRINT = 2; // Пленка с печатью без ламинации
const ET_PRINT_1 = 3; // Пленка с печатью и ламинацией
const ET_PRINT_2 = 4; // Пленка с печатью и двумя ламинациями

$id = filter_input(INPUT_GET, 'id');

// Берём расчёт из таблицы базы
$extracharge = null; $usd = null; $euro = null; $cost = null; $cost_per_unit = null; $material = null;
$material_price = null; $material_price_per_unit = null; $material_width = null; $material_weight = null; $material_length = null; $material_weight_with_tuning = null; $material_length_with_tuning = null;
$material_lamination1_price = null; $material_lamination1_price_per_unit = null; $material_lamination1_width = null; $material_lamination1_weight = null; $material_lamination1_length = null; $material_lamination1_weight_with_tuning = null; $material_lamination1_length_with_tuning = null;
$material_lamination2_price = null; $material_lamination2_price_per_unit = null; $material_lamination2_width = null; $material_lamination2_weight = null; $material_lamination2_length = null; $material_lamination2_weight_with_tuning = null; $material_lamination2_length_with_tuning = null;
$expenses_waste = null; $expenses_waste_weight = null; $expenses_ink = null; $expenses_ink_weight = null; $expenses_work = null; $expenses_work_time = null;
$expenses_lamination1_waste = null; $expenses_lamination1_waste_weight = null; $expenses_lamination1_glue = null; $expenses_lamination1_glue_weight = null; $expenses_lamination1_work = null; $expenses_lamination1_work_time = null;
$expenses_lamination2_waste = null; $expenses_lamination2_waste_weight = null; $expenses_lamination2_glue = null; $expenses_lamination2_glue_weight = null; $expenses_lamination2_work = null; $expenses_lamination2_work_time = null;
        
$sql_calculation_result = "select extracharge, usd, euro, cost, cost_per_unit, material, "
        . "material_price, material_price_per_unit, material_width, material_weight, material_length, material_weight_with_tuning, material_length_with_tuning, "
        . "material_lamination1_price, material_lamination1_price_per_unit, material_lamination1_width, material_lamination1_weight, material_lamination1_length, material_lamination1_weight_with_tuning, material_lamination1_length_with_tuning, "
        . "material_lamination2_price, material_lamination2_price_per_unit, material_lamination2_width, material_lamination2_weight, material_lamination2_length, material_lamination2_weight_with_tuning, material_lamination2_length_with_tuning, "
        . "expenses_waste, expenses_waste_weight, expenses_ink, expenses_ink_weight, expenses_work, expenses_work_time, "
        . "expenses_lamination1_waste, expenses_lamination1_waste_weight, expenses_lamination1_glue, expenses_lamination1_glue_weight, expenses_lamination1_work, expenses_lamination1_work_time, "
        . "expenses_lamination2_waste, expenses_lamination2_waste_weight, expenses_lamination2_glue, expenses_lamination2_glue_weight, expenses_lamination2_work, expenses_lamination2_work_time "
        . "from calculation_result where calculation_id = $id order by id desc limit 1";
$fetcher = new Fetcher($sql_calculation_result);

if($fetcher->Fetch()) {
    echo "OK";
}
else {
    include './calculation.php';
    
    // ПОЛУЧАЕМ ИСХОДНЫЕ ДАННЫЕ
    $date = null;
    $name = null;
    $unit = null; // Кг или шт
    $quantity = null; // Размер тиража
    $work_type_id = null; // Типа работы: с печатью или без печати
    
    $film = null; // Основная пленка, марка
    $thickness = null; // Основная пленка, толщина, мкм
    $density = null; // Основная пленка, плотность, г/м2
    $price = null; // Основная пленка, цена
    $currency = null; // Основная пленка, валюта
    $customers_material = null; // Основная плёнка, другая, материал заказчика
    $ski = null; // Основная пленка, лыжи
    $width_ski = null; // Основная пленка, ширина пленки, мм
        
    $lamination1_film = null; // Ламинация 1, марка
    $lamination1_thickness = null; // Ламинация 1, толщина, мкм
    $lamination1_density = null; // Ламинация 1, плотность, г/м2
    $lamination1_price = null; // Ламинация 1, цена
    $lamination1_lamination1_currency = null; // Ламинация 1, валюта
    $lamination1_customers_material = null; // Ламинация 1, другая, материал заказчика
    $lamination1_ski = null; // Ламинация 1, лыжи
    $lamination1_width_ski = null; // Ламинация 1, ширина пленки, мм

    $lamination2_film = null; // Ламинация 2, марка
    $lamination2_thickness = null; // Ламинация 2, толщина, мкм
    $lamination2_density = null; // Ламинация 2, плотность, г/м2
    $lamination2_price = null; // Ламинация 2, цена
    $lamination2_currency = null; // Ламинация 2, валюта
    $lamination2_customers_material = null; // Ламинация 2, другая, уд. вес
    $lamination2_ski = null; // Ламинация 2, лыжи
    $lamination2_width_ski = null;  // Ламинация 2, ширина пленки, мм
    
    $machine = null;
    $machine_shortname = null;
    $machine_id = null;
    $length = null; // Длина этикетки, мм
    $width = null; // Обрезная ширина, мм (если плёнка без печати)
    $stream_width = null; // Ширина ручья, мм (если плёнка с печатью)
    $streams_number = null; // Количество ручьёв
    $raport = null; // Рапорт
    $lamination_roller_width = null; // Ширина ламинирующего вала
    $ink_number = 0; // Красочность
    
    $sql = "select rc.date, rc.name, rc.unit, rc.quantity, rc.work_type_id, "
            . "f.name film, fv.thickness thickness, fv.weight density, "
            . "rc.film_variation_id, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
            . "rc.customers_material, rc.ski, rc.width_ski, "
            . "lamination1_f.name lamination1_film, lamination1_fv.thickness lamination1_thickness, lamination1_fv.weight lamination1_density, "
            . "rc.lamination1_film_variation_id, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, "
            . "rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
            . "lamination2_f.name lamination2_film, lamination2_fv.thickness lamination2_thickness, lamination2_fv.weight lamination2_density, "
            . "rc.lamination2_film_variation_id, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, "
            . "rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
            . "m.name machine, m.shortname machine_shortname, rc.machine_id, rc.length, rc.stream_width, rc.streams_number, rc.raport, rc.lamination_roller_width, rc.ink_number, "
            . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
            . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
            . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
            . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
            . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8 "
            . "from calculation rc "
            . "left join machine m on rc.machine_id = m.id "
            . "left join film_variation fv on rc.film_variation_id = fv.id "
            . "left join film f on fv.film_id = f.id "
            . "left join film_variation lamination1_fv on rc.lamination1_film_variation_id = lamination1_fv.id "
            . "left join film lamination1_f on lamination1_fv.film_id = lamination1_f.id "
            . "left join film_variation lamination2_fv on rc.lamination2_film_variation_id = lamination2_fv.id "
            . "left join film lamination2_f on lamination2_fv.film_id = lamination2_f.id "
            . "where rc.id = $id";
    $fetcher = new Fetcher($sql);
    
    if ($row = $fetcher->Fetch()) {
        $date = $row['date'];
        $name = $row['name'];
        
        $unit = $row['unit']; // Кг или шт
        $quantity = $row['quantity']; // Размер тиража в кг или шт
        $work_type_id = $row['work_type_id']; // Тип работы: с печатью или без печати
        
        if(!empty($row['film_variation_id'])) {
            $film = $row['film']; // Основная пленка, марка
            $thickness = $row['thickness']; // Основная пленка, толщина, мкм
            $density = $row['density']; // Основная пленка, плотность, г/м2
        }
        else {
            $film = $row['individual_film_name']; // Основная пленка, марка
            $thickness = $row['individual_thickness']; // Основная пленка, толщина, мкм
            $density = $row['individual_density']; // Основная пленка, плотность, г/м2
        }
        $price = $row['price']; // Основная пленка, цена
        $currency = $row['currency']; // Основная пленка, валюта
        $customers_material = $row['customers_material']; // Основная плёнка, другая, материал заказчика
        $ski = $row['ski']; // Основная пленка, лыжи
        $width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        if(!empty($row['lamination1_film_variation_id'])) {
            $lamination1_film = $row['lamination1_film']; // Ламинация 1, марка
            $lamination1_thickness = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
            $lamination1_density = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
        }
        else {
            $lamination1_film = $row['lamination1_individual_film_name']; // Ламинация 1, марка
            $lamination1_thickness = $row['lamination1_individual_thickness']; // Ламинация 1, толщина, мкм
            $lamination1_density = $row['lamination1_individual_density']; // Ламинация 1, плотность, г/м2
        }
        $lamination1_price = $row['lamination1_price']; // Ламинация 1, цена
        $lamination1_currency = $row['lamination1_currency']; // Ламинация 1, валюта
        $lamination1_customers_material = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
        $lamination1_ski = $row['lamination1_ski']; // Ламинация 1, лыжи
        $lamination1_width_ski = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
        if(!empty($row['lamination2_film_variation_id'])) {
            $lamination2_film = $row['lamination2_film']; // Ламинация 2, марка
            $lamination2_thickness = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
            $lamination2_density = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
        }
        else {
            $lamination2_film = $row['lamination2_individual_film_name']; // Ламинация 2, марка
            $lamination2_thickness = $row['lamination2_individual_thickness']; // Ламинация 2, толщина, мкм
            $lamination2_density = $row['lamination2_individual_density']; // Ламинация 2, плотность, г/м2
        }
        $lamination2_price = $row['lamination2_price']; // Ламинация 2, цена
        $lamination2_currency = $row['lamination2_currency']; // Ламинация 2, валюта
        $lamination2_customers_material = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
        $lamination2_ski = $row['lamination2_ski']; // Ламинация 2, лыжи
        $lamination2_width_ski = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
        $machine = $row['machine'];
        $machine_shortname = $row['machine_shortname'];
        $machine_id = $row['machine_id'];
        $length = $row['length']; // Длина этикетки, мм
        $stream_width = $row['stream_width']; // Ширина ручья, мм
        $streams_number = $row['streams_number']; // Количество ручьёв
        $raport = $row['raport']; // Рапорт
        $lamination_roller_width = $row['lamination_roller_width']; // Ширина ламинирующего вала
        $ink_number = $row['ink_number']; // Красочность
        
        $ink_1 = $row['ink_1']; $ink_2 = $row['ink_2']; $ink_3 = $row['ink_3']; $ink_4 = $row['ink_4']; $ink_5 = $row['ink_5']; $ink_6 = $row['ink_6']; $ink_7 = $row['ink_7']; $ink_8 = $row['ink_8'];
        $color_1 = $row['color_1']; $color_2 = $row['color_2']; $color_3 = $row['color_3']; $color_4 = $row['color_4']; $color_5 = $row['color_5']; $color_6 = $row['color_6']; $color_7 = $row['color_7']; $color_8 = $row['color_8'];
        $cmyk_1 = $row['cmyk_1']; $cmyk_2 = $row['cmyk_2']; $cmyk_3 = $row['cmyk_3']; $cmyk_4 = $row['cmyk_4']; $cmyk_5 = $row['cmyk_5']; $cmyk_6 = $row['cmyk_6']; $cmyk_7 = $row['cmyk_7']; $cmyk_8 = $row['cmyk_8'];
        $percent_1 = $row['percent_1']; $percent_2 = $row['percent_2']; $percent_3 = $row['percent_3']; $percent_4 = $row['percent_4']; $percent_5 = $row['percent_5']; $percent_6 = $row['percent_6']; $percent_7 = $row['percent_7']; $percent_8 = $row['percent_8'];
        $cliche_1 = $row['cliche_1']; $cliche_2 = $row['cliche_2']; $cliche_3 = $row['cliche_3']; $cliche_4 = $row['cliche_4']; $cliche_5 = $row['cliche_5']; $cliche_6 = $row['cliche_6']; $cliche_7 = $row['cliche_7']; $cliche_8 = $row['cliche_8'];
    }
    
    $error_message = $fetcher->error;
    
    // Курсы валют
    $new_usd = null;
    $new_euro = null;
    
    if(empty($date)) {
        $error_message = "Ошибка при получении даты расчёта";
    }
    
    if(empty($error_message)) {
        $sql = "select usd, euro from currency where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $new_usd = $row['usd'];
            $new_euro = $row['euro'];
        }
    }
    
    // ПОЛУЧЕНИЕ НОРМ
    $tuning_data = new TuningData(null, null, null);
    $laminator_tuning_data = new TuningData(null, null, null);
    $machine_data = new MachineData(null, null, null);
    $laminator_machine_data = new MachineData(null, null, null);
    $ink_data = new InkData(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    $glue_data = new GlueData(null, null, null, null, null, null, null);
    
    if(empty($error_message)) {
        $sql = "select machine_id, time, length, waste_percent from norm_tuning where id in (select max(id) from norm_tuning where date <= '$date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            if($row['machine_id'] == $machine_id) {
                $tuning_data = new TuningData($row['time'], $row['length'], $row['waste_percent']);
            }
        }
        
        $sql = "select time, length, waste_percent from norm_laminator_tuning where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $laminator_tuning_data = new TuningData($row['time'], $row['length'], $row['waste_percent']);
        }
        
        $sql = "select machine_id, price, speed, max_width from norm_machine where id in (select max(id) from norm_machine where date <= '$date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            if($row['machine_id'] == $machine_id) {
                $machine_data = new MachineData($row['price'], $row['speed'], $row['max_width']);
            }
        }
        
        $sql = "select price, speed, max_width from norm_laminator where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $laminator_machine_data = new MachineData($row['price'], $row['speed'], $row['max_width']);
        }
        
        $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, solvent_part, min_price "
                . "from norm_ink where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $ink_data = new InkData($row['c'], $row['c_currency'], $row['c_expense'], $row['m'], $row['m_currency'], $row['m_expense'], $row['y'], $row['y_currency'], $row['y_expense'], $row['k'], $row['k_currency'], $row['k_expense'], $row['white'], $row['white_currency'], $row['white_expense'], $row['panton'], $row['panton_currency'], $row['panton_expense'], $row['lacquer'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price']);
        }
        
        $sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, solvent_part "
                . "from norm_glue where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $glue_data = new GlueData($row['glue'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent'], $row['solvent_currency'], $row['solvent_part']);
        }
    }
    
    // ДЕЛАЕМ РАСЧЁТ
    $calculation = new Calculation($tuning_data, $laminator_tuning_data, $machine_data, $laminator_machine_data, $ink_data, $glue_data, $usd, $euro, $unit, $quantity, $work_type_id, $film, $thickness, $density, $price, $currency, $customers_material, $ski, $width_ski, $lamination1_film, $lamination1_thickness, $lamination1_density, $lamination1_price, $lamination1_currency, $lamination1_customers_material, $lamination1_ski, $lamination1_width_ski, $lamination2_film, $lamination2_thickness, $lamination2_density, $lamination2_price, $lamination2_currency, $lamination2_customers_material, $lamination2_ski, $lamination2_width_ski, $machine_id, $machine_shortname, $length, $stream_width, $streams_number, $raport, $lamination_roller_width, $ink_number, $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8);
    
    // Себестоимость
    $new_cost = null;
    $new_cost_per_unit = null; 
    $new_material = null;
    $new_material_price = null; 
    $new_material_price_per_unit = null; 
    $new_material_width = null; 
    $new_material_weight = null; 
    $new_material_length = null; 
    
    // Масса с приладкой = масса плёнки грязная
    $new_material_weight_with_tuning = $calculation->mdirty->value;
    
    // Длина с приладкой = длина плёнки грязная
    $new_material_length_with_tuning = $calculation->lengthdirty->value;
    $new_material_lamination1_price = null; $new_material_lamination1_price_per_unit = null; $new_material_lamination1_width = null; $new_material_lamination1_weight = null; $new_material_lamination1_length = null; $new_material_lamination1_weight_with_tuning = null; $new_material_lamination1_length_with_tuning = null;
    $new_material_lamination2_price = null; $new_material_lamination2_price_per_unit = null; $new_material_lamination2_width = null; $new_material_lamination2_weight = null; $new_material_lamination2_length = null; $new_material_lamination2_weight_with_tuning = null; $new_material_lamination2_length_with_tuning = null;
    $new_expenses_waste = null; $new_expenses_waste_weight = null; $new_expenses_ink = null; $new_expenses_ink_weight = null; $new_expenses_work = null; $new_expenses_work_time = null;
    $new_expenses_lamination1_waste = null; $new_expenses_lamination1_waste_weight = null; $new_expenses_lamination1_glue = null; $new_expenses_lamination1_glue_weight = null; $new_expenses_lamination1_work = null; $new_expenses_lamination1_work_time = null;
    $new_expenses_lamination2_waste = null; $new_expenses_lamination2_waste_weight = null; $new_expenses_lamination2_glue = null; $new_expenses_lamination2_glue_weight = null; $new_expenses_lamination2_work = null; $new_expenses_lamination2_work_time = null;
    
    // Наценка
    $new_extracharge = null;
    $ech_weight = $calculation->weight->value;
    $ech_type = 0;
    
    if($work_type_id == Calculation::WORK_TYPE_NOPRINT) {
        $ech_type = ET_NOPRINT;
    }
    elseif($calculation->laminations_number == 0) {
        $ech_type = ET_PRINT;
    }
    elseif($calculation->laminations_number == 1) {
        $ech_type = ET_PRINT_1;
    }
    elseif($calculation->laminations_number == 2) {
        $ech_type = ET_PRINT_2;
    }
    
    if($ech_type == 0) {
        $error_message = "Неправильно определён тип наценки";
    }
    
    if(empty($error_message)) {
        $sql = "select value from extracharge where extracharge_type_id = $ech_type and $ech_weight >= from_weight and ($ech_weight <= to_weight or to_weight is null)";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $new_extracharge = $row['value'];
        }
    }
    
    if($new_extracharge === null) {
        $error_message = "Ошибка при определении наценки";
    }
    
    // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
    if(empty($error_message)) {
        $sql = "insert into calculation_result (extracharge, usd, euro, cost, cost_per_unit, material, "
                . "material_price, material_price_per_unit, material_width, material_weight, material_length, material_weight_with_tuning, material_length_with_tuning, "
                . "material_lamination1_price, material_lamination1_price_per_unit, material_lamination1_width, material_lamination1_weight, material_lamination1_length, material_lamination1_weight_with_tuning, material_lamination1_length_with_tuning, "
                . "material_lamination2_price, material_lamination2_price_per_unit, material_lamination2_width, material_lamination2_weight, material_lamination2_length, material_lamination2_weight_with_tuning, material_lamination2_length_with_tuning, "
                . "expenses_waste, expenses_waste_weight, expenses_ink, expenses_ink_weight, expenses_work, expenses_work_time, "
                . "expenses_lamination1_waste, expenses_lamination1_waste_weight, expenses_lamination1_glue, expenses_lamination1_glue_weight, expenses_lamination1_work, expenses_lamination1_work_time, "
                . "expenses_lamination2_waste, expenses_lamination2_waste_weight, expenses_lamination2_glue, expenses_lamination2_glue_weight, expenses_lamination2_work, expenses_lamination2_work_time) "
                . "values ($new_extracharge, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_material, "
                . "$new_material_price, $new_material_price_per_unit, $new_material_width, $new_material_weight, $new_material_length, $new_material_weight_with_tuning, $new_material_length_with_tuning, "
                . "$new_material_lamination1_price, $new_material_lamination1_price_per_unit, $new_material_lamination1_width, $new_material_lamination1_weight, $new_material_lamination1_length, $new_material_lamination1_weight_with_tuning, $new_material_lamination1_length_with_tuning, "
                . "$new_material_lamination2_price, $new_material_lamination2_price_per_unit, $new_material_lamination2_width, $new_material_lamination2_weight, $new_material_lamination2_length, $new_material_lamination2_weight_with_tuning, $new_material_lamination2_length_with_tuning, "
                . "$new_expenses_waste, $new_expenses_waste_weight, $new_expenses_ink, $new_expenses_ink_weight, $new_expenses_work, $new_expenses_work_time, "
                . "$new_expenses_lamination1_waste, $new_expenses_lamination1_waste_weight, $new_expenses_lamination1_glue, $new_expenses_lamination1_glue_weight, $new_expenses_lamination1_work, $new_expenses_lamination1_work_time, "
                . "$new_expenses_lamination2_waste, $new_expenses_lamination2_waste_weight, $new_expenses_lamination2_glue, $new_expenses_lamination2_glue_weight, $new_expenses_lamination2_work, $new_expenses_lamination2_work_time)";
        echo $sql;
    }
}
?>
<div id="calculation"<?=$calculation_class ?> style="position: absolute; bottom: auto; right: 10px; margin-top: 60px;">
    <div style="position: absolute; right: 30px; top: 0px;" class="d-none">
        <a class="btn btn-outline-dark" target="_blank" style="margin-top: 20px;" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
    </div>
    <div class="d-flex justify-content-between p-2">
        <div>
            <h1>Расчет</h1>
        </div>
        <div>
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
            <a class="btn btn-outline-dark" target="_blank" style="width: 3rem;" title="Печать" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка</div>
                <?php if($status_id == 1 || $status_id == 2): ?>
                <div class="input-group">
                    <input type="text" id="extracharge" name="extracharge" data-id="<?=$id ?>" style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" value="30" required="required" />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <?php else: ?>
                <span class="text-nowrap">30%</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                <?=number_format($euro, 2, ',', ' ') ?>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                <?=number_format($usd, 2, ',', ' ') ?>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <h2>Стоимость</h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Себестоимость</h3>
            <div>Себестоимость</div>
            <div class="value mb-2">860 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765,563 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость</div>
            <div class="value">1 200 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">236,216 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
        </div>
        <div class="col-4" style="width: 250px;"></div>
    </div>
    <?php if($work_type_id == 2): ?>
    <div class="row text-nowrap">
        <div class="col-12">
            <div>Себестоимость форм</div>
            <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;420&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;329,5&nbsp;мм</span></div>    
        </div>
    </div>
    <?php endif; ?>
    <div class="mt-3">
        <h2>Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 кг</span></h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Основная пленка&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">236 &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2">800 мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
        </div>
        <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 1&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">236 &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2">800 мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
        </div>
        <?php else: ?>
        <div class="col-4" style="width: 250px;"></div>
        <?php endif; ?>
        <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 2&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">236 &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2">800 мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
        </div>
        <?php else: ?>
        <div class="col-4" style="width: 250px;"></div>
        <?php endif; ?>
    </div>
    <?php
    if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name) || !empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name) || $work_type_id == 2):
    ?>
    <div id="show_costs">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
            </div>
            <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <?php endif; ?>
            <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <?php endif; ?>
        </div>
    </div>
    <div id="costs" class="d-none">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                <h2 class="mt-2">Расходы</h2>
            </div>
            <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <?php endif; ?>
            <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <?php endif; ?>
        </div>
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <div>Отходы</div>
                <div class="value mb-2">1 280 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">4,5 кг</span></div>
                <?php if($work_type_id == 2): ?>
                <div>Краска</div>
                <div class="value mb-2">17 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">17,5 кг</span></div>
                <?php
                endif;
                if($work_type_id == 2):
                ?>
                <div>Печать тиража</div>
                <div class="value mb-2">470 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">6 ч. 30 мин.</span></div>
                <?php
                endif;
                ?>
            </div>
            <?php if(!empty($lamination1_film_variation_id) || !empty($lamination1_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2">1 280 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">4,5 кг</span></div>
                <div>Клей</div>
                <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">1,0 кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2">1 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">3 часа</span></div>
            </div>
            <?php else: ?>
            <div class="col-4" style="width: 250px;"></div>
            <?php endif; ?>
            <?php if(!empty($lamination2_film_variation_id) || !empty($lamination2_individual_film_name)): ?>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2">1 280 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">4,5 кг</span></div>
                <div>Клей</div>
                <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">1,0 кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2">1 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">3 часа</span></div>
            </div>
            <?php else: ?>
            <div class="col-4" style="width: 250px;"></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    endif;
    ?>
    <div style="clear:both"></div>
    <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
    <input type="hidden" id="change_status_submit" name="change_status_submit" />
        <?php if (empty($techmap_id)): ?>
    <a href="techmap.php?calculation_id=<?=$id ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Составить тех. карту</a>
        <?php else: ?>
    <a href="techmap.php?id=<?=$techmap_id ?>" class="btn btn-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
        <?php endif; ?>
</div>