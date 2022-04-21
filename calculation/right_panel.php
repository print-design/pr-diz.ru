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
$material_lamination1_price = null; $material_lamination1_price_per_unit = null; $material_lamination1_width = null; $lamination1_weight_pure = null; $lamination1_length_pure = null; $lamination1_weight_dirty = null; $lamination1_length_dirty = null;
$material_lamination2_price = null; $material_lamination2_price_per_unit = null; $material_lamination2_width = null; $lamination2_weight_pure = null; $lamination2_length_pure = null; $lamination2_weight_dirty = null; $lamination2_length_dirty = null;
$film_waste_price = null; $film_waste = null; $ink_price = null; $ink_weight = null; $work_price = null; $work_time = null;
$lamination1_film_waste_price = null; $lamination1_film_waste = null; $glue_price1 = null; $glue_expense1 = null; $lamination1_work_price = null; $lamination1_work_time = null;
$lamination2_film_waste_price = null; $lamination2_film_waste = null; $glue_price2 = null; $glue_expense2 = null; $lamination2_work_price = null; $lamination2_work_time = null;
        
$sql_calculation_result = "select extracharge, usd, euro, cost, cost_per_unit, material, "
        . "material_price, material_price_per_unit, material_width, material_weight, material_length, material_weight_with_tuning, material_length_with_tuning, "
        . "material_lamination1_price, material_lamination1_price_per_unit, material_lamination1_width, lamination1_weight_pure, lamination1_length_pure, lamination1_weight_dirty, lamination1_length_dirty, "
        . "material_lamination2_price, material_lamination2_price_per_unit, material_lamination2_width, lamination2_weight_pure, lamination2_length_pure, lamination2_weight_dirty, lamination2_length_dirty, "
        . "film_waste_price, film_waste, ink_price, ink_weight, work_price, work_time, "
        . "lamination1_film_waste_price, lamination1_film_waste, glue_price1, glue_expense1, lamination1_work_price, lamination1_work_time, "
        . "lamination2_film_waste_price, lamination2_film_waste, glue_price2, glue_expense2, lamination2_work_price, lamination2_work_time "
        . "from calculation_result where calculation_id = $id order by id desc limit 1";
$fetcher = new Fetcher($sql_calculation_result);

