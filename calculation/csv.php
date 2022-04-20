<?php
include '../include/topscripts.php';
include './calculation.php';

function Display($value) {
    if(is_float($value) || is_double($value)) {
        return number_format($fvalue, 2, ",", " ");
    }
    elseif(is_string($value)) {
        return str_replace(".", ",", $value);
    }
    else {
        return $value;
    }
}

function GetSkiName($ski) {
    switch ($ski) {
        case Calculation::NO_SKI:
            return "Без лыж";
        case Calculation::STANDARD_SKI:
            return "Стандартные лыжи";
        case Calculation::NONSTANDARD_SKI:
            return "Нестандартные лыжи";
        default :
            return "Неизвестно";
    }
}

function GetInkName($ink) {
    switch ($ink) {
        case Calculation::CMYK:
            return "CMYK";
            
        case Calculation::PANTON:
            return "Пантон";
            
        case Calculation::WHITE:
            return "Белая";
            
        case Calculation::LACQUER:
            return "Лак";
            
        default :
            return "Неизвестная";
    }
}

function GetClicheName($cliche) {
    switch ($cliche) {
        case Calculation::OLD:
            return "старая";
            
        case Calculation::FLINT:
            return "новая Флинт";
            
        case Calculation::KODAK:
            return "новая Кодак";
            
        case Calculation::TVER:
            return "новая Тверь";
            
        default :
            return "Неизвестная";
    }
}

