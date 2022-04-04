<?php
include '../include/topscripts.php';
include './calculation.php';

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
        
        array_push($file_data, array("Масса тиража, кг", $quantity, "", ""));
        array_push($file_data, array("Основная пленка, марка", $film, "", ""));
        array_push($file_data, array("Основная пленка, толщина, мкм", $thickness, "", ""));
        $density_format = empty($density) ? "0" : number_format($density, 2, ",", " ");
        array_push($file_data, array("Основная пленка, плотность, г/м2", $density_format, "", ""));
        array_push($file_data, array("Основная пленка, лыжи", $data['ski_name'], "", ""));
        
        if($data['laminations_number'] > 0) {
            array_push($file_data, array("Ламинация 1, марка", $lam1_film, "", ""));
            array_push($file_data, array("Ламинация 1, толщина, мкм", $lam1_thickness, "", ""));
            $lam1_density_format = empty($lam1_density) ? "0" : number_format($lam1_density, 2, ",", " ");
            array_push($file_data, array("Ламинация 1, плотность, г/м2", $lam1_density_format, "", ""));
            array_push($file_data, array("Ламинация 1, лыжи", $data['lam1_ski_name'], "", ""));
        }
        
        if($data['laminations_number'] > 1) {
            array_push($file_data, array("Ламинация 2, марка", $lam2_film, "", ""));
            array_push($file_data, array("Ламинация 2, толщина, мкм", $lam2_thickness, "", ""));
            $lam2_density_format = empty($lam2_density) ? "0" : number_format($lam2_density, 2, ",", " ");
            array_push($file_data, array("Ламинация 2, плотность, г/м2", $lam2_density_format, "", ""));
            array_push($file_data, array("Ламинация 2, лыжи", $data['lam2_ski_name'], "", ""));
        }
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        $raport_format = number_format($raport, 3, ",", "");
        array_push($file_data, array("Рапорт", $raport_format, "", ""));
        array_push($file_data, array("Основная пленка, ширина материала, мм", $data['width'], $data['width_calculation'], $data['width_comment']));
        
        if($data['laminations_number'] > 0) {
            array_push($file_data, array("Ламинация 1, ширина материала, мм", $data['lam1_width'], $data['lam1_width_calculation'], $data['lam1_width_comment']));
        }
        
        if($data['laminations_number'] > 1) {
            array_push($file_data, array("Ламинация 2, ширина материала, мм", $data['lam2_width'], $data['lam2_width_calculation'], $data['lam2_width_comment']));
        }
        
        $m2pure_format = number_format($data['m2pure'], 2, ",", " ");
        array_push($file_data, array("М2 чистые, м2", $m2pure_format, "$quantity * 1000 / ($density_format + $lam1_density_format + $lam2_density_format)", "масса тиража * 1000 / (осн. пл. уд. вес + лам. 1 уд. вес + лам. 2 уд. вес)"));
        
        $mpogpure_format = number_format($data['mpogpure'], 2, ",", " ");
        array_push($file_data, array("М пог. чистые, м", $mpogpure_format, "$m2pure_format / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)"));
        
        if(!empty($machine_id)) {
            $waste_length_format = number_format($data['waste_length'], 2, ",", " ");
            array_push($file_data, array("Основная пленка, метраж отходов, м", $waste_length_format, $tuning_data[$machine_id]['waste_percent']." * $mpogpure_format / 100", "процент отходов печати * м. пог. чистые / 100"));
        }
        
        if($data['laminations_number'] > 0) {
            $lam1_waste_length_format = number_format($data['lam1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Ламинация 1, метраж отходов, м", $lam1_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if($data['laminations_number'] > 1) {
            $lam2_waste_length_format = number_format($data['lam1_waste_length'], 2, ",", " ");
            array_push($file_data, array("Ламинация 2, метраж отходов, м", $lam2_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if(!empty($ink_number)) {
            array_push($file_data, array("Красочность", $ink_number, "", ""));
        }
        
        if(!empty($machine_id)) {
            $mpogdirty_format = number_format($data['mpogdirty'], 2, ",", " ");
            array_push($file_data, array("Основная пленка, м. пог. грязные, м", $mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + $ink_number * ".$tuning_data[$machine_id]['length']." + ".$data['laminations_number']." * ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + красочность * метраж приладки 1 краски + кол-во ламинаций * метраж приладки ламинации"));
        }
        
        if($data['laminations_number'] > 0) {
            $lam1_mpogdirty_format = number_format($data['lam1_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("Ламинация 1, м. пог. грязные, м", $lam1_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length']." * 2", "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации * 2"));
        }
        
        if($data['laminations_number'] > 1) {
            $lam2_mpogdirty_format = number_format($data['lam2_mpogdirty'], 2, ",", " ");
            array_push($file_data, array("Ламинация 2, м. пог. грязные, м", $lam2_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации"));
        }
        
        if(!empty($machine_id)) {
            $m2dirty_format = number_format($data['m2dirty'], 2, ",", " ");
            array_push($file_data, array("Основная пленка, м2 грязные, м2", $m2dirty_format, "$mpogdirty_format * ".$data['width']." / 1000", "м. пог. грязные * ширина материала основной пленки / 1000"));
        }
        
        if($data['laminations_number'] > 0) {
            $lam1_m2dirty_format = number_format($data['lam1_m2dirty'], 2, ",", " ");
            array_push($file_data, array("Ламинация 1, м2 грязные, м2", $lam1_m2dirty_format, "$lam1_mpogdirty_format * ".$data['lam1_width']." / 1000", "м. пог. грязные * ширина материала пленки ламинации 1 / 1000"));
        }
        
        if($data['laminations_number'] > 1) {
            $lam2_m2dirty_format = number_format($data['lam2_m2dirty'], 2, ",", " ");
            array_push($file_data, array("Ламинация 2, м2 грязные, м2", $lam2_m2dirty_format, "$lam2_mpogdirty_format * ".$data['lam2_width']." / 1000", "м. пог. грязные * ширина материала пленки ламинации 2 / 1000"));
        }
        
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