<?php

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

function Calculate($tuning_data, 
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
        ) {
    $result = array();
    
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
    
    $ski_name = GetSkiName($ski);
    $lam1_ski_name = GetSkiName($lam1_ski);
    $lam2_ski_name = GetSkiName($lam2_ski);
    $result['ski_name'] = $ski_name;
    $result['lam1_ski_name'] = $lam1_ski_name;
    $result['lam2_ski_name'] = $lam2_ski_name;
    
    $laminations_number = 0; // Количество ламинаций
    
    if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
        $laminations_number = 2;
    }
    elseif(!empty ($lam1_film) && !empty ($lam1_thickness) && !empty ($lam1_density)) {
        $laminations_number = 1;
    }
    
    $result['laminations_number'] = $laminations_number;
        
    $width_data = GetWidthData($ski, $streams_number, $stream_width, $width_ski);
    $result['width'] = $width_data['width'];
    $result['width_calculation'] = $width_data['calculation'];
    $result['width_comment'] = $width_data['comment'];
        
    if($laminations_number > 0) {
        $lam1_width_data = GetWidthData($lam1_ski, $streams_number, $stream_width, $lam1_width_ski);
        $result['lam1_width'] = $lam1_width_data['width'];
        $result['lam1_width_calculation'] = $lam1_width_data['calculation'];
        $result['lam1_width_comment'] = $lam1_width_data['comment'];
    }
        
    if($laminations_number > 1) {
        $lam2_width_data = GetWidthData($lam2_ski, $streams_number, $stream_width, $lam2_width_ski);
        $result['lam2_width'] = $lam2_width_data['width'];
        $result['lam2_width_calculation'] = $lam2_width_data['calculation'];
        $result['lam2_width_comment'] = $lam2_width_data['comment'];
    }
        
    $m2pure = $quantity * 1000 / ($density + $lam1_density ?? 0 + $lam2_density ?? 0);
    $result['m2pure'] = $m2pure;
        
    $mpogpure = $m2pure / ($streams_number * $stream_width);
    $result['mpogpure'] = $mpogpure;
        
    if(!empty($machine_id)) {
        $waste_length = $tuning_data[$machine_id]['waste_percent'] * $mpogpure / 100;
        $result['waste_length'] = $waste_length;
    }
        
    if($laminations_number > 0) {
        $lam1_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
        $result['lam1_waste_length'] = $lam1_waste_length;
    }
        
    if($laminations_number > 1) {
        $lam2_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
        $result['lam2_waste_length'] = $lam2_waste_length;
    }
        
    if(!empty($machine_id)) {
        $mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $ink_number * $tuning_data[$machine_id]['length'] + $laminations_number * $laminator_tuning_data['length'];
        $result['mpogdirty'] = $mpogdirty;
    }
        
    if($laminations_number > 0) {
        $lam1_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'] * 2;
        $result['lam1_mpogdirty'] = $lam1_mpogdirty;
    }
        
    if($laminations_number > 1) {
        $lam2_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'];
        $result['lam2_mpogdirty'] = $lam1_mpogdirty;
    }
        
    if(!empty($machine_id)) {
        $m2dirty = $mpogdirty * $width_data['width'] / 1000;
        $result['m2dirty'] = $m2dirty;
    }
        
    if($laminations_number > 0) {
        $lam1_m2dirty = $lam1_mpogdirty * $lam1_width_data['width'] / 1000;
        $result['lam1_m2dirty'] = $lam1_m2dirty;
    }
        
    if($laminations_number > 1) {
        $lam2_m2dirty = $lam2_mpogdirty * $lam2_width_data['width'] / 1000;
        $result['lam2_m2dirty'] = $lam2_m2dirty;
    }
        
    return $result;
}
?>