function GetUnitName($unit) {
    switch ($unit) {
        case Calculation::KG:
            return "кг";
            
        case Calculation::PIECES:
            return "шт";
            
        default :
            return "";
    }
}

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
    $tuning_data = new TuningData(null, null, null);
    $laminator_tuning_data = new TuningData(null, null, null);
    $machine_data = new MachineData(null, null, null);
    $laminator_machine_data = new MachineData(null, null, null);
    $ink_data = new InkData(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    $glue_data = new GlueData(null, null, null, null, null, null, null);
    
    if(!empty($date)) {
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
        
    if(!empty($date)) {
        // Расчёт
        $calculation = new Calculation($tuning_data, 
                $laminator_tuning_data,
                $machine_data,
                $laminator_machine_data,
                $ink_data,
                $glue_data,
                $usd, // Курс доллара
                $euro, // Курс евро
                $unit, // Кг или шт
                $quantity, // Размер тиража в кг или шт
                $work_type_id, // Тип работы: с печатью или без печати
                
                $film, // Основная пленка, марка
                $thickness, // Основная пленка, толщина, мкм
                $density, // Основная пленка, плотность, г/м2
                $price, // Основная пленка, цена
                $currency, // Основная пленка, валюта
                $customers_material, // Основная плёнка, другая, материал заказчика
                $ski, // Основная пленка, лыжи
                $width_ski, // Основная пленка, ширина пленки, мм
                
                $lamination1_film, // Ламинация 1, марка
                $lamination1_thickness, // Ламинация 1, толщина, мкм
                $lamination1_density, // Ламинация 1, плотность, г/м2
                $lamination1_price, // Ламинация 1, цена
                $lamination1_currency, // Ламинация 1, валюта
                $lamination1_customers_material, // Ламинация 1, другая, материал заказчика
                $lamination1_ski, // Ламинация 1, лыжи
                $lamination1_width_ski, // Ламинация 1, ширина пленки, мм
                
                $lamination2_film, // Ламинация 2, марка
                $lamination2_thickness, // Ламинация 2, толщина, мкм
                $lamination2_density, // Ламинация 2, плотность, г/м2
                $lamination2_price, // Ламинация 2, цена
                $lamination2_currency, // Ламинация 2, валюта
                $lamination2_customers_material, // Ламинация 2, другая, уд. вес
                $lamination2_ski, // Ламинация 2, лыжи
                $lamination2_width_ski,  // Ламинация 2, ширина пленки, мм
                
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
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8
                );
        
        // Данные CSV-файла
        $file_data = array();
        
        array_push($file_data, array("Курс доллара, руб", Display($usd), "", ""));
        array_push($file_data, array("Курс евро, руб", Display($euro), "", ""));
        
        if(!empty($machine_id)) {
            array_push($file_data, array("Машина", $machine, "", ""));
        }
        
        array_push($file_data, array("Размер тиража", $quantity.' '. GetUnitName($unit), "", ""));
        array_push($file_data, array("Марка (осн)", $film, "", ""));
        array_push($file_data, array("Толщина (осн), мкм", $thickness, "", ""));
        array_push($file_data, array("Плотность (осн), г/м2", Display($density), "", ""));
        array_push($file_data, array("Лыжи (осн)", GetSkiName($ski), "", ""));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array("Марка (лам 1)", $lamination1_film, "", ""));
            array_push($file_data, array("Толщина (лам 1), мкм", $lamination1_thickness, "", ""));
            array_push($file_data, array("Плотность (лам 1), г/м2", Display($lamination1_density), "", ""));
            array_push($file_data, array("Лыжи (лам 1)", GetSkiName($lamination1_ski), "", ""));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array("Марка (лам 2)", $lamination2_film, "", ""));
            array_push($file_data, array("Толщина (лам 2), мкм", $lamination2_thickness, "", ""));
            array_push($file_data, array("Плотность (лам 2), г/м2", Display($lamination2_density), "", ""));
            array_push($file_data, array("Лыжи (лам 2)", GetSkiName($lamination2_ski), "", ""));
        }
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        $raport_format = number_format($raport, 3, ",", "");
        array_push($file_data, array("Рапорт", $raport_format, "", ""));
        
        if(!empty($machine_id)) {
            for($i=1; $i<=$ink_number; $i++) {
                $ink = "ink_$i";
                $color = "color_$i";
                $cmyk = "cmyk_$i";
                $percent = "percent_$i";
                $cliche = "cliche_$i";
                array_push($file_data, array("Краска $i:", GetInkName($$ink).(empty($$color) ? "" : " ".$$color).(empty($$cmyk) ? "" : " ".$$cmyk)." ".$$percent."% ".GetClicheName($$cliche), "", ""));
            }
        }
        
        array_push($file_data, array("", "", "", ""));
        
        // Основные величины
        foreach($calculation->base_values as $base_value) {
            array_push($file_data, array($base_value->name, $base_value->display, $base_value->formula, $base_value->comment));
        }
        
        // Расход краски
        if(!empty($ink_number)) {
            array_push($file_data, array("Красочность", $ink_number, "", ""));
            
            for($i=1; $i<=$ink_number; $i++) {
                // Цена 1 кг чистой краски
                array_push($file_data, array($calculation->ink_kg_prices[$i]->name, $calculation->ink_kg_prices[$i]->display, $calculation->ink_kg_prices[$i]->formula, $calculation->ink_kg_prices[$i]->comment));
                
                // Цена 1 кг КраскаСмеси
                array_push($file_data, array($calculation->mix_ink_kg_prices[$i]->name, $calculation->mix_ink_kg_prices[$i]->display, $calculation->mix_ink_kg_prices[$i]->formula, $calculation->mix_ink_kg_prices[$i]->comment));
                
                // Расход КраскаСмеси, кг
                array_push($file_data, array($calculation->ink_expenses[$i]->name, $calculation->ink_expenses[$i]->display, $calculation->ink_expenses[$i]->formula, $calculation->ink_expenses[$i]->comment));
                
                // Стоимость КраскаСмеси, руб
                array_push($file_data, array($calculation->ink_prices[$i]->name, $calculation->ink_prices[$i]->display, $calculation->ink_prices[$i]->formula, $calculation->ink_prices[$i]->comment));
            }
        }
        
        // Расход клея
        foreach($calculation->glue_values as $glue_value) {
            array_push($file_data, array($glue_value->name, $glue_value->display, $glue_value->formula, $glue_value->comment));
        }
        
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
        <h1 style="text-decoration: underline;">Чтобы экспортировать в CSV надо наэати на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>