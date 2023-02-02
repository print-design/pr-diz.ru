<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');
$work_type_id = filter_input(INPUT_GET, 'work_type_id');
$shipping_cost_per_unit = filter_input(INPUT_GET, 'shipping_cost_per_unit');
$result = array();

if(empty($id)) {
    $result['error'] = "Не указан ID расчёта";
}
elseif(empty($work_type_id)) {
    $result['error'] = "Не указан тип работы";
}
elseif($shipping_cost_per_unit === null || $shipping_cost_per_unit === '') {
    $result['error'] = "Не указана отгрузочная стоимость за единицу";
}
else {
    $sql = "update calculation_result set shipping_cost_per_unit = $shipping_cost_per_unit where calculation_id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && empty($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * (select sum(quantity) from calculation_quantity where calculation_id = $id)) - cr.cost) / cr.cost) * 100 where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    elseif(empty ($error_message)) {
        $sql = "update calculation c inner join calculation_result cr on c.id = cr.calculation_id set c.extracharge = (((cr.shipping_cost_per_unit * c.quantity) - cr.cost) / cr.cost) * 100 where c.id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
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
    
    $shipping_cost = 0;
    $income = 0;
    $income_per_unit = 0;
    
    if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && empty($error_message) && !empty($date)) {
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
        
        $shipping_cost = $calculation->shipping_cost;
        $income = $calculation->income;
        $income_per_unit = $calculation->income_per_unit;
    }
    elseif (empty ($error_message) && !empty ($date)) {
        $calculation = new Calculation($data_priladka, 
                $data_priladka_laminator,
                $data_machine,
                $data_laminator,
                $data_ink,
                $data_glue,
                $data_cliche,
                $data_extracharge,
                $usd, // Курс доллара
                $euro, // Курс евро
                $unit, // Кг или шт
                $quantity, // Размер тиража в кг или шт
                $work_type_id, // Тип работы: с печатью или без печати
                
                $film_1, // Основная пленка, марка
                $thickness_1, // Основная пленка, толщина, мкм
                $density_1, // Основная пленка, плотность, г/м2
                $price_1, // Основная пленка, цена
                $currency_1, // Основная пленка, валюта
                $customers_material_1, // Основная плёнка, другая, материал заказчика
                $ski_1, // Основная пленка, лыжи
                $width_ski_1, // Основная пленка, ширина пленки, мм
                
                $film_2, // Ламинация 1, марка
                $thickness_2, // Ламинация 1, толщина, мкм
                $density_2, // Ламинация 1, плотность, г/м2
                $price_2, // Ламинация 1, цена
                $currency_2, // Ламинация 1, валюта
                $customers_material_2, // Ламинация 1, другая, материал заказчика
                $ski_2, // Ламинация 1, лыжи
                $width_ski_2, // Ламинация 1, ширина пленки, мм
                
                $film_3, // Ламинация 2, марка
                $thickness_3, // Ламинация 2, толщина, мкм
                $density_3, // Ламинация 2, плотность, г/м2
                $price_3, // Ламинация 2, цена
                $currency_3, // Ламинация 2, валюта
                $customers_material_3, // Ламинация 2, другая, уд. вес
                $ski_3, // Ламинация 2, лыжи
                $width_ski_3,  // Ламинация 2, ширина пленки, мм
                
                $machine_id, // Машина
                $machine_shortname, // Короткое название машины
                $length, // Длина этикетки, мм
                $stream_width, // Ширина ручья, мм
                $streams_number, // Количество ручьёв
                $raport, // Рапорт
                $lamination_roller_width, // Ширина ламинирующего вала
                $ink_number, // Красочность
                
                $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
                $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
                $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
                $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, 
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                
                $cliche_in_price, // Стоимость ПФ включается в себестоимость
                $extracharge, // Наценка на тираж
                $extracharge_cliche, // Наценка на ПФ
                $customer_pays_for_cliche, // Заказчик платит за ПФ
                $extra_expense); // Дополнительные расходы с кг/шт
        
        $shipping_cost = $calculation->shipping_cost;
        $income = $calculation->income;
        $income_per_unit = $calculation->income_per_unit;
    }
    
    if(empty($error_message)) {
        $sql = "update calculation_result set shipping_cost = $shipping_cost, income = $income, income_per_unit = $income_per_unit where calculation_id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select c.extracharge, cr.shipping_cost, cr.shipping_cost_per_unit, cr.income, cr.income_per_unit, cr.income_cliche, cr.income_knife from calculation_result cr inner join calculation c on cr.calculation_id = c.id where c.id = $id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $result['extracharge'] = $row['extracharge'];
            $result['shipping_cost'] = CalculationBase::Display(floatval($row['shipping_cost']), 0);
            $result['shipping_cost_per_unit'] = CalculationBase::Display(floatval($row['shipping_cost_per_unit']), 3);
            $result['income'] = CalculationBase::Display(floatval($row['income']), 0);
            $result['income_per_unit'] = CalculationBase::Display(floatval($row['income_per_unit']), 3);
            $result['income_total'] = CalculationBase::Display(floatval($row['income']) + floatval($row['income_cliche']) + floatval($row['income_knife']), 0);
        }
    }
    
    $result['error'] = $error_message;
}

echo json_encode($result);
?>