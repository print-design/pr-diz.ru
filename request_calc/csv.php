<?php
include '../include/topscripts.php';
include './calculation.php';

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

$id = filter_input(INPUT_GET, 'id');

if($id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
    // ПОЛУЧЕНИЕ ИСХОДНЫХ ДАННЫХ
    $date = null;
    $name = null;
        
    $quantity = null; // Масса тиража
    $film = null; // Основная пленка, марка
    $thickness = null; // Основная пленка, толщина, мкм
    $density = null; // Основная пленка, плотность, г/м2
    $price = null; // Основная пленка, цена
    $currency = null; // Основная пленка, валюта
    $individual_film_name = null; // Основная плёнка, другая, название
    $individual_thickness = null; // Основная плёнка, другая, толщина
    $individual_density = null; // Основная плёнка, другая, уд.вес
    $customers_material = null; // Основная плёнка, другая, материал заказчика
    $ski = null; // Основная пленка, лыжи
    $width_ski = null; // Основная пленка, ширина пленки, мм
        
    $lamination1_film = null; // Ламинация 1, марка
    $lamination1_thickness = null; // Ламинация 1, толщина, мкм
    $lamination1_density = null; // Ламинация 1, плотность, г/м2
    $lamination1_price = null; // Ламинация 1, цена
    $lamination1_lamination1_currency = null; // Ламинация 1, валюта
    $lamination1_individual_film_name = null; // Ламинация 1, другая, название
    $lamination1_individual_thickness = null; // Ламинация 1, другая, толщина
    $lamination1_individual_density = null; // Ламинация 1, другая, уд. вес
    $lamination1_customers_material = null; // Ламинация 1, другая, материал заказчика
    $lamination1_ski = null; // Ламинация 1, лыжи
    $lamination1_width_ski = null; // Ламинация 1, ширина пленки, мм

    $lamination2_film = null; // Ламинация 2, марка
    $lamination2_thickness = null; // Ламинация 2, толщина, мкм
    $lamination2_density = null; // Ламинация 2, плотность, г/м2
    $lamination2_price = null; // Ламинация 2, цена
    $lamination2_currency = null; // Ламинация 2, валюта
    $lamination2_individual_film_name = null; // Ламинация 2, другая, название
    $lamination2_individual_thickness = null; // Ламинация 2, другая, толщина
    $lamination2_individual_density = null; // Ламинация 2, другая, уд.вес
    $lamination2_customers_material = null; // Ламинация 2, другая, уд. вес
    $lamination2_ski = null; // Ламинация 2, лыжи
    $lamination2_width_ski = null;  // Ламинация 2, ширина пленки, мм
        
    $machine_id = null;
    $stream_width = null; // Ширина ручья, мм
    $streams_number = null; // Количество ручьёв
    $raport = null; // Рапорт
    $ink_number = null; // Красочность
        
    $sql = "select rc.date, rc.name, rc.quantity, rc.unit, "
            . "f.name film, fv.thickness thickness, fv.weight density, "
            . "rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
            . "rc.customers_material, rc.ski, rc.width_ski, "
            . "lamination1_f.name lamination1_film, lamination1_fv.thickness lamination1_thickness, lamination1_fv.weight lamination1_density, "
            . "rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, "
            . "rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
            . "lamination2_f.name lamination2_film, lamination2_fv.thickness lamination2_thickness, lamination2_fv.weight lamination2_density, "
            . "rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, "
            . "rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
            . "rc.machine_id, rc.stream_width, rc.streams_number, rc.raport, rc.ink_number "
            . "from request_calc rc "
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
        
        $quantity = $row['quantity']; // Масса тиража
        $film = $row['film']; // Основная пленка, марка
        $thickness = $row['thickness']; // Основная пленка, толщина, мкм
        $density = $row['density']; // Основная пленка, плотность, г/м2
        $price = $row['price']; // Основная пленка, цена
        $currency = $row['currency']; // Основная пленка, валюта
        $individual_film_name = $row['individual_film_name']; // Основная плёнка, другая, название
        $individual_thickness = $row['individual_thickness']; // Основная плёнка, другая, толщина
        $individual_density = $row['individual_density']; // Основная плёнка, другая, уд.вес
        $customers_material = $row['customers_material']; // Основная плёнка, другая, материал заказчика
        $ski = $row['ski']; // Основная пленка, лыжи
        $width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        $lamination1_film = $row['lamination1_film']; // Ламинация 1, марка
        $lamination1_thickness = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
        $lamination1_density = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
        $lamination1_price = $row['lamination1_price']; // Ламинация 1, цена
        $lamination1_currency = $row['lamination1_currency']; // Ламинация 1, валюта
        $lamination1_individual_film_name = $row['lamination1_individual_film_name']; // Ламинация 1, другая, название
        $lamination1_individual_thickness = $row['lamination1_individual_thickness']; // Ламинация 1, другая, толщина
        $lamination1_individual_density = $row['lamination1_individual_density']; // Ламинация 1, другая, уд. вес
        $lamination1_customers_material = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
        $lamination1_ski = $row['lamination1_ski']; // Ламинация 1, лыжи
        $lamination1_width_ski = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
        $lamination2_film = $row['lamination2_film']; // Ламинация 2, марка
        $lamination2_thickness = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
        $lamination2_density = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
        $lamination2_price = $row['lamination2_price']; // Ламинация 2, цена
        $lamination2_currency = $row['lamination2_currency']; // Ламинация 2, валюта
        $lamination2_individual_film_name = $row['lamination2_individual_film_name']; // Ламинация 2, другая, название
        $lamination2_individual_thickness = $row['lamination2_individual_thickness']; // Ламинация 2, другая, толщина
        $lamination2_individual_density = $row['lamination2_individual_density']; // Ламинация 2, другая, уд.вес
        $lamination2_customers_material = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
        $lamination2_ski = $row['lamination2_ski']; // Ламинация 2, лыжи
        $lamination2_width_ski = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
        $machine_id = $row['machine_id'];
        $stream_width = $row['stream_width']; // Ширина ручья, мм
        $streams_number = $row['streams_number']; // Количество ручьёв
        $raport = $row['raport']; // Рапорт
        $ink_number = $row['ink_number']; // Красочность
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
    $tuning_data = null;
    $laminator_tuning_data = null;
    $machine_data = null;
    $laminator_machine_data = null;
    
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
    }
        
    if(!empty($date)) {
        // Расчёт
        $calculation = new Calculation($tuning_data, 
                $laminator_tuning_data,
                $machine_data,
                $laminator_machine_data,
                $usd, // Курс доллара
                $euro, // Курс евро
                $quantity, // Масса тиража
                
                $film, // Основная пленка, марка
                $thickness, // Основная пленка, толщина, мкм
                $density, // Основная пленка, плотность, г/м2
                $price, // Основная пленка, цена
                $currency, // Основная пленка, валюта
                $individual_film_name, // Основная плёнка, другая, название
                $individual_thickness, // Основная плёнка, другая, толщина
                $individual_density, // Основная плёнка, другая, уд.вес
                $customers_material, // Основная плёнка, другая, материал заказчика
                $ski, // Основная пленка, лыжи
                $width_ski, // Основная пленка, ширина пленки, мм
                
                $lamination1_film, // Ламинация 1, марка
                $lamination1_thickness, // Ламинация 1, толщина, мкм
                $lamination1_density, // Ламинация 1, плотность, г/м2
                $lamination1_price, // Ламинация 1, цена
                $lamination1_currency, // Ламинация 1, валюта
                $lamination1_individual_film_name, // Ламинация 1, другая, название
                $lamination1_individual_thickness, // Ламинация 1, другая, толщина
                $lamination1_individual_density, // Ламинация 1, другая, уд. вес
                $lamination1_customers_material, // Ламинация 1, другая, материал заказчика
                $lamination1_ski, // Ламинация 1, лыжи
                $lamination1_width_ski, // Ламинация 1, ширина пленки, мм
                
                $lamination2_film, // Ламинация 2, марка
                $lamination2_thickness, // Ламинация 2, толщина, мкм
                $lamination2_density, // Ламинация 2, плотность, г/м2
                $lamination2_price, // Ламинация 2, цена
                $lamination2_currency, // Ламинация 2, валюта
                $lamination2_individual_film_name, // Ламинация 2, другая, название
                $lamination2_individual_thickness, // Ламинация 2, другая, толщина
                $lamination2_individual_density, // Ламинация 2, другая, уд.вес
                $lamination2_customers_material, // Ламинация 2, другая, уд. вес
                $lamination2_ski, // Ламинация 2, лыжи
                $lamination2_width_ski,  // Ламинация 2, ширина пленки, мм
                
                $machine_id, // Машина
                $stream_width, // Ширина ручья, мм
                $streams_number, // Количество ручьёв
                $raport, // Рапорт
                $ink_number // Красочность
                );
        
        // Данные CSV-файла
        $file_data = array();
        
        array_push($file_data, array("Курс доллара, руб", $usd, "", ""));
        array_push($file_data, array("Курс евро, руб", $euro, "", ""));
        
        array_push($file_data, array("Масса тиража, кг", $quantity, "", ""));
        array_push($file_data, array("Марка (осн)", $film, "", ""));
        array_push($file_data, array("Толщина (осн), мкм", $thickness, "", ""));
        $density_format = empty($density) ? "0" : number_format($density, 2, ",", " ");
        array_push($file_data, array("Плотность (осн), г/м2", $density_format, "", ""));
        array_push($file_data, array("Лыжи (осн)", GetSkiName($ski), "", ""));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array("Марка (лам 1)", $lamination1_film, "", ""));
            array_push($file_data, array("Толщина (лам 1), мкм", $lamination1_thickness, "", ""));
            $lamination1_density_format = empty($lamination1_density) ? "0" : number_format($lamination1_density, 2, ",", " ");
            array_push($file_data, array("Плотность (лам 1), г/м2", $lamination1_density_format, "", ""));
            array_push($file_data, array("Лыжи (лам 1)", GetSkiName($lamination1_ski), "", ""));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array("Марка (лам 2)", $lamination2_film, "", ""));
            array_push($file_data, array("Толщина (лам 2), мкм", $lamination2_thickness, "", ""));
            $lamination2_density_format = empty($lamination2_density) ? "0" : number_format($lamination2_density, 2, ",", " ");
            array_push($file_data, array("Плотность (лам 2), г/м2", $lamination2_density_format, "", ""));
            array_push($file_data, array("Лыжи (лам 2)", GetSkiName($lamination2_ski), "", ""));
        }
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        $raport_format = number_format($raport, 3, ",", "");
        array_push($file_data, array("Рапорт", $raport_format, "", ""));
        
        // Ширина материала, мм
        array_push($file_data, array($calculation->width->name, $calculation->width->display, $calculation->width->formula, $calculation->width->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_width->name, $calculation->lamination1_width->display, $calculation->lamination1_width->formula, $calculation->lamination1_width->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_width->name, $calculation->lamination2_width->display, $calculation->lamination2_width->formula, $calculation->lamination2_width->comment));
        }        
        
        // М2 чистые, м2
        array_push($file_data, array($calculation->m2pure->name, $calculation->m2pure->display, $calculation->m2pure->formula, $calculation->m2pure->comment));

        // М пог. чистые, м
        array_push($file_data, array($calculation->mpogpure->name, $calculation->mpogpure->display, $calculation->mpogpure->formula, $calculation->mpogpure->comment));
        
        // Метраж отходов, м
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->waste_length->name, $calculation->waste_length->display, $calculation->waste_length->formula, $calculation->waste_length->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_waste_length->name, $calculation->lamination1_waste_length->display, $calculation->lamination1_waste_length->formula, $calculation->lamination1_waste_length->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_waste_length->name, $calculation->lamination2_waste_length->display, $calculation->lamination2_waste_length->formula, $calculation->lamination2_waste_length->comment));
        }
        
        // Красочность
        if(!empty($ink_number)) {
            array_push($file_data, array("Красочность", $ink_number, "", ""));
        }
        
        // М. пог. грязные, м
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->mpogdirty->name, $calculation->mpogdirty->display, $calculation->mpogdirty->formula, $calculation->mpogdirty->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_mpogdirty->name, $calculation->lamination1_mpogdirty->display, $calculation->lamination1_mpogdirty->formula, $calculation->lamination1_mpogdirty->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_mpogdirty->name, $calculation->lamination2_mpogdirty->display, $calculation->lamination2_mpogdirty->formula, $calculation->lamination2_mpogdirty->comment));
        }
        
        // М2 грязные, м2
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->m2dirty->name, $calculation->m2dirty->display, $calculation->m2dirty->formula, $calculation->m2dirty->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_m2dirty->name, $calculation->lamination1_m2dirty->display, $calculation->lamination1_m2dirty->formula, $calculation->lamination1_m2dirty->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_m2dirty->name, $calculation->lamination2_m2dirty->display, $calculation->lamination2_m2dirty->formula, $calculation->lamination2_m2dirty->comment));
        }
        
        //****************************
        // Массы и длины пленок
        // ***************************
        
        // Масса плёнки чистая
        array_push($file_data, array($calculation->mpure->name, $calculation->mpure->display, $calculation->mpure->formula, $calculation->mpure->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_mpure->name, $calculation->lamination1_mpure->display, $calculation->lamination1_mpure->formula, $calculation->lamination1_mpure->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_mpure->name, $calculation->lamination2_mpure->display, $calculation->lamination2_mpure->formula, $calculation->lamination2_mpure->comment));
        }
        
        // Длина пленки чистая
        array_push($file_data, array($calculation->lengthpure->name, $calculation->lengthpure->display, $calculation->lengthpure->formula, $calculation->lengthpure->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_lengthpure->name, $calculation->lamination1_lengthpure->display, $calculation->lamination1_lengthpure->formula, $calculation->lamination1_lengthpure->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_lengthpure->name, $calculation->lamination2_lengthpure->display, $calculation->lamination2_lengthpure->formula, $calculation->lamination2_lengthpure->comment));
        }
        
        // Масса плёнки грязная (с приладкой), кг
        array_push($file_data, array($calculation->mdirty->name, $calculation->mdirty->display, $calculation->mdirty->formula, $calculation->mdirty->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_mdirty->name, $calculation->lamination1_mdirty->display, $calculation->lamination1_mdirty->formula, $calculation->lamination1_mdirty->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_mdirty->name, $calculation->lamination2_mdirty->display, $calculation->lamination2_mdirty->formula, $calculation->lamination2_mdirty->comment));
        }
        
        // Длина плёнки грязная, м
        array_push($file_data, array($calculation->lengthdirty->name, $calculation->lengthdirty->display, $calculation->lengthdirty->formula, $calculation->lengthdirty->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_lengthdirty->name, $calculation->lamination1_lengthdirty->display, $calculation->lamination1_lengthdirty->formula, $calculation->lamination1_lengthdirty->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_lengthdirty->name, $calculation->lamination2_lengthdirty->display, $calculation->lamination2_lengthdirty->formula, $calculation->lamination2_lengthdirty->comment));
        }
        
        //***************************************************
        // Себестоимость плёнок
        //***************************************************
        
        // Себестоимость грязная (с приладки), руб
        array_push($file_data, array($calculation->film_price->name, $calculation->film_price->display, $calculation->film_price->formula, $calculation->film_price->comment));
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_film_price->name, $calculation->lamination1_film_price->display, $calculation->lamination1_film_price->formula, $calculation->lamination1_film_price->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_film_price->name, $calculation->lamination2_film_price->display, $calculation->lamination2_film_price->formula, $calculation->lamination2_film_price->comment));
        }
        
        //******************************************************
        // Время - Деньги
        //******************************************************
        
        // Время приладки, мин
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->tuning_time->name, $calculation->tuning_time->display, $calculation->tuning_time->formula, $calculation->tuning_time->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_tuning_time->name, $calculation->lamination1_tuning_time->display, $calculation->lamination1_tuning_time->formula, $calculation->lamination1_tuning_time->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_tuning_time->name, $calculation->lamination2_tuning_time->display, $calculation->lamination2_tuning_time->formula, $calculation->lamination2_tuning_time->comment));
        }
        
        // Время печати и ламинации (без приладки), ч
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->print_time->name, $calculation->print_time->display, $calculation->print_time->formula, $calculation->print_time->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_time->name, $calculation->lamination1_time->display, $calculation->print_time->formula, $calculation->print_time->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_time->name, $calculation->lamination2_time->display, $calculation->print_time->formula, $calculation->print_time->comment));
        }
        
        // Общее время выполнения тиража
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->work_time->name, $calculation->work_time->display, $calculation->work_time->formula, $calculation->work_time->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_work_time->name, $calculation->lamination1_work_time->display, $calculation->lamination1_work_time->formula, $calculation->lamination1_work_time->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_work_time->name, $calculation->lamination2_work_time->display, $calculation->lamination2_work_time->formula, $calculation->lamination2_work_time->comment));
        }
        
        // Стоимость выполнения тиража
        if(!empty($machine_id)) {
            array_push($file_data, array($calculation->work_price->name, $calculation->work_price->display, $calculation->work_price->formula, $calculation->work_price->comment));
        }
        
        if($calculation->laminations_number > 0) {
            array_push($file_data, array($calculation->lamination1_work_price->name, $calculation->lamination1_work_price->display, $calculation->lamination1_work_price->formula, $calculation->lamination1_work_price->comment));
        }
        
        if($calculation->laminations_number > 1) {
            array_push($file_data, array($calculation->lamination2_work_price->name, $calculation->lamination2_work_price->display, $calculation->lamination2_work_price->formula, $calculation->lamination2_work_price->comment));
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