if($row = $fetcher->Fetch()) {
    $extracharge = $row['extracharge']; $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $material = $row['material'];
    $material_price = $row['material_price']; $material_price_per_unit = $row['material_price_per_unit']; $material_width = $row['material_width']; $material_weight = $row['material_weight']; $material_length = $row['material_length']; $material_weight_with_tuning = $row['material_weight_with_tuning']; $material_length_with_tuning = $row['material_length_with_tuning'];
    $material_lamination1_price = $row['material_lamination1_price']; $material_lamination1_price_per_unit = $row['material_lamination1_price_per_unit']; $material_lamination1_width = $row['material_lamination1_width']; $lamination1_weight_pure = $row['lamination1_weight_pure']; $lamination1_length_pure = $row['lamination1_length_pure']; $lamination1_weight_dirty = $row['lamination1_weight_dirty']; $lamination1_length_dirty = $row['lamination1_length_dirty'];
    $material_lamination2_price = $row['material_lamination2_price']; $material_lamination2_price_per_unit = $row['material_lamination2_price_per_unit']; $material_lamination2_width = $row['material_lamination2_width']; $lamination2_weight_pure = $row['lamination2_weight_pure']; $lamination2_length_pure = $row['lamination2_length_pure']; $lamination2_weight_dirty = $row['lamination2_weight_dirty']; $lamination2_length_dirty = $row['lamination2_length_dirty'];
    $film_waste_price = $row['film_waste_price']; $film_waste = $row['film_waste']; $ink_price = $row['ink_price']; $ink_weight = $row['ink_weight']; $work_price = $row['work_price']; $work_time = $row['work_time'];
    $lamination1_film_waste_price = $row['lamination1_film_waste_price']; $lamination1_film_waste = $row['lamination1_film_waste']; $glue_price1 = $row['glue_price1']; $glue_expense1 = $row['glue_expense1']; $lamination1_work_price = $row['lamination1_work_price']; $lamination1_work_time = $row['lamination1_work_time'];
    $lamination2_film_waste_price = $row['lamination2_film_waste_price']; $lamination2_film_waste = $row['lamination2_film_waste']; $glue_price2 = $row['glue_price2']; $glue_expense2 = $row['glue_expense2']; $lamination2_work_price = $row['lamination2_work_price']; $lamination2_work_time = $row['lamination2_work_time'];
}
else {
    include './calculation.php';
    
    // ПОЛУЧАЕМ ИСХОДНЫЕ ДАННЫЕ
    $param_date = null;
    $param_name = null;
    $param_unit = null; // Кг или шт
    $param_quantity = null; // Размер тиража
    $param_work_type_id = null; // Типа работы: с печатью или без печати
    
    $param_film = null; // Основная пленка, марка
    $param_thickness = null; // Основная пленка, толщина, мкм
    $param_density = null; // Основная пленка, плотность, г/м2
    $param_price = null; // Основная пленка, цена
    $param_currency = null; // Основная пленка, валюта
    $param_customers_material = null; // Основная плёнка, другая, материал заказчика
    $param_ski = null; // Основная пленка, лыжи
    $param_width_ski = null; // Основная пленка, ширина пленки, мм
        
    $param_lamination1_film = null; // Ламинация 1, марка
    $param_lamination1_thickness = null; // Ламинация 1, толщина, мкм
    $param_lamination1_density = null; // Ламинация 1, плотность, г/м2
    $param_lamination1_price = null; // Ламинация 1, цена
    $param_lamination1_lamination1_currency = null; // Ламинация 1, валюта
    $param_lamination1_customers_material = null; // Ламинация 1, другая, материал заказчика
    $param_lamination1_ski = null; // Ламинация 1, лыжи
    $param_lamination1_width_ski = null; // Ламинация 1, ширина пленки, мм

    $param_lamination2_film = null; // Ламинация 2, марка
    $param_lamination2_thickness = null; // Ламинация 2, толщина, мкм
    $param_lamination2_density = null; // Ламинация 2, плотность, г/м2
    $param_lamination2_price = null; // Ламинация 2, цена
    $param_lamination2_currency = null; // Ламинация 2, валюта
    $param_lamination2_customers_material = null; // Ламинация 2, другая, уд. вес
    $param_lamination2_ski = null; // Ламинация 2, лыжи
    $param_lamination2_width_ski = null;  // Ламинация 2, ширина пленки, мм
    
    $param_machine = null;
    $param_machine_shortname = null;
    $param_machine_id = null;
    $param_length = null; // Длина этикетки, мм
    $param_width = null; // Обрезная ширина, мм (если плёнка без печати)
    $param_stream_width = null; // Ширина ручья, мм (если плёнка с печатью)
    $param_streams_number = null; // Количество ручьёв
    $param_raport = null; // Рапорт
    $param_lamination_roller_width = null; // Ширина ламинирующего вала
    $param_ink_number = 0; // Красочность
    
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
        $param_date = $row['date'];
        $param_name = $row['name'];
        
        $param_unit = $row['unit']; // Кг или шт
        $param_quantity = $row['quantity']; // Размер тиража в кг или шт
        $param_work_type_id = $row['work_type_id']; // Тип работы: с печатью или без печати
        
        if(!empty($row['film_variation_id'])) {
            $param_film = $row['film']; // Основная пленка, марка
            $param_thickness = $row['thickness']; // Основная пленка, толщина, мкм
            $param_density = $row['density']; // Основная пленка, плотность, г/м2
        }
        else {
            $param_film = $row['individual_film_name']; // Основная пленка, марка
            $param_thickness = $row['individual_thickness']; // Основная пленка, толщина, мкм
            $param_density = $row['individual_density']; // Основная пленка, плотность, г/м2
        }
        $param_price = $row['price']; // Основная пленка, цена
        $param_currency = $row['currency']; // Основная пленка, валюта
        $param_customers_material = $row['customers_material']; // Основная плёнка, другая, материал заказчика
        $param_ski = $row['ski']; // Основная пленка, лыжи
        $param_width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        if(!empty($row['lamination1_film_variation_id'])) {
            $param_lamination1_film = $row['lamination1_film']; // Ламинация 1, марка
            $param_lamination1_thickness = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
            $param_lamination1_density = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
        }
        else {
            $param_lamination1_film = $row['lamination1_individual_film_name']; // Ламинация 1, марка
            $param_lamination1_thickness = $row['lamination1_individual_thickness']; // Ламинация 1, толщина, мкм
            $param_lamination1_density = $row['lamination1_individual_density']; // Ламинация 1, плотность, г/м2
        }
        $param_lamination1_price = $row['lamination1_price']; // Ламинация 1, цена
        $param_lamination1_currency = $row['lamination1_currency']; // Ламинация 1, валюта
        $param_lamination1_customers_material = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
        $param_lamination1_ski = $row['lamination1_ski']; // Ламинация 1, лыжи
        $param_lamination1_width_ski = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
        if(!empty($row['lamination2_film_variation_id'])) {
            $param_lamination2_film = $row['lamination2_film']; // Ламинация 2, марка
            $param_lamination2_thickness = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
            $param_lamination2_density = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
        }
        else {
            $param_lamination2_film = $row['lamination2_individual_film_name']; // Ламинация 2, марка
            $param_lamination2_thickness = $row['lamination2_individual_thickness']; // Ламинация 2, толщина, мкм
            $param_lamination2_density = $row['lamination2_individual_density']; // Ламинация 2, плотность, г/м2
        }
        $param_lamination2_price = $row['lamination2_price']; // Ламинация 2, цена
        $param_lamination2_currency = $row['lamination2_currency']; // Ламинация 2, валюта
        $param_lamination2_customers_material = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
        $param_lamination2_ski = $row['lamination2_ski']; // Ламинация 2, лыжи
        $param_lamination2_width_ski = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
        $param_machine = $row['machine'];
        $param_machine_shortname = $row['machine_shortname'];
        $param_machine_id = $row['machine_id'];
        $param_length = $row['length']; // Длина этикетки, мм
        $param_stream_width = $row['stream_width']; // Ширина ручья, мм
        $param_streams_number = $row['streams_number']; // Количество ручьёв
        $param_raport = $row['raport']; // Рапорт
        $param_lamination_roller_width = $row['lamination_roller_width']; // Ширина ламинирующего вала
        $param_ink_number = $row['ink_number']; // Красочность
        
        $param_ink_1 = $row['ink_1']; $param_ink_2 = $row['ink_2']; $param_ink_3 = $row['ink_3']; $param_ink_4 = $row['ink_4']; $param_ink_5 = $row['ink_5']; $param_ink_6 = $row['ink_6']; $param_ink_7 = $row['ink_7']; $param_ink_8 = $row['ink_8'];
        $param_color_1 = $row['color_1']; $param_color_2 = $row['color_2']; $param_color_3 = $row['color_3']; $param_color_4 = $row['color_4']; $param_color_5 = $row['color_5']; $param_color_6 = $row['color_6']; $param_color_7 = $row['color_7']; $param_color_8 = $row['color_8'];
        $param_cmyk_1 = $row['cmyk_1']; $param_cmyk_2 = $row['cmyk_2']; $param_cmyk_3 = $row['cmyk_3']; $param_cmyk_4 = $row['cmyk_4']; $param_cmyk_5 = $row['cmyk_5']; $param_cmyk_6 = $row['cmyk_6']; $param_cmyk_7 = $row['cmyk_7']; $param_cmyk_8 = $row['cmyk_8'];
        $param_percent_1 = $row['percent_1']; $param_percent_2 = $row['percent_2']; $param_percent_3 = $row['percent_3']; $param_percent_4 = $row['percent_4']; $param_percent_5 = $row['percent_5']; $param_percent_6 = $row['percent_6']; $param_percent_7 = $row['percent_7']; $param_percent_8 = $row['percent_8'];
        $param_cliche_1 = $row['cliche_1']; $param_cliche_2 = $row['cliche_2']; $param_cliche_3 = $row['cliche_3']; $param_cliche_4 = $row['cliche_4']; $param_cliche_5 = $row['cliche_5']; $param_cliche_6 = $row['cliche_6']; $param_cliche_7 = $row['cliche_7']; $param_cliche_8 = $row['cliche_8'];
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
        $sql = "select machine_id, time, length, waste_percent from norm_tuning where id in (select max(id) from norm_tuning where date <= '$param_date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            if($row['machine_id'] == $param_machine_id) {
                $tuning_data = new TuningData($row['time'], $row['length'], $row['waste_percent']);
            }
        }
        
        $sql = "select time, length, waste_percent from norm_laminator_tuning where date <= '$param_date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $laminator_tuning_data = new TuningData($row['time'], $row['length'], $row['waste_percent']);
        }
        
        $sql = "select machine_id, price, speed, max_width from norm_machine where id in (select max(id) from norm_machine where date <= '$param_date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            if($row['machine_id'] == $param_machine_id) {
                $machine_data = new MachineData($row['price'], $row['speed'], $row['max_width']);
            }
        }
        
        $sql = "select price, speed, max_width from norm_laminator where date <= '$param_date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $laminator_machine_data = new MachineData($row['price'], $row['speed'], $row['max_width']);
        }
        
        $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, solvent_part, min_price "
                . "from norm_ink where date <= '$param_date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $ink_data = new InkData($row['c'], $row['c_currency'], $row['c_expense'], $row['m'], $row['m_currency'], $row['m_expense'], $row['y'], $row['y_currency'], $row['y_expense'], $row['k'], $row['k_currency'], $row['k_expense'], $row['white'], $row['white_currency'], $row['white_expense'], $row['panton'], $row['panton_currency'], $row['panton_expense'], $row['lacquer'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price']);
        }
        
        $sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, solvent_part "
                . "from norm_glue where date <= '$param_date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $glue_data = new GlueData($row['glue'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent'], $row['solvent_currency'], $row['solvent_part']);
        }
    }
    
    // ДЕЛАЕМ РАСЧЁТ
    $calculation = new Calculation($tuning_data, $laminator_tuning_data, $machine_data, $laminator_machine_data, $ink_data, $glue_data, $new_usd, $new_euro, 
            $param_unit, $param_quantity, $param_work_type_id, $param_film, $param_thickness, $param_density, $param_price, $param_currency, $param_customers_material, $param_ski, $param_width_ski, 
            $param_lamination1_film, $param_lamination1_thickness, $param_lamination1_density, $param_lamination1_price, $param_lamination1_currency, $param_lamination1_customers_material, $param_lamination1_ski, $param_lamination1_width_ski, 
            $param_lamination2_film, $param_lamination2_thickness, $param_lamination2_density, $param_lamination2_price, $param_lamination2_currency, $param_lamination2_customers_material, $param_lamination2_ski, $param_lamination2_width_ski, 
            $param_machine_id, $param_machine_shortname, $param_length, $param_stream_width, $param_streams_number, $param_raport, $param_lamination_roller_width, $param_ink_number, 
            $param_ink_1, $param_ink_2, $param_ink_3, $param_ink_4, $param_ink_5, $param_ink_6, $param_ink_7, $param_ink_8, 
            $param_color_1, $param_color_2, $param_color_3, $param_color_4, $param_color_5, $param_color_6, $param_color_7, $param_color_8, 
            $param_cmyk_1, $param_cmyk_2, $param_cmyk_3, $param_cmyk_4, $param_cmyk_5, $param_cmyk_6, $param_cmyk_7, $param_cmyk_8, 
            $param_percent_1, $param_percent_2, $param_percent_3, $param_percent_4, $param_percent_5, $param_percent_6, $param_percent_7, $param_percent_8, 
            $param_cliche_1, $param_cliche_2, $param_cliche_3, $param_cliche_4, $param_cliche_5, $param_cliche_6, $param_cliche_7, $param_cliche_8);
    
    // Себестоимость = стоимость плёнки + работ + краски + клея
    $film_cost = $calculation->film_price->value + (empty($calculation->lamination1_film_price) ? 0 : $calculation->lamination1_film_price->value) + (empty($calculation->lamination2_film_price) ? 0 : $calculation->lamination2_film_price->value);
    $work_cost = (empty($calculation->work_price) ? 0 : $calculation->work_price->value) + (empty($calculation->lamination1_work_price) ? 0 : $calculation->lamination1_work_price->value) + (empty($calculation->lamination2_work_price) ? 0 : $calculation->lamination2_work_price->value);
    $ink_cost = 0;
    for($i=1; $i<=$param_ink_number; $i++) {
        if(!empty($calculation->ink_prices[$i]->value)) {
            $ink_cost += $calculation->ink_prices[$i]->value;
        }
    }
    $glue_cost = (empty($calculation->glue_price1) ? 0 : $calculation->glue_price1->value) + (empty($calculation->glue_price2) ? 0 : $calculation->glue_price2->value);
    $new_cost = $film_cost + $work_cost + $ink_cost + $glue_cost;
    if($new_cost === null) $new_cost = "NULL";
    
    // Себестоимость на 1 шт/кг = Себестоимость / массу тиража или кол-во штук
    $new_cost_per_unit = $new_cost / $param_quantity;
    if($new_cost_per_unit === null) $new_cost_per_unit = "NULL";
    
    // Материалы = масса с приладкой осн. + масса с приладкой лам. 1 + масса с приладкой лам. 2
    $new_material = $calculation->weight_dirty->value + (empty($calculation->lamination1_weight_dirty) ? 0 : $calculation->lamination1_weight_dirty->value) + (empty($calculation->lamination2_weight_dirty) ? 0 : $calculation->lamination2_weight_dirty->value);
    if($new_material === null) $new_material = "NULL";
    
    // Основная пленка цена = стоимость основной плёнки
    $new_material_price = $calculation->film_price->value;
    if($new_material_price === null) $new_material_price = "NULL";
    
    // Основная плёнка цена за шт/кг = стоимость основной плёнки / количество
    $new_material_price_per_unit = $calculation->film_price->value / $param_quantity;
    if($new_material_price_per_unit === null) $new_material_price_per_unit = "NULL";
    
    // Ширина основной плёнки = ширина осн. плёнки
    $new_material_width = $calculation->width->value;
    if($new_material_width === null) $new_material_width = "NULL";
    
    // Масса без приладки = масса плёнки чистая
    $new_material_weight = $calculation->weight_pure->value;
    if($new_material_weight === null) $new_material_weight = "NULL";
    
    // Длина без приладки = длина плёнки чистая
    $new_material_length = $calculation->length_pure->value;
    if($new_material_length === null) $new_material_length = "NULL";
    
    // Масса с приладкой = масса плёнки грязная
    $new_material_weight_with_tuning = $calculation->weight_dirty->value;
    if($new_material_weight_with_tuning === null) $new_material_weight_with_tuning = "NULL";
    
    // Длина с приладкой = метры погонные грязные
    $new_material_length_with_tuning = $calculation->length_dirty->value;
    if($new_material_length_with_tuning === null) $new_material_length_with_tuning = "NULL";
    
    // Лам 1 цена = лам 1 цена
    $new_material_lamination1_price = empty($calculation->lamination1_film_price) ? null : $calculation->lamination1_film_price->value;
    if($new_material_lamination1_price === null) $new_material_lamination1_price = "NULL";
    
    // Лам 1 цена за шт/кг = лам 1 цена / кол-во
    $new_material_lamination1_price_per_unit = (empty($calculation->lamination1_film_price) ? 0 : $calculation->lamination1_film_price->value) / $param_quantity;
    if($new_material_lamination1_price_per_unit === null) $new_material_lamination1_price_per_unit = "NULL";
    
    // Лам 1 ширина = лам 1 ширина
    $new_material_lamination1_width = empty($calculation->lamination1_width) ? null : $calculation->lamination1_width->value;
    if($new_material_lamination1_width === null) $new_material_lamination1_width = "NULL";
    
    // Лам 1 масса без приладки = лам 1 масса чистая
    $new_lamination1_weight_pure = empty($calculation->lamination1_weight_pure) ? null : $calculation->lamination1_weight_pure->value;
    if($new_lamination1_weight_pure === null) $new_lamination1_weight_pure = "NULL";
    
    // Лам 1 длина без приладки = лам 1 длина чистая
    $new_lamination1_length_pure = empty($calculation->lamination1_length_pure) ? null : $calculation->lamination1_length_pure->value;
    if($new_lamination1_length_pure === null) $new_lamination1_length_pure = "NULL";
    
    // Лам 1 масса с приладкой = лам 1 масса грязная
    $new_lamination1_weight_dirty = empty($calculation->lamination1_weight_dirty) ? null : $calculation->lamination1_weight_dirty->value;
    if($new_lamination1_weight_dirty === null) $new_lamination1_weight_dirty = "NULL";
    
    // Лам 1 длина с приладкой = лам 1 длина грязная
    $new_lamination1_length_dirty = empty($calculation->lamination1_length_dirty) ? null : $calculation->lamination1_length_dirty->value;
    if($new_lamination1_weight_dirty === null) $new_lamination1_weight_dirty = "NULL";
    
    // Лам 2 плёнка цена
    $new_material_lamination2_price = empty($calculation->lamination2_film_price) ? null : $calculation->lamination2_film_price->value;
    if($new_material_lamination2_price === null) $new_material_lamination2_price = "NULL";
    
    // Лам 2 цена за шт/кг = лам 2 плёнка цена / кол-во
    $new_material_lamination2_price_per_unit = (empty($calculation->lamination2_film_price) ? 0 : $calculation->lamination2_film_price->value) / $param_quantity;
    if($new_material_lamination2_price_per_unit === null) $new_material_lamination2_price_per_unit = "NULL";
    
    // Лам 2 ширина = лам 2 ширина
    $new_material_lamination2_width = empty($calculation->lamination2_width) ? null : $calculation->lamination2_width->value;
    if($new_material_lamination2_width === null) $new_material_lamination2_width = "NULL";
    
    // Лам 2 масса без приладки
    $new_lamination2_weight_pure = empty($calculation->lamination2_weight_pure) ? null : $calculation->lamination2_weight_pure->value;
    if($new_lamination2_weight_pure === null) $new_lamination2_weight_pure = "NULL";
    
    // Лам 2 длина без приладки
    $new_lamination2_length_pure = empty($calculation->lamination2_length_pure) ? null : $calculation->lamination2_length_pure->value;
    if($new_lamination2_length_pure === null) $new_lamination2_length_pure = "NULL";
    
    // Лам 2 масса с приладкой = лам 2 масса грязная
    $new_lamination2_weight_dirty = empty($calculation->lamination2_weight_dirty) ? null : $calculation->lamination2_weight_dirty->value;
    if($new_lamination2_weight_dirty === null) $new_lamination2_weight_dirty = "NULL";
    
    // Лам 2 длина с приладкой = лам 2 длина грязная
    $new_lamination2_length_dirty = empty($calculation->lamination2_length_dirty) ? null : $calculation->lamination2_length_dirty->value;
    if($new_lamination2_length_dirty === null) $new_lamination2_length_dirty = "NULL";
    
    // Отходы плёнка цена = (масса грязная - масса чистая) * стоимость за 1 кг * курс валюты
    $new_film_waste_price = ($calculation->weight_dirty->value - $calculation->weight_pure->value) * $param_price * $calculation->GetCurrencyRate($param_currency, $new_usd, $new_euro);
    if($new_film_waste_price === null) $new_film_waste_price = "NULL";
    
    // Отходы плёнка масса = масса грязная - масса чистая
    $new_film_waste = $calculation->weight_dirty->value - $calculation->weight_pure->value;
    if($new_film_waste === null) $new_film_waste = "NULL";
    
    // Стоимость всех красок
    $ink_price = 0;
    for($i=1; $i<=$param_ink_number; $i++) {
        if(!empty($calculation->ink_prices[$i]->value)) {
            $ink_price += $calculation->ink_prices[$i]->value;
        }
    }
    $new_ink_price = null;
    if(!empty($ink_price)) $new_ink_price = $ink_price;
    if($new_ink_price === null) $new_ink_price = "NULL";
    
    // Расход всех красок
    $ink_expense = 0;
    for($i=1; $i<=$param_ink_number; $i++) {
        if(!empty($calculation->ink_expenses[$i]->value)) {
            $ink_expense += $calculation->ink_expenses[$i]->value;
        }
    }
    $new_ink_weight = null;
    if(!empty($ink_expense)) $new_ink_weight = $ink_expense;
    if($new_ink_weight === null) $new_ink_weight = "NULL";
    
    // Работа по печати тиража, руб
    $new_work_price = empty($calculation->work_price) ? null : $calculation->work_price->value;
    if($new_work_price === null) $new_work_price = "NULL";
    
    // Работа по печати тиража, ч
    $new_work_time = empty($calculation->work_time->value) ? null : $calculation->work_time->value;
    if($new_work_time === null) $new_work_time = "NULL";
    
    // Отходы плёнки ламинации 1, руб
    $new_lamination1_film_waste_price = (empty($calculation->lamination1_weight_dirty) || empty($calculation->lamination1_weight_pure)) ? null : ($calculation->lamination1_weight_dirty->value - $calculation->lamination1_weight_pure->value) * $param_lamination1_price * $calculation->GetCurrencyRate($param_lamination1_currency, $new_usd, $new_euro);
    if($new_lamination1_film_waste_price === null) $new_lamination1_film_waste_price = "NULL";
    
    // Отходы плёнки ламинации 1, кг
    $new_lamination1_film_waste = (empty($calculation->lamination1_weight_dirty) || empty($calculation->lamination1_weight_pure)) ? null : $calculation->lamination1_weight_dirty->value - $calculation->lamination1_weight_pure->value;
    if($new_lamination1_film_waste === null) $new_lamination1_film_waste = "NULL";
    
    // Стоимость клея лам 1
    $new_glue_price1 = empty($calculation->glue_price1) ? null : $calculation->glue_price1->value;
    if($new_glue_price1 === null) $new_glue_price1 = "NULL";
    
    // Расход клея лам 1
    $new_glue_expense1 = empty($calculation->glue_expense1) ? null : $calculation->glue_expense1->value;
    if($new_glue_expense1 === null) $new_glue_expense1 = "NULL";
    
    // Работа лам 1, руб
    $new_lamination1_work_price = empty($calculation->lamination1_work_price) ? null : $calculation->lamination1_work_price->value;
    if($new_lamination1_work_price === null) $new_lamination1_work_price = "NULL";
    
    // Работа лам 1, ч
    $new_lamination1_work_time = empty($calculation->lamination1_work_time) ? null : $calculation->lamination1_work_time->value;
    if($new_lamination1_work_time === null) $new_lamination1_work_time = "NULL";
    
    // Отходы плёнки лам 2, руб
    $new_lamination2_film_waste_price = (empty($calculation->lamination2_weight_dirty) || empty($calculation->lamination2_weight_pure)) ? null : ($calculation->lamination2_weight_dirty->value - $calculation->lamination2_weight_pure->value) * $param_lamination2_price * $calculation->GetCurrencyRate($param_lamination2_currency, $new_usd, $new_euro);
    if($new_lamination2_film_waste_price === null) $new_lamination2_film_waste_price = "NULL";
    
    // Отходы плёнки лам 2, кг
    $new_lamination2_film_waste = (empty($calculation->lamination2_weight_dirty) || empty($calculation->lamination2_weight_pure)) ? null : $calculation->lamination2_weight_dirty->value - $calculation->lamination2_weight_pure->value;
    if($new_lamination2_film_waste === null) $new_lamination2_film_waste = "NULL";
    
    // Стоимость клея лам 2
    $new_glue_price2 = empty($calculation->glue_price2) ? null : $calculation->glue_price2->value;
    if($new_glue_price2 === null) $new_glue_price2 = "NULL";
    
    // Расход клея лам 2
    $new_glue_expense2 = empty($calculation->glue_expense2) ? null : $calculation->glue_expense2->value;
    if($new_glue_expense2 === null) $new_glue_expense2 = "NULL";
    
    // Работа лам 2, руб
    $new_lamination2_work_price = empty($calculation->lamination2_work_price) ? null : $calculation->lamination2_work_price->value;
    if($new_lamination2_work_price === null) $new_lamination2_work_price = "NULL";
    
    // Работа лам 2, ч
    $new_lamination2_work_time = empty($calculation->lamination2_work_time) ? null : $calculation->lamination2_work_time->value;
    if($new_lamination2_work_time === null) $new_lamination2_work_time = "NULL";
    
    //**************************************
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
    
    //****************************************************
    // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
    if(empty($error_message)) {
        $sql = "insert into calculation_result (calculation_id, extracharge, usd, euro, cost, cost_per_unit, material, "
                . "material_price, material_price_per_unit, material_width, material_weight, material_length, material_weight_with_tuning, material_length_with_tuning, "
                . "material_lamination1_price, material_lamination1_price_per_unit, material_lamination1_width, lamination1_weight_pure, lamination1_length_pure, lamination1_weight_dirty, lamination1_length_dirty, "
                . "material_lamination2_price, material_lamination2_price_per_unit, material_lamination2_width, lamination2_weight_pure, lamination2_length_pure, lamination2_weight_dirty, lamination2_length_dirty, "
                . "film_waste_price, film_waste, ink_price, ink_weight, work_price, work_time, "
                . "lamination1_film_waste_price, lamination1_film_waste, glue_price1, glue_expense1, lamination1_work_price, lamination1_work_time, "
                . "lamination2_film_waste_price, lamination2_film_waste, glue_price2, glue_expense2, lamination2_work_price, lamination2_work_time) "
                . "values ($id, $new_extracharge, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_material, "
                . "$new_material_price, $new_material_price_per_unit, $new_material_width, $new_material_weight, $new_material_length, $new_material_weight_with_tuning, $new_material_length_with_tuning, "
                . "$new_material_lamination1_price, $new_material_lamination1_price_per_unit, $new_material_lamination1_width, $new_lamination1_weight_pure, $new_lamination1_length_pure, $new_lamination1_weight_dirty, $new_lamination1_length_dirty, "
                . "$new_material_lamination2_price, $new_material_lamination2_price_per_unit, $new_material_lamination2_width, $new_lamination2_weight_pure, $new_lamination2_length_pure, $new_lamination2_weight_dirty, $new_lamination2_length_dirty, "
                . "$new_film_waste_price, $new_film_waste, $new_ink_price, $new_ink_weight, $new_work_price, $new_work_time, "
                . "$new_lamination1_film_waste_price, $new_lamination1_film_waste, $new_glue_price1, $new_glue_expense1, $new_lamination1_work_price, $new_lamination1_work_time, "
                . "$new_lamination2_film_waste_price, $new_lamination2_film_waste, $new_glue_price2, $new_glue_expense2, $new_lamination2_work_price, $new_lamination2_work_time)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    //***************************************************
    // ЧИТАЕМ СОХРАНЁННЫЕ РЕЗУЛЬТАТЫ ИЗ БАЗЫ
    $fetcher = new Fetcher($sql_calculation_result);
    
    if($row = $fetcher->Fetch()) {
        $extracharge = $row['extracharge']; $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $material = $row['material'];
        $material_price = $row['material_price']; $material_price_per_unit = $row['material_price_per_unit']; $material_width = $row['material_width']; $material_weight = $row['material_weight']; $material_length = $row['material_length']; $material_weight_with_tuning = $row['material_weight_with_tuning']; $material_length_with_tuning = $row['material_length_with_tuning'];
        $material_lamination1_price = $row['material_lamination1_price']; $material_lamination1_price_per_unit = $row['material_lamination1_price_per_unit']; $material_lamination1_width = $row['material_lamination1_width']; $lamination1_weight_pure = $row['lamination1_weight_pure']; $lamination1_length_pure = $row['lamination1_length_pure']; $lamination1_weight_dirty = $row['lamination1_weight_dirty']; $lamination1_length_dirty = $row['lamination1_length_dirty'];
        $material_lamination2_price = $row['material_lamination2_price']; $material_lamination2_price_per_unit = $row['material_lamination2_price_per_unit']; $material_lamination2_width = $row['material_lamination2_width']; $lamination2_weight_pure = $row['lamination2_weight_pure']; $lamination2_length_pure = $row['lamination2_length_pure']; $lamination2_weight_dirty = $row['lamination2_weight_dirty']; $lamination2_length_dirty = $row['lamination2_length_dirty'];
        $film_waste_price = $row['film_waste_price']; $film_waste = $row['film_waste']; $ink_price = $row['ink_price']; $ink_weight = $row['ink_weight']; $work_price = $row['work_price']; $work_time = $row['work_time'];
        $lamination1_film_waste_price = $row['lamination1_film_waste_price']; $lamination1_film_waste = $row['lamination1_film_waste']; $glue_price1 = $row['glue_price1']; $glue_expense1 = $row['glue_expense1']; $lamination1_work_price = $row['lamination1_work_price']; $lamination1_work_time = $row['lamination1_work_time'];
        $lamination2_film_waste_price = $row['lamination2_film_waste_price']; $lamination2_film_waste = $row['lamination2_film_waste']; $glue_price2 = $row['glue_price2']; $glue_expense2 = $row['glue_expense2']; $lamination2_work_price = $row['lamination2_work_price']; $lamination2_work_time = $row['lamination2_work_time'];
    }
    else {
        $error_message = "Ошибка при чтении из базы сохранённых данных";
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