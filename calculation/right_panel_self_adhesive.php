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
        $sql = "update calculation_result cr inner join calculation c on cr.calculation_id = c.id set cr.shipping_cost_per_unit = cr.shipping_cost / $quantity where c.id = $id";
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
    
    if(empty($error_message)) {
        $sql = "update calculation_result set income_cliche = shipping_cliche_cost - cliche_cost";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Берём расчёт из таблицы базы
$usd = null; $euro = null; $cost = null; $cost_per_unit = null; $shipping_cost = null; $shipping_cost_per_unit = null; $income = null; $income_per_unit = null; 
$cliche_cost = null; $shipping_cliche_cost = null; $income_cliche = null; 
$knife_cost = null; $shipping_knife_cost = null; $income_knife = null; 
$total_weight_dirty = null;
$film_cost = null; $film_cost_per_unit = null; $width = null; $weight_pure = null; $length_pure = null; $weight_dirty = null; $length_dirty = null;
$film_waste_cost = null; $film_waste_weight = null; $ink_cost = null; $ink_weight = null; $work_cost = null; $work_time = null; $priladka_printing;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql_calculation_result = "select usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, "
            . "cliche_cost, shipping_cliche_cost, income_cliche, "
            . "knife_cost, shipping_knife_cost, income_knife, "
            . "total_weight_dirty, "
            . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
            . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, priladka_printing "
            . "from calculation_result where calculation_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql_calculation_result);
    
    if($row = $fetcher->Fetch()) {
        $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; 
        $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche']; 
        $knife_cost = $row['knife_cost']; $shipping_knife_cost = $row['shipping_knife_cost']; $income_knife = $row['income_knife'];
        $total_weight_dirty = $row['total_weight_dirty'];
        $film_cost = $row['film_cost_1']; $film_cost_per_unit = $row['film_cost_per_unit_1']; $width = $row['width_1']; $weight_pure = $row['weight_pure_1']; $length_pure = $row['length_pure_1']; $weight_dirty = $row['weight_dirty_1']; $length_dirty = $row['length_dirty_1'];
        $film_waste_cost = $row['film_waste_cost_1']; $film_waste_weight = $row['film_waste_weight_1']; $ink_cost = $row['ink_cost']; $ink_weight = $row['ink_weight']; $work_cost = $row['work_cost_1']; $work_time = $row['work_time_1']; $priladka_printing = $row['priladka_printing'];
    }
    else {
        // ПОЛУЧЕНИЕ ИСХОДНЫХ ДАННЫХ
        $date = null;
        $name = null;
        $unit = null; // Кг или шт
        $quantity = null; // Размер тиража
        $work_type_id = null; // Типа работы: с печатью или без печати
    
        $film_1 = null; // Основная пленка, марка
        $thickness_1 = null; // Основная пленка, толщина, мкм
        $density_1 = null; // Основная пленка, плотность, г/м2
        $price_1 = null; // Основная пленка, цена
        $currency_1 = null; // Основная пленка, валюта
        $customers_material_1 = null; // Основная плёнка, другая, материал заказчика
        $ski_1 = null; // Основная пленка, лыжи
        $width_ski_1 = null; // Основная пленка, ширина пленки, мм
        
        $film_2 = null; // Ламинация 1, марка
        $thickness_2 = null; // Ламинация 1, толщина, мкм
        $density_2 = null; // Ламинация 1, плотность, г/м2
        $price_2 = null; // Ламинация 1, цена
        $currency_2 = null; // Ламинация 1, валюта
        $customers_material_2 = null; // Ламинация 1, другая, материал заказчика
        $ski_2 = null; // Ламинация 1, лыжи
        $width_ski_2 = null; // Ламинация 1, ширина пленки, мм
        
        $film_3 = null; // Ламинация 2, марка
        $thickness_3 = null; // Ламинация 2, толщина, мкм
        $density_3 = null; // Ламинация 2, плотность, г/м2
        $price_3 = null; // Ламинация 2, цена
        $currency_3 = null; // Ламинация 2, валюта
        $customers_material_3 = null; // Ламинация 2, другая, уд. вес
        $ski_3 = null; // Ламинация 2, лыжи
        $width_ski_3 = null;  // Ламинация 2, ширина пленки, мм
        
        $machine = null;
        $machine_shortname = null;
        $machine_id = null;
        $laminator = null;
        $laminator_id = null;
        $length = null; // Длина этикетки, мм
        $width = null; // Обрезная ширина, мм (если плёнка без печати)
        $stream_width = null; // Ширина ручья, мм (если плёнка с печатью)
        $streams_number = null; // Количество ручьёв
        $raport = null; // Рапорт
        $lamination_roller_width = null; // Ширина ламинирующего вала
        $ink_number = 0; // Красочность
        
        $cliche_in_price = null; // Включить формы в стоимость
        $extracharge = null; // Наценка на тираж
        $extracharge_cliche = null; // Наценка на ПФ
        $customer_pays_for_cliche = null; // Заказчик платит за ПФ
        $extra_expense = null; // Дополнительные расходы с кг/шт
        
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
                . "m.name machine, m.shortname machine_shortname, rc.machine_id, lam.name laminator, rc.laminator_id, rc.length, rc.stream_width, rc.streams_number, rc.raport, rc.lamination_roller_width, rc.ink_number, "
                . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
                . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
                . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
                . "rc.lacquer_1, rc.lacquer_2, rc.lacquer_3, rc.lacquer_4, rc.lacquer_5, rc.lacquer_6, rc.lacquer_7, rc.lacquer_8, "
                . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
                . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
                . "rc.cliche_in_price, rc.cliches_count_flint, rc.cliches_count_kodak, rc.cliches_count_old, rc.extracharge, rc.extracharge_cliche, rc.customer_pays_for_cliche, "
                . "rc.knife, rc.extracharge_knife, rc.knife_in_price, rc.customer_pays_for_knife, rc.extra_expense "
                . "from calculation rc "
                . "left join machine m on rc.machine_id = m.id "
                . "left join laminator lam on rc.laminator_id = lam.id "
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
                $film_1 = $row['film']; // Основная пленка, марка
                $thickness_1 = $row['thickness']; // Основная пленка, толщина, мкм
                $density_1 = $row['density']; // Основная пленка, плотность, г/м2
            }
            else {
                $film_1 = $row['individual_film_name']; // Основная пленка, марка
                $thickness_1 = $row['individual_thickness']; // Основная пленка, толщина, мкм
                $density_1 = $row['individual_density']; // Основная пленка, плотность, г/м2
            }
            $price_1 = $row['price']; // Основная пленка, цена
            $currency_1 = $row['currency']; // Основная пленка, валюта
            $customers_material_1 = $row['customers_material']; // Основная плёнка, другая, материал заказчика
            $ski_1 = $row['ski']; // Основная пленка, лыжи
            $width_ski_1 = $row['width_ski']; // Основная пленка, ширина пленки, мм
            
            if(!empty($row['lamination1_film_variation_id'])) {
                $film_2 = $row['lamination1_film']; // Ламинация 1, марка
                $thickness_2 = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
                $density_2 = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
            }
            else {
                $film_2 = $row['lamination1_individual_film_name']; // Ламинация 1, марка
                $thickness_2 = $row['lamination1_individual_thickness']; // Ламинация 1, толщина, мкм
                $density_2 = $row['lamination1_individual_density']; // Ламинация 1, плотность, г/м2
            }
            $price_2 = $row['lamination1_price']; // Ламинация 1, цена
            $currency_2 = $row['lamination1_currency']; // Ламинация 1, валюта
            $customers_material_2 = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
            $ski_2 = $row['lamination1_ski']; // Ламинация 1, лыжи
            $width_ski_2 = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
            
            if(!empty($row['lamination2_film_variation_id'])) {
                $film_3 = $row['lamination2_film']; // Ламинация 2, марка
                $thickness_3 = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
                $density_3 = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
            }
            else {
                $film_3 = $row['lamination2_individual_film_name']; // Ламинация 2, марка
                $thickness_3 = $row['lamination2_individual_thickness']; // Ламинация 2, толщина, мкм
                $density_3 = $row['lamination2_individual_density']; // Ламинация 2, плотность, г/м2
            }
            $price_3 = $row['lamination2_price']; // Ламинация 2, цена
            $currency_3 = $row['lamination2_currency']; // Ламинация 2, валюта
            $customers_material_3 = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
            $ski_3 = $row['lamination2_ski']; // Ламинация 2, лыжи
            $width_ski_3 = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
            
            $machine = $row['machine'];
            $machine_shortname = $row['machine_shortname'];
            $machine_id = $row['machine_id'];
            $laminator = $row['laminator'];
            $laminator_id = $row['laminator_id'];
            $length = $row['length']; // Длина этикетки, мм
            $stream_width = $row['stream_width']; // Ширина ручья, мм
            $streams_number = $row['streams_number']; // Количество ручьёв
            $raport = $row['raport']; // Рапорт
            $lamination_roller_width = $row['lamination_roller_width']; // Ширина ламинирующего вала
            $ink_number = $row['ink_number']; // Красочность
            
            $ink_1 = $row['ink_1']; $ink_2 = $row['ink_2']; $ink_3 = $row['ink_3']; $ink_4 = $row['ink_4']; $ink_5 = $row['ink_5']; $ink_6 = $row['ink_6']; $ink_7 = $row['ink_7']; $ink_8 = $row['ink_8'];
            $color_1 = $row['color_1']; $color_2 = $row['color_2']; $color_3 = $row['color_3']; $color_4 = $row['color_4']; $color_5 = $row['color_5']; $color_6 = $row['color_6']; $color_7 = $row['color_7']; $color_8 = $row['color_8'];
            $cmyk_1 = $row['cmyk_1']; $cmyk_2 = $row['cmyk_2']; $cmyk_3 = $row['cmyk_3']; $cmyk_4 = $row['cmyk_4']; $cmyk_5 = $row['cmyk_5']; $cmyk_6 = $row['cmyk_6']; $cmyk_7 = $row['cmyk_7']; $cmyk_8 = $row['cmyk_8'];
            $lacquer_1 = $row['lacquer_1']; $lacquer_2 = $row['lacquer_2']; $lacquer_3 = $row['lacquer_3']; $lacquer_4 = $row['lacquer_4']; $lacquer_5 = $row['lacquer_5']; $lacquer_6 = $row['lacquer_6']; $lacquer_7 = $row['lacquer_7']; $lacquer_8 = $row['lacquer_8'];
            $percent_1 = $row['percent_1']; $percent_2 = $row['percent_2']; $percent_3 = $row['percent_3']; $percent_4 = $row['percent_4']; $percent_5 = $row['percent_5']; $percent_6 = $row['percent_6']; $percent_7 = $row['percent_7']; $percent_8 = $row['percent_8'];
            $cliche_1 = $row['cliche_1']; $cliche_2 = $row['cliche_2']; $cliche_3 = $row['cliche_3']; $cliche_4 = $row['cliche_4']; $cliche_5 = $row['cliche_5']; $cliche_6 = $row['cliche_6']; $cliche_7 = $row['cliche_7']; $cliche_8 = $row['cliche_8'];
            
            $cliche_in_price = $row['cliche_in_price']; // Включать стоимиость ПФ в тираж
            $cliches_count_flint = $row['cliches_count_flint']; // Количество форм Флинт
            $cliches_count_kodak = $row['cliches_count_kodak']; // Количество форм Кодак
            $cliches_count_old = $row['cliches_count_old']; // Количество старых форм
            $extracharge = $row['extracharge']; // Наценка на тираж
            $extracharge_cliche = $row['extracharge_cliche']; // Наценка на ПФ
            $customer_pays_for_cliche = $row['customer_pays_for_cliche']; // Заказчик платит за ПФ
            
            $knife = $row['knife']; // Стоимость ножа
            $extracharge_knife = $row['extracharge_knife']; // Наценка на нож
            $knife_in_price = $row['knife_in_price']; // Нож включен в себестоимость
            $customer_pays_for_knife = $row['customer_pays_for_knife']; // Заказчик платит за нож
            $extra_expense = $row['extra_expense']; // Дополнительные расходы с кг/шт
            
            // Если тип работы - плёнка без печати, то 
            // машина = пустая, красочность = 0, рапорт = 0
            if($work_type_id == Calculation::WORK_TYPE_NOPRINT) {
                $machine_id = null;
                $ink_number = 0;
                $raport = 0;
            }
            
            // Если нет ламинации, то ламинатор = пустой, ширина ламинирующего вала = 0, лыжи для плёнки 2 = 0
            if(empty($film_2) && empty($film_3)) {
                $laminator_id = null;
                $lamination_roller_width = 0;
                $ski_2 = 0;
            }
            
            // Если нет ламинации 2, то лыжи для плёнки 3 = 0
            if(empty($film_3)) {
                $ski_3 = 0;
            }
        }
        
        // Курсы валют
        $usd = null;
        $euro = null;
        
        if(!empty($date)) {
            $sql = "select usd, euro from currency where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $usd = $row['usd'];
                $euro = $row['euro'];
            }
        }
        
        // Размеры тиражей
        $quantities = array();
        
        if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && empty($error_message)) {
            $sql = "select id, quantity from calculation_quantity where calculation_id = $id";
            $fetcher = new Fetcher($sql);
            
            while($row = $fetcher->Fetch()) {
                $quantities[$row['id']] = $row['quantity'];
            }
        }
        
        // ПОЛУЧЕНИЕ НОРМ
        $data_priladka = new DataPriladka(null, null, null, null);
        $data_priladka_laminator = new DataPriladka(null, null, null, null);
        $data_machine = new DataMachine(null, null, null, null);
        $data_laminator = new DataLaminator(null, null, null);
        $data_gap = new DataGap(null, null, null);
        $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
        $data_glue = new DataGlue(null, null, null, null, null, null, null);
        $data_cliche = new DataCliche(null, null, null, null, null, null);
        $data_extracharge = array();
        
        if(!empty($date)) {
            if(empty($machine_id)) {
                $data_priladka = new DataPriladka(0, 0, 0, 0);
            }
            else {
                $sql = "select time, length, stamp, waste_percent from norm_priladka where date <= '$date' and machine_id = $machine_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if ($row = $fetcher->Fetch()) {
                    $data_priladka = new DataPriladka($row['time'], $row['length'], $row['stamp'], $row['waste_percent']);
                }
            }
            
            if(empty($laminator_id)) {
                $data_priladka_laminator = new DataPriladka(0, 0, 0, 0);
            }
            else {
                $sql = "select time, length, waste_percent from norm_laminator_priladka where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_priladka_laminator = new DataPriladka($row['time'], $row['length'], 0, $row['waste_percent']);
                }
            }
            
            if(empty($machine_id)) {
                $data_machine = new DataMachine(0, 0, 0, 0);
            }
            else {
                $sql = "select price, speed, width, vaporization_expense from norm_machine where date <= '$date' and machine_id = $machine_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if ($row = $fetcher->Fetch()) {
                    $data_machine = new DataMachine($row['price'], $row['speed'], $row['width'], $row['vaporization_expense']);
                }
            }
            
            if(empty($laminator_id)) {
                $data_laminator = new DataLaminator(0, 0, 0);
            }
            else {
                $sql = "select price, speed, max_width from norm_laminator where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_laminator = new DataLaminator($row['price'], $row['speed'], $row['max_width']);
                }
            }
            
            $sql = "select gap_raport, gap_stream, ski from norm_gap where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_gap = new DataGap($row['gap_raport'], $row['gap_stream'], $row['ski']);
            }
            
            $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_glossy_price, lacquer_glossy_currency, lacquer_glossy_expense, lacquer_matte_price, lacquer_matte_currency, lacquer_matte_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price_per_ink, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense, min_percent "
                    . "from norm_ink where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_glossy_price'], $row['lacquer_glossy_currency'], $row['lacquer_glossy_expense'], $row['lacquer_matte_price'], $row['lacquer_matte_currency'], $row['lacquer_matte_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price_per_ink'], $row['self_adhesive_laquer_price'], $row['self_adhesive_laquer_currency'], $row['self_adhesive_laquer_expense'], $row['min_percent']);
            }
            
            if(empty($laminator_id)) {
                $data_glue = new DataGlue(0, 0, 0, 0, 0, 0, 0);
            }
            else {
                $sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part "
                        . "from norm_glue where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_glue = new DataGlue($row['glue_price'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent_price'], $row['solvent_currency'], $row['solvent_part']);
                }
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
        $calculation = new CalculationSelfAdhesive($data_priladka, 
                $data_machine, 
                $data_gap, 
                $data_ink, 
                $data_cliche, 
                $data_extracharge, 
                $usd, // Курс доллара
                $euro, // Курс евро
                $quantities, // Размер тиража в шт
                
                $film_1, // Марка материла
                $thickness_1, // Толщина материала, мкм
                $density_1, // Плотность материала, г/м2
                $price_1, // Цена материала
                $currency_1, // Валюта цены материала
                $customers_material_1, // Материал заказчика
                $ski_1, // Лыжи
                $width_ski_1, // Ширина материала, мм
                
                $length, // Длина этикетки, мм
                $stream_width, // Ширина этикетки, мм
                $streams_number, // Количество ручьёв
                $raport, // Рапорт, мм
                $ink_number, // Красочность
                
                $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
                $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
                $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
                $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, 
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                
                $cliche_in_price, // Стоимость ПФ включается в себестоимость
                $cliches_count_flint, // Количество форм Флинт
                $cliches_count_kodak, // Количество форм Кодак
                $cliches_count_old, // Количество старых форм
                $extracharge, // Наценка на тираж
                $extracharge_cliche, // Наценка на ПФ
                $customer_pays_for_cliche,  // Заказчик платит за ПФ
                
                $knife, // Стоимость ножа
                $extracharge_knife, // Наценка на нож
                $knife_in_price, // Включать нож в себестоимость
                $customer_pays_for_knife, // Заказчик платит за нож
                $extra_expense); // Дополнительные расходы с кг/шт
        
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
        
        // Наценка на нож
        $new_extracharge_knife = $calculation->extracharge_knife;
        
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
        
        // Себестоимость ПФ
        $new_cliche_cost = $calculation->cliche_cost;
        if($new_cliche_cost === null) $new_cliche_cost = "NULL";
        
        // Отгрузочная стоимость ПФ
        $new_shipping_cliche_cost = $calculation->shipping_cliche_cost;
        if($new_shipping_cliche_cost === null) $new_shipping_cliche_cost = "NULL";
        
        // Прибыль ПФ
        $new_income_cliche = $calculation->income_cliche;
        if($new_income_cliche === null) $new_income_cliche = "NULL";
        
        // Себестоимость ножа
        $new_knife_cost = $calculation->knife_cost;
        if($new_knife_cost === null) $new_knife_cost = "NULL";
        
        // Отгрузочная стоимость ножа
        $new_shipping_knife_cost = $calculation->shipping_knife_cost;
        if($new_shipping_knife_cost === null) $new_shipping_knife_cost = "NULL";
        
        // Прибыль на нож
        $new_income_knife = $calculation->income_knife;
        if($new_income_knife === null) $new_income_knife = "NULL";
    
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
            $sql = "update calculation set extracharge = $new_extracharge, extracharge_cliche = $new_extracharge_cliche, extracharge_knife = $new_extracharge_knife where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        //****************************************************
        // Присваиваем новые значения наценки для отображения в правой панели
        $extracharge = intval($new_extracharge);
        $extracharge_cliche = intval($new_extracharge_cliche);
        $extracharge_knife = intval($new_extracharge_knife);
        
        //****************************************************
        // ПОМЕЩАЕМ РЕЗУЛЬТАТЫ ВЫЧИСЛЕНИЙ В БАЗУ
        if(empty($error_message)) {
            $sql = "insert into calculation_result (calculation_id, usd, euro, cost, cost_per_unit, shipping_cost, shipping_cost_per_unit, income, income_per_unit, "
                    . "cliche_cost, shipping_cliche_cost, income_cliche, "
                    . "knife_cost, shipping_knife_cost, income_knife, "
                    . "total_weight_dirty, "
                    . "film_cost_1, film_cost_per_unit_1, width_1, weight_pure_1, length_pure_1, weight_dirty_1, length_dirty_1, "
                    . "film_waste_cost_1, film_waste_weight_1, ink_cost, ink_weight, work_cost_1, work_time_1, gap, priladka_printing) "
                    . "values ($id, $usd, $euro, $new_cost, $new_cost_per_unit, $new_shipping_cost, $new_shipping_cost_per_unit, $new_income, $new_income_per_unit, "
                    . "$new_cliche_cost, $new_shipping_cliche_cost, $new_income_cliche, "
                    . "$new_knife_cost, $new_shipping_knife_cost, $new_income_knife, "
                    . "$new_total_weight_dirty, "
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
            $usd = $row['usd']; $euro = $row['euro']; $cost = $row['cost']; $cost_per_unit = $row['cost_per_unit']; $shipping_cost = $row['shipping_cost']; $shipping_cost_per_unit = $row['shipping_cost_per_unit']; $income = $row['income']; $income_per_unit = $row['income_per_unit']; 
            $cliche_cost = $row['cliche_cost']; $shipping_cliche_cost = $row['shipping_cliche_cost']; $income_cliche = $row['income_cliche'];
            $knife_cost = $row['knife_cost']; $shipping_knife_cost = $row['shipping_knife_cost']; $income_knife = $row['income_knife'];
            $total_weight_dirty = $row['total_weight_dirty'];
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
    <div class="d-flex justify-content-between">
        <div>
            <h1>Расчет</h1>
        </div>
        <div>
            <a class="btn btn-outline-dark mr-3" style="width: 3rem;" title="Скачать" href="csv_self_adhesive.php?id=<?=$id ?>"><i class="fas fa-file-csv"></i></a>
            <a class="btn btn-outline-dark" target="_blank" style="width: 3rem;" title="Печать" href="print.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
        </div>
    </div>
    <div class="d-flex justify-content-start mb-4">
        <div class="mr-4">
            <div class="text-nowrap">Наценка на тираж</div>
            <form>
                <div class="input-group mb-2">
                    <input type="text" 
                           id="extracharge" 
                           name="extracharge" 
                           style="width: 75px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?=$extracharge ?>" 
                           required="required"
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');" 
                           onfocusout="javascript: $(this).attr('id', 'extracharge'); $(this).attr('name', 'extracharge');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text"
                           class="float-only"
                           id="input_shipping_cost_per_unit"
                           name="input_shipping_cost_per_unit"
                           style="width: 75px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?= floatval($shipping_cost_per_unit)  ?>" 
                           required="required"
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');" 
                           onfocusout="javascript: $(this).attr('id', 'input_shipping_cost_per_unit'); $(this).attr('name', 'input_shipping_cost_per_unit');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">&#8381;</span>
                    </div>
                </div>
            </form>
        </div>
        <?php if($cliche_in_price != 1): ?>
        <div class="mr-4 p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px; margin-top: 25px;">
            <div class="text-nowrap" style="font-size: x-small;">Наценка на ПФ</div>
            <form>
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
                           onfocusout="javascript: $(this).attr('id', 'extracharge_cliche'); $(this).attr('name', 'extracharge_cliche');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <?php if($knife_in_price != 1): ?>
        <div class="mr-4 p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px; margin-top: 25px;">
            <div class="text-nowrap" style="font-size: x-small;">Наценка на нож</div>
            <form>
                <input type="hidden" name="id" value="<?=$id ?>" />
                <div class="input-group">
                    <input type="text" 
                           id="extracharge_knife" 
                           name="extracharge_knife" 
                           style="width: 35px; height: 28px; border: 1px solid #ced4da; font-size: 16px;" 
                           value="<?=$extracharge_knife ?>" 
                           required="required" 
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');" 
                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                           onkeyup="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');" 
                           onfocusout="javascript: $(this).attr('id', 'extracharge_knife'); $(this).attr('name', 'extracharge_knife');"<?=$disabled_attr ?> />
                    <div class="input-group-append" style="height: 28px;">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <div class="mr-4" style="margin-top: 29px;">
            <div class="text-nowrap">Курс &#8364;</div>
            <div class="font-weight-bold" style="font-size: larger;"><?= number_format($euro, 2, ',', ' ') ?></div>
        </div>
        <div class="mr-4" style="margin-top: 29px;">
            <div class="text-nowrap">Курс &#36;</div>
            <div class="font-weight-bold" style="font-size: larger;"><?= number_format($usd, 2, ',', ' ') ?></div>
        </div>
    </div>
    <div class="mt-3">
        <h2>Стоимость</h2>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <h3>Себестоимость</h3>
            <div>Себестоимость</div>
            <div class="value mb-2"><?= CalculationBase::Display(floatval($cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($cost_per_unit), 3) ?> &#8381; за шт</span></div>
            <div class="mt-2">Себестоимость ПФ</div>
            <div class="value"><?= CalculationBase::Display(floatval($cliche_cost), 0) ?> &#8381;</div>
            <div class="value mb-2 font-weight-normal" id="right_panel_new_forms"><?=$new_forms_number ?>&nbsp;шт&nbsp;<?= (empty($stream_width) || empty($streams_number)) ? "" : CalculationBase::Display($stream_width * $streams_number + 20, 0) ?>&nbsp;мм&nbsp;<i class="fas fa-times" style="font-size: small;"></i>&nbsp;<?= CalculationBase::Display((intval($raport) + 20) + 20, 0) ?>&nbsp;мм</div>
        </div>
        <div class="col-4 pr-4">
            <h3>Отгрузочная стоимость</h3>
            <div>Отгрузочная стоимость</div>
            <div class="value"><span id="shipping_cost"><?= CalculationBase::Display(floatval($shipping_cost), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="shipping_cost_per_unit"><?= CalculationBase::Display(floatval($shipping_cost_per_unit), 3) ?></span> &#8381; за шт</span></div>
            <div class="mt-2">Отгрузочная стоимость ПФ</div>
            <div class="value"><span id="shipping_cliche_cost"><?= CalculationBase::Display(floatval($shipping_cliche_cost), 0) ?></span> &#8381;</div>
        </div>
        <div class="col-4">
            <h3>Прибыль</h3>
            <div>Прибыль</div>
            <div class="value mb-2"><span id="income"><?= CalculationBase::Display(floatval($income), 0) ?></span> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><span id="income_per_unit"><?= CalculationBase::Display(floatval($income_per_unit), 3) ?></span> &#8381; за шт</span></div>
            <div class="mt-2">Прибыль ПФ</div>
            <div class="value"><span id="income_cliche"><?= CalculationBase::Display(floatval($income_cliche), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4">
            <div>Себестоимость ножа</div>
            <div class="value"><?= CalculationBase::Display(floatval($knife), 0) ?> &#8381;</div>
        </div>
        <div class="col-4 pr-4">
            <div>Отгрузочная стоимость ножа</div>
            <div class="value"><span id="shipping_knife_cost"><?= CalculationBase::Display(floatval($shipping_knife_cost), 0) ?></span> &#8381;</div>
        </div>
        <div class="col-4 pr-4">
            <div>Прибыль на нож</div>
            <div class="value"><span id="income_knife"><?= CalculationBase::Display(floatval($income_knife), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="row text-nowrap">
        <div class="col-4 pr-4"></div>
        <div class="col-4 pr-4"></div>
        <div class="col-4">
            <div>Итоговая прибыль</div>
            <div class="value mb-2"><span id="income_total"><?=CalculationBase::Display(floatval($income) + floatval($income_cliche) + floatval($income_knife), 0) ?></span> &#8381;</div>
        </div>
    </div>
    <div class="mt-3 row text-nowrap">
        <div class="col-4">
            <h2>Материалы&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;"><?= CalculationBase::Display(floatval($total_weight_dirty), 0) ?> кг</span></h2>
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
            <div class="value mb-2"><?= CalculationBase::Display(floatval($film_cost), 0) ?> &#8381;&nbsp;&nbsp;&nbsp;<span class="font-weight-normal"><?= CalculationBase::Display(floatval($film_cost_per_unit), 3) ?> &#8381; за м<sup>2</sup></div>
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
    <?php include 'change_status_buttons.php'; ?>
</div>