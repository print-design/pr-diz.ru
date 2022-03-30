<?php
include '../include/topscripts.php';

// Лыжи
const NO_SKI = 0;
const STANDARD_SKI = 1;
const NONSTANDARD_SKI = 2;

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

function GetWidthData($ski, $streams_number, $stream_width, $width_ski) {
    $result = array();
    
    switch($ski) {
        case NO_SKI:
            $result['width'] = $streams_number * $stream_width;
            $result['calculation'] = "$streams_number * $stream_width";
            $result['comment'] = "количество ручьёв * ширина ручья";
            break;
        
        case STANDARD_SKI:
            $result['width'] = $streams_number * $stream_width + 20;
            $result['calculation'] = "$streams_number * $stream_width + 20";
            $result['comment'] = "количество ручьёв * ширина ручья + 20 мм";
            break;
        
        case NONSTANDARD_SKI:
            $result['width'] = $width_ski;
            $result['calculation'] = "";
            $result['comment'] = "вводится вручную";
            break;
    }
    
    return $result;
}

$export_submit = filter_input(INPUT_POST, 'export_submit');
$id = filter_input(INPUT_POST, 'id');

if($export_submit !== null && $id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
    // Данные CSV-файла
    $data = array();
    
    // ПОЛУЧЕНИЕ ИСХОДНЫХ ДАННЫХ
            
    // Масса тиража
    $quantity = null;
            
    // Типы, толщины и удельные веса плёнок, лыжи и ширина плёнки
    $film = null;
    $thickness = null;
    $density = null;
    $ski = null;
    $width_ski = null;
            
    $lam1_film = null;
    $lam1_thickness = null;
    $lam1_density = null;
    $lam1_ski = null;
    $lam1_width_ski = null;
            
    $lam2_film = null;
    $lam2_thickness = null;
    $lam2_density = null;
    $lam2_ski = null;
    $lam2_width_ski = null;
            
    // Ширина ручья
    $stream_width = null;
            
    // Количество ручьёв
    $streams_number = null;
            
    // Рапорт
    $raport = null;
    
    // Ширина материала
    $width = null;
    $lam1_width = null;
    $lam2_width = null;
    
    // М2 чистые
    $m2pure = null;
    
    // М пог. чистые
    $mpogpure = null;
    
    $sql = "select rc.date, rc.name, rc.quantity, rc.unit, "
            . "f.name film, fv.thickness thickness, fv.weight density, rc.ski, rc.width_ski, "
            . "lam1_f.name lam1_film, lam1_fv.thickness lam1_thickness, lam1_fv.weight lam1_density, rc.lamination1_ski, rc.lamination1_width_ski, "
            . "lam2_f.name lam2_film, lam2_fv.thickness lam2_thickness, lam2_fv.weight lam2_density, rc.lamination2_ski, rc.lamination2_width_ski, "
            . "rc.stream_width, rc.streams_number, rc.raport "
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
        
        $stream_width = $row['stream_width']; // Ширина ручья, мм
        $streams_number = $row['streams_number']; // Количество ручьёв
        $raport = $row['raport']; // Рапорт
        
        array_push($data, array("Масса тиража, кг", $quantity, "", ""));
        array_push($data, array("Основная пленка, марка", $film, "", ""));
        array_push($data, array("Основная пленка, толщина, мкм", $thickness, "", ""));
        array_push($data, array("Основная пленка, плотность, г/м2", number_format($density, 2, ",", " "), "", ""));
        array_push($data, array("Основная пленка, лыжи", GetSkiName($ski), "", ""));
        
        if(!empty($lam1_film) && !empty($lam1_thickness) && !empty($lam1_density)) {
            array_push($data, array("Ламинация 1, марка", $lam1_film, "", ""));
            array_push($data, array("Ламинация 1, толщина, мкм", $lam1_thickness, "", ""));
            array_push($data, array("Ламинация 1, плотность, г/м2", number_format($lam1_density, 2, ",", " "), "", ""));
            array_push($data, array("Ламинация 1, лыжи", GetSkiName($lam1_ski), "", ""));
        }
        
        if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
            array_push($data, array("Ламинация 2, марка", $lam2_film, "", ""));
            array_push($data, array("Ламинация 2, толщина, мкм", $lam2_thickness, "", ""));
            array_push($data, array("Ламинация 2, плотность, г/м2", number_format($lam2_density, 2, ",", " "), "", ""));
            array_push($data, array("Ламинация 2, лыжи", GetSkiName($lam2_ski), "", ""));
        }
        
        array_push($data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($data, array("Количество ручьёв", $streams_number, "", ""));
        array_push($data, array("Рапорт", number_format($raport, 3, ",", ""), "", ""));
        
        $width_data = GetWidthData($ski, $streams_number, $stream_width, $width_ski);
        $width = $width_data['width'];
        array_push($data, array("Основная пленка, ширина материала, мм", $width, $width_data['calculation'], $width_data['comment']));
        
        if(!empty($lam1_film) && !empty($lam1_thickness) && !empty($lam1_density)) {
            $lam1_width_data = GetWidthData($lam1_ski, $streams_number, $stream_width, $lam1_width_ski);
            $lam1_width = $lam1_width_data['width'];
            array_push($data, array("Ламинация 1, ширина материала, мм", $lam1_width, $lam1_width_data['calculation'], $lam1_width_data['comment']));
        }
        
        if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
            $lam2_width_data = GetWidthData($lam2_ski, $streams_number, $stream_width, $lam2_width_ski);
            $lam2_width = $lam2_width_data['width'];
            array_push($data, array("Ламинация 2, ширина материала, мм", $lam2_width, $lam2_width_data['calculation'], $lam2_width_data['comment']));
        }
        
        $m2pure = $quantity * 1000 / ($density + $lam1_density ?? 0 + $lam2_density ?? 0);
        array_push($data, array("М2 чистые, м2", number_format($m2pure, 2, ",", " "), "$quantity * 1000 / ($density + ".($lam1_density ?? 0)." + ".($lam2_density ?? 0).")", "масса тиража * 1000 / (осн. пл. уд. вес + лам. 1 уд. вес + лам. 2 уд. вес)"));
        
        $mpogpure = $m2pure / ($streams_number * $stream_width);
        array_push($data, array("М пог. чистые, м", number_format($mpogpure, 2, ",", " "), "$m2pure / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)"));
    }
    
    $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y')." $name.csv";
    
    DownloadSendHeaders($file_name);
    echo Array2Csv($data, $titles);
    die();
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать в CSV надо наэати на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>