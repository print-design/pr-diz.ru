<?php
include '../include/topscripts.php';

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
            
    // Типы, толщины и удельные веса плёнок
    $film = null;
    $thickness = null;
    $density = null;
            
    $lam1_film = null;
    $lam1_thickness = null;
    $lam1_density = null;
            
    $lam2_film = null;
    $lam2_thickness = null;
    $lam2_density = null;
            
    // Ширина ручья
    $stream_width = null;
            
    // Количество ручьёв
    $streams_count = null;
            
    // Рапорт
    $raport = null;
            
    // Лыжи
    $ski = null;
    $lam1_ski = null;
    $lam2_ski = null;
    
    $sql = "select rc.date, rc.name, rc.quantity, rc.unit, f.name film, fv.thickness thickness, fv.weight density, "
            . "lam1_f.name lam1_film, lam1_fv.thickness lam1_thickness, lam1_fv.weight lam1_density, "
            . "lam2_f.name lam2_film, lam2_fv.thickness lam2_thickness, lam2_fv.weight lam2_density "
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
        
        $lam1_film = $row['lam1_film']; // Ламинация 1, марка
        $lam1_thickness = $row['lam1_thickness']; // Ламинация 1, толщина, мкм
        $lam1_density = $row['lam1_density']; // Ламинация 1, плотность, г/м2
        
        $lam2_film = $row['lam2_film']; // Ламинация 2, марка
        $lam2_thickness = $row['lam2_thickness']; // Ламинация 2, толщина, мкм
        $lam2_density = $row['lam2_density']; // Ламинация 2, плотность, г/м2
        
        array_push($data, array("Масса тиража, кг", $quantity, "", ""));
        array_push($data, array("Основная пленка, марка", $film, "", ""));
        array_push($data, array("Основная пленка, толщина, мкм", $thickness, "", ""));
        array_push($data, array("Основная пленка, плотность, г/м2", number_format($density, 2, ",", " "), "", ""));
        
        if(!empty($lam1_film) && !empty($lam1_thickness) && !empty($lam1_density)) {
            array_push($data, array("Ламинация 1, марка", $lam1_film, "", ""));
            array_push($data, array("Ламинация 1, толщина, мкм", $lam1_thickness, "", ""));
            array_push($data, array("Ламинация 1, плотность, г/м2", $lam1_density, "", ""));
        }
        
        if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
            array_push($data, array("Ламинация 2, марка", $lam2_thickness, "", ""));
            array_push($data, array("Ламинация 2, толщина, мкм", $lam2_thickness, "", ""));
            array_push($data, array("Ламинация 2, плотность, г/м2", $lam1_density, "", ""));
        }
    }
            
    // Ширина материала
    
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