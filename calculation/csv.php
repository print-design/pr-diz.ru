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
        
        $cliche_in_price = $row['cliche_in_price']; // Включать стоимиость ПФ в тираж
        $extracharge = $row['extracharge']; // Наценка на тираж
        $extracharge_cliche = $row['extracharge_cliche']; // Наценка на ПФ
        
        // Если тип работы - плёнка без печати, то 
        // машина = пустая, красочность = 0, рапорт = 0
        if($work_type_id == Calculation::WORK_TYPE_NOPRINT) {
            $machine_id = null;
            $ink_number = 0;
            $raport = 0;
        }
        
        // Если нет ламинации, то ширина ламинирующего вала = 0, лыжи для плёнки 2 = 0
        if(empty($film_2) && empty($film_3)) {
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
    
    // ПОЛУЧЕНИЕ НОРМ
    $data_priladka = new DataPriladka(null, null, null);
    $data_priladka_laminator = new DataPriladka(null, null, null);
    $data_machine = new DataMachine(null, null, null);
    $data_machine_laminator = new DataMachine(null, null, null);
    $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    $data_glue = new DataGlue(null, null, null, null, null, null, null);
    $data_cliche = new DataCliche(null, null, null, null, null, null);
    $data_extracharge = array();
    
    if(!empty($date)) {
        if(empty($machine_id)) {
            $data_priladka = new DataPriladka(0, 0, 0);
        }
        else {
            $sql = "select machine_id, time, length, waste_percent from norm_priladka where id in (select max(id) from norm_priladka where date <= '$date' group by machine_id)";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                if($row['machine_id'] == $machine_id) {
                    $data_priladka = new DataPriladka($row['time'], $row['length'], $row['waste_percent']);
                }
            }
        }
        
        $sql = "select time, length, waste_percent from norm_laminator_priladka where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_priladka_laminator = new DataPriladka($row['time'], $row['length'], $row['waste_percent']);
        }
        
        if(empty($machine_id)) {
            $data_machine = new DataMachine(0, 0, 0);
        }
        else {
            $sql = "select machine_id, price, speed, max_width from norm_machine where id in (select max(id) from norm_machine where date <= '$date' group by machine_id)";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                if($row['machine_id'] == $machine_id) {
                    $data_machine = new DataMachine($row['price'], $row['speed'], $row['max_width']);
                }
            }
        }
        
        $sql = "select price, speed, max_width from norm_laminator where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_machine_laminator = new DataMachine($row['price'], $row['speed'], $row['max_width']);
        }
        
        $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_price, lacquer_currency, lacquer_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price "
                . "from norm_ink where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_price'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price']);
        }
        
        $sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part "
                . "from norm_glue where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_glue = new DataGlue($row['glue_price'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent_price'], $row['solvent_currency'], $row['solvent_part']);
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
        $calculation = new Calculation($data_priladka, 
                $data_priladka_laminator,
                $data_machine,
                $data_machine_laminator,
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
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                
                $cliche_in_price, // Стоимость ПФ включается в себестоимость
                $extracharge, // Наценка на тираж
                $extracharge_cliche); // Наценка на ПФ
        
        // Данные CSV-файла
        $file_data = array();
        
        array_push($file_data, array("Курс доллара, руб", CalculationBase::Display($usd, 2), "", ""));
        array_push($file_data, array("Курс евро, руб", CalculationBase::Display($euro, 2), "", ""));
        if($work_type_id == Calculation::WORK_TYPE_PRINT) array_push ($file_data, array("Тип работы", "Плёнка с печатью", "", ""));
        elseif($work_type_id == Calculation::WORK_TYPE_NOPRINT) array_push ($file_data, array("Тип работы", "Плёнка без печати", "", ""));
        
        if(!empty($machine_id)) {
            array_push($file_data, array("Машина", $machine, "", ""));
        }
        
        array_push($file_data, array("Размер тиража", $quantity.' '. $calculation->GetUnitName($unit), "", ""));
        array_push($file_data, array("Марка 1", $film_1, "", ""));
        array_push($file_data, array("Толщина 1, мкм", $thickness_1, "", ""));
        array_push($file_data, array("Плотность 1, г/м2", CalculationBase::Display($density_1, 2), "", ""));
        array_push($file_data, array("Лыжи 1", $calculation->GetSkiName($ski_1), "", ""));
        if($ski_1 == Calculation::NONSTANDARD_SKI) array_push ($file_data, array("Ширина плёнки 1, мм", CalculationBase::Display($width_ski_1, 2), "", ""));
        if($customers_material_1 == true) array_push ($file_data, array("Материал заказчика 1", "", "", ""));
        else array_push ($file_data, array("Цена 1", CalculationBase::Display($price_1, 2)." ". $calculation->GetCurrencyName($currency_1).($currency_1 == Calculation::USD ? " (". CalculationBase::Display($price_1 * $usd, 2)." руб)" : "").($currency_1 == Calculation::EURO ? " (". CalculationBase::Display($price_1 * $euro, 2)." руб)" : ""), "", ""));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array("Марка 2", $film_2, "", ""));
            array_push($file_data, array("Толщина 2, мкм", $thickness_2, "", ""));
            array_push($file_data, array("Плотность 2, г/м2", CalculationBase::Display($density_2, 2), "", ""));
            array_push($file_data, array("Лыжи 2", $calculation->GetSkiName($ski_2), "", ""));
            if($ski_2 == Calculation::NONSTANDARD_SKI) array_push($file_data, array("Ширина пленки 2, мм", CalculationBase::Display($width_ski_2, 2), "", ""));
            if($customers_material_2 == true) array_push ($file_data, array("Материал заказчика 2", "", "", ""));
            else array_push ($file_data, array("Цена 2", CalculationBase::Display($price_2, 2)." ". $calculation->GetCurrencyName($currency_2).($currency_2 == Calculation::USD ? " (".CalculationBase::Display($price_2 * $usd, 2)." руб)" : "").($currency_2 == Calculation::EURO ? " (".CalculationBase::Display($price_2 * $euro, 2)." руб)" : ""), "", ""));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array("Марка 3", $film_3, "", ""));
            array_push($file_data, array("Толщина 3, мкм", $thickness_3, "", ""));
            array_push($file_data, array("Плотность 3, г/м2", CalculationBase::Display($density_3, 2), "", ""));
            array_push($file_data, array("Лыжи 3", $calculation->GetSkiName($ski_3), "", ""));
            if($ski_3 == Calculation::NONSTANDARD_SKI) array_push ($file_data, array("Ширина плёнки 3, мм", CalculationBase::Display($width_ski_3, 2), "", ""));
            if($customers_material_3 == true) array_push ($file_data, array("Материал заказчика (лам 2)", "", "", ""));
            else array_push ($file_data, array("Цена 3", CalculationBase::Display($price_3, 2)." ". $calculation->GetCurrencyName($currency_3).($currency_3 == Calculation::USD ? " (".CalculationBase::Display($price_3 * $usd, 2)." руб)" : "").($currency_3 == Calculation::EURO ? " (".CalculationBase::Display($price_3 * $euro, 2)." руб)" : ""), "", ""));
        }
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        
        if(!empty($machine_id)) {
            array_push($file_data, array("Рапорт", CalculationBase::Display($raport, 2), "", ""));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array("Ширина ламинирующего вала, мм", CalculationBase::Display($lamination_roller_width, 2), "", ""));
        }
        
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
        
        array_push($file_data, array("", "", "", ""));
        
        // Значения по умолчанию
        if(empty($thickness_2)) $thickness_2 = 0;
        if(empty($density_2)) $density_2 = 0;
        if(empty($price_2)) $price_2 = 0;
        if(empty($thickness_3)) $thickness_3 = 0;
        if(empty($density_3)) $density_3 = 0;
        if(empty($price_3)) $price_3 = 0;
        if($work_type_id == Calculation::WORK_TYPE_NOPRINT) $machine_id = null;
        if(empty($raport)) $raport = 0;
        if(empty($lamination_roller_width)) $lamination_roller_width = 0;
        if(empty($ink_number)) $ink_number = 0;
        
        // Если материал заказчика, то его цена = 0
        if($customers_material_1 == true) $price_1 = 0;
        if($customers_material_2 == true) $price_2 = 0;
        if($customers_material_3 == true) $price_3 = 0;
        
        // Уравнивающий коэффициент
        array_push($file_data, array("УК1", $calculation->uk1, "", "нет печати - 0, есть печать - 1"));
        array_push($file_data, array("УК2", $calculation->uk2, "", "нет ламинации - 0, есть ламинация - 1"));
        array_push($file_data, array("УК3", $calculation->uk3, "", "нет второй ламинации - 0, есть вторая ламинация - 1"));
        array_push($file_data, array("УКПФ", $calculation->ukpf, "", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1"));
        
        // Результаты вычислений
        array_push($file_data, array("М2 чистые, м2",
            CalculationBase::Display($calculation->area_pure_start, 2),
            $unit == Calculation::KG ? "" : "|= ".CalculationBase::Display($length, 2)." * ".CalculationBase::Display($stream_width, 2)." * ".CalculationBase::Display($quantity, 2)." / 1000000",
            $unit == Calculation::KG ? "Считается только при размере тиража в штуках" : "длина этикетки * ширина ручья * количество штук / 1 000 000"));
        
        array_push($file_data, array("Масса тиража, кг", 
            CalculationBase::Display($calculation->weight, 2),
            $unit == Calculation::KG ? "|= ".$quantity : "|= ".CalculationBase::Display($calculation->area_pure_start, 2)." * (".CalculationBase::Display($density_1, 2)." + ".CalculationBase::Display($density_2, 2)." + ".CalculationBase::Display($density_3, 2).") / 1000",
            $unit == Calculation::KG ? "размер тиража в кг" : "м2 чистые * (уд. вес 1 + уд. вес 2 + уд. вес 3) / 1000"));
        
        $width_1_formula = "";
        
        switch ($ski_1) {
            case Calculation::NO_SKI:
                $width_1_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2);
                break;
            
            case Calculation::STANDARD_SKI:
                $width_1_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." + 20";
                break;
            
            case Calculation::NONSTANDARD_SKI:
                $width_1_formula = "|= ".CalculationBase::Display($width_ski_1, 2);
                break;
        }
        
        array_push($file_data, array("Ширина материала 1, мм",
            CalculationBase::Display($calculation->width_1, 2),
            $width_1_formula,
            "без лыж 1: количество ручьёв * ширина ручья, стандартные лыжи 1: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 1: вводится вручную"));
        
        $width_2_formula = "";
        switch ($ski_2) {
            case Calculation::NO_SKI:
                $width_2_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2);
                break;
            
            case Calculation::STANDARD_SKI:
                $width_2_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." + 20";
                break;
            
            case Calculation::NONSTANDARD_SKI:
                $width_2_formula = "|= ".CalculationBase::Display($width_ski_2, 2);
                break;
        }
        
        array_push($file_data, array("Ширина материала 2, мм",
            CalculationBase::Display($calculation->width_2, 2),
            $width_2_formula,
            "без лыж 2: количество ручьёв * ширина ручья, стандартные лыжи 2: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 2: вводится вручную"));
        
        $width_3_formula = "";
        switch ($ski_3) {
            case Calculation::NO_SKI:
                $width_3_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2);
                break;
            
            case Calculation::STANDARD_SKI:
                $width_3_formula = "|= ".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." + 20";
                break;
            
            case Calculation::NONSTANDARD_SKI:
                $width_3_formula = "|= ".CalculationBase::Display($width_ski_3, 2);
                break;
        }
        
        array_push($file_data, array("Ширина материала 3, мм",
            CalculationBase::Display($calculation->width_3, 2),
            $width_3_formula,
            "без лыж 3: количество ручьёв * ширина ручья, стандартные лыжи 3: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 3: вводится вручную"));
        
        array_push($file_data, array("М2 чистые 1, м2",
            CalculationBase::Display($calculation->area_pure_1, 2),
            "|= ".CalculationBase::Display($calculation->weight, 2)." * 1000 / (".CalculationBase::Display($density_1, 2)." + ".CalculationBase::Display($density_2, 2)." + ".CalculationBase::Display($density_3, 2).")",
            "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3)"));
        
        array_push($file_data, array("М2 чистые 2, м2",
            CalculationBase::Display($calculation->area_pure_2, 2),
            "|= ".CalculationBase::Display($calculation->weight, 2)." * 1000 / (".CalculationBase::Display($density_1, 2)." + ".CalculationBase::Display($density_2, 2)." + ".CalculationBase::Display($density_3, 2).") * ".$calculation->uk2,
            "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК2"));
        
        array_push($file_data, array("М2 чистые 3, м2",
            CalculationBase::Display($calculation->area_pure_3, 2),
            "|= ".CalculationBase::Display($calculation->weight, 2)." * 1000 / (".CalculationBase::Display($density_1, 2)." + ".CalculationBase::Display($density_2, 2)." + ".CalculationBase::Display($density_3, 2).") * ".$calculation->uk3,
            "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК3"));
        
        array_push($file_data, array("М пог чистые 1, м",
            CalculationBase::Display($calculation->length_pure_start_1, 2),
            "|= ".CalculationBase::Display($calculation->area_pure_1, 2)." / (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." / 1000)",
            "м2 чистые 1 / (количество ручьёв * ширина ручья / 1000)"));
        
        array_push($file_data, array("М пог чистые 2, м",
            CalculationBase::Display($calculation->length_pure_start_2, 2),
            "|= ".CalculationBase::Display($calculation->area_pure_2, 2)." / (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." / 1000)",
            "м2 чистые 2 / (количество ручьёв * ширина ручья / 1000)"));
        
        array_push($file_data, array("М пог чистые 2, м",
            CalculationBase::Display($calculation->length_pure_start_3, 2),
            "|= ".CalculationBase::Display($calculation->area_pure_3, 2)." / (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." / 1000)",
            "м2 чистые 3 / (количество ручьёв * ширина ручья / 1000)"));
        
        array_push($file_data, array("СтартСтопОтход 1",
            CalculationBase::Display($calculation->waste_length_1, 2),
            "|= ".CalculationBase::Display($data_priladka->waste_percent, 2)." * ".CalculationBase::Display($calculation->length_pure_start_1, 2)." / 100",
            "СтартСтопОтход печати * м пог чистые 1 / 100"));
        
        array_push($file_data, array("СтартСтопОтход 2",
            CalculationBase::Display($calculation->waste_length_2, 2),
            "|= ".CalculationBase::Display($data_priladka_laminator->waste_percent, 2)." * ".CalculationBase::Display($calculation->length_pure_start_2, 2)." / 100",
            "СтартСтопОтход ламинации * м. пог. чистые 2 / 100"));
        
        array_push($file_data, array("СтартСтопОтход 3",
            CalculationBase::Display($calculation->waste_length_3, 2),
            "|= ".CalculationBase::Display($data_priladka_laminator->waste_percent, 2)." * ".CalculationBase::Display($calculation->length_pure_start_3, 2)." / 100",
            "СтартСтопОтход ламинации * м. пог. чистые 3 / 100"));
        
        array_push($file_data, array("М пог грязные 1",
            CalculationBase::Display($calculation->length_dirty_start_1, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_1, 2)." + (".CalculationBase::Display($ink_number, 2)." * ".CalculationBase::Display($data_priladka->length, 2).") + (".CalculationBase::Display($calculation->laminations_number, 2)." * ".CalculationBase::Display($data_priladka_laminator->length, 2).") + ".CalculationBase::Display($calculation->waste_length_1, 2),
            "м пог чистые 1 + (красочность * метраж приладки 1 краски) + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 1"));
        
        array_push($file_data, array("М пог грязные 2",
            CalculationBase::Display($calculation->length_dirty_start_2, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_2, 2)." + (".CalculationBase::Display($calculation->laminations_number, 2)." * ".CalculationBase::Display($data_priladka_laminator->length, 2).") + ".CalculationBase::Display($calculation->waste_length_2, 2),
            "м пог чистые 2 + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 2"));
        
        array_push($file_data, array("М пог грязные 3",
            CalculationBase::Display($calculation->length_dirty_start_3, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_3, 2)." + (".CalculationBase::Display($data_priladka_laminator->length, 2)." * ".CalculationBase::Display($calculation->uk3, 0).") + ".CalculationBase::Display($calculation->waste_length_3, 2),
            "м пог чистые 3 + (метраж приладки ламинации * УК3) + СтартСтопОтход 3"));
        
        array_push($file_data, array("М2 грязные 1",
            CalculationBase::Display($calculation->area_dirty_1, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_start_1, 2)." * ".CalculationBase::Display($calculation->width_1, 2)." / 1000",
            "м пог грязные 1 * ширина материала 1 / 1000"));
        
        array_push($file_data, array("М2 грязные 2",
            CalculationBase::Display($calculation->area_dirty_2, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_start_2, 2)." * ".CalculationBase::Display($calculation->width_2, 2)." / 1000",
            "м пог грязные 2 * ширина материала 2 / 1000"));
        
        array_push($file_data, array("М2 грязные 3",
            CalculationBase::Display($calculation->area_dirty_3, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_start_3, 2)." * ".CalculationBase::Display($calculation->width_3, 2)." / 1000",
            "м пог грязные 3 * ширина материала 3 / 1000"));
        
        //****************************************
        // Массы и длины плёнок
        //****************************************
        
        array_push($file_data, array("Масса плёнки чистая 1",
            CalculationBase::Display($calculation->weight_pure_1, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_1, 2)." * ".CalculationBase::Display($calculation->width_1, 2)." * ".CalculationBase::Display($density_1, 2)." / 1000000",
            "м пог чистые 1 * ширина материала 1 * уд вес 1 / 1000000"));
        
        array_push($file_data, array("Масса плёнки чистая 2",
            CalculationBase::Display($calculation->weight_pure_2, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_2, 2)." * ".CalculationBase::Display($calculation->width_2, 2)." * ".CalculationBase::Display($density_2, 2)." / 1000000",
            "м пог чистые 2 * ширина материала 2 * уд вес 2 / 1000000"));
        
        array_push($file_data, array("Масса плёнки чистая 3",
            CalculationBase::Display($calculation->weight_pure_3, 2),
            "|= ".CalculationBase::Display($calculation->length_pure_start_3, 2)." * ".CalculationBase::Display($calculation->width_3, 2)." * ".CalculationBase::Display($density_3, 2)." / 1000000",
            "м пог чистые 3 * ширина материала 3 * уд вес 3 / 1000000"));
        
        array_push($file_data, array("Длина пленки чистая 1, м",
            CalculationBase::Display($calculation->length_pure_1, 2),
            "|= ". CalculationBase::Display($calculation->length_pure_start_1, 2),
            "м пог чистые 1"));
        
        array_push($file_data, array("Длина пленки чистая 2, м",
            CalculationBase::Display($calculation->length_pure_2, 2),
            "|= ". CalculationBase::Display($calculation->length_pure_start_2, 2),
            "м пог чистые 2"));
        
        array_push($file_data, array("Длина пленки чистая 3, м",
            CalculationBase::Display($calculation->length_pure_3, 2),
            "|= ". CalculationBase::Display($calculation->length_pure_start_3, 2),
            "м пог чистые 3"));
        
        array_push($file_data, array("Масса плёнки грязная 1, кг",
            CalculationBase::Display($calculation->weight_dirty_1, 2),
            "|= ".CalculationBase::Display($calculation->area_dirty_1, 2)." * ".CalculationBase::Display($density_1, 2)." / 1000",
            "м2 грязные 1 * уд вес 1 / 1000"));
        
        array_push($file_data, array("Масса плёнки грязная 2, кг",
            CalculationBase::Display($calculation->weight_dirty_2, 2),
            "|= ".CalculationBase::Display($calculation->area_dirty_2, 2)." * ".CalculationBase::Display($density_2, 2)." / 1000",
            "м2 грязные 2 * уд вес 2 / 1000"));
        
        array_push($file_data, array("Масса плёнки грязная 3, кг",
            CalculationBase::Display($calculation->weight_dirty_3, 2),
            "|= ".CalculationBase::Display($calculation->area_dirty_3, 2)." * ".CalculationBase::Display($density_3, 2)." / 1000",
            "м2 грязные 3 * уд вес 3 / 1000"));
        
        array_push($file_data, array("Длина плёнки грязная 1, м",
            CalculationBase::Display($calculation->length_dirty_1, 2),
            "|= ". CalculationBase::Display($calculation->length_dirty_start_1, 2),
            "м пог грязные 1"));
        
        array_push($file_data, array("Длина плёнки грязная 2, м",
            CalculationBase::Display($calculation->length_dirty_2, 2),
            "|= ". CalculationBase::Display($calculation->length_dirty_start_2, 2),
            "м пог грязные 2"));
        
        array_push($file_data, array("Длина плёнки грязная 3, м",
            CalculationBase::Display($calculation->length_dirty_3, 2),
            "|= ". CalculationBase::Display($calculation->length_dirty_start_3, 2),
            "м пог грязные 3"));
        
        //****************************************
        // Общая стоимость плёнок
        //****************************************
        
        array_push($file_data, array("Общая стоимость грязная 1, руб",
            CalculationBase::Display($calculation->film_cost_1, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_1, 2)." * ".CalculationBase::Display($price_1, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_1, $usd, $euro), 2),
            "масса пленки 1 * цена плёнки 1 * курс валюты"));
        
        array_push($file_data, array("Общая стоимость грязная 2, руб",
            CalculationBase::Display($calculation->film_cost_2, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_2, 2)." * ".CalculationBase::Display($price_2, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_2, $usd, $euro), 2),
            "масса пленки 2 * цена плёнки 2 * курс валюты"));
        
        array_push($file_data, array("Общая стоимость грязная 3, руб",
            CalculationBase::Display($calculation->film_cost_3, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_3, 2)." * ".CalculationBase::Display($price_3, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_3, $usd, $euro), 2),
            "масса пленки 3 * цена плёнки 3 * курс валюты"));
        
        array_push($file_data, array("", "", "", ""));
        
        //*****************************************
        // Время - деньги
        //*****************************************
        
        array_push($file_data, array("Время приладки 1, мин",
            CalculationBase::Display($calculation->priladka_time_1, 2),
            "|= ".CalculationBase::Display($ink_number, 2)." * ".CalculationBase::Display($data_priladka->time, 2),
            "красочность * время приладки 1 краски"));
        
        array_push($file_data, array("Время приладки 2, мин",
            CalculationBase::Display($calculation->priladka_time_2, 2),
            "|= ".CalculationBase::Display($data_priladka_laminator->time, 2)." * ".CalculationBase::Display($calculation->uk2, 0),
            "время приладки ламинатора * УК2"));
        
        array_push($file_data, array("Время приладки 3, мин",
            CalculationBase::Display($calculation->priladka_time_3, 2),
            "|= ".CalculationBase::Display($data_priladka_laminator->time, 2)." * ".CalculationBase::Display($calculation->uk3, 0),
            "время приладки ламинатора * УК3"));
        
        array_push($file_data, array("Время печати (без приладки) 1, ч",
            CalculationBase::Display($calculation->print_time_1, 2),
            "|= (".CalculationBase::Display($calculation->length_pure_start_1, 2)." + ".CalculationBase::Display($calculation->waste_length_1, 2).") / ".CalculationBase::Display($data_machine->speed, 2)." / 1000 * ".CalculationBase::Display($calculation->uk1, 0),
            "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы машины / 1000 * УК1"));
        
        array_push($file_data, array("Время ламинации (без приладки) 2, ч",
            CalculationBase::Display($calculation->lamination_time_2, 2),
            "|= (".CalculationBase::Display($calculation->length_pure_start_2, 2)." + ".CalculationBase::Display($calculation->waste_length_2, 2).") / ".CalculationBase::Display($data_machine_laminator->speed, 2)." / 1000 * ".CalculationBase::Display($calculation->uk2, 0),
            "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы ламинатора /1000 * УК2"));
        
        array_push($file_data, array("Время ламинации (без приладки) 3, ч",
            CalculationBase::Display($calculation->lamination_time_3, 2),
            "|= (".CalculationBase::Display($calculation->length_pure_start_3, 2)." + ".CalculationBase::Display($calculation->waste_length_3, 2).") / ".CalculationBase::Display($data_machine_laminator->speed, 2)." / 1000 * ".CalculationBase::Display($calculation->uk3, 0),
            "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы ламинатора / 1000 * УК3"));
        
        array_push($file_data, array("Общее время выполнения тиража 1, ч",
            CalculationBase::Display($calculation->work_time_1, 2),
            "|= ".CalculationBase::Display($calculation->priladka_time_1, 2)." / 60 + ".CalculationBase::Display($calculation->print_time_1, 2),
            "время приладки 1 / 60 + время печати"));
        
        array_push($file_data, array("Общее время выполнения тиража 2, ч",
            CalculationBase::Display($calculation->work_time_2, 2),
            "|= ".CalculationBase::Display($calculation->priladka_time_2, 2)." / 60 + ".CalculationBase::Display($calculation->lamination_time_2, 2),
            "время приладки 2 / 60 + время ламинации 1"));
        
        array_push($file_data, array("Общее время выполнения тиража 3, ч",
            CalculationBase::Display($calculation->work_time_3, 2),
            "|= ".CalculationBase::Display($calculation->priladka_time_3, 2)." / 60 + ".CalculationBase::Display($calculation->lamination_time_3, 2),
            "время приладки 3 / 60 + время ламинации 2"));
        
        array_push($file_data, array("Стоимость выполнения тиража 1, руб",
            CalculationBase::Display($calculation->work_cost_1, 2),
            "|= ".CalculationBase::Display($calculation->work_time_1, 2)." * ".CalculationBase::Display($data_machine->price, 2),
            "общее время выполнения 1 * цена работы оборудования 1"));
        
        array_push($file_data, array("Стоимость выполнения тиража 2, руб",
            CalculationBase::Display($calculation->work_cost_2, 2),
            "|= ".CalculationBase::Display($calculation->work_time_2, 2)." * ".CalculationBase::Display($data_machine_laminator->price, 2),
            "общее время выполнения 2 * цена работы оборудования 2"));
        
        array_push($file_data, array("Стоимость выполнения тиража 3, руб",
            CalculationBase::Display($calculation->work_cost_3, 2),
            "|= ".CalculationBase::Display($calculation->work_time_3, 2)." * ".CalculationBase::Display($data_machine_laminator->price, 2),
            "общее время выполнения 3 * цена работы оборудования 3"));
        
        array_push($file_data, array("", "", "", ""));
        
        //****************************************
        // Расход краски
        //****************************************
        
        array_push($file_data, array("Площадь запечатки, м2",
            CalculationBase::Display($calculation->print_area, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_1, 2)." * (".CalculationBase::Display($stream_width, 2)." * ".CalculationBase::Display($streams_number, 2)." + 10) / 1000",
            "м пог грязные 1 * (ширина ручья * кол-во ручьёв + 10 мм) / 1000"));
        
        array_push($file_data, array("Расход КраскаСмеси на 1 кг краски, кг",
            CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
            "|= 1 + ".CalculationBase::Display($data_ink->solvent_part, 2),
            "1 + расход растворителя на 1 кг краски"));
        
        array_push($file_data, array("Цена 1 кг чистого флексоля 82, руб",
            CalculationBase::Display($calculation->ink_flexol82_kg_price, 2),
            "|= ".CalculationBase::Display($data_ink->solvent_flexol82_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($data_ink->solvent_flexol82_currency, $usd, $euro), 2),
            "цена 1 кг флексоля 82 * курс валюты"));
        
        array_push($file_data, array("Цена 1 кг чистого этоксипропанола, руб",
            CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 2),
            "|= ". CalculationBase::Display($data_ink->solvent_etoxipropanol_price, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro), 2),
            "цена 1 кг этоксипропанола * курс валюты"));
        
        $ink_solvent_kg_price = 0;
            
        if($machine_shortname == Calculation::COMIFLEX) {
            $ink_solvent_kg_price = $calculation->ink_flexol82_kg_price;
        }
        else {
            $ink_solvent_kg_price = $calculation->ink_etoxypropanol_kg_price;
        }
        
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            $price = $calculation->GetInkPrice($$ink, $$cmyk, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_price, $data_ink->lacquer_currency);
            
            array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                "|= ". CalculationBase::Display($price->value, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($price->currency, $usd, $euro), 2),
                "цена 1 кг чистой краски $i * курс валюты"));
            
            array_push($file_data, array("Цена 1 кг КраскаСмеси $i, руб",
                CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2),
                "|= ((".CalculationBase::Display($calculation->ink_kg_prices[$i], 2)." * 1) + (".CalculationBase::Display($ink_solvent_kg_price, 2)." * ".CalculationBase::Display($data_ink->solvent_part, 2).")) / ".CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
                "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски"));
            
            array_push($file_data, array("Расход КраскаСмеси $i, кг",
                CalculationBase::Display($calculation->ink_expenses[$i], 2),
                "|= ".CalculationBase::Display($calculation->print_area, 2)." * ".CalculationBase::Display($calculation->GetInkExpense($$ink, $$cmyk, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_expense), 2)." * ".CalculationBase::Display($$percent, 2)." / 1000 / 100",
                "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100"));
            
            array_push($file_data, array("Стоимость КраскаСмеси $i, руб",
                CalculationBase::Display($calculation->ink_costs[$i], 2),
                "|= ". CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2)." * ". CalculationBase::Display($calculation->ink_expenses[$i], 2),
                "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i"));
        }
        
        array_push($file_data, array("", "", "", ""));
        
        //********************************************
        // Расход клея
        //********************************************
        
        array_push($file_data, array("Расход КлеяСмеси на 1 кг клея, кг",
            CalculationBase::Display($calculation->glue_kg_weight, 2),
            "|= 1 + ".CalculationBase::Display($data_glue->solvent_part, 2),
            "1 + расход растворителя на 1 кг клея"));
        
        array_push($file_data, array("Цена 1 кг чистого клея, руб",
            CalculationBase::Display($calculation->glue_kg_price, 2),
            "|= ".CalculationBase::Display($data_glue->glue_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($data_glue->glue_currency, $usd, $euro), 2),
            "цена 1 кг клея * курс валюты"));
        
        array_push($file_data, array("Цена 1 кг чистого растворителя для клея, руб",
            CalculationBase::Display($calculation->glue_solvent_kg_price, 2),
            "|= ".CalculationBase::Display($data_glue->solvent_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($data_glue->solvent_currency, $usd, $euro), 2),
            "цена 1 кг растворителя для клея * курс валюты"));
        
        array_push($file_data, array("Цена 1 кг КлеяСмеси, руб",
            CalculationBase::Display($calculation->mix_glue_kg_price, 2),
            "|= ((1 * ".CalculationBase::Display($calculation->glue_kg_price, 2).") + (".CalculationBase::Display($data_glue->solvent_part, 2)." * ".CalculationBase::Display($calculation->glue_solvent_kg_price, 2).")) / ".CalculationBase::Display($calculation->glue_kg_weight, 2),
            "((1 * цена 1 кг чистого клея) + (расход растворителя на 1 кг клея * цена 1 кг чистого растворителя)) / расход КлеяСмеси на 1 кг клея"));
        
        array_push($file_data, array("Площадь заклейки 2, м2",
            CalculationBase::Display($calculation->glue_area2, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_2, 2)." * ".CalculationBase::Display($lamination_roller_width, 2)." / 1000",
            "м пог грязные 2 * ширина ламинирующего вала / 1000"));
        
        array_push($file_data, array("Площадь заклейки 3, м2",
            CalculationBase::Display($calculation->glue_area3, 2),
            "|= ".CalculationBase::Display($calculation->length_dirty_3, 2)." * ".CalculationBase::Display($lamination_roller_width, 2)." / 1000",
            "м пог грязные 2 * ширина ламинирующего вала / 1000"));
        
        $glue_expense2_formula = CalculationBase::Display($calculation->glue_area2, 2)." * ".CalculationBase::Display($data_glue->glue_expense, 2)." / 1000";
        $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси в 1 м2 / 1000";
        
        if((strlen($film_1) > 3 && substr($film_1, 0, 3) == "Pet") || (strlen($film_2) > 3 && substr($film_2, 0, 3) == "Pet")) {
            $glue_expense2_formula = CalculationBase::Display($calculation->glue_area2, 2)." * ".CalculationBase::Display($data_glue->glue_expense_pet, 2)." / 1000";
            $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
        }
        
        array_push($file_data, array("Расход КлеяСмеси 2, кг",
            CalculationBase::Display($calculation->glue_expense2, 2),
            "|= ".$glue_expense2_formula,
            $glue_expense2_comment));
        
        $glue_expense3_formula = CalculationBase::Display($calculation->glue_area3, 2)." * ".CalculationBase::Display($data_glue->glue_expense, 2)." / 1000";
        $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси в 1 м2 / 1000";
        
        if((strlen($film_2) > 3 && substr($film_2, 0, 3) == "Pet") || (strlen($film_3) > 3 && substr($film_3, 0, 3) == "Pet")) {
            $glue_expense3_formula = CalculationBase::Display($calculation->glue_area3, 2)." * ".CalculationBase::Display($data_glue->glue_expense_pet, 2)." / 1000";
            $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
        }
        
        array_push($file_data, array("Расход КлеяСмеси 3, кг",
            CalculationBase::Display($calculation->glue_expense3, 2),
            "|= ".$glue_expense3_formula,
            $glue_expense3_comment));
        
        array_push($file_data, array("Стоимость КлеяСмеси 2, руб",
            CalculationBase::Display($calculation->glue_cost2, 2),
            "|= ".CalculationBase::Display($calculation->glue_expense2, 2)." * ".CalculationBase::Display($calculation->mix_glue_kg_price, 2),
            "расход КлеяСмеси 2 * цена 1 кг КлеяСмеси"));
        
        array_push($file_data, array("Стоимость КлеяСмеси 3, руб",
            CalculationBase::Display($calculation->glue_cost3, 2),
            "|= ".CalculationBase::Display($calculation->glue_expense3, 2)." * ".CalculationBase::Display($calculation->mix_glue_kg_price, 2),
            "расход КлеяСмеси 3 * цена 1 кг КлеяСмеси"));
        
        array_push($file_data, array("", "", "", ""));
        
        //***********************************
        // Стоимость форм
        //***********************************
        
        array_push($file_data, array("Высота форм, мм",
            CalculationBase::Display($calculation->cliche_height, 2),
            "|= ".CalculationBase::Display($raport, 2)." + 20",
            "рапорт + 20мм"));
        
        array_push($file_data, array("Ширина форм, мм",
            CalculationBase::Display($calculation->cliche_width, 2),
            "|= (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." + 20) + ".((!empty($ski_1) && $ski_1 == Calculation::NO_SKI) ? 0 : 20),
            "(кол-во ручьёв * ширина ручьёв + 20 мм), если есть лыжи (стандартные или нестандартные), то ещё + 20 мм"));
        
        array_push($file_data, array("Площадь форм, см",
            CalculationBase::Display($calculation->cliche_area, 2),
            "|= ".CalculationBase::Display($calculation->cliche_height, 2)." * ".CalculationBase::Display($calculation->cliche_width, 2)." / 100",
            "высота форм * ширина форм / 100"));
        
        array_push($file_data, array("Количество новых форм",
            CalculationBase::Display($calculation->cliche_new_number, 2),"", ""));
        
        for($i=1; $i<=$ink_number; $i++) {
            $cliche = "cliche_$i";
            
            $cliche_sm_price = 0;
            $cliche_currency = "";
            
            switch ($$cliche) {
                case Calculation::FLINT:
                    $cliche_sm_price = $data_cliche->flint_price;
                    $cliche_currency = $data_cliche->flint_currency;
                    break;
                
                case Calculation::KODAK:
                    $cliche_sm_price = $data_cliche->kodak_price;
                    $cliche_currency = $data_cliche->kodak_currency;
                    break;
            }
            
            array_push($file_data, array("Цена формы $i, руб",
                CalculationBase::Display($calculation->cliche_costs[$i], 2),
                "|= ".CalculationBase::Display($calculation->cliche_area, 2)." * ".CalculationBase::Display($cliche_sm_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($cliche_currency, $usd, $euro), 2),
                "площадь формы * цена формы за 1 см * курс валюты"));
        }
        
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Наценка
        //*******************************************
        
        array_push($file_data, array("Наценка на тираж, %", CalculationBase::Display($calculation->extracharge, 2), "", ""));
        array_push($file_data, array("Наценка на ПФ, %", CalculationBase::Display($calculation->extracharge_cliche, 2), "", "Если УКПФ = 1, то наценка на ПФ всегда 0"));
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Данные для правой панели
        //*******************************************
        
        array_push($file_data, array("Общая стоимость всех плёнок, руб",
            CalculationBase::Display($calculation->film_cost, 2),
            "|= ".CalculationBase::Display($calculation->film_cost_1, 2)." + ".CalculationBase::Display($calculation->film_cost_2, 2)." + ".CalculationBase::Display($calculation->film_cost_3, 2),
            "стоимость плёнки грязная 1 + стоимость плёнки грязная 2 + стоимость плёнки грязная 3"));
        
        array_push($file_data, array("Общая стоимость работ, руб",
            CalculationBase::Display($calculation->work_cost, 2),
            "|= ".CalculationBase::Display($calculation->work_cost_1, 2)." + ".CalculationBase::Display($calculation->work_cost_2, 2)." + ".CalculationBase::Display($calculation->work_cost_3, 2),
            "стоимость выполнения тиража 1 + стоимость выполнения тиража 2 + стоимость выполнения тиража 3"));
        
        $total_ink_cost_formula = "";
        $total_ink_expense_formula = "";
        
        for($i=1; $i<=$ink_number; $i++) {
            if(!empty($total_ink_cost_formula)) {
                $total_ink_cost_formula .= " + ";
            }
            $total_ink_cost_formula .= CalculationBase::Display($calculation->ink_costs[$i], 2);
            
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
        
        array_push($file_data, array("Стоимость клея, руб",
            CalculationBase::Display($calculation->glue_cost, 2),
            "|= ".CalculationBase::Display($calculation->glue_cost2, 2)." + ".CalculationBase::Display($calculation->glue_cost3, 2),
            "стоимость клея 2 + стоимость клея 3"));
        
        $total_cliche_cost_formula = "";
        
        for($i=1; $i<=$ink_number; $i++) {
            if(!empty($total_cliche_cost_formula)) {
                $total_cliche_cost_formula .= " + ";
            }
            $total_cliche_cost_formula .= CalculationBase::Display($calculation->cliche_costs[$i], 2);
        }
        
        array_push($file_data, array("Стоимость форм, руб",
            CalculationBase::Display($calculation->cliche_cost, 2),
            "|= ".$total_cliche_cost_formula,
            "сумма стоимости всех форм"));
        
        array_push($file_data, array("Себестоимость, руб",
            CalculationBase::Display($calculation->cost, 2),
            "|= ". CalculationBase::Display($calculation->film_cost, 2)." + ". CalculationBase::Display($calculation->work_cost, 2)." + ". CalculationBase::Display($calculation->ink_cost, 2)." + ". CalculationBase::Display($calculation->glue_cost, 2)." + (". CalculationBase::Display($calculation->cliche_cost, 2)." * ". CalculationBase::Display($calculation->ukpf, 0).")",
            "стоимость плёнки + стоимость работы + стоимость краски + стоимость клея + (стоимость форм * УКПФ)"));
        
        array_push($file_data, array("Себестоимость за ". $calculation->GetUnitName($unit).", руб",
            CalculationBase::Display($calculation->cost_per_unit, 2),
            "|= ". CalculationBase::Display($calculation->cost, 2)." / ". CalculationBase::Display($quantity, 2),
            "себестоимость / размер тиража"));
        
        array_push($file_data, array("Отгрузочная стоимость, руб",
            CalculationBase::Display($calculation->shipping_cost, 2),
            "|= ".CalculationBase::Display($calculation->cost, 1)." + (".CalculationBase::Display($calculation->cost, 2)." * ".CalculationBase::Display($calculation->extracharge, 2)." / 100)",
            "себестоимость + (себестоимость * наценка на тираж / 100)"));
            
        array_push($file_data, array("Отгрузочная стоимость за ".$calculation->GetUnitName($unit).", руб",
            CalculationBase::Display($calculation->shipping_cost_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost, 2)." / ".CalculationBase::Display($quantity, 2),
            "отгрузочная стоимость / размер тиража"));
            
        array_push($file_data, array("Прибыль, руб",
            CalculationBase::Display($calculation->income, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost, 2)." - ".CalculationBase::Display($calculation->cost, 2),
            "отгрузочная стоимость - себестоимость"));
            
        array_push($file_data, array("Прибыль за ".$calculation->GetUnitName($unit).", руб",
            CalculationBase::Display($calculation->income_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost_per_unit, 2)." - ".CalculationBase::Display($calculation->cost_per_unit, 2),
            "отгрузочная стоимость за ". $calculation->GetUnitName($unit)." - себестоимость за ". $calculation->GetUnitName($unit)));
            
        array_push($file_data, array("Отгрузочная стоимость ПФ, руб",
            CalculationBase::Display($calculation->shipping_cliche_cost, 2),
            "|= ".CalculationBase::Display($calculation->cliche_cost, 2)." + (".CalculationBase::Display($calculation->cliche_cost, 2)." * ".CalculationBase::Display($calculation->extracharge_cliche, 2)." / 100)",
            "сумма стоимости всех форм + (сумма стоимости всех форм * наценка на ПФ / 100)"));
        
        array_push($file_data, array("Общий вес всех плёнок с приладкой, кг",
            CalculationBase::Display($calculation->total_weight_dirty, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_1, 2)." + ".CalculationBase::Display($calculation->weight_dirty_2, 2)." + ".CalculationBase::Display($calculation->weight_dirty_3, 2),
            "масса плёнки грязная 1 + масса плёнки грязная 2 + масса плёнки грязная 3"));
        
        array_push($file_data, array("Стоимость за кг 1, руб",
            CalculationBase::Display($calculation->film_cost_per_unit_1, 2),
            "|= ".CalculationBase::Display($calculation->film_cost_1, 2)." / ".CalculationBase::Display($calculation->weight_dirty_1, 2),
            "общая стоимость грязная 1 / масса плёнки грязная 1"));
        
        array_push($file_data, array("Стоимость за кг 2, руб",
            CalculationBase::Display($calculation->film_cost_per_unit_2, 2),
            "|= ".CalculationBase::Display($calculation->film_cost_2, 2)." / ".CalculationBase::Display($calculation->weight_dirty_2, 2),
            "общая стоимость грязная 2 / масса плёнки грязная 2"));
        
        array_push($file_data, array("Стоимость за кг 3, руб",
            CalculationBase::Display($calculation->film_cost_per_unit_3, 2),
            "|= ".CalculationBase::Display($calculation->film_cost_3, 2)." / ".CalculationBase::Display($calculation->weight_dirty_3, 2),
            "общая стоимость грязная 3 / масса плёнки грязная 3"));
        
        array_push($file_data, array("Отходы 1, руб",
            CalculationBase::Display($calculation->film_waste_cost_1, 2),
            "|= ".CalculationBase::Display($calculation->film_waste_weight_1, 2)." * ".CalculationBase::Display($price_1, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_1, $usd, $euro), 2),
            "отходы 1, кг * цена плёнки 1 * курс валюты"));
        
        array_push($file_data, array("Отходы 2, руб",
            CalculationBase::Display($calculation->film_waste_cost_2, 2),
            "|= ".CalculationBase::Display($calculation->film_waste_weight_2, 2)." * ".CalculationBase::Display($price_2, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_2, $usd, $euro), 2),
            "отходы 2, кг * цена плёнки 2 * курс валюты"));
        
        array_push($file_data, array("Отходы 3, руб",
            CalculationBase::Display($calculation->film_waste_cost_3, 2),
            "|= ".CalculationBase::Display($calculation->film_waste_weight_3, 2)." * ".CalculationBase::Display($price_3, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($currency_3, $usd, $euro), 2),
            "отходы 3, кг * цена плёнки 3 * курс валюты"));
        
        array_push($file_data, array("Отходы 1, кг",
            CalculationBase::Display($calculation->film_waste_weight_1, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_1, 2)." - ".CalculationBase::Display($calculation->weight_pure_1, 2),
            "масса плёнки грязная 1 - масса плёнки чистая 1"));
        
        array_push($file_data, array("Отходы 2, кг",
            CalculationBase::Display($calculation->film_waste_weight_2, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_2, 2)." - ".CalculationBase::Display($calculation->weight_pure_2, 2),
            "масса плёнки грязная 2 - масса плёнки чистая 2"));
        
        array_push($file_data, array("Отходы 3, кг",
            CalculationBase::Display($calculation->film_waste_weight_3, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty_3, 2)." - ".CalculationBase::Display($calculation->weight_pure_3, 2),
            "масса плёнки грязная 3 - масса плёнки чистая 3"));
        
        //***************************************************
        // Сохранение в файл
        $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y')." $name.csv";
        
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