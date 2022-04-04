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
        
    $lam1_film = null; // Ламинация 1, марка
    $lam1_thickness = null; // Ламинация 1, толщина, мкм
    $lam1_density = null; // Ламинация 1, плотность, г/м2
    $lamination1_price = null; // Ламинация 1, цена
    $lamination1_currency = null; // Ламинация 1, валюта
    $individual_film_name = null; // Ламинация 1, другая, название
    $individual_thickness = null; // Ламинация 1, другая, толщина
    $individual_density = null; // Ламинация 1, другая, уд. вес
    $customers_material = null; // Ламинация 1, другая, маткриал заказчика
    $lam1_ski = null; // Ламинация 1, лыжи
    $lam1_width_ski = null; // Ламинация 1, ширина пленки, мм
        
    $lam2_film = null; // Ламинация 2, марка
    $lam2_thickness = null; // Ламинация 2, толщина, мкм
    $lam2_density = null; // Ламинация 2, плотность, г/м2
    $lamination2_price = null; // Ламинация 2, цена
    $lamination2_currency = null; // Ламинация 2, валюта
    $lamination2_individual_film_name; // Ламинация 2, другая, название
    $lamination2_individual_thickness; // Ламинация 2, другая, толщина
    $lamination2_individual_density; // Ламинация 2, другая, уд.вес
    $lamination2_customers_material; // Ламинация 2, другая, уд. вес
    $lam2_ski = null; // Ламинация 2, лыжи
    $lam2_width_ski = null;  // Ламинация 2, ширина пленки, мм
        
    $machine_id = null;
    $stream_width = null; // Ширина ручья, мм
    $streams_number = null; // Количество ручьёв
    $raport = null; // Рапорт
    $ink_number = null; // Красочность
        
    $sql = "select rc.date, rc.name, rc.quantity, rc.unit, "
            . "f.name film, fv.thickness thickness, fv.weight density, "
            . "rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
            . "rc.customers_material, rc.ski, rc.width_ski, "
            . "lam1_f.name lam1_film, lam1_fv.thickness lam1_thickness, lam1_fv.weight lam1_density, "
            . "rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, "
            . "rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
            . "lam2_f.name lam2_film, lam2_fv.thickness lam2_thickness, lam2_fv.weight lam2_density, "
            . "rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, "
            . "rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
            . "rc.machine_id, rc.stream_width, rc.streams_number, rc.raport, rc.ink_number "
            . "from request_calc rc "
            . "left join film_variation fv on rc.film_variation_id = fv.id "
            . "left join film f on fv.film_id = f.id "
            . "left join film_variation lam1_fv on rc.lamination1_film_variation_id = lam1_fv.id "
            . "left join film lam1_f on lam1_fv.film_id = lam1_f.id "
            . "left join film_variation lam2_fv on rc.lamination2_film_variation_id = lam2_fv.id "
            . "left join film lam2_f on lam2_fv.film_id = lam2_f.id "
            . "where rc.id = $id";
    $fetcher = new Fetcher($sql);
    
    while ($row = $fetcher->Fetch()) {
        $date = $row['date'];
        $name = $row['name'];
        
        $quantity = $row['quantity']; // Масса тиража
        $film = $row['film']; // Основная пленка, марка
        $thickness = $row['thickness']; // Основная пленка, толщина, мкм
        $density = $row['density']; // Основная пленка, плотность, г/м2
        $ski = $row['ski']; // Основная пленка, лыжи
        $width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        $lam1_film = $row['lam1_film']; // Ламинация 1, марка
        $lam1_thickness = $row['lam1_thickness']; // Ламинация 1, толщина, мкм
        $lam1_density = $row['lam1_density']; // Ламинация 1, плотность, г/м2
        $lam1_ski = $row['lamination1_ski']; // Ламинация 1, лыжи
        $lam1_width_ski = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
        $lam2_film = $row['lam2_film']; // Ламинация 2, марка
        $lam2_thickness = $row['lam2_thickness']; // Ламинация 2, толщина, мкм
        $lam2_density = $row['lam2_density']; // Ламинация 2, плотность, г/м2
        $lam2_ski = $row['lamination2_ski']; // Ламинация 2, лыжи
        $lam2_width_ski = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
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
                $ski, // Основная пленка, лыжи
                $width_ski, // Основная пленка, ширина пленки, мм
                
                $lam1_film, // Ламинация 1, марка
                $lam1_thickness, // Ламинация 1, толщина, мкм
                $lam1_density, // Ламинация 1, плотность, г/м2
                $lam1_ski, // Ламинация 1, лыжи
                $lam1_width_ski, // Ламинация 1, ширина пленки, мм
                
                $lam2_film, // Ламинация 2, марка
                $lam2_thickness, // Ламинация 2, толщина, мкм
                $lam2_density, // Ламинация 2, плотность, г/м2
                $lam2_ski, // Ламинация 2, лыжи
                $lam2_width_ski,  // Ламинация 2, ширина пленки, мм
                
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
            array_push($file_data, array("Марка (лам 1)", $lam1_film, "", ""));
            array_push($file_data, array("Толщина (лам 1), мкм", $lam1_thickness, "", ""));
            $lam1_density_format = empty($lam1_density) ? "0" : number_format($lam1_density, 2, ",", " ");
            array_push($file_data, array("Плотность (лам 1), г/м2", $lam1_density_format, "", ""));
            array_push($file_data, array("Лыжи (лам 1)", GetSkiName($lam1_ski), "", ""));
        }
        
        if($laminations_number > 1) {
            array_push($file_data, array("Марка (лам 2)", $lam2_film, "", ""));
            array_push($file_data, array("Толщина (лам 2), мкм", $lam2_thickness, "", ""));
            $lam2_density_format = empty($lam2_density) ? "0" : number_format($lam2_density, 2, ",", " ");
            array_push($file_data, array("Плотность (лам 2), г/м2", $lam2_density_format, "", ""));
            array_push($file_data, array("Лыжи (лам 2)", GetSkiName($lam2_ski), "", ""));
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
            $lam1_width = $data['lam1_width'];
            $lam1_width_calculation = GetWidthCalculation($lam1_ski, $streams_number, $stream_width, $lam1_width_ski);
            $lam1_width_comment = GetWidthComment($lam1_ski);
            array_push($file_data, array("Ширина материала (лам 1), мм", $lam1_width, $lam1_width_calculation, $lam1_width_comment));
        }
        
        if($laminations_number > 1) {
            $lam2_width = $data['lam2_width'];
            $lam2_width_calculation = GetWidthCalculation($lam2_ski, $streams_number, $stream_width, $lam2_width_ski);
            $lam2_width_comment = GetWidthComment($lam2_ski);
            array_push($file_data, array("Ширина материала (лам 2), мм", $lam2_width, $lam2_width_calculation, $lam2_width_comment));
        }
        
        $m2pure_format = number_format($data['m2pure'], 2, ",", " ");
        array_push($file_data, array("М2 чистые, м2", $m2pure_format, "$quantity * 1000 / ($density_format + $lam1_density_format + $lam2_density_format)", "масса тиража * 1000 / (осн. пл. уд. вес + лам. 1 уд. вес + лам. 2 уд. вес)"));
        
        $mpogpure_format = number_format($data['mpogpure'], 2, ",", " ");
        array_push($file_data, array("М пог. чистые, м", $mpogpure_format, "$m2pure_format / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)"));
        
        if(!empty($machine_id)) {
            $waste_length_format = number_format($data['waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $waste_length_format, $tuning_data[$machine_id]['waste_percent']." * $mpogpure_format / 100", "процент отходов печати * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 0) {
            $lam1_waste_length_format = number_format($data['lam1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $lam1_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 1) {
            $lam2_waste_length_format = number_format($data['lam1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Метраж отходов (осн), м", $lam2_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if(!empty($ink_number)) {
            array_push($file_data, array("Красочность", $ink_number, "", ""));
        }
        
        if(!empty($machine_id)) {
            $mpogdirty_format = number_format($data['mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (осн), м", $mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + $ink_number * ".$tuning_data[$machine_id]['length']." + $laminations_number * ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + красочность * метраж приладки 1 краски + кол-во ламинаций * метраж приладки ламинации"));
        }
        
        if($laminations_number > 0) {
            $lam1_mpogdirty_format = number_format($data['lam1_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (лам 1), м", $lam1_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length']." * 2", "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации * 2"));
        }
        
        if($laminations_number > 1) {
            $lam2_mpogdirty_format = number_format($data['lam2_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("М. пог. грязные (лам 2), м", $lam2_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации"));
        }
        
        if(!empty($machine_id)) {
            $m2dirty_format = number_format($data['m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (осн), м2", $m2dirty_format, "$mpogdirty_format * $width / 1000", "м. пог. грязные * ширина материала основной пленки / 1000"));
        }
        
        if($laminations_number > 0) {
            $lam1_m2dirty_format = number_format($data['lam1_m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (лам 1), м2", $lam1_m2dirty_format, "$lam1_mpogdirty_format * $lam1_width / 1000", "м. пог. грязные * ширина материала ламинации 1 / 1000"));
        }
        
        if($laminations_number > 1) {
            $lam2_m2dirty_format = number_format($data['lam2_m2dirty'], 2, ",", " ");
            array_push($file_data, array("М2 грязные (лам 2), м2", $lam2_m2dirty_format, "$lam2_mpogdirty_format * $lam2_width / 1000", "м. пог. грязные * ширина материала ламинации 2 / 1000"));
        }
        
        //****************************
        // Массы и длины пленок
        // ***************************
        
        // Масса плёнки чистая
        $mpure_format = number_format($data['mpure'], 2, ",", " ");
        array_push($file_data, array("Масса плёнки чистая (осн), кг", $mpure_format, "$mpogpure_format * $width * $density_format / 1000", "м. пог. чистые * ширина материала основной пленки / 1000"));
        
        if($laminations_number > 0) {
            $lam1_mpure_format = number_format($data['lam1_mpure'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки чистая (лам 1), кг", $lam1_mpure_format, "$mpogpure_format * $lam1_width * $lam1_density_format / 1000", "м. пог. чистые * ширина материала ламинации 1 / 1000"));
        }
        
        if($laminations_number > 1) {
            $lam2_mpure_format = number_format($data['lam2_mpure'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки чистая (лам 2), кг", $lam1_mpure_format, "$mpogpure_format * $lam1_width * $lam2_density_format / 1000", "м. пог. чистые * ширина материала ламинации 2 / 1000"));
        }
        
        // Длина пленки чистая
        $lengthpure_format = number_format($data['lengthpure'], 2, ",", " ");
        array_push($file_data, array("Длина плёнки чистая (осн), м", $lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        
        if($laminations_number > 0) {
            $lam1_lengthpure_format = number_format($data['lam1_lengthpure'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки чистая (лам 1), м", $lam1_lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        }
        
        if($laminations_number > 1) {
            $lam2_lengthpure_format = number_format($data['lam2_lengthpure'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки чистая (лам 2), м", $lam2_lengthpure_format, $mpogpure_format, "м. пог. чистые"));
        }
        
        // Масса плёнки грязная (с приладкой), кг
        $mdirty_format = number_format($data['mdirty'], 2, ",", " ");
        array_push($file_data, array("Масса плёнки грязная (осн), м", $mdirty_format, "$m2dirty_format * $density_format / 1000", "м2 грязные * уд. вес / 1000"));
        
        if($laminations_number > 0) {
            $lam1_mdirty_format = number_format($data['lam1_mdirty'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки грязная (лам 1), м", $lam1_mdirty_format, "$lam1_m2dirty_format * $lam1_density_format / 1000", "м2 грязные * уд. вес / 1000"));
        }
        
        if($laminations_number > 1) {
            $lam2_mdirty_format = number_format($data['lam2_mdirty'], 2, ",", " ");
            array_push($file_data, array("Масса плёнки грязная (лам 2), м", $lam2_mdirty_format, "$lam2_m2dirty_format * $lam2_m2dirty_format / 1000", "м2 грязные * уд. вес / 1000"));
        }
        
        // Длина плёнки грязная, м
        $lengthdirty_format = number_format($data['lengthdirty'], 2, ",", " ");
        array_push($file_data, array("Длина плёнки грязная (осн), м", $lengthdirty_format, $lam1_mpogdirty_format, "м пог. грязные осн. плёнки"));
        
        if($laminations_number > 0) {
            $lam1_lengthdirty_format = number_format($data['lam1_lengthdirty'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки грязная (лам 1), м2", $lam1_lengthdirty_format, $lam1_mpogdirty_format, "м. пог. грязные ламинации 1"));
        }
        
        if($laminations_number > 1) {
            $lam2_lengthdirty_format = number_format($data['lam2_lengthdirty'], 2, ",", " ");
            array_push($file_data, array("Длина плёнки грязная (лам 2), м2", $lam2_lengthdirty_format, $lam2_mpogdirty_format, "м. пог. грязные даминации 2"));
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