<?php
include '../include/topscripts.php';
include './calculation.php';

function GetSkiName($ski) {
    switch ($ski) {
        case NO_SKI:
            return "Без лыж";
        case STANDARD_SKI:
            return "Стандартные лыжи";
        case NONSTANDARD_SKI:
            return "Нестандартные лыжи";
        default :
            return "Неизвестно";
    }
}

function GetWidthCalculation($ski, $streams_number, $stream_width, $width_ski) {
    $result = "";
    
    switch($ski) {
        case NO_SKI:
            $result = "$streams_number * $stream_width";
            break;
        
        case STANDARD_SKI:
            $result = "$streams_number * $stream_width + 20";
            break;
        
        case NONSTANDARD_SKI:
            $result = "";
            break;
    }
    
    return $result;
}

function GetWidthComment($ski) {
    $result = "";
    
    switch($ski) {
        case NO_SKI:
            $result = "количество ручьёв * ширина ручья";
            break;
        
        case STANDARD_SKI:
            $result = "количество ручьёв * ширина ручья + 20 мм";
            break;
        
        case NONSTANDARD_SKI:
            $result = "вводится вручную";
            break;
    }
    
    return $result;
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
    
    while ($row = $fetcher->Fetch()) {
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
    
    if(!empty($date)) {
        $sql = "select machine_id, time, length, waste_percent from norm_tuning where id in (select max(id) from norm_tuning where date <= '$date' group by machine_id)";
        $fetcher = new Fetcher($sql);
        while ($row = $fetcher->Fetch()) {
            $tuning_data[$row['machine_id']] = array("time" => $row['time'], "length" => $row['length'], "waste_percent" => $row['waste_percent']);
        }
        
        $sql = "select time, length, waste_percent from norm_laminator_tuning where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $laminator_tuning_data = array("time" => $row['time'], "length" => $row['length'], "waste_percent" => $row['waste_percent']);
        }
    }
        
    if(!empty($date)) {
        // Данные расчёта
        $data = Calculate($tuning_data, 
                $laminator_tuning_data,
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
        
        $laminations_number = $data['laminations_number'];
        
        if($laminations_number > 0) {
            array_push($file_data, array("Марка (лам 1)", $lamination1_film, "", ""));
            array_push($file_data, array("Толщина (лам 1), мкм", $lamination1_thickness, "", ""));
            $lamination1_density_format = empty($lamination1_density) ? "0" : number_format($lamination1_density, 2, ",", " ");
            array_push($file_data, array("Плотность (лам 1), г/м2", $lamination1_density_format, "", ""));
            array_push($file_data, array("Лыжи (лам 1)", GetSkiName($lamination1_ski), "", ""));
        }
        
        if($laminations_number > 1) {
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
        
        // Результаты вычислений
        $width = $data['width'];
        $width_calculation = GetWidthCalculation($ski, $streams_number, $stream_width, $width_ski);
        $width_comment = GetWidthComment($ski);
        array_push($file_data, array("Ширина материала (осн), мм", $width, $width_calculation, $width_comment));
        
        if($laminations_number > 0) {
            $lamination1_width = $data['lamination1_width'];
            $lamination1_width_calculation = GetWidthCalculation($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski);
            $lamination1_width_comment = GetWidthComment($lamination1_ski);
            array_push($file_data, array("Ширина материала (лам 1), мм", $lamination1_width, $lamination1_width_calculation, $lamination1_width_comment));
        }
        
        if($laminations_number > 1) {
            $lamination2_width = $data['lamination2_width'];
            $lamination2_width_calculation = GetWidthCalculation($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski);
            $lamination2_width_comment = GetWidthComment($lamination2_ski);
            array_push($file_data, array("Ширина материала (лам 2), мм", $lamination2_width, $lamination2_width_calculation, $lamination2_width_comment));
        }
        
        $m2pure_format = number_format($data['m2pure'], 2, ",", " ");
        array_push($file_data, array("М2 чистые, м2", $m2pure_format, "$quantity * 1000 / ($density_format + $lamination1_density_format + $lamination2_density_format)", "масса тиража * 1000 / (осн. пл. уд. вес + лам. 1 уд. вес + лам. 2 уд. вес)"));
        
        $mpogpure_format = number_format($data['mpogpure'], 2, ",", " ");
        array_push($file_data, array("М пог. чистые, м", $mpogpure_format, "$m2pure_format / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)"));
        
        if(!empty($machine_id)) {
            $waste_length_format = number_format($data['waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $waste_length_format, $tuning_data[$machine_id]['waste_percent']." * $mpogpure_format / 100", "процент отходов печати * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 0) {
            $lamination1_waste_length_format = number_format($data['lamination1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $lamination1_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 1) {
            $lamination2_waste_length_format = number_format($data['lamination1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $lamination2_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if(!empty($ink_number)) {
            array_push($file_data, array("Красочность", $ink_number, "", ""));
        }
        
        if(!empty($machine_id)) {
            $mpogdirty_format = number_format($data['mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (осн), м", $mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + $ink_number * ".$tuning_data[$machine_id]['length']." + $laminations_number * ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + красочность * метраж приладки 1 краски + кол-во ламинаций * метраж приладки ламинации"));
        }
        
        if($laminations_number > 0) {
            $lamination1_mpogdirty_format = number_format($data['lamination1_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (лам 1), м", $lamination1_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length']." * 2", "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации * 2"));
        }
        
        if($laminations_number > 1) {
            $lamination2_mpogdirty_format = number_format($data['lamination2_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (лам 2), м", $lamination2_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации"));
        }
        
        if(!empty($machine_id)) {
            $m2dirty_format = number_format($data['m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (осн), м2", $m2dirty_format, "$mpogdirty_format * $width / 1000", "м. пог. грязные * ширина материала основной пленки / 1000"));
        }
        
        if($laminations_number > 0) {
            $lamination1_m2dirty_format = number_format($data['lamination1_m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (лам 1), м2", $lamination1_m2dirty_format, "$lamination1_mpogdirty_format * $lamination1_width / 1000", "м. пог. грязные * ширина материала ламинации 1 / 1000"));
        }
        
        if($laminations_number > 1) {
            $lamination2_m2dirty_format = number_format($data['lamination2_m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (лам 2), м2", $lamination2_m2dirty_format, "$lamination2_mpogdirty_format * $lamination2_width / 1000", "м. пог. грязные * ширина материала ламинации 2 / 1000"));
        }
        
        //****************************
        // Массы и длины пленок
        // ***************************
        
        // Масса плёнки чистая
        $mpure_format = number_format($data['mpure'], 2, ",", " ");
        array_push($file_data, array("Масса плёнки чистая (осн), кг", $mpure_format, "$mpogpure_format * $width * $density_format / 1000", "м. пог. чистые * ширина материала основной пленки / 1000"));
        
        if($laminations_number > 0) {
            $lamination1_mpure_format = number_format($data['lamination1_mpure'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки чистая (лам 1), кг", $lamination1_mpure_format, "$mpogpure_format * $lamination1_width * $lamination1_density_format / 1000", "м. пог. чистые * ширина материала ламинации 1 / 1000"));
        }
        
        if($laminations_number > 1) {
            $lamination2_mpure_format = number_format($data['lamination2_mpure'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки чистая (лам 2), кг", $lamination1_mpure_format, "$mpogpure_format * $lamination1_width * $lamination2_density_format / 1000", "м. пог. чистые * ширина материала ламинации 2 / 1000"));
        }
        
        // Длина пленки чистая
        $lengthpure_format = number_format($data['lengthpure'], 2, ",", " ");
        array_push($file_data, array("Длина плёнки чистая (осн), м", $lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        
        if($laminations_number > 0) {
            $lamination1_lengthpure_format = number_format($data['lamination1_lengthpure'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки чистая (лам 1), м", $lamination1_lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        }
        
        if($laminations_number > 1) {
            $lamination2_lengthpure_format = number_format($data['lamination2_lengthpure'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки чистая (лам 2), м", $lamination2_lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        }
        
        // Масса плёнки грязная (с приладкой), кг
        $mdirty_format = number_format($data['mdirty'], 2, ",", " ");
        array_push($file_data, array("Масса плёнки грязная (осн), м", $mdirty_format, "$m2dirty_format * $density_format / 1000", "м2 грязные * уд. вес / 1000"));
        
        if($laminations_number > 0) {
            $lamination1_mdirty_format = number_format($data['lamination1_mdirty'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки грязная (лам 1), м", $lamination1_mdirty_format, "$lamination1_m2dirty_format * $lamination1_density_format / 1000", "м2 грязные * уд. вес / 1000"));
        }
        
        if($laminations_number > 1) {
            $lamination2_mdirty_format = number_format($data['lamination2_mdirty'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки грязная (лам 2), м", $lamination2_mdirty_format, "$lamination2_m2dirty_format * $lamination2_m2dirty_format / 1000", "м2 грязные * уд. вес / 1000"));
        }
        
        // Длина плёнки грязная, м
        $lengthdirty_format = number_format($data['lengthdirty'], 2, ",", " ");
        array_push($file_data, array("Длина плёнки грязная (осн), м", $lengthdirty_format, $mpogdirty_format, "м пог. грязные осн. плёнки"));
        
        if($laminations_number > 0) {
            $lamination1_lengthdirty_format = number_format($data['lamination1_lengthdirty'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки грязная (лам 1), м2", $lamination1_lengthdirty_format, $lamination1_mpogdirty_format, "м. пог. грязные ламинации 1"));
        }
        
        if($laminations_number > 1) {
            $lamination2_lengthdirty_format = number_format($data['lamination2_lengthdirty'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки грязная (лам 2), м2", $lamination2_lengthdirty_format, $lamination2_mpogdirty_format, "м. пог. грязные даминации 2"));
        }
        
        //***************************************************
        // Себестоимость плёнок
        //***************************************************
        
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