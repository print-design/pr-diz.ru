<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');

if($id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
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
    
    if(!empty($date)) {
        // Расчёт
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
        
        // Данные CSV-файла
        $file_data = array();
        
        array_push($file_data, array("Курс доллара, руб", CalculationBase::Display($usd, 2), "", ""));
        array_push($file_data, array("Курс евро, руб", CalculationBase::Display($euro, 2), "", ""));
        array_push($file_data, array("Машина", $machine, "", ""));
        array_push($file_data, array("Количество тиражей", count($quantities), "", ""));
        
        $i = 1;
        foreach($quantities as $key => $quantity) {
            array_push($file_data, array("Тираж $i, шт", CalculationBase::Display(intval($quantity), 0), "", ""));
            $i++;
        }
        
        array_push($file_data, array("Суммарное количество этикеток, шт", CalculationBase::Display($calculation->quantity, 0), "", ""));
        array_push($file_data, array("Марка", $film_1, "", ""));
        array_push($file_data, array("Толщина", CalculationBase::Display($thickness_1, 2), "", ""));
        array_push($file_data, array("Плотность", CalculationBase::Display($density_1, 2), "", ""));
        array_push($file_data, array("Лыжи", $calculation->GetSkiName($ski_1), "", ""));
        if($ski_1 == CalculationBase::NONSTANDARD_SKI) array_push ($file_data, array("Ширина материала, мм", CalculationBase::Display ($width_ski_1, 2), "", ""));
        if($customers_material_1 == true) array_push ($file_data, array("Материал заказчика", "", "", ""));
        else array_push ($file_data, array("Цена", CalculationBase::Display ($price_1, 2)." ".$calculation->GetCurrencyName ($currency_1).($currency_1 == CalculationBase::USD ? " (".CalculationBase::Display ($price_1 * $usd, 2)." руб)" : "").($currency_1 == CalculationBase::EURO ? " (".CalculationBase::Display ($price_1 * $euro, 2)." руб)" : ""), "", ""));
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        array_push($file_data, array("Рапорт", CalculationBase::Display($raport, 2), "", ""));
        
        if(!empty($machine_id)) {
            for($i=1; $i<=$ink_number; $i++) {
                $ink = "ink_$i";
                $color = "color_$i";
                $cmyk = "cmyk_$i";
                $percent = "percent_$i";
                $cliche = "cliche_$i";
                array_push($file_data, array("Краска $i:", $calculation->GetInkName($$ink).(empty($$color) ? "" : " ".$$color).(empty($$cmyk) ? "" : " ".$$cmyk)." ".$$percent."% ".$calculation->GetClicheName($$cliche), "", ""));
            }
        }
        
        if($cliche_in_price == 1) {
            array_push($file_data, array("Включить ПФ в себестоимость", "", "", ""));
        }
        else {
            array_push($file_data, array("Не включать ПФ в себестоимость", "", "", ""));
        }
        
        if($customer_pays_for_cliche == 1) {
            array_push($file_data, array("Заказчик платит за ПФ", "", "", ""));
        }
        else {
            array_push($file_data, array("Мы платим за ПФ", "", "", ""));
        }
        
        array_push($file_data, array("Дополнительные расходы с шт, руб", CalculationBase::Display($extra_expense, 3), "", ""));
        
        array_push($file_data, array("ЗазорРапорт", CalculationBase::Display($data_gap->gap_raport, 2), "", ""));
        array_push($file_data, array("ЗазорРучей", CalculationBase::Display($data_gap->gap_stream, 2), "", ""));
        
        array_push($file_data, array("", "", "", ""));
        
        // Если материал заказчика, то его цена = 0
        if($customers_material_1 == true) $price_1 = 0;
        
        // Результаты вычислений
        array_push($file_data, array("Ширина материала, мм",
            CalculationBase::Display($calculation->width_mat, 2),
            $ski_1 == CalculationBase::NONSTANDARD_SKI ? "|= ".CalculationBase::Display($width_ski_1, 2) : "|= ($streams_number * (".CalculationBase::Display($stream_width, 2)." + ".CalculationBase::Display($data_gap->gap_stream, 2).")) + (".CalculationBase::Display($data_gap->ski, 2)." * 2)",
            $ski_1 == CalculationBase::NONSTANDARD_SKI ? "вводится вручную" : "(количество ручьёв * (ширина этикетки + ЗазорРучей)) + (ширина одной лыжи * 2)"));
        
        array_push($file_data, array("Высота этикетки грязная, мм",
            CalculationBase::Display($calculation->length_label_dirty, 2),
            "|= ". CalculationBase::Display($length, 2)." + ". CalculationBase::Display($data_gap->gap_raport, 2),
            "высота этикетки + ЗазорРапорт"));
        
        array_push($file_data, array("Ширина этикетки грязная, мм",
            CalculationBase::Display($calculation->width_dirty, 2),
            "|= ". CalculationBase::Display($stream_width, 2)." + ". CalculationBase::Display($data_gap->gap_stream, 2),
            "ширина этикетки + ЗазорРучей"));
        
        array_push($file_data, array("Количество этикеток в рапорте грязное",
            CalculationBase::Display($calculation->number_in_raport_dirty, 2),
            "|= ". CalculationBase::Display($raport, 2)." / ". CalculationBase::Display($calculation->length_label_dirty, 2),
            "рапорт / высота этикетки грязная"));
        
        array_push($file_data, array("Количество этикеток в рапорте чистое",
            CalculationBase::Display($calculation->number_in_raport_pure, 2),
            "|= ОКРВНИЗ(".CalculationBase::Display($calculation->number_in_raport_dirty, 2).";1)",
            "количество этикеток в рапорте грязное - округление в меньшую сторону"));
        
        array_push($file_data, array("Фактический зазор, мм",
            CalculationBase::Display($calculation->gap, 2),
            "|= (".CalculationBase::Display($raport, 2)." - (".CalculationBase::Display($length, 2)." * ".CalculationBase::Display($calculation->number_in_raport_pure, 2).")) / ".$calculation->number_in_raport_pure,
            "(рапорт - (высота этикетки чистая * количество этикеток в рапорте чистое)) / количество этикеток в рапорте чистое"));
        
        //***************************
        // Рассчёт по КГ
        //***************************
        
        array_push($file_data, array("Метраж приладки одного тиража",
            CalculationBase::Display($calculation->priladka_printing, 0),
            "|= ($ink_number * ". CalculationBase::Display($data_priladka->length, 0).") + ".CalculationBase::Display($data_priladka->stamp, 0),
            "(красочность * метраж приладки 1 краски) + метраж приладки штампа"));
        
        array_push($file_data, array("М2 чистые, м2", 
            CalculationBase::Display($calculation->area_pure, 2),
            "|= (".CalculationBase::Display($length, 2)." + ".CalculationBase::Display($calculation->gap, 2).") * (".CalculationBase::Display($stream_width, 2)." + ".CalculationBase::Display($data_gap->gap_stream, 2).") * ". CalculationBase::Display($calculation->quantity, 0)." / 1 000 000",
            "(длина этикетки чистая + фактический зазор) * (ширина этикетки + ЗазорРучей) * суммарное кол-во этикеток всех тиражей / 1 000 000"));
        
        array_push($file_data, array("М. пог. чистые, м",
            CalculationBase::Display($calculation->length_pog_pure, 2),
            "|= ".CalculationBase::Display($calculation->area_pure, 2)." / (".CalculationBase::Display($calculation->width_dirty, 2)." * $streams_number / 1000)",
            "м2 чистые / (ширина этикетки грязная * кол-во ручьев / 1000)"));
        
        array_push($file_data, array("СтартСтопОтход, м",
            CalculationBase::Display($calculation->waste_length, 2),
            "|= ". CalculationBase::Display($data_priladka->waste_percent, 2)." * ". CalculationBase::Display($calculation->length_pog_pure, 2)." / 100",
            "процент отходов на СтартСтоп * м.пог чистые / 100"));
        
        array_push($file_data, array("М пог. грязные, м",
            CalculationBase::Display($calculation->length_pog_dirty, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2)." + (".$calculation->quantities_count." * ". CalculationBase::Display($calculation->priladka_printing, 0).") + ". CalculationBase::Display($calculation->waste_length, 2),
            "м. пог чистые + (количество тиражей * метраж приладки 1 тиража) + СтартСтопОтход"));
        
        array_push($file_data, array("М2 грязные, m2",
            CalculationBase::Display($calculation->area_dirty, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_dirty, 2)." * ". CalculationBase::Display($calculation->width_mat, 2)." / 1000",
            "м. пог грязные * ширина материала / 1000"));
        
        //***************************
        // Массы и длины плёнок
        //***************************
        
        array_push($file_data, array("Масса материала чистая (без приладки), кг",
            CalculationBase::Display($calculation->weight_pure, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2)." * ". CalculationBase::Display($calculation->width_mat, 2)." * ". CalculationBase::Display($density_1, 2)." / 1 000 000",
            "м. пог чистые * ширина материала * уд. вес / 1 000 000"));
        
        array_push($file_data, array("Длина материала чистая, м",
            CalculationBase::Display($calculation->length_pure, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2),
            "м. пог. чистые"));
        
        array_push($file_data, array("Масса материала грязная (с приладкой), кг",
            CalculationBase::Display($calculation->weight_dirty, 2),
            "|= ". CalculationBase::Display($calculation->area_dirty, 2)." * ". CalculationBase::Display($density_1, 2)." / 1000",
            "м2 грязные * удельный вес / 1000"));
        
        array_push($file_data, array("Длина материала грязная, м",
            CalculationBase::Display($calculation->length_dirty, 2),
            "|= ".CalculationBase::Display($calculation->length_pog_dirty, 2),
            "м. пог. чистые"));
        
        //*****************************
        // Себестоимость плёнок
        //*****************************
        
        array_push($file_data, array("Себестоимость материала грязная (с приладкой), руб",
            CalculationBase::Display($calculation->film_cost, 2),
            "|= ". CalculationBase::Display($calculation->area_dirty, 2)." * ". CalculationBase::Display($price_1, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency_1, $usd, $euro), 2),
            "м2 грязные * цена * курс валюты"));
        
        array_push($file_data, array("", "", "", ""));
        
        //*****************************
        // Время - деньги
        //*****************************
        
        array_push($file_data, array("Время приладки, ч",
            CalculationBase::Display($calculation->priladka_time, 2),
            "|= $ink_number"." * ".CalculationBase::Display($data_priladka->time, 2)." / 60 * ".$calculation->quantities_count,
            "красочность * время приладки 1 краски, мин / 60 * количество тиражей"));
        
        array_push($file_data, array("Время печати тиража, без приладки, ч",
            CalculationBase::Display($calculation->print_time, 2),
            "|= (". CalculationBase::Display($calculation->length_pog_pure, 2)." + ". CalculationBase::Display($calculation->waste_length, 2).") / ". CalculationBase::Display($data_machine->speed, 2)." / 1000",
            "(м. пог. чистые + СтартСтопОтход) / скорость работы машины / 1000"));
        
        array_push($file_data, array("Общее время выполнения тиража, ч",
            CalculationBase::Display($calculation->work_time, 2),
            "|= ". CalculationBase::Display($calculation->priladka_time, 2)." + ". CalculationBase::Display($calculation->print_time, 2),
            "время приладки + время печати тиража"));
        
        array_push($file_data, array("Стоимость выполнения, руб",
            CalculationBase::Display($calculation->work_cost, 2),
            "|= ". CalculationBase::Display($calculation->work_time, 2)." * ". CalculationBase::Display($data_machine->price, 2),
            "общее время выполнения тиража * стоимость работы машины"));
        
        array_push($file_data, array("", "", "", ""));
        
        //************************
        // Расход краски
        //************************
        
        array_push($file_data, array("М2 запечатки, м2",
            CalculationBase::Display($calculation->print_area, 2),
            "|= ((". CalculationBase::Display($stream_width, 2)." + ". CalculationBase::Display($data_gap->gap_stream, 2).") * (". CalculationBase::Display($length, 2)." + ". CalculationBase::Display($data_gap->gap_raport, 2).") * ". CalculationBase::Display($calculation->quantity, 0)." / 1 000 000".") + (". CalculationBase::Display($calculation->length_pog_dirty, 2)." * 0,01)",
            "((ширина этикетки + ЗазорРучей) * (длина этикетки + ЗазорРапорт) * суммарное кол-во этикеток всех тиражей / 1 000 000) + (м. пог. грязные * 0,01)"));
        
        array_push($file_data, array("Масса краски в смеси, кг",
            CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
            "|= 1 + ". CalculationBase::Display($data_ink->solvent_part, 2),
            "1 + доля растворителя в смеси"));
        
        array_push($file_data, array("Цена 1 кг чистого этоксипропанола, руб",
            CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 2),
            "|= ". CalculationBase::Display($data_ink->solvent_etoxipropanol_price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro), 2),
            "цена этоксипропанола * курс валюты"));
        
        array_push($file_data, array("М2 испарения грязная, м2",
            CalculationBase::Display($calculation->vaporization_area_dirty, 2),
            "|= ". CalculationBase::Display($data_machine->width, 0)." * ". CalculationBase::Display($calculation->length_pog_dirty, 2)." / 1000",
            "Ширина машины * м. пог грязные / 1000"));
        
        array_push($file_data, array("М2 испарения чистая, м2",
            CalculationBase::Display($calculation->vaporization_area_pure, 2),
            "|= ". CalculationBase::Display($calculation->vaporization_area_dirty, 2)." - ". CalculationBase::Display($calculation->print_area, 2),
            "М2 испарения грязное - М2 запечатки"));
        
        array_push($file_data, array("Расход испарения растворителя, кг",
            CalculationBase::Display($calculation->vaporization_expense, 2),
            "|= ". CalculationBase::Display($calculation->vaporization_area_pure, 2)." * ". CalculationBase::Display($data_machine->vaporization_expense, 2),
            "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2)"));
        
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $lacquer = "lacquer_$i";
            $percent = "percent_$i";
            
            // Поскольку в самоклейке лак используется без растворителя, для лака используем другой расчёт
            if($$ink == CalculationBase::LACQUER) {
                array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "|= ". CalculationBase::Display($data_ink->self_adhesive_laquer_price, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($data_ink->self_adhesive_laquer_currency, $usd, $euro), 2),
                    "цена 1 кг чистой краски $i * курс валюты"));
                
                array_push($file_data, array("Расход чистой краски $i, кг",
                    CalculationBase::Display($calculation->ink_expenses[$i], 2),
                    "|= ".CalculationBase::Display($calculation->print_area, 2)." * ".CalculationBase::Display($data_ink->self_adhesive_laquer_expense, 2)." * ".CalculationBase::Display($$percent, 2)." / 1000 / 100",
                    "площадь запечатки * расход чистой краски за 1 м2 * процент краски $i / 1000 / 100"));
                
                array_push($file_data, array("Стоимость чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_costs[$i], 2),
                    "|= ". CalculationBase::Display($calculation->ink_expenses[$i], 2)." * ".CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "Расход чистой краски $i * цена 1 кг чистой краски $i"));
            }
            else {
                $price1 = $calculation->GetInkPrice($$ink, $$cmyk, $$lacquer, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_glossy_price, $data_ink->lacquer_glossy_currency, $data_ink->lacquer_matte_price, $data_ink->lacquer_matte_currency);
            
                array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "|= ". CalculationBase::Display($price1->value, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($price1->currency, $usd, $euro), 2),
                    "цена 1 кг чистой краски $i * курс валюты"));
            
                array_push($file_data, array("Цена 1 кг КраскаСмеси $i, руб",
                    CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2),
                    "|= ((".CalculationBase::Display($calculation->ink_kg_prices[$i], 2)." * 1) + (".CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 2)." * ".CalculationBase::Display($data_ink->solvent_part, 2).")) / ".CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
                    "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски"));
            
                array_push($file_data, array("Расход КраскаСмеси $i, кг",
                    CalculationBase::Display($calculation->ink_expenses[$i], 2),
                    "|= ".CalculationBase::Display($calculation->print_area, 2)." * ".CalculationBase::Display($calculation->GetInkExpense($$ink, $$cmyk, $$lacquer, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_glossy_expense, $data_ink->lacquer_matte_expense), 2)." * ".CalculationBase::Display($$percent, 2)." / 1000 / 100",
                    "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100"));
            
                array_push($file_data, array("Стоимость КраскаСмеси $i, руб",
                    CalculationBase::Display($calculation->ink_costs[$i], 2),
                    "|= ". CalculationBase::Display($calculation->ink_expenses[$i], 2)." * ".CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2),
                    "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i"));
                
                array_push($file_data, array("Расход (краска + растворитель на одну краску), руб",
                    CalculationBase::Display($calculation->ink_costs_mix[$i], 2),
                    "|= ". CalculationBase::Display($calculation->ink_costs[$i], 2),
                    "Стоимость КраскаСмеси на тираж ₽"));
                
                array_push($file_data, array("Стоимость КраскаСмеси $i финальная, руб",
                    CalculationBase::Display($calculation->ink_costs_final[$i], 2),
                    "|= ЕСЛИ(".CalculationBase::Display($calculation->ink_costs_mix[$i], 2)." < ".CalculationBase::Display($data_ink->min_price_per_ink, 2)." ; ".CalculationBase::Display($data_ink->min_price_per_ink, 2)." ; ".CalculationBase::Display($calculation->ink_costs_mix[$i], 2).")",
                    "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - расход (краска + растворитель на одну краску)"));
            }
        }
        
        array_push($file_data, array("", "", "", ""));
        
        //***********************************
        // Стоимость форм
        //***********************************
        
        array_push($file_data, array("Высота форм, м",
            CalculationBase::Display($calculation->cliche_height, 2),
            "|= (".CalculationBase::Display($raport, 2)." + 20) / 1000",
            "(рапорт + 20мм) / 1000"));
        
        array_push($file_data, array("Ширина форм, м",
            CalculationBase::Display($calculation->cliche_width, 2),
            "|= (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($calculation->width_dirty, 2)." + 20 + 20) / 1000",
            "(кол-во ручьёв * ширина этикетки грязная + 20 мм + 20 мм) / 1000 (для самоклейки без лыж не бывает)"));
        
        array_push($file_data, array("Площадь форм, м2",
            CalculationBase::Display($calculation->cliche_area, 2),
            "|= ".CalculationBase::Display($calculation->cliche_height, 2)." * ".CalculationBase::Display($calculation->cliche_width, 2),
            "высота форм * ширина форм"));
        
        array_push($file_data, array("Себестоимость 1 формы Флинт, руб",
            CalculationBase::Display($calculation->cliche_flint_price, 2),
            "|= ".CalculationBase::Display($calculation->cliche_area, 2)." * 10000 * ".CalculationBase::Display($data_cliche->flint_price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($data_cliche->flint_currency, $usd, $euro), 2),
            "площадь формы * 10000 * стоимиость формы Флинт * валюта"));
        
        array_push($file_data, array("Себестоимость 1 формы Кодак, руб",
            CalculationBase::Display($calculation->cliche_kodak_price, 2),
            "|= ".CalculationBase::Display($calculation->cliche_area, 2)." * 10000 * ".CalculationBase::Display($data_cliche->kodak_price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($data_cliche->kodak_currency, $usd, $euro), 2),
            "площадь формы * 10000 * стоимость формы Кодак * валюта"));
        
        array_push($file_data, array("Себестоимость всех форм Флинт, руб",
            CalculationBase::Display($calculation->cliche_all_flint_price, 2),
            "|= $cliches_count_flint * ".CalculationBase::Display($calculation->cliche_flint_price, 2),
            "количество форм Флинт * себестоимость 1 формы Флинт"));
        
        array_push($file_data, array("Себестоимость всех форм Кодак, руб",
            CalculationBase::Display($calculation->cliche_all_kodak_price, 2),
            "|= $cliches_count_kodak * ".CalculationBase::Display($calculation->cliche_kodak_price, 2),
            "количество форм Кодак * себестоимость 1 формы Кодак"));
        
        array_push($file_data, array("Количество новых форм",
            CalculationBase::Display($calculation->cliche_new_number, 2),
            "|= $cliches_count_flint + $cliches_count_kodak",
            "количество форм Флинт + количество форм Кодак"));
        
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Стоимость скотча
        //*******************************************
        
        $scotch_formula = "";
        $scotch_comment = "";
        
        for($i = 1; $i <= 8; $i++) {
            if(!empty($scotch_formula)) {
                $scotch_formula .= " + ";
            }
            
            if(!empty($scotch_comment)) {
                $scotch_comment .= " + ";
            }
            
            $scotch_formula .= CalculationBase::Display($calculation->scotch_costs[$i], 2);
            $scotch_comment .= "стоимость скотча цвет $i";
            
            $cliche_area = 0;
            
            if($i <= $ink_number) {
                $cliche_area = $calculation->cliche_area;
            }
            
            array_push($file_data, array("Стоимость скотча Цвет $i, руб",
                CalculationBase::Display($calculation->scotch_costs[$i], 2),
                "|= ".CalculationBase::Display($cliche_area, 2)." * ".CalculationBase::Display($data_cliche->scotch_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($data_cliche->scotch_currency, $usd, $euro), 2),
                "площадь формы цвет $i, см2 * цена скотча за м2 * курс валюты"));
        }
        
        array_push($file_data, array("Общая себестоимость скотча, руб",
            CalculationBase::Display($calculation->scotch_cost, 2),
            "|= ".$scotch_formula,
            $scotch_comment));
        
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Наценка
        //*******************************************
        
        array_push($file_data, array("Наценка на тираж, %", CalculationBase::Display($calculation->extracharge, 2), "", ""));
        array_push($file_data, array("Наценка на ПФ, %", CalculationBase::Display($calculation->extracharge_cliche, 2), "", "Если УКПФ = 1, то наценка на ПФ всегда 0"));
        array_push($file_data, array("Наценка на нож, %", CalculationBase::Display($calculation->extracharge_knife, 2), "", "Если УКНОЖ = 1, то наценка на нож всегда 0"));
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Данные для правой панели
        //*******************************************
        
        $total_ink_cost_formula = "";
        $total_ink_expense_formula = "";
        
        for($i=1; $i<=$ink_number; $i++) {
            if(!empty($total_ink_cost_formula)) {
                $total_ink_cost_formula .= " + ";
            }
            $total_ink_cost_formula .= CalculationBase::Display($calculation->ink_costs_final[$i], 2);
            
            if(!empty($total_ink_expense_formula)) {
                $total_ink_expense_formula .= " + ";
            }
            $total_ink_expense_formula .= CalculationBase::Display($calculation->ink_expenses[$i], 2);
        }
        
        array_push($file_data, array("Стоимость краски, руб",
            CalculationBase::Display($calculation->ink_cost, 2),
            "|= ".$total_ink_cost_formula,
            "Сумма стоимость всех красок"));
        
        array_push($file_data, array("Расход краски, кг",
            CalculationBase::Display($calculation->ink_expense, 2),
            "|= ".$total_ink_expense_formula,
            "Сумма расход всех красок"));
        
        array_push($file_data, array("Себестоимость, руб",
            CalculationBase::Display($calculation->cost, 2),
            "|= ". CalculationBase::Display($calculation->film_cost, 2)." + ". CalculationBase::Display($calculation->work_cost, 2)." + ". CalculationBase::Display($calculation->ink_cost, 2)." + (". CalculationBase::Display($calculation->cliche_cost, 2)." * ". CalculationBase::Display($calculation->ukpf, 0).") + (".CalculationBase::Display($calculation->knife_cost, 2)." * ".CalculationBase::Display($calculation->ukknife, 0).") + ".CalculationBase::Display($calculation->scotch_cost, 2),
            "стоимость материала + стоимость работы + стоимость краски + (стоимость форм * УКПФ) + (стоимость ножа * УКНОЖ) + стоимость скотча"));
        
        array_push($file_data, array("Себестоимость за шт, руб",
            CalculationBase::Display($calculation->cost_per_unit, 2),
            "|= ". CalculationBase::Display($calculation->cost, 2)." / ". CalculationBase::Display($calculation->quantity, 2),
            "себестоимость / суммарное кол-во этикеток всех тиражей"));
        
        array_push($file_data, array("Себестоимость форм, руб",
            CalculationBase::Display($calculation->cliche_cost, 2),
            "|= ".CalculationBase::Display($calculation->cliche_all_flint_price, 2)." + ".CalculationBase::Display($calculation->cliche_all_kodak_price, 2),
            "себестоимость всех форм Флинт + себестоимость всех форм Кодак"));
        
        array_push($file_data, array("Себестоимость ножа, руб",
            CalculationBase::Display($calculation->knife_cost, 2),
            "|= ".CalculationBase::Display($knife, 2),
            "вводится пользователем"));
        
        array_push($file_data, array("Отгрузочная стоимость, руб",
            CalculationBase::Display($calculation->shipping_cost, 2),
            "|= ".CalculationBase::Display($calculation->cost, 1)." + (".CalculationBase::Display($calculation->cost, 2)." * ".CalculationBase::Display($calculation->extracharge, 2)." / 100)",
            "себестоимость + (себестоимость * наценка на тираж / 100)"));
            
        array_push($file_data, array("Отгрузочная стоимость за шт, руб",
            CalculationBase::Display($calculation->shipping_cost_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost, 2)." / ".CalculationBase::Display($calculation->quantity, 0),
            "отгрузочная стоимость / суммарное кол-во этикеток всех тиражей"));
        
        array_push($file_data, array("Отгрузочная стоимость ПФ, руб",
            CalculationBase::Display($calculation->shipping_cliche_cost, 2),
            "|= (".CalculationBase::Display($calculation->cliche_cost, 2)." + (".CalculationBase::Display($calculation->cliche_cost, 2)." * ".CalculationBase::Display($calculation->extracharge_cliche, 2)." / 100)) * ".$calculation->ukcuspaypf." * ((".$calculation->ukpf." - 1) / -1)",
            "(сумма стоимости всех форм + (сумма стоимости всех форм * наценка на ПФ / 100)) * CusPayPF * ((КоэфПФ - 1) / -1)"));
        
        array_push($file_data, array("Отгрузочная стоимость ножа, руб",
            CalculationBase::Display($calculation->shipping_knife_cost, 2),
            "|= (".CalculationBase::Display($calculation->knife_cost, 2)." + (".CalculationBase::Display($calculation->knife_cost, 2)." * ".CalculationBase::Display($calculation->extracharge_knife, 2)." / 100)) * ".$calculation->ukcuspayknife." * ((".$calculation->ukknife." - 1) / -1)",
            "(себестоимость ножа + (себестоимость ножа * наценка на нож / 100)) * CusPayKnife * ((КоэфНож - 1) / -1)"));
        
        array_push($file_data, array("Прибыль, руб",
            CalculationBase::Display($calculation->income, 2),
            "|= (".CalculationBase::Display($calculation->shipping_cost, 2)." - ".CalculationBase::Display($calculation->cost, 2).") - (".CalculationBase::Display($extra_expense, 3)." * ".$calculation->quantity.")",
            "(отгрузочная стоимость - себестоимость) - (доп. расходы на кг / шт * объём заказа, кг/шт)"));
            
        array_push($file_data, array("Прибыль за шт, руб",
            CalculationBase::Display($calculation->income_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost_per_unit, 2)." - ".CalculationBase::Display($calculation->cost_per_unit, 2)." - ".CalculationBase::Display($extra_expense, 3),
            "отгрузочная стоимость за шт - себестоимость за шт - дополнительные расходы за шт"));
        
        array_push($file_data, array("Прибыль ПФ, руб",
            CalculationBase::Display($calculation->income_cliche, 2),
            "|= (".CalculationBase::Display($calculation->shipping_cliche_cost, 2)." - ".CalculationBase::Display($calculation->cliche_cost, 2).") * ((".$calculation->ukpf." - 1) / -1)",
            "(отгрузочная стоимость ПФ - себестоимость ПФ) * ((КоэфПФ - 1) / -1)"));
        
        array_push($file_data, array("Прибыль на нож, руб",
            CalculationBase::Display($calculation->income_knife, 2),
            "|= (".CalculationBase::Display($calculation->shipping_knife_cost, 2)." - ".CalculationBase::Display($calculation->knife_cost, 2).") * ((".$calculation->ukknife." - 1) / -1)",
            "(отгрузочная стоимость ножа - себестоимость ножа) * ((КоэфНож - 1) / -1)"));
        
        array_push($file_data, array("Общий вес всех материала с приладкой, кг",
            CalculationBase::Display($calculation->total_weight_dirty, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty, 2),
            "масса материала грязная"));
        
        array_push($file_data, array("Стоимость за м2 1, руб",
            CalculationBase::Display($calculation->film_cost_per_unit, 2),
            "|= ".CalculationBase::Display($price_1, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency_1, $usd, $euro), 2),
            "цена материала * курс валюты"));
        
        array_push($file_data, array("Отходы, руб",
            CalculationBase::Display($calculation->film_waste_cost, 2),
            "|= ".CalculationBase::Display($calculation->film_waste_weight, 2)." * ".CalculationBase::Display($price_1, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency_1, $usd, $euro), 2),
            "отходы, кг * цена материала * курс валюты"));
        
        array_push($file_data, array("Отходы, кг",
            CalculationBase::Display($calculation->film_waste_weight, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty, 2)." - ".CalculationBase::Display($calculation->weight_pure, 2),
            "масса материала грязная - масса материала чистая"));
        
        array_push($file_data, array("", "", "", ""));
        
        $i = 1;
        
        foreach($quantities as $key => $quantity) {
            array_push($file_data, array("Длина тиража $i, м",
                CalculationBase::Display($calculation->lengths[$key], 2),
                "|= (".CalculationBase::Display(intval($length), 2)." + ".CalculationBase::Display($calculation->gap, 2).") * ".CalculationBase::Display(intval($quantities[$key]), 0)." / $streams_number / 1000",
                "(длина этикетки + фактический зазор) * кол-во этикеток этого тиража / кол-во ручьёв / 1000"));
            $i++;
        }
        
        //****************************************
        // Сохранение в файл
        $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y').' '.str_replace(',', '_', $name).".csv";
        
        DownloadSendHeaders($file_name);
        echo Array2Csv($file_data, $titles);
        die();
    }
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы экспортировать в CSV надо нажать на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>