<?php

// Лыжи
const NO_SKI = 0;
const STANDARD_SKI = 1;
const NONSTANDARD_SKI = 2;

function GetWidth($ski, $streams_number, $stream_width, $width_ski) {
    $result = 0;
    
    switch($ski) {
        case NO_SKI:
            $result = $streams_number * $stream_width;
            break;
        
        case STANDARD_SKI:
            $result = $streams_number * $stream_width + 20;
            break;
        
        case NONSTANDARD_SKI:
            $result = $width_ski;
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
    
    $laminations_number = 0; // Количество ламинаций
    
    if(!empty($lam2_film) && !empty($lam2_thickness) && !empty($lam2_density)) {
        $laminations_number = 2;
    }
    elseif(!empty ($lam1_film) && !empty ($lam1_thickness) && !empty ($lam1_density)) {
        $laminations_number = 1;
    }
    
    $result['laminations_number'] = $laminations_number;

    $width = GetWidth($ski, $streams_number, $stream_width, $width_ski);
    $result['width'] = $width;
        
    if($laminations_number > 0) {
        $lam1_width = GetWidth($lam1_ski, $streams_number, $stream_width, $lam1_width_ski);
        $result['lam1_width'] = $lam1_width;
    }
        
    if($laminations_number > 1) {
        $lam2_width = GetWidth($lam2_ski, $streams_number, $stream_width, $lam2_width_ski);
        $result['lam2_width'] = $lam2_width;
    }

    // Площадь чистая
    $m2pure = $quantity * 1000 / ($density + $lam1_density ?? 0 + $lam2_density ?? 0);
    $result['m2pure'] = $m2pure;
        
    // Метры погонные чистые
    $mpogpure = $m2pure / ($streams_number * $stream_width);
    $result['mpogpure'] = $mpogpure;
        
    // Метраж отходов, исходя из склее и инерции
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
        
    // Метры погонные грязные
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
        $result['lam2_mpogdirty'] = $lam2_mpogdirty;
    }
        
    // Площадь грязная
    if(!empty($machine_id)) {
        $m2dirty = $mpogdirty * $width / 1000;
        $result['m2dirty'] = $m2dirty;
    }
        
    if($laminations_number > 0) {
        $lam1_m2dirty = $lam1_mpogdirty * $lam1_width / 1000;
        $result['lam1_m2dirty'] = $lam1_m2dirty;
    }
        
    if($laminations_number > 1) {
        $lam2_m2dirty = $lam2_mpogdirty * $lam2_width / 1000;
        $result['lam2_m2dirty'] = $lam2_m2dirty;
    }
    
    //****************************************
    // Массы и длины плёнок
    //****************************************
    
    // Масса плёнки чистая (без приладки), кг
    $mpure = $mpogpure * $width * $density / 1000;
    $result['mpure'] = $mpure;
    
    if($laminations_number > 0) {
        $lam1_mpure = $mpogpure * $lam1_width * $lam1_density / 1000;
        $result['lam1_mpure'] = $lam1_mpure;
    }
    
    if($laminations_number > 1) {
        $lam2_mpure = $mpogpure * $lam1_width * $lam2_density / 1000;
        $result['lam2_mpure'] = $lam2_mpure;
    }
    
    // Длина пленки чистая, м
    $lengthpure = $mpogpure;
    $result['lengthpure'] = $lengthpure;
    
    if($laminations_number > 0) {
        $lam1_lengthpure = $mpogpure;
        $result['lam1_lengthpure'] = $lam1_lengthpure;
    }
    
    if($laminations_number > 1) {
        $lam2_lengthpure = $mpogpure;
        $result['lam2_lengthpure'] = $mpogpure;
    }
    
    // Масса плёнки грязная (с приладкой), кг
    $mdirty = $m2dirty * $density / 1000;
    $result['mdirty'] = $mdirty;
    
    if($laminations_number > 0) {
        $lam1_mdirty  = $lam1_m2dirty * $lam1_density / 1000;
        $result['lam1_mdirty'] = $lam1_mdirty;
    }
    
    if($laminations_number > 1) {
        $lam2_mdirty = $lam2_m2dirty * $lam2_m2dirty / 1000;
        $result['lam2_mdirty'] = $lam2_m2dirty;
    }
    
    // Длина плёнки грязная, м
    $lengthdirty = $mpogdirty;
    $result['lengthdirty'] = $lengthdirty;
    
    if($laminations_number > 0) {
        $lam1_lengthdirty = $lam1_mpogdirty;
        $result['lam1_lengthdirty'] = $lam1_lengthdirty;
    }
    
    if($laminations_number > 1) {
        $lam2_lengthdirty = $lam2_mpogdirty;
        $result['lam2_lengthdirty'] = $lam2_lengthdirty;
    }
    
    //****************************************
    // Себестоимость плёнок
    //****************************************
    
    // Себестоимость грязная (с приладки), руб
            
    return $result;
}
?>