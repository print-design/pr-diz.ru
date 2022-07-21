<?php
$calculation_class = "";

if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}

// Редактирование наценки на тираж
if(null !== filter_input(INPUT_POST, 'extracharge-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    
    $sql = "update calculation set extracharge=$extracharge where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost = cr.cost + (cr.cost * c.extracharge / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / c.quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income = shipping_cost - cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_per_unit = cr.income / c.quantity where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Редактирование наценки на ПФ
if(null !== filter_input(INPUT_POST, 'extracharge-cliche-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge_cliche = filter_input(INPUT_POST, 'extracharge_cliche');
    
    $sql = "update calculation set extracharge_cliche=$extracharge_cliche where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cliche_cost = cr.cliche_cost + (cr.cliche_cost * c.extracharge_cliche / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Берём расчёт из таблицы базы
$usd = null; $euro = null; $cost = null; $cost_per_unit = null; $shipping_cost = null; $shipping_cost_per_unit = null; $income = null; $income_per_unit = null; $cliche_cost = null; $shipping_cliche_cost = null; $total_weight_dirty = null;
$film_cost_1 = null; $film_cost_per_unit_1 = null; $width_1 = null; $weight_pure_1 = null; $length_pure_1 = null; $weight_dirty_1 = null; $length_dirty_1 = null;
$film_cost_2 = null; $film_cost_per_unit_2 = null; $width_2 = null; $weight_pure_2 = null; $length_pure_2 = null; $weight_dirty_2 = null; $length_dirty_2 = null;
$film_cost_3 = null; $film_cost_per_unit_3 = null; $width_3 = null; $weight_pure_3 = null; $length_pure_3 = null; $weight_dirty_3 = null; $length_dirty_3 = null;
$film_waste_cost_1 = null; $film_waste_weight_1 = null; $ink_cost = null; $ink_weight = null; $work_cost_1 = null; $work_time_1 = null;
$film_waste_cost_2 = null; $film_waste_weight_2 = null; $glue_cost_2 = null; $glue_expense_2 = null; $work_cost_2 = null; $work_time_2 = null;
$film_waste_cost_3 = null; $film_waste_weight_3 = null; $glue_cost_3 = null; $glue_expense_3 = null; $work_cost_3 = null; $work_time_3 = null;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql_calculation_result = "select usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, cliche_cost, shipping_cliche_cost, total_weight_dirty, "
            . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
            . "film_cost_2, film_cost_per_unit_2, width_2, weight_pure_2, length_pure_2, weight_dirty_2, length_dirty_2, "
            . "film_cost_3, film_cost_per_unit_3, width_3, weight_pure_3, length_pure_3, weight_dirty_3, length_dirty_3, "
            . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, "
            . "film_waste_cost_2, film_waste_weight_2, glue_cost_2, glue_expense_2, work_cost_2, work_time_2, "
            . "film_waste_cost_3, film_waste_weight_3, glue_cost_3, glue_expense_3, work_cost_3, work_time_3 "
            . "from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql_calculation_result);

    if($row = $fetcher->Fetch()) {
        $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $total_weight_dirty = $row['total_weight_dirty'];
        $film_cost_1 = $row['film_cost_1']; $film_cost_per_unit_1 = $row['film_cost_per_unit_1']; $width_1 = $row['width_1']; $weight_pure_1 = $row['weight_pure_1']; $length_pure_1 = $row['length_pure_1']; $weight_dirty_1 = $row['weight_dirty_1']; $length_dirty_1 = $row['length_dirty_1'];
        $film_cost_2 = $row['film_cost_2']; $film_cost_per_unit_2 = $row['film_cost_per_unit_2']; $width_2 = $row['width_2']; $weight_pure_2 = $row['weight_pure_2']; $length_pure_2 = $row['length_pure_2']; $weight_dirty_2 = $row['weight_dirty_2']; $length_dirty_2 = $row['length_dirty_2'];
        $film_cost_3 = $row['film_cost_3']; $film_cost_per_unit_3 = $row['film_cost_per_unit_3']; $width_3 = $row['width_3']; $weight_pure_3 = $row['weight_pure_3']; $length_pure_3 = $row['length_pure_3']; $weight_dirty_3 = $row['weight_dirty_3']; $length_dirty_3 = $row['length_dirty_3'];
        $film_waste_cost_1 = $row['film_waste_cost_1']; $film_waste_weight_1 = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost_1 = $row['work_cost_1']; $work_time_1 = $row['work_time_1'];
        $film_waste_cost_2 = $row['film_waste_cost_2']; $film_waste_weight_2 = $row['film_waste_weight_2']; $glue_cost_2 = $row['glue_cost_2']; $glue_expense_2 = $row['glue_expense_2']; $work_cost_2 = $row['work_cost_2']; $work_time_2 = $row['work_time_2'];
        $film_waste_cost_3 = $row['film_waste_cost_3']; $film_waste_weight_3 = $row['film_waste_weight_3']; $glue_cost_3 = $row['glue_cost_3']; $glue_expense_3 = $row['glue_expense_3']; $work_cost_3 = $row['work_cost_3']; $work_time_3 = $row['work_time_3'];
    }
    else {
        // ПОЛУЧАЕМ ИСХОДНЫЕ ДАННЫЕ
        $param_date = null;
        $param_name = null;
        $param_unit = null; // Кг или шт
        $param_quantity = null; // Размер тиража
        $param_work_type_id = null; // Типа работы: с печатью или без печати
    
        $param_film_1 = null; // Основная пленка, марка
        $param_thickness_1 = null; // Основная пленка, толщина, мкм
        $param_density_1 = null; // Основная пленка, плотность, г/м2
        $param_price_1 = null; // Основная пленка, цена
        $param_currency_1 = null; // Основная пленка, валюта
        $param_customers_material_1 = null; // Основная плёнка, другая, материал заказчика
        $param_ski_1 = null; // Основная пленка, лыжи
        $param_width_ski_1 = null; // Основная пленка, ширина пленки, мм
        
        $param_film_2 = null; // Ламинация 1, марка
        $param_thickness_2 = null; // Ламинация 1, толщина, мкм
        $param_density_2 = null; // Ламинация 1, плотность, г/м2
        $param_price_2 = null; // Ламинация 1, цена
        $param_currency_2 = null; // Ламинация 1, валюта
        $param_customers_material_2 = null; // Ламинация 1, другая, материал заказчика
        $param_ski_2 = null; // Ламинация 1, лыжи
        $param_width_ski_2 = null; // Ламинация 1, ширина пленки, мм

        $param_film_3 = null; // Ламинация 2, марка
        $param_thickness_3 = null; // Ламинация 2, толщина, мкм
        $param_density_3 = null; // Ламинация 2, плотность, г/м2
        $param_price_3 = null; // Ламинация 2, цена
        $param_currency_3 = null; // Ламинация 2, валюта
        $param_customers_material_3 = null; // Ламинация 2, другая, уд. вес
        $param_ski_3 = null; // Ламинация 2, лыжи
        $param_width_ski_3 = null;  // Ламинация 2, ширина пленки, мм
    
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
        
        $param_cliche_in_price = null; // Включить ПФ в стоимость
        $param_extracharge = null; // Наценка на тираж
        $param_extracharge_cliche = null; // Наценка на ПФ
    
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
                . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
                . "rc.cliche_in_price, rc.extracharge, rc.extracharge_cliche "
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
                $param_film_1 = $row['film']; // Основная пленка, марка
                $param_thickness_1 = $row['thickness']; // Основная пленка, толщина, мкм
                $param_density_1 = $row['density']; // Основная пленка, плотность, г/м2
            }
            else {
                $param_film_1 = $row['individual_film_name']; // Основная пленка, марка
                $param_thickness_1 = $row['individual_thickness']; // Основная пленка, толщина, мкм
                $param_density_1 = $row['individual_density']; // Основная пленка, плотность, г/м2
            }
            $param_price_1 = $row['price']; // Основная пленка, цена
            $param_currency_1 = $row['currency']; // Основная пленка, валюта
            $param_customers_material_1 = $row['customers_material']; // Основная плёнка, другая, материал заказчика
            $param_ski_1 = $row['ski']; // Основная пленка, лыжи
            $param_width_ski_1 = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
            if(!empty($row['lamination1_film_variation_id'])) {
                $param_film_2 = $row['lamination1_film']; // Ламинация 1, марка
                $param_thickness_2 = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
                $param_density_2 = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
            }
            else {
                $param_film_2 = $row['lamination1_individual_film_name']; // Ламинация 1, марка
                $param_thickness_2 = $row['lamination1_individual_thickness']; // Ламинация 1, толщина, мкм
                $param_density_2 = $row['lamination1_individual_density']; // Ламинация 1, плотность, г/м2
            }
            $param_price_2 = $row['lamination1_price']; // Ламинация 1, цена
            $param_currency_2 = $row['lamination1_currency']; // Ламинация 1, валюта
            $param_customers_material_2 = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
            $param_ski_2 = $row['lamination1_ski']; // Ламинация 1, лыжи
            $param_width_ski_2 = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
            if(!empty($row['lamination2_film_variation_id'])) {
                $param_film_3 = $row['lamination2_film']; // Ламинация 2, марка
                $param_thickness_3 = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
                $param_density_3 = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
            }
            else {
                $param_film_3 = $row['lamination2_individual_film_name']; // Ламинация 2, марка
                $param_thickness_3 = $row['lamination2_individual_thickness']; // Ламинация 2, толщина, мкм
                $param_density_3 = $row['lamination2_individual_density']; // Ламинация 2, плотность, г/м2
            }
            $param_price_3 = $row['lamination2_price']; // Ламинация 2, цена
            $param_currency_3 = $row['lamination2_currency']; // Ламинация 2, валюта
            $param_customers_material_3 = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
            $param_ski_3 = $row['lamination2_ski']; // Ламинация 2, лыжи
            $param_width_ski_3 = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
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
            
            $param_cliche_in_price = $row['cliche_in_price'];
            $param_extracharge = $row['extracharge'];
            $param_extracharge_cliche = $row['extracharge_cliche'];
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
        $data_priladka = new DataPriladka(null, null, null, null);
        $data_priladka_laminator = new DataPriladka(null, null, null, null);
        $data_machine = new DataMachine(null, null, null);
        $data_machine_laminator = new DataMachine(null, null, null);
        $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
        $data_glue = new DataGlue(null, null, null, null, null, null, null);
        $data_cliche = new DataCliche(null, null, null, null, null, null);
        $data_extracharge = array();
    
        if(empty($error_message)) {
            if(empty($param_machine_id)) {
                $data_priladka = new DataPriladka(0, 0, 0, 0);
            }
            else {
                $sql = "select machine_id, time, length, stamp, waste_percent from norm_priladka where id in (select max(id) from norm_priladka where date <= '$param_date' group by machine_id)";
                $fetcher = new Fetcher($sql);
                while ($row = $fetcher->Fetch()) {
                    if($row['machine_id'] == $param_machine_id) {
                        $data_priladka = new DataPriladka($row['time'], $row['length'], $row['stamp'], $row['waste_percent']);
                    }
                }
            }
        
            $sql = "select time, length, stamp, waste_percent from norm_laminator_priladka where date <= '$param_date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_priladka_laminator = new DataPriladka($row['time'], $row['length'], $row['stamp'], $row['waste_percent']);
            }
        
            if(empty($param_machine_id)) {
                $data_machine = new DataMachine(0, 0, 0);
            }
            else {
                $sql = "select machine_id, price, speed, max_width from norm_machine where id in (select max(id) from norm_machine where date <= '$param_date' group by machine_id)";
                $fetcher = new Fetcher($sql);
                while ($row = $fetcher->Fetch()) {
                    if($row['machine_id'] == $param_machine_id) {
                        $data_machine = new DataMachine($row['price'], $row['speed'], $row['max_width']);
                    }
                }
            }
        
            $sql = "select price, speed, max_width from norm_laminator where date <= '$param_date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_machine_laminator = new DataMachine($row['price'], $row['speed'], $row['max_width']);
            }
        
            $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_price, lacquer_currency, lacquer_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense "
                    . "from norm_ink where date <= '$param_date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_price'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price'], $row['self_adhesive_laquer_price'], $row['self_adhesive_laquer_currency'], $row['self_adhesive_laquer_expense']);
            }
        
            $sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part "
                    . "from norm_glue where date <= '$param_date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_glue = new DataGlue($row['glue_price'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent_price'], $row['solvent_currency'], $row['solvent_part']);
            }
            
            $sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency "
                    . "from norm_cliche where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $cliche_data = new DataCliche($row['flint_price'], $row['flint_currency'], $row['kodak_price'], $row['kodak_currency'], $row['scotch_price'], $row['scotch_currency']);
            }
            
            $sql = "select extracharge_type_id, from_weight, to_weight, value from extracharge";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                array_push($data_extracharge, new DataExtracharge($row['value'], $row['extracharge_type_id'], $row['from_weight'], $row['to_weight']));
            }
        }
    
        // ДЕЛАЕМ РАСЧЁТ
        $calculation = new Calculation($data_priladka, $data_priladka_laminator, $data_machine, $data_machine_laminator, $data_ink, $data_glue, $cliche_data, $data_extracharge, $new_usd, $new_euro, 
                $param_unit, $param_quantity, $param_work_type_id, 
                $param_film_1, $param_thickness_1, $param_density_1, $param_price_1, $param_currency_1, $param_customers_material_1, $param_ski_1, $param_width_ski_1, 
                $param_film_2, $param_thickness_2, $param_density_2, $param_price_2, $param_currency_2, $param_customers_material_2, $param_ski_2, $param_width_ski_2, 
                $param_film_3, $param_thickness_3, $param_density_3, $param_price_3, $param_currency_3, $param_customers_material_3, $param_ski_3, $param_width_ski_3, 
                $param_machine_id, $param_machine_shortname, $param_length, $param_stream_width, $param_streams_number, $param_raport, $param_lamination_roller_width, $param_ink_number, 
                $param_ink_1, $param_ink_2, $param_ink_3, $param_ink_4, $param_ink_5, $param_ink_6, $param_ink_7, $param_ink_8, 
                $param_color_1, $param_color_2, $param_color_3, $param_color_4, $param_color_5, $param_color_6, $param_color_7, $param_color_8, 
                $param_cmyk_1, $param_cmyk_2, $param_cmyk_3, $param_cmyk_4, $param_cmyk_5, $param_cmyk_6, $param_cmyk_7, $param_cmyk_8, 
                $param_percent_1, $param_percent_2, $param_percent_3, $param_percent_4, $param_percent_5, $param_percent_6, $param_percent_7, $param_percent_8, 
                $param_cliche_1, $param_cliche_2, $param_cliche_3, $param_cliche_4, $param_cliche_5, $param_cliche_6, $param_cliche_7, $param_cliche_8, 
                $param_cliche_in_price, $param_extracharge, $param_extracharge_cliche);
    
        // Себестоимость форм
        $new_cliche_cost = $calculation->cliche_cost;
        if($new_cliche_cost === null) $new_cliche_cost = "NULL";
        
        // Себестоимость
        $new_cost = $calculation->cost;
        if($new_cost === null) $new_cost = "NULL";
    
        // Себестоимость на 1 шт/кг = Себестоимость / массу тиража или кол-во штук
        $new_cost_per_unit = $calculation->cost_per_unit;
        if($new_cost_per_unit === null) $new_cost_per_unit = "NULL";
        
        // Наценка на тираж
        $new_extracharge = $calculation->extracharge;
        
        // Наценка на ПФ
        $new_extracharge_cliche = $calculation->extracharge_cliche;
        
        // Отгрузочная стоимость
        $new_shipping_cost = $calculation->shipping_cost;
        if($new_shipping_cost === null) $new_shipping_cost = "NULL";
        
        // Отгрузочная стоимость за единицу
        $new_shipping_cost_per_unit = $calculation->shipping_cost_per_unit;
        if($new_shipping_cost_per_unit === null) $new_shipping_cost_per_unit = "NULL";
        
        // Прибыль
        $new_income = $calculation->income;
        if($new_income === null) $new_income = "NULL";
        
        // Прибыль за единицу
        $new_income_per_unit = $calculation->income_per_unit;
        if($new_income_per_unit === null) $new_income_per_unit = "NULL";
        
        // Отгрузочная стоимость ПФ
        $new_shipping_cliche_cost = $calculation->shipping_cliche_cost;
        if($new_shipping_cliche_cost === null) $new_shipping_cliche_cost = "NULL";
    
        // Материалы = масса с приладкой осн. + масса с приладкой лам. 1 + масса с приладкой лам. 2
        $new_total_weight_dirty = $calculation->total_weight_dirty;
        if($new_total_weight_dirty === null) $new_total_weight_dirty = "NULL";
    
        // Основная пленка цена = стоимость основной плёнки
        $new_film_cost_1 = $calculation->film_cost_1;
        if($new_film_cost_1 === null) $new_film_cost_1 = "NULL";
    
        // Основная плёнка цена за кг = стоимость основной плёнки / вес
        $new_film_cost_per_unit_1 = $calculation->film_cost_per_unit_1;
        if($new_film_cost_per_unit_1 === null) $new_film_cost_per_unit_1 = "NULL";
    
        // Ширина основной плёнки = ширина осн. плёнки
        $new_width_1 = $calculation->width_1;
        if($new_width_1 === null) $new_width_1 = "NULL";
    
        // Масса без приладки = масса плёнки чистая
        $new_weight_pure_1 = $calculation->weight_pure_1;
        if($new_weight_pure_1 === null) $new_weight_pure_1 = "NULL";
    
        // Длина без приладки = длина плёнки чистая
        $new_length_pure_1 = $calculation->length_pure_1;
        if($new_length_pure_1 === null) $new_length_pure_1 = "NULL";
    
        // Масса с приладкой = масса плёнки грязная
        $new_weight_dirty_1 = $calculation->weight_dirty_1;
        if($new_weight_dirty_1 === null) $new_weight_dirty_1 = "NULL";
    
        // Длина с приладкой = метры погонные грязные
        $new_length_dirty_1 = $calculation->length_dirty_1;
        if($new_length_dirty_1 === null) $new_length_dirty_1 = "NULL";
    
        // Лам 1 цена = лам 1 цена
        $new_film_cost_2 = $calculation->film_cost_2;
        if($new_film_cost_2 === null) $new_film_cost_2 = "NULL";
    
        // Лам 1 цена за кг = лам 1 цена / вес
        $new_film_cost_per_unit_2 = $calculation->film_cost_per_unit_2;
        if($new_film_cost_per_unit_2 === null) $new_film_cost_per_unit_2 = "NULL";
    
        // Лам 1 ширина = лам 1 ширина
        $new_width_2 = $calculation->width_2;
        if($new_width_2 === null) $new_width_2 = "NULL";
        
        // Лам 1 масса без приладки = лам 1 масса чистая
        $new_weight_pure_2 = $calculation->weight_pure_2;
        if($new_weight_pure_2 === null) $new_weight_pure_2 = "NULL";
    
        // Лам 1 длина без приладки = лам 1 длина чистая
        $new_length_pure_2 = $calculation->length_pure_2;
        if($new_length_pure_2 === null) $new_length_pure_2 = "NULL";
    
        // Лам 1 масса с приладкой = лам 1 масса грязная
        $new_weight_dirty_2 = $calculation->weight_dirty_2;
        if($new_weight_dirty_2 === null) $new_weight_dirty_2 = "NULL";
    
        // Лам 1 длина с приладкой = лам 1 длина грязная
        $new_length_dirty_2 = $calculation->length_dirty_2;
        if($new_length_dirty_2 === null) $new_length_dirty_2 = "NULL";
    
        // Лам 2 плёнка цена
        $new_film_cost_3 = $calculation->film_cost_3;
        if($new_film_cost_3 === null) $new_film_cost_3 = "NULL";
    
        // Лам 2 цена за кг = лам 2 плёнка цена / вес
        $new_film_cost_per_unit_3 = $calculation->film_cost_per_unit_3;
        if($new_film_cost_per_unit_3 === null) $new_film_cost_per_unit_3 = "NULL";
    
        // Лам 2 ширина = лам 2 ширина
        $new_width_3 = $calculation->width_3;
        if($new_width_3 === null) $new_width_3 = "NULL";
    
        // Лам 2 масса без приладки
        $new_weight_pure_3 = $calculation->weight_pure_3;
        if($new_weight_pure_3 === null) $new_weight_pure_3 = "NULL";
    
        // Лам 2 длина без приладки
        $new_length_pure_3 = $calculation->length_pure_3;
        if($new_length_pure_3 === null) $new_length_pure_3 = "NULL";
    
        // Лам 2 масса с приладкой = лам 2 масса грязная
        $new_weight_dirty_3 = $calculation->weight_dirty_3;
        if($new_weight_dirty_3 === null) $new_weight_dirty_3 = "NULL";
    
        // Лам 2 длина с приладкой = лам 2 длина грязная
        $new_length_dirty_3 = $calculation->length_dirty_3;
        if($new_length_dirty_3 === null) $new_length_dirty_3 = "NULL";
    
        // Отходы плёнка цена = (масса грязная - масса чистая) * стоимость за 1 кг * курс валюты
        $new_film_waste_cost_1 = $calculation->film_waste_cost_1;
        if($new_film_waste_cost_1 === null) $new_film_waste_cost_1 = "NULL";
    
        // Отходы плёнка масса = масса грязная - масса чистая
        $new_film_waste_weight_1 = $calculation->film_waste_weight_1;
        if($new_film_waste_weight_1 === null) $new_film_waste_weight_1 = "NULL";
    
        // Стоимость всех красок
        $new_ink_cost = null;
        if(!empty($calculation->ink_cost)) $new_ink_cost = $calculation->ink_cost;
        if($new_ink_cost === null) $new_ink_cost = "NULL";
    
        // Расход всех красок
        $new_ink_weight = $calculation->ink_expense;
        if($new_ink_weight === null) $new_ink_weight = "NULL";
    
        // Работа по печати тиража, руб
        $new_work_cost_1 = $calculation->work_cost_1;
        if($new_work_cost_1 === null) $new_work_cost_1 = "NULL";
    
        // Работа по печати тиража, ч
        $new_work_time_1 = $calculation->work_time_1;
        if($new_work_time_1 === null) $new_work_time_1 = "NULL";
    
        // Отходы плёнки ламинации 1, руб
        $new_film_waste_cost_2 = $calculation->film_waste_cost_2;
        if($new_film_waste_cost_2 === null) $new_film_waste_cost_2 = "NULL";
    
        // Отходы плёнки ламинации 1, кг
        $new_film_waste_weight_2 = $calculation->film_waste_weight_2;
        if($new_film_waste_weight_2 === null) $new_film_waste_weight_2 = "NULL";
    
        // Стоимость клея лам 1
        $new_glue_cost_2 = $calculation->glue_cost2;
        if($new_glue_cost_2 === null) $new_glue_cost_2 = "NULL";
    
        // Расход клея лам 1
        $new_glue_expense_2 = $calculation->glue_expense2;
        if($new_glue_expense_2 === null) $new_glue_expense_2 = "NULL";
    
        // Работа лам 1, руб
        $new_work_cost_2 = $calculation->work_cost_2;
        if($new_work_cost_2 === null) $new_work_cost_2 = "NULL";
    
        // Работа лам 1, ч
        $new_work_time_2 = $calculation->work_time_2;
        if($new_work_time_2 === null) $new_work_time_2 = "NULL";
    
        // Отходы плёнки лам 2, руб
        $new_film_waste_cost_3 = $calculation->film_waste_cost_3;
        if($new_film_waste_cost_3 === null) $new_film_waste_cost_3 = "NULL";
    
        // Отходы плёнки лам 2, кг
        $new_film_waste_weight_3 = $calculation->film_waste_weight_3;
        if($new_film_waste_weight_3 === null) $new_film_waste_weight_3 = "NULL";
    
        // Стоимость клея лам 2
        $new_glue_cost_3 = $calculation->glue_cost3;
        if($new_glue_cost_3 === null) $new_glue_cost_3 = "NULL";
    
        // Расход клея лам 2
        $new_glue_expense_3 = $calculation->glue_expense3;
        if($new_glue_expense_3 === null) $new_glue_expense_3 = "NULL";
    
        // Работа лам 2, руб
        $new_work_cost_3 = $calculation->work_cost_3;
        if($new_work_cost_3 === null) $new_work_cost_3 = "NULL";
    
        // Работа лам 2, ч
        $new_work_time_3 = $calculation->work_time_3;
        if($new_work_time_3 === null) $new_work_time_3 = "NULL";
        
        //****************************************************
        // ПОМЕЩАЕМ НАЦЕНКУ В БАЗУ
        if(empty($error_message)) {
            $sql = "update calculation set extracharge = $new_extracharge, extracharge_cliche = $new_extracharge_cliche where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        //****************************************************
        // Присваиваем новые значения наценки для отображения в правой панели
        $extracharge = intval($new_extracharge);
        $extracharge_cliche = intval($new_extracharge_cliche);
        
        //****************************************************
        // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
        if(empty($error_message)) {
            $sql = "insert into calculation_result (calculation_id, usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, cliche_cost, shipping_cliche_cost, total_weight_dirty, "
                    . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
                    . "film_cost_2, film_cost_per_unit_2, width_2, weight_pure_2, length_pure_2, weight_dirty_2, length_dirty_2, "
                    . "film_cost_3, film_cost_per_unit_3, width_3, weight_pure_3, length_pure_3, weight_dirty_3, length_dirty_3, "
                    . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, "
                    . "film_waste_cost_2, film_waste_weight_2, glue_cost_2, glue_expense_2, work_cost_2, work_time_2, "
                    . "film_waste_cost_3, film_waste_weight_3, glue_cost_3, glue_expense_3, work_cost_3, work_time_3) "
                    . "values ($id, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_shipping_cost, $new_shipping_cost_per_unit, $new_income, $new_income_per_unit, $new_cliche_cost, $new_shipping_cliche_cost, $new_total_weight_dirty, "
                    . "$new_film_cost_1, $new_film_cost_per_unit_1, $new_width_1, $new_weight_pure_1, $new_length_pure_1, $new_weight_dirty_1, $new_length_dirty_1, "
                    . "$new_film_cost_2, $new_film_cost_per_unit_2, $new_width_2, $new_weight_pure_2, $new_length_pure_2, $new_weight_dirty_2, $new_length_dirty_2, "
                    . "$new_film_cost_3, $new_film_cost_per_unit_3, $new_width_3, $new_weight_pure_3, $new_length_pure_3, $new_weight_dirty_3, $new_length_dirty_3, "
                    . "$new_film_waste_cost_1, $new_film_waste_weight_1, $new_ink_cost, $new_ink_weight, $new_work_cost_1, $new_work_time_1, "
                    . "$new_film_waste_cost_2, $new_film_waste_weight_2, $new_glue_cost_2, $new_glue_expense_2, $new_work_cost_2, $new_work_time_2, "
                    . "$new_film_waste_cost_3, $new_film_waste_weight_3, $new_glue_cost_3, $new_glue_expense_3, $new_work_cost_3, $new_work_time_3)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    
        //***************************************************
        // ЧИТАЕМ СОХРАНЁННЫЕ РЕЗУЛЬТАТЫ ИЗ БАЗЫ
        $fetcher = new Fetcher($sql_calculation_result);
    
        if($row = $fetcher->Fetch()) {
            $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $total_weight_dirty = $row['total_weight_dirty'];
            $film_cost_1 = $row['film_cost_1']; $film_cost_per_unit_1 = $row['film_cost_per_unit_1']; $width_1 = $row['width_1']; $weight_pure_1 = $row['weight_pure_1']; $length_pure_1 = $row['length_pure_1']; $weight_dirty_1 = $row['weight_dirty_1']; $length_dirty_1 = $row['length_dirty_1'];
            $film_cost_2 = $row['film_cost_2']; $film_cost_per_unit_2 = $row['film_cost_per_unit_2']; $width_2 = $row['width_2']; $weight_pure_2 = $row['weight_pure_2']; $length_pure_2 = $row['length_pure_2']; $weight_dirty_2 = $row['weight_dirty_2']; $length_dirty_2 = $row['length_dirty_2'];
            $film_cost_3 = $row['film_cost_3']; $film_cost_per_unit_3 = $row['film_cost_per_unit_3']; $width_3 = $row['width_3']; $weight_pure_3 = $row['weight_pure_3']; $length_pure_3 = $row['length_pure_3']; $weight_dirty_3 = $row['weight_dirty_3']; $length_dirty_3 = $row['length_dirty_3'];
            $film_waste_cost_1 = $row['film_waste_cost_1']; $film_waste_weight_1 = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost_1 = $row['work_cost_1']; $work_time_1 = $row['work_time_1'];
            $film_waste_cost_2 = $row['film_waste_cost_2']; $film_waste_weight_2 = $row['film_waste_weight_2']; $glue_cost_2 = $row['glue_cost_2']; $glue_expense_2 = $row['glue_expense_2']; $work_cost_2 = $row['work_cost_2']; $work_time_2 = $row['work_time_2'];
            $film_waste_cost_3 = $row['film_waste_cost_3']; $film_waste_weight_3 = $row['film_waste_weight_3']; $glue_cost_3 = $row['glue_cost_3']; $glue_expense_3 = $row['glue_expense_3']; $work_cost_3 = $row['work_cost_3']; $work_time_3 = $row['work_time_3'];
        }
        else {
            $error_message = "Ошибка при чтении из базы сохранённых данных";
        }
    }
}
?>
<div id="calculation"<?=$calculation_class ?>>
    <div class="d-flex justify-content-between p-2">
        <div>
            <h1>Расчет</h1>
        </div>
        <div>
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
            <a class="btn btn-outline-dark" target="_blank" style="width: 3rem;" title="Печать" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
        </div>
    </div>
    <div class="d-flex justify-content-start">
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка на тираж</div>
                <form method="post" class="form-inline">
                    <input type="hidden" name="id" value="<?=$id ?>" />
                    <div class="input-group">
                        <input type="text" 
                               id="extracharge" 
                               name="extracharge" 
                               style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                               value="<?=$extracharge ?>" 
                               required="required"
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                               onmouseup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                               onkeyup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                               onfocusout="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');"<?=DISABLED_ATTR ?> />
                        <div class="input-group-append" style="height: 28px;">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-dark d-none" id="extracharge-submit" name="extracharge-submit">Сохранить</button>
                </form>
            </div>
        </div>
        <?php if($cliche_in_price != 1): ?>
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Наценка на ПФ</div>
                <form method="post" class="form-inline">
                    <input type="hidden" name="id" value="<?=$id ?>" />
                    <div class="input-group">
                        <input type="text" 
                               id="extracharge_cliche" 
                               name="extracharge_cliche" 
                               style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                               value="<?=$extracharge_cliche ?>" 
                               required="required" 
                               onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                               onmouseup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                               onkeyup="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');" 
                               onfocusout="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');"<?=DISABLED_ATTR ?> />
                        <div class="input-group-append" style="height: 28px;">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-dark d-none" id="extracharge-cliche-submit" name="extracharge-cliche-submit">Сохранить</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <div class="mr-4">
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                <?=number_format($euro, 2, ',', ' ') ?>
            </div>
        </div>
        <div>
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
            <div>Себестоимость <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($cost_per_unit), 3) ?> &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= CalculationBase::Display(($stream_width * $streams_number + 20) + ($ski == CalculationBase::NO_SKI ? 0 : 20), 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= (intval($raport) + 20) ?>&nbsp;мм</div>            
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($shipping_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($shipping_cost_per_unit), 3) ?> &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
            <?php if($cliche_in_price != 1): ?>
            <div class="mt-2">Отгрузочная стоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($shipping_cliche_cost), 0) ?> &#8381;</div>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <h3>Прибыль</h3>
            <div>Прибыль <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($income), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display($income_per_unit, 3) ?> &#8381; за <?=(empty($unit) || $unit == 'kg' ? 'кг' : 'шт') ?></span></div>            
        </div>
    </div>
    <div class="mt-3">
        <h2>Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($total_weight_dirty), 0) ?> кг</span></h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Основная пленка&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_1), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_1), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_1), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_1), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_1), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_1), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_1), 0) ?> м</span></div>
        </div>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 1&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_2), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_2), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_2), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_2), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_2), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_2), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_2), 0) ?> м</span></div>
        </div>
        <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
            <h3>Ламинация 2&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty_3), 0) ?> кг</span></h3>
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit_3), 3) ?> &#8381; за кг</span></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width_3), 0) ?> мм</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure_3), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_pure_3), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty_3), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($length_dirty_3), 0) ?> м</span></div>
        </div>
    </div>
    <div id="show_costs">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
            </div>
        </div>
    </div>
    <div id="costs" class="d-none">
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                <h2 class="mt-2">Расходы</h2>
            </div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;"></div>
        </div>
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_1), 2) ?> кг</span></div>
                <div>Краска</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($ink_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($ink_weight), 2) ?> кг</span></div>
                <div>Печать тиража</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_1), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_1), 2) ?> ч</span></div>
            </div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_2), 2) ?> кг</span></div>
                <div>Клей</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($glue_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($glue_expense_2), 2) ?> кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_2), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_2), 2) ?> ч</span></div>
            </div>
            <div class="col-4 pr-4" style="border-left: solid 2px #ced4da;">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight_3), 2) ?> кг</span></div>
                <div>Клей</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($glue_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($glue_expense_3), 2) ?> кг</span></div>
                <div>Работа ламинатора</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost_3), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time_3), 2) ?> ч</span></div>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
        <?php if ($status_id == DRAFT): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=CALCULATION ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
    </form>
        <?php elseif($status_id == CALCULATION): ?>
    <div class="d-flex justify-content-between">
        <div>
            <a href="techmap.php?calculation_id=<?=$id ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
        </div>
        <div>
            <form method="post">
                <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
                <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3">Отправить в черновики</button>
            </form>
        </div>
    </div>
        <?php elseif($status_id == TECHMAP): ?>
    <a href="techmap.php?id=<?=$techmap_id ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
        <?php endif; ?>
</div>