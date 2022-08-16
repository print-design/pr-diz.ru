<?php
$calculation_class = "";

if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}

// Редактирование наценки на тираж
if(null !== filter_input(INPUT_POST, 'extracharge-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $extracharge = filter_input(INPUT_POST, 'extracharge');
    $quantity = 0;
    
    $sql = "select sum(quantity) from calculation_quantity where calculation_id = $id order by id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $quantity = $row[0];
    }
    
    $sql = "update calculation set extracharge=$extracharge where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost = cr.cost + (cr.cost * c.extracharge / 100) where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / $quantity where c.id = $id"; echo $sql;
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income = shipping_cost - cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.income_per_unit = cr.income / $quantity where c.id = $id";
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
$film_cost = null; $film_cost_per_unit = null; $width = null; $weight_pure = null; $length_pure = null; $weight_dirty = null; $length_dirty = null;
$film_waste_cost = null; $film_waste_weight = null; $ink_cost = null; $ink_weight = null; $work_cost = null; $work_time = null; $priladka_printing;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql_calculation_result = "select usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, cliche_cost, shipping_cliche_cost, total_weight_dirty, "
            . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
            . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, priladka_printing "
            . "from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql_calculation_result);
    
    if($row = $fetcher->Fetch()) {
        $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $total_weight_dirty = $row['total_weight_dirty'];
        $film_cost = $row['film_cost_1']; $film_cost_per_unit = $row['film_cost_per_unit_1']; $width = $row['width_1']; $weight_pure = $row['weight_pure_1']; $length_pure = $row['length_pure_1']; $weight_dirty = $row['weight_dirty_1']; $length_dirty = $row['length_dirty_1'];
        $film_waste_cost = $row['film_waste_cost_1']; $film_waste_weight = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost = $row['work_cost_1']; $work_time = $row['work_time_1']; $priladka_printing = $row['priladka_printing'];
    }
    else {
        // ПОЛУЧАЕМ ИСХОДНЫЕ ДАННЫЕ
        $param_date = null;
        $param_name = null;
        
        $param_film = null; // Марка материала
        $param_thickness = null; // Толщина, мкм
        $param_density = null; // Плотность, г/см2
        $param_price = null; // Цена, руб
        $param_currency = null; // Валюта
        $param_customers_material = null; // Материал заказчика
        $param_ski = null; // Лыжи
        $param_width_ski = null; // Ширина материала
        
        $param_machine = null;
        $param_machine_id = null;
        $param_length = null; // Длина этикетки, мм
        $param_stream_width = null; // Ширина этикетки
        $param_streams_number = null; // Количество ручьёв
        $param_raport = null; // Рапорт
        $param_ink_number = 0; // Красочность
        
        $param_cliche_in_price = null; // Включить ПФ в стоимость
        $cliches_count_flint = null; // Количество форм Флинт
        $cliches_count_kodak = null; // Количество форм Кодак
        $cliches_count_old = null; // Количество старых форм
        $param_extracharge = null; // Наценка на тираж
        $param_extracharge_cliche = null; // Наценка на ПФ
        
        $sql = "select rc.date, rc.name, rc.unit, "
                . "f.name film, fv.thickness thickness, fv.weight density, "
                . "rc.film_variation_id, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
                . "rc.customers_material, rc.ski, rc.width_ski, "
                . "m.name machine, rc.machine_id, rc.length, rc.stream_width, rc.streams_number, rc.raport, rc.ink_number, "
                . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
                . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
                . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
                . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
                . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
                . "rc.cliche_in_price, rc.cliches_count_flint, rc.cliches_count_kodak, rc.cliches_count_old, rc.extracharge, rc.extracharge_cliche "
                . "from calculation rc "
                . "left join machine m on rc.machine_id = m.id "
                . "left join film_variation fv on rc.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "where rc.id = $id";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $param_date = $row['date'];
            $param_name = $row['name'];
            
            if(!empty($row['film_variation_id'])) {
                $param_film = $row['film']; // Марка материала
                $param_thickness = $row['thickness']; // Толщина, мкм
                $param_density = $row['density']; // Плотность, г/м2
            }
            else {
                $param_film = $row['individual_film_name']; // Марка материала
                $param_thickness = $row['individual_thickness']; // Толщина, мкм
                $param_density = $row['individual_density']; // Плотность, г/м2
            }
            $param_price = $row['price']; // Цена
            $param_currency = $row['currency']; // Валюта
            $param_customers_material = $row['customers_material']; // Материал заказчика
            $param_ski = $row['ski']; // Лыжи
            $param_width_ski = $row['width_ski']; // Ширина материала, мм
            
            $param_machine = $row['machine'];
            $param_machine_id = $row['machine_id'];
            $param_length = $row['length']; // Длина этикетки, мм
            $param_stream_width = $row['stream_width']; // Ширина ручья, мм
            $param_streams_number = $row['streams_number']; // Количество ручьёв
            $param_raport = $row['raport']; // Рапорт
            $param_ink_number = $row['ink_number']; // Красочность
            
            $param_ink_1 = $row['ink_1']; $param_ink_2 = $row['ink_2']; $param_ink_3 = $row['ink_3']; $param_ink_4 = $row['ink_4']; $param_ink_5 = $row['ink_5']; $param_ink_6 = $row['ink_6']; $param_ink_7 = $row['ink_7']; $param_ink_8 = $row['ink_8'];
            $param_color_1 = $row['color_1']; $param_color_2 = $row['color_2']; $param_color_3 = $row['color_3']; $param_color_4 = $row['color_4']; $param_color_5 = $row['color_5']; $param_color_6 = $row['color_6']; $param_color_7 = $row['color_7']; $param_color_8 = $row['color_8'];
            $param_cmyk_1 = $row['cmyk_1']; $param_cmyk_2 = $row['cmyk_2']; $param_cmyk_3 = $row['cmyk_3']; $param_cmyk_4 = $row['cmyk_4']; $param_cmyk_5 = $row['cmyk_5']; $param_cmyk_6 = $row['cmyk_6']; $param_cmyk_7 = $row['cmyk_7']; $param_cmyk_8 = $row['cmyk_8'];
            $param_percent_1 = $row['percent_1']; $param_percent_2 = $row['percent_2']; $param_percent_3 = $row['percent_3']; $param_percent_4 = $row['percent_4']; $param_percent_5 = $row['percent_5']; $param_percent_6 = $row['percent_6']; $param_percent_7 = $row['percent_7']; $param_percent_8 = $row['percent_8'];
            $param_cliche_1 = $row['cliche_1']; $param_cliche_2 = $row['cliche_2']; $param_cliche_3 = $row['cliche_3']; $param_cliche_4 = $row['cliche_4']; $param_cliche_5 = $row['cliche_5']; $param_cliche_6 = $row['cliche_6']; $param_cliche_7 = $row['cliche_7']; $param_cliche_8 = $row['cliche_8'];
            
            $param_cliche_in_price = $row['cliche_in_price'];
            $cliches_count_flint = $row['cliches_count_flint']; // Количество форм Флинт
            $cliches_count_kodak = $row['cliches_count_kodak']; // Количество форм Кодак
            $cliches_count_old = $row['cliches_count_old']; // Количество старых форм
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
        
        // Размеры тиражей
        $param_quantities = array();
        $sql = "select id, quantity from calculation_quantity where calculation_id = $id";
        $fetcher = new Fetcher($sql);
    
        while($row = $fetcher->Fetch()) {
            $param_quantities[$row['id']] = $row['quantity'];
        }
        
        // ПОЛУЧЕНИЕ НОРМ
        $data_priladka = new DataPriladka(null, null, null, null);
        $data_machine = new DataMachine(null, null, null);
        $data_gap = new DataGap(null, null, null);
        $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
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
            
            $sql = "select gap_raport, gap_stream, ski from norm_gap where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_gap = new DataGap($row['gap_raport'], $row['gap_stream'], $row['ski']);
            }
        
            $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_price, lacquer_currency, lacquer_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense "
                    . "from norm_ink where date <= '$param_date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_price'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price'], $row['self_adhesive_laquer_price'], $row['self_adhesive_laquer_currency'], $row['self_adhesive_laquer_expense']);
            }
        
            $sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency "
                    . "from norm_cliche where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_cliche = new DataCliche($row['flint_price'], $row['flint_currency'], $row['kodak_price'], $row['kodak_currency'], $row['scotch_price'], $row['scotch_currency']);
            }
            
            $sql = "select extracharge_type_id, from_weight, to_weight, value from extracharge";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                array_push($data_extracharge, new DataExtracharge($row['value'], $row['extracharge_type_id'], $row['from_weight'], $row['to_weight']));
            }
        }
        
        // ДЕЛАЕМ РАСЧЁТ
        $calculation = new CalculationSelfAdhesive($data_priladka, $data_machine, $data_gap, $data_ink, $data_cliche, $data_extracharge, $new_usd, $new_euro, $param_quantities, $param_film, $param_thickness, $param_density, $param_price, $param_currency, $param_customers_material, $param_ski, $param_width_ski, $param_length, $param_stream_width, $param_streams_number, $param_raport, $param_ink_number, $param_ink_1, $param_ink_2, $param_ink_3, $param_ink_4, $param_ink_5, $param_ink_6, $param_ink_7, $param_ink_8, $param_color_1, $param_color_2, $param_color_3, $param_color_4, $param_color_5, $param_color_6, $param_color_7, $param_color_8, $param_cmyk_1, $param_cmyk_2, $param_cmyk_3, $param_cmyk_4, $param_cmyk_5, $param_cmyk_6, $param_cmyk_7, $param_cmyk_8, $param_percent_1, $param_percent_2, $param_percent_3, $param_percent_4, $param_percent_5, $param_percent_6, $param_percent_7, $param_percent_8, $param_cliche_1, $param_cliche_2, $param_cliche_3, $param_cliche_4, $param_cliche_5, $param_cliche_6, $param_cliche_7, $param_cliche_8, $cliche_in_price, $cliches_count_flint, $cliches_count_kodak, $cliches_count_old, $param_extracharge, $param_extracharge_cliche);
        
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
    
        // Цена материала
        $new_film_cost = $calculation->film_cost;
        if($new_film_cost === null) $new_film_cost = "NULL";
    
        // Цена материала за 1 шт
        $new_film_cost_per_unit = $calculation->film_cost_per_unit;
        if($new_film_cost_per_unit === null) $new_film_cost_per_unit = "NULL";
    
        // Ширина материала
        $new_width = $calculation->width_mat;
        if($new_width === null) $new_width = "NULL";
    
        // Масса без приладки = масса плёнки чистая
        $new_weight_pure = $calculation->weight_pure;
        if($new_weight_pure === null) $new_weight_pure = "NULL";
    
        // Длина без приладки = длина плёнки чистая
        $new_length_pure = $calculation->length_pure;
        if($new_length_pure === null) $new_length_pure = "NULL";
    
        // Масса с приладкой = масса плёнки грязная
        $new_weight_dirty = $calculation->weight_dirty;
        if($new_weight_dirty === null) $new_weight_dirty = "NULL";
    
        // Длина с приладкой = метры погонные грязные
        $new_length_dirty = $calculation->length_dirty;
        if($new_length_dirty === null) $new_length_dirty = "NULL";
    
        // Отходы плёнка цена = (масса грязная - масса чистая) * стоимость за 1 кг * курс валюты
        $new_film_waste_cost = $calculation->film_waste_cost;
        if($new_film_waste_cost === null) $new_film_waste_cost = "NULL";
    
        // Отходы плёнка масса = масса грязная - масса чистая
        $new_film_waste_weight = $calculation->film_waste_weight;
        if($new_film_waste_weight === null) $new_film_waste_weight = "NULL";
    
        // Стоимость всех красок
        $new_ink_cost = null;
        if(!empty($calculation->ink_cost)) $new_ink_cost = $calculation->ink_cost;
        if($new_ink_cost === null) $new_ink_cost = "NULL";
    
        // Расход всех красок
        $new_ink_weight = $calculation->ink_expense;
        if($new_ink_weight === null) $new_ink_weight = "NULL";
    
        // Работа по печати тиража, руб
        $new_work_cost = $calculation->work_cost;
        if($new_work_cost === null) $new_work_cost = "NULL";
    
        // Работа по печати тиража, ч
        $new_work_time = $calculation->work_time;
        if($new_work_time === null) $new_work_time = "NULL";
        
        // Фактический зазор, мм
        $new_gap = $calculation->gap;
        if($new_gap === null) $new_gap = "NULL";
        
        // Метраж приладки одного тиража, м
        $new_priladka_printing = $calculation->priladka_printing;
        if($new_priladka_printing === null) $new_priladka_printing = "NULL";
        
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
                    . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, gap, priladka_printing) "
                    . "values ($id, $new_usd, $new_euro, $new_cost, $new_cost_per_unit, $new_shipping_cost, $new_shipping_cost_per_unit, $new_income, $new_income_per_unit, $new_cliche_cost, $new_shipping_cliche_cost, $new_total_weight_dirty, "
                    . "$new_film_cost, $new_film_cost_per_unit, $new_width, $new_weight_pure, $new_length_pure, $new_weight_dirty, $new_length_dirty, "
                    . "$new_film_waste_cost, $new_film_waste_weight, $new_ink_cost, $new_ink_weight, $new_work_cost, $new_work_time, $new_gap, $new_priladka_printing)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            foreach($calculation->lengths as $key => $value) {
                $sql = "update calculation_quantity set length = ".$value." where id = $key";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        
        //***************************************************
        // ЧИТАЕМ СОХРАНЁННЫЕ РЕЗУЛЬТАТЫ ИЗ БАЗЫ
        $fetcher = new Fetcher($sql_calculation_result);
    
        if($row = $fetcher->Fetch()) {
            $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $total_weight_dirty = $row['total_weight_dirty'];
            $film_cost = $row['film_cost_1']; $film_cost_per_unit = $row['film_cost_per_unit_1']; $width = $row['width_1']; $weight_pure = $row['weight_pure_1']; $length_pure = $row['length_pure_1']; $weight_dirty = $row['weight_dirty_1']; $length_dirty = $row['length_dirty_1'];
            $film_waste_cost = $row['film_waste_cost_1']; $film_waste_weight = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost = $row['work_cost_1']; $work_time = $row['work_time_1']; $priladka_printing = $row['priladka_printing'];
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
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv_self_adhesive.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
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
                <?= number_format($euro, 2, ',', ' ') ?>
            </div>
        </div>
        <div>
            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                <div class="text-nowrap" style="font-size: x-small;">Курс доллара</div>
                <?= number_format($usd, 2, ',', ' ') ?>
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
            <div class="value mb-2"><?= CalculationBase::Display(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($cost_per_unit), 3) ?> &#8381; за шт</span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= (empty($stream_width) || empty($streams_number)) ? "" : CalculationBase::Display($stream_width * $streams_number + 20, 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= CalculationBase::Display((intval($raport) + 20) + 20, 0) ?>&nbsp;мм</div>
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($shipping_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($shipping_cost_per_unit), 3) ?> &#8381; за шт</span></div>
            <?php if($cliche_in_price != 1): ?>
            <div class="mt-2">Отгрузочная стоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($shipping_cliche_cost), 0) ?> &#8381;</div>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <h3>Прибыль</h3>
            <div>Прибыль <?=$cliche_in_price == 1 ? 'с' : 'без' ?> ПФ</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($income), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display($income_per_unit, 3) ?> &#8381; за шт</span></div>
        </div>
    </div>
    <div class="mt-3 row text-nowrap">
        <div class="col-4">
            <h2>Материалы</h2>
        </div>
        <div class="col-8">
            <?php
            $sql = "select quantity, length from calculation_quantity where calculation_id = $id";
            $grabber = new Grabber($sql);
            $rows = $grabber->result;
            $printings_number = count($rows);
            ?>
            <h2>Тиражей&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?=$printings_number ?></span></h2>
        </div>
    </div>
    <h3>Самоклеящийся материал&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($weight_dirty), 0) ?> кг</span></h3>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <div>Закупочная стоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit), 3) ?>&#8381; за м<sup>2</sup></div>
            <div>Ширина</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($width), 0) ?> мм</div>
            <div>На приладку тиража</div>
            <div class="value mb-2"><?= CalculationBase::Display(intval($priladka_printing), 0) ?> м</div>
            <div>Масса без приладки</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_pure), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(intval($length_pure), 0) ?> м</span></div>
            <div>Масса с приладкой</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($weight_dirty), 0) ?> кг&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(intval($length_dirty), 0) ?> м</span></div>
        </div>
        <div class="col-8">
            <div class="row">
                <div class="col-6">
                <?php
                $half = ceil(count($rows) / 2);
                $i = 1;
                foreach($rows as $row):
                ?>
                    <div class='value mb-2'><span class='font-weight-normal'><?=$i ?>.&nbsp;&nbsp;&nbsp;</span><?=CalculationBase::Display(intval($row['quantity']), 0) ?> шт&nbsp;&nbsp;&nbsp;<span class='font-weight-normal'><?= CalculationBase::Display(intval($row['length']), 0) ?> м</span></div>
                <?php if($i == $half): ?>
                </div>
                <div class="col-6">
                <?php
                endif;
                $i++;
                endforeach;
                ?>
                </div>
            </div>
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
        </div>
        <div class="row text-nowrap">
            <div class="col-4 pr-4">
                <div>Отходы</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($film_waste_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_waste_weight), 2) ?> кг</span></div>
                <div>Краска</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($ink_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($ink_weight), 2) ?> кг</span></div>
                <div>Печать тиража</div>
                <div class="value mb-2"><?= CalculationBase::Display(floatval($work_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($work_time), 2) ?> ч</span></div>
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
    <?php if($status_id == DRAFT): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=CALCULATION ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
    </form>
    <?php elseif ($status_id == CALCULATION): ?>
    <div class="d-flex justify-content-between">
        <div>
            <a href="techmap.php?calculation_id=<?=$id ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
        </div>
        <div>
            <form method="post">
                <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
                <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3">Отправить в черновики</button>
            </form>
        </div>
    </div>
    <?php elseif ($status_id == TECHMAP): ?>
    <a href="techmap.php?id=<?=$techmap_id ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
    <?php endif; ?>
</div>