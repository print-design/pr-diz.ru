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
        $lamination1_lamination1_currency, // Ламинация 1, валюта
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
        ) {
    $result = array();
    
    $laminations_number = 0; // Количество ламинаций
    
    if(!empty($lamination2_film) && !empty($lamination2_thickness) && !empty($lamination2_density)) {
        $laminations_number = 2;
    }
    elseif(!empty ($lamination1_film) && !empty ($lamination1_thickness) && !empty ($lamination1_density)) {
        $laminations_number = 1;
    }
    
    $result['laminations_number'] = $laminations_number;

    $width = GetWidth($ski, $streams_number, $stream_width, $width_ski);
    $result['width'] = $width;
        
    if($laminations_number > 0) {
        $lamination1_width = GetWidth($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski);
        $result['lamination1_width'] = $lamination1_width;
    }
        
    if($laminations_number > 1) {
        $lamination2_width = GetWidth($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski);
        $result['lamination2_width'] = $lamination2_width;
    }

    // Площадь чистая
    $m2pure = $quantity * 1000 / ($density + $lamination1_density ?? 0 + $lamination2_density ?? 0);
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
        $lamination1_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
        $result['lamination1_waste_length'] = $lamination1_waste_length;
    }
        
    if($laminations_number > 1) {
        $lamination2_waste_length = $laminator_tuning_data['waste_percent'] * $mpogpure / 100;
        $result['lamination2_waste_length'] = $lamination2_waste_length;
    }
        
    // Метры погонные грязные
    if(!empty($machine_id)) {
        $mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $ink_number * $tuning_data[$machine_id]['length'] + $laminations_number * $laminator_tuning_data['length'];
        $result['mpogdirty'] = $mpogdirty;
    }
        
    if($laminations_number > 0) {
        $lamination1_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'] * 2;
        $result['lamination1_mpogdirty'] = $lamination1_mpogdirty;
    }
        
    if($laminations_number > 1) {
        $lamination2_mpogdirty = $mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'];
        $result['lamination2_mpogdirty'] = $lamination2_mpogdirty;
    }
        
    // Площадь грязная
    if(!empty($machine_id)) {
        $m2dirty = $mpogdirty * $width / 1000;
        $result['m2dirty'] = $m2dirty;
    }
        
    if($laminations_number > 0) {
        $lamination1_m2dirty = $lamination1_mpogdirty * $lamination1_width / 1000;
        $result['lamination1_m2dirty'] = $lamination1_m2dirty;
    }
        
    if($laminations_number > 1) {
        $lamination2_m2dirty = $lamination2_mpogdirty * $lamination2_width / 1000;
        $result['lamination2_m2dirty'] = $lamination2_m2dirty;
    }
    
    //****************************************
    // Массы и длины плёнок
    //****************************************
    
    // Масса плёнки чистая (без приладки), кг
    $mpure = $mpogpure * $width * $density / 1000;
    $result['mpure'] = $mpure;
    
    if($laminations_number > 0) {
        $lamination1_mpure = $mpogpure * $lamination1_width * $lamination1_density / 1000;
        $result['lamination1_mpure'] = $lamination1_mpure;
    }
    
    if($laminations_number > 1) {
        $lamination2_mpure = $mpogpure * $lamination1_width * $lamination2_density / 1000;
        $result['lamination2_mpure'] = $lamination2_mpure;
    }
    
    // Длина пленки чистая, м
    $lengthpure = $mpogpure;
    $result['lengthpure'] = $lengthpure;
    
    if($laminations_number > 0) {
        $lamination1_lengthpure = $mpogpure;
        $result['lamination1_lengthpure'] = $lamination1_lengthpure;
    }
    
    if($laminations_number > 1) {
        $lamination2_lengthpure = $mpogpure;
        $result['lamination2_lengthpure'] = $mpogpure;
    }
    
    // Масса плёнки грязная (с приладкой), кг
    $mdirty = $m2dirty * $density / 1000;
    $result['mdirty'] = $mdirty;
    
    if($laminations_number > 0) {
        $lamination1_mdirty  = $lamination1_m2dirty * $lamination1_density / 1000;
        $result['lamination1_mdirty'] = $lamination1_mdirty;
    }
    
    if($laminations_number > 1) {
        $lamination2_mdirty = $lamination2_m2dirty * $lamination2_m2dirty / 1000;
        $result['lamination2_mdirty'] = $lamination2_m2dirty;
    }
    
    // Длина плёнки грязная, м
    $lengthdirty = $mpogdirty;
    $result['lengthdirty'] = $lengthdirty;
    
    if($laminations_number > 0) {
        $lamination1_lengthdirty = $lamination1_mpogdirty;
        $result['lamination1_lengthdirty'] = $lamination1_lengthdirty;
    }
    
    if($laminations_number > 1) {
        $lamination2_lengthdirty = $lamination2_mpogdirty;
        $result['lamination2_lengthdirty'] = $lamination2_lengthdirty;
    }
    
    //****************************************
    // Себестоимость плёнок
    //****************************************
    
    // Себестоимость грязная (с приладки), руб
            
    return $result;
}
?>