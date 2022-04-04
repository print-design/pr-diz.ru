<?php
function Calculate($tuning_data, 
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
        ) {
    $result = array();
    
    $laminations_number = 0; // Количество ламинаций
        
        if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
            $laminations_number = 2;
        }
        elseif(!empty ($lam1_film) && !empty ($lam1_thickness) && !empty ($lam1_density)) {
            $laminations_number = 1;
        }
        
        array_push($result, array("Масса тиража, кг", $quantity, "", ""));
        array_push($result, array("Основная пленка, марка", $film, "", ""));
        array_push($result, array("Основная пленка, толщина, мкм", $thickness, "", ""));
        array_push($result, array("Основная пленка, плотность, г/м2", $density_format, "", ""));
        array_push($result, array("Основная пленка, лыжи", GetSkiName($ski), "", ""));
        
        if($laminations_number > 0) {
            array_push($result, array("Ламинация 1, марка", $lam1_film, "", ""));
            array_push($result, array("Ламинация 1, толщина, мкм", $lam1_thickness, "", ""));
            array_push($result, array("Ламинация 1, плотность, г/м2", $lam1_density_format, "", ""));
            array_push($result, array("Ламинация 1, лыжи", GetSkiName($lam1_ski), "", ""));
        }
        
        if($laminations_number > 1) {
            array_push($result, array("Ламинация 2, марка", $lam2_film, "", ""));
            array_push($result, array("Ламинация 2, толщина, мкм", $lam2_thickness, "", ""));
            array_push($result, array("Ламинация 2, плотность, г/м2", $lam2_density_format, "", ""));
            array_push($result, array("Ламинация 2, лыжи", GetSkiName($lam2_ski), "", ""));
        }
        
        array_push($result, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($result, array("Количество ручьёв", $streams_number, "", ""));
        array_push($result, array("Рапорт", $raport_format, "", ""));
        
        $width_data = GetWidthData($ski, $streams_number, $stream_width, $width_ski);
        $width = $width_data['width'];
        array_push($result, array("Основная пленка, ширина материала, мм", $width, $width_data['calculation'], $width_data['comment']));
        
        if(!empty($lam1_film) && !empty($lam1_thickness) && !empty($lam1_density)) {
            $lam1_width_data = GetWidthData($lam1_ski, $streams_number, $stream_width, $lam1_width_ski);
            $lam1_width = $lam1_width_data['width'];
            array_push($result, array("Ламинация 1, ширина материала, мм", $lam1_width, $lam1_width_data['calculation'], $lam1_width_data['comment']));
        }
        
        if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
            $lam2_width_data = GetWidthData($lam2_ski, $streams_number, $stream_width, $lam2_width_ski);
            $lam2_width = $lam2_width_data['width'];
            array_push($result, array("Ламинация 2, ширина материала, мм", $lam2_width, $lam2_width_data['calculation'], $lam2_width_data['comment']));
        }
        
        $m2pure = $quantity * 1000 / ($density + $lam1_density ?? 0 + $lam2_density ?? 0);
        $m2pure_format = number_format($m2pure, 2, ",", " ");
        array_push($result, array("М2 чистые, м2", $m2pure_format, "$quantity * 1000 / ($density_format + $lam1_density_format + $lam2_density_format)", "масса тиража * 1000 / (осн. пл. уд. вес + лам. 1 уд. вес + лам. 2 уд. вес)"));
        
        $mpogpure = $m2pure / ($streams_number * $stream_width);
        $mpogpure_format = number_format($mpogpure, 2, ",", " ");
        array_push($result, array("М пог. чистые, м", $mpogpure_format, "$m2pure_format / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)"));
        
        if(!empty($machine_id)) {
            $waste_length = $tuning_data[$machine_id]['waste_percent'] * $mpogpure / 100;
            $waste_length_format = number_format($waste_length, 2, ",", " ");
            array_push($result, array("Основная пленка, метраж отходов, м", $waste_length_format, $tuning_data[$machine_id]['waste_percent']." * $mpogpure_format / 100", "процент отходов печати * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 0) {
            $lam1_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
            $lam1_waste_length_format = number_format($lam1_waste_length, 2, ",", " ");
            array_push($result, array("Ламинация 1, метраж отходов, м", $lam1_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if($laminations_number > 1) {
            $lam2_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
            $lam2_waste_length_format = number_format($lam1_waste_length, 2, ",", " ");
            array_push($result, array("Ламинация 2, метраж отходов, м", $lam2_waste_length_format, $laminator_tuning_data['waste_percent']." * $mpogpure_format / 100", "процент отходов ламинации * м. пог. чистые / 100"));
        }
        
        if(!empty($ink_number)) {
            array_push($result, array("Красочность", $ink_number, "", ""));
        }
        
        if(!empty($machine_id)) {
            $mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $ink_number * $tuning_data[$machine_id]['length'] + $laminations_number * $laminator_tuning_data['length'];
            $mpogdirty_format = number_format($mpogdirty, 2, ",", " ");
            array_push($result, array("Основная пленка, м. пог. грязные, м", $mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + $ink_number * ".$tuning_data[$machine_id]['length']." + $laminations_number * ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + красочность * метраж приладки 1 краски + кол-во ламинаций * метраж приладки ламинации"));
        }
        
        if($laminations_number > 0) {
            $lam1_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'] * 2;
            $lam1_mpogdirty_format = number_format($lam1_mpogdirty, 2, ",", " ");
            array_push($result, array("Ламинация 1, м. пог. грязные, м", $lam1_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length']." * 2", "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации * 2"));
        }
        
        if($laminations_number > 1) {
            $lam2_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'];
            $lam2_mpogdirty_format = number_format($lam2_mpogdirty, 2, ",", " ");
            array_push($result, array("Ламинация 2, м. пог. грязные, м", $lam2_mpogdirty_format, "$mpogpure_format * ".$tuning_data[$machine_id]['waste_percent']." + ".$laminator_tuning_data['length'], "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации"));
        }
        
        if(!empty($machine_id)) {
            $m2dirty = $mpogdirty * $width / 1000;
            $m2dirty_format = number_format($m2dirty, 2, ",", " ");
            array_push($result, array("Основная пленка, м2 грязные, м2", $m2dirty_format, "$mpogdirty_format * $width / 1000", "м. пог. грязные * ширина материала основной пленки / 1000"));
        }
        
        if($laminations_number > 0) {
            $lam1_m2dirty = $lam1_mpogdirty * $lam1_width / 1000;
            $lam1_m2dirty_format = number_format($lam1_m2dirty, 2, ",", " ");
            array_push($result, array("Ламинация 1, м2 грязные, м2", $lam1_m2dirty_format, "$lam1_mpogdirty_format * $lam1_width / 1000", "м. пог. грязные * ширина материала пленки ламинации 1 / 1000"));
        }
        
        if($laminations_number > 1) {
            $lam2_m2dirty = $lam2_mpogdirty * $lam2_width / 1000;
            $lam2_m2dirty_format = number_format($lam2_m2dirty, 2, ",", " ");
            array_push($result, array("Ламинация 2, м2 грязные, м2", $lam2_m2dirty_format, "$lam2_mpogdirty_format * $lam2_width / 1000", "м. пог. грязные * ширина материала пленки ламинации 2 / 1000"));
        }
        
        return $result;
    }
?>