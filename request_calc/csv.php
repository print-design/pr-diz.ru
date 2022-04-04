<?php
include '../include/topscripts.php';
include './calculation.php';

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

$id = filter_input(INPUT_GET, 'id');

if($id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
    // ПОЛУЧЕНИЕ НОРМ
    $tuning_data = array();
    $sql = "select machine_id, time, length, waste_percent from norm_tuning where id in (select max(id) from norm_tuning group by machine_id)";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $tuning_data[$row['machine_id']] = array("time" => $row['time'], "length" => $row['length'], "waste_percent" => $row['waste_percent']);
    }
    
    $laminator_tuning_data = null;
    $sql = "select time, length, waste_percent from norm_laminator_tuning order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminator_tuning_data = array("time" => $row['time'], "length" => $row['length'], "waste_percent" => $row['waste_percent']);
    }
    
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
    
    // Машина
    $machine_id = null;
            
    // Ширина ручья
    $stream_width = null;
            
    // Количество ручьёв
    $streams_number = null;
            
    // Рапорт
    $raport = null;
    
    // Красочность
    $ink_number = null;
    
    // Ширина материала
    $width = null;
    $lam1_width = null;
    $lam2_width = null;
    
    // М2 чистые
    $m2pure = null;
    
    // М пог. чистые
    $mpogpure = null;
    
    // Метраж отходов, м
    $waste_length = null;
    $lam1_waste_length = null;
    $lam2_waste_length = null;
    
    // Красочность
    $ink_number = null;
    
    // М пог. грязные
    $mpogdirty = null;
    $lam1_mpogdirty = null;
    $lam2_mpogdirty = null;
    
    // М2 грязные
    $m2dirty = null;
    $lam1_m2dirty = null;
    $lam2_m2dirty = null;
    
    $sql = "select rc.date, rc.name, rc.quantity, rc.unit, "
            . "f.name film, fv.thickness thickness, fv.weight density, rc.ski, rc.width_ski, "
            . "lam1_f.name lam1_film, lam1_fv.thickness lam1_thickness, lam1_fv.weight lam1_density, rc.lamination1_ski, rc.lamination1_width_ski, "
            . "lam2_f.name lam2_film, lam2_fv.thickness lam2_thickness, lam2_fv.weight lam2_density, rc.lamination2_ski, rc.lamination2_width_ski, "
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
        $density_format = empty($density) ? "0" : number_format($density, 2, ",", " ");
        $ski = $row['ski']; // Основная пленка, лыжи
        $width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        $lam1_film = $row['lam1_film']; // Ламинация 1, марка
        $lam1_thickness = $row['lam1_thickness']; // Ламинация 1, толщина, мкм
        $lam1_density = $row['lam1_density']; // Ламинация 1, плотность, г/м2
        $lam1_density_format = empty($lam1_density) ? "0" : number_format($lam1_density, 2, ",", " ");
        $lam1_ski = $row['lamination1_ski']; // Ламинация 1, лыжи
        $lam1_width_ski = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
        
        $lam2_film = $row['lam2_film']; // Ламинация 2, марка
        $lam2_thickness = $row['lam2_thickness']; // Ламинация 2, толщина, мкм
        $lam2_density = $row['lam2_density']; // Ламинация 2, плотность, г/м2
        $lam2_density_format = empty($lam2_density) ? "0" : number_format($lam2_density, 2, ",", " ");
        $lam2_ski = $row['lamination2_ski']; // Ламинация 2, лыжи
        $lam2_width_ski = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
        
        $machine_id = $row['machine_id'];
        $stream_width = $row['stream_width']; // Ширина ручья, мм
        $streams_number = $row['streams_number']; // Количество ручьёв
        $raport = $row['raport']; // Рапорт
        $raport_format = number_format($raport, 3, ",", "");
        $ink_number = $row['ink_number']; // Красочность
        
        
    // Данные CSV-файла
    $data = Calculate($tuning_data, 
            $laminator_tuning_data,
            
            $quantity, // Масса тиража
        $film, // Основная пленка, марка
        $thickness, // Основная пленка, толщина, мкм
        $density, // Основная пленка, плотность, г/м2
        $density_format,
        $ski, // Основная пленка, лыжи
        $width_ski, // Основная пленка, ширина пленки, мм
        
        $lam1_film, // Ламинация 1, марка
        $lam1_thickness, // Ламинация 1, толщина, мкм
        $lam1_density, // Ламинация 1, плотность, г/м2
        $lam1_density_format,
        $lam1_ski, // Ламинация 1, лыжи
        $lam1_width_ski, // Ламинация 1, ширина пленки, мм
        
        $lam2_film, // Ламинация 2, марка
        $lam2_thickness, // Ламинация 2, толщина, мкм
        $lam2_density, // Ламинация 2, плотность, г/м2
        $lam2_density_format,
        $lam2_ski, // Ламинация 2, лыжи
        $lam2_width_ski,  // Ламинация 2, ширина пленки, мм
        
        $machine_id,
        $stream_width, // Ширина ручья, мм
        $streams_number, // Количество ручьёв
        $raport, // Рапорт
        $raport_format,
        $ink_number // Красочность
        );
    
    // Сохранение в файл
    $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y')." $name.csv";
    
    DownloadSendHeaders($file_name);
    echo Array2Csv($data, $titles);
    die();
}
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать в CSV надо наэати на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>