<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');

if($id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
    // Расчёт
    $calculation = CalculationBase::Create($id);
    
    // Данные CSV-файла
    $file_data = array();
        
    array_push($file_data, array("Курс доллара, руб", CalculationBase::Display($calculation->usd, 5), "", ""));
    array_push($file_data, array("Курс евро, руб", CalculationBase::Display($calculation->euro, 5), "", ""));
    if($calculation->work_type_id == WORK_TYPE_PRINT) array_push ($file_data, array("Тип работы", "Плёнка с печатью", "", ""));
    elseif($calculation->work_type_id == WORK_TYPE_NOPRINT) array_push ($file_data, array("Тип работы", "Плёнка без печати", "", ""));
        
    if(!empty($calculation->machine_id)) {
        array_push($file_data, array("Машина", PRINTER_NAMES[$calculation->machine_id], "", ""));
    }
        
    if(!empty($calculation->laminator_id)) {
        array_push($file_data, array("Ламинатор", LAMINATOR_NAMES[$calculation->laminator_id], "", ""));
    }
        
    array_push($file_data, array("Размер тиража", $calculation->quantity.' '. $calculation->GetUnitName($calculation->unit), "", ""));
    array_push($file_data, array("Марка 1", $calculation->film_1, "", ""));
    array_push($file_data, array("Толщина 1, мкм", $calculation->thickness_1, "", ""));
    array_push($file_data, array("Плотность 1, г/м2", CalculationBase::Display($calculation->density_1, 5), "", ""));
    array_push($file_data, array("Лыжи 1", $calculation->GetSkiName($calculation->ski_1), "", ""));
    if($calculation->ski_1 == Calculation::NONSTANDARD_SKI) array_push ($file_data, array("Ширина плёнки 1, мм", CalculationBase::Display($calculation->width_ski_1, 5), "", ""));
    if($calculation->customers_material_1 == true) array_push ($file_data, array("Материал заказчика 1", "", "", ""));
    else array_push ($file_data, array("Цена 1", CalculationBase::Display($calculation->price_1, 5)." ". $calculation->GetCurrencyName($calculation->currency_1).($calculation->currency_1 == Calculation::USD ? " (". CalculationBase::Display($calculation->price_1 * $calculation->usd, 5)." руб)" : "").($calculation->currency_1 == Calculation::EURO ? " (". CalculationBase::Display($calculation->price_1 * $calculation->euro, 5)." руб)" : ""), "", ""));
        
    if($calculation->laminations_number > 0) {
        array_push($file_data, array("Марка 2", $calculation->film_2, "", ""));
        array_push($file_data, array("Толщина 2, мкм", $calculation->thickness_2, "", ""));
        array_push($file_data, array("Плотность 2, г/м2", CalculationBase::Display($calculation->density_2, 5), "", ""));
        array_push($file_data, array("Лыжи 2", $calculation->GetSkiName($calculation->ski_2), "", ""));
        if($calculation->ski_2 == Calculation::NONSTANDARD_SKI) array_push($file_data, array("Ширина пленки 2, мм", CalculationBase::Display($calculation->width_ski_2, 5), "", ""));
        if($calculation->customers_material_2 == true) array_push ($file_data, array("Материал заказчика 2", "", "", ""));
        else array_push ($file_data, array("Цена 2", CalculationBase::Display($calculation->price_2, 5)." ". $calculation->GetCurrencyName($calculation->currency_2).($calculation->currency_2 == Calculation::USD ? " (".CalculationBase::Display($calculation->price_2 * $calculation->usd, 5)." руб)" : "").($calculation->currency_2 == Calculation::EURO ? " (".CalculationBase::Display($calculation->price_2 * $calculation->euro, 5)." руб)" : ""), "", ""));
    }
        
    if($calculation->laminations_number > 1) {
        array_push($file_data, array("Марка 3", $calculation->film_3, "", ""));
        array_push($file_data, array("Толщина 3, мкм", $calculation->thickness_3, "", ""));
        array_push($file_data, array("Плотность 3, г/м2", CalculationBase::Display($calculation->density_3, 5), "", ""));
        array_push($file_data, array("Лыжи 3", $calculation->GetSkiName($calculation->ski_3), "", ""));
        if($calculation->ski_3 == Calculation::NONSTANDARD_SKI) array_push ($file_data, array("Ширина плёнки 3, мм", CalculationBase::Display($calculation->width_ski_3, 5), "", ""));
        if($calculation->customers_material_3 == true) array_push ($file_data, array("Материал заказчика (лам 2)", "", "", ""));
        else array_push ($file_data, array("Цена 3", CalculationBase::Display($calculation->price_3, 5)." ". $calculation->GetCurrencyName($calculation->currency_3).($calculation->currency_3 == Calculation::USD ? " (".CalculationBase::Display($calculation->price_3 * $calculation->usd, 5)." руб)" : "").($calculation->currency_3 == Calculation::EURO ? " (".CalculationBase::Display($calculation->price_3 * $calculation->euro, 5)." руб)" : ""), "", ""));
    }
        
    array_push($file_data, array("Ширина ручья, мм", $calculation->stream_width, "", ""));
    array_push($file_data, array("Количество ручьёв", $calculation->streams_number, "", ""));
        
    if(!empty($calculation->machine_id)) {
        array_push($file_data, array("Рапорт", CalculationBase::Display($calculation->raport, 5), "", ""));
    }
        
    if($calculation->laminations_number > 0) {
        array_push($file_data, array("Ширина ламинирующего вала, мм", CalculationBase::Display($calculation->lamination_roller_width, 5), "", ""));
    }
        
    if(!empty($calculation->machine_id)) {
        for($i=1; $i<=$calculation->ink_number; $i++) {
            $ink = "ink_$i";
            $color = "color_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            $cliche = "cliche_$i";
            array_push($file_data, array("Краска $i:", $calculation->GetInkName(get_object_vars($calculation)[$ink]).(empty(get_object_vars($calculation)[$color]) ? "" : " ".get_object_vars($calculation)[$color]).(empty(get_object_vars($calculation)[$cmyk]) ? "" : " ".get_object_vars($calculation)[$cmyk])." ".get_object_vars($calculation)[$percent]."% ".$calculation->GetClicheName(get_object_vars($calculation)[$cliche]), "", ""));
        }
    }
        
    if($calculation->cliche_in_price == 1) {
        array_push($file_data, array("Включить ПФ в себестоимость", "", "", ""));
    }
    else {
        array_push($file_data, array("Не включать ПФ в себестоимость", "", "", ""));
    }
        
    if($calculation->customer_pays_for_cliche == 1) {
        array_push($file_data, array("Заказчик платит за ПФ", "", "", ""));
    }
    else {
        array_push($file_data, array("Мы платим за ПФ", "", "", ""));
    }
        
    array_push($file_data, array("Дополнительные расходы с ".$calculation->GetUnitName($calculation->unit).", руб", CalculationBase::Display($calculation->extra_expense, 5), "", ""));
    
    array_push($file_data, array("", "", "", ""));
        
    // Значения по умолчанию
    if(empty($calculation->thickness_2)) $calculation->thickness_2 = 0;
    if(empty($calculation->density_2)) $calculation->density_2 = 0;
    if(empty($calculation->price_2)) $calculation->price_2 = 0;
    if(empty($calculation->thickness_3)) $calculation->thickness_3 = 0;
    if(empty($calculation->density_3)) $calculation->density_3 = 0;
    if(empty($calculation->price_3)) $calculation->price_3 = 0;
    if($calculation->work_type_id == WORK_TYPE_NOPRINT) $calculation->machine_id = null;
    if(empty($calculation->raport)) $calculation->raport = 0;
    if(empty($calculation->lamination_roller_width)) $calculation->lamination_roller_width = 0;
    if(empty($calculation->ink_number)) $calculation->ink_number = 0;
        
    // Если материал заказчика, то его цена = 0
    if($calculation->customers_material_1 == true) $calculation->price_1 = 0;
    if($calculation->customers_material_2 == true) $calculation->price_2 = 0;
    if($calculation->customers_material_3 == true) $calculation->price_3 = 0;
        
    // Уравнивающий коэффициент
    array_push($file_data, array("УК1", $calculation->uk1, "", "нет печати - 0, есть печать - 1"));
    array_push($file_data, array("УК2", $calculation->uk2, "", "нет ламинации - 0, есть ламинация - 1"));
    array_push($file_data, array("УК3", $calculation->uk3, "", "нет второй ламинации - 0, есть вторая ламинация - 1"));
    array_push($file_data, array("УКПФ", $calculation->ukpf, "", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1"));
        
    // Результаты вычислений
    array_push($file_data, array("М2 чистые, м2",
        CalculationBase::Display($calculation->area_pure_start, 5),
        $calculation->unit == Calculation::KG ? "" : "|= ".CalculationBase::Display($calculation->length, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." * ".CalculationBase::Display($calculation->quantity, 5)." / 1000000",
        $calculation->unit == Calculation::KG ? "Считается только при размере тиража в штуках" : "длина этикетки * ширина ручья * количество штук / 1 000 000"));
        
    array_push($file_data, array("Масса тиража, кг", 
        CalculationBase::Display($calculation->weight, 5),
        $calculation->unit == Calculation::KG ? "|= ".$calculation->quantity : "|= ".CalculationBase::Display($calculation->area_pure_start, 5)." * (".CalculationBase::Display($calculation->density_1, 5)." + ".CalculationBase::Display($calculation->density_2, 5)." + ".CalculationBase::Display($calculation->density_3, 5).") / 1000",
        $calculation->unit == Calculation::KG ? "размер тиража в кг" : "м2 чистые * (уд. вес 1 + уд. вес 2 + уд. вес 3) / 1000"));
        
    $width_1_formula = "";
        
    switch ($calculation->ski_1) {
        case Calculation::NO_SKI:
            $calculation->width_1_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5);
            break;
            
        case Calculation::STANDARD_SKI:
            $calculation->width_1_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." + 20";
            break;
            
        case Calculation::NONSTANDARD_SKI:
            $calculation->width_1_formula = "|= ".CalculationBase::Display($calculation->width_ski_1, 5);
            break;
    }
        
    array_push($file_data, array("Ширина материала (начальная) 1, мм",
        CalculationBase::Display($calculation->width_start_1, 5),
        $calculation->width_1_formula,
        "без лыж 1: количество ручьёв * ширина ручья, стандартные лыжи 1: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 1: вводится вручную"));
        
    $calculation->width_2_formula = "";
    switch ($calculation->ski_2) {
        case Calculation::NO_SKI:
            $calculation->width_2_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5);
            break;
            
        case Calculation::STANDARD_SKI:
            $calculation->width_2_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." + 20";
            break;
            
        case Calculation::NONSTANDARD_SKI:
            $calculation->width_2_formula = "|= ".CalculationBase::Display($calculation->width_ski_2, 5);
            break;
    }
        
    array_push($file_data, array("Ширина материала (начальная) 2, мм",
        CalculationBase::Display($calculation->width_start_2, 5),
        $calculation->width_2_formula,
        "без лыж 2: количество ручьёв * ширина ручья, стандартные лыжи 2: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 2: вводится вручную"));
        
    $calculation->width_3_formula = "";
    switch ($calculation->ski_3) {
        case Calculation::NO_SKI:
            $calculation->width_3_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5);
            break;
            
        case Calculation::STANDARD_SKI:
            $calculation->width_3_formula = "|= ".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." + 20";
            break;
            
        case Calculation::NONSTANDARD_SKI:
            $calculation->width_3_formula = "|= ".CalculationBase::Display($calculation->width_ski_3, 5);
            break;
    }
        
    array_push($file_data, array("Ширина материала (начальная) 3, мм",
        CalculationBase::Display($calculation->width_start_3, 5),
        $calculation->width_3_formula,
        "без лыж 3: количество ручьёв * ширина ручья, стандартные лыжи 3: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 3: вводится вручную"));
    
    array_push($file_data, array("Ширина материала (кратная 5) 1, мм",
        CalculationBase::Display($calculation->width_1, 5),
        "|= ОКРВВЕРХ(".CalculationBase::Display($calculation->width_start_1, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 1 / 5) * 5"));
    
    array_push($file_data, array("Ширина материала (кратная 5) 2, мм",
        CalculationBase::Display($calculation->width_2, 5),
        "|= ОКРВВЕРХ(".CalculationBase::Display($calculation->width_start_2, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 2 / 5) * 5"));
    
    array_push($file_data, array("Ширина материала (кратная 5) 3, мм",
        CalculationBase::Display($calculation->width_3, 5),
        "|= ОКРВВЕРХ(".CalculationBase::Display($calculation->width_start_3, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 3 / 5) * 5"));
        
    array_push($file_data, array("М2 чистые 1, м2",
        CalculationBase::Display($calculation->area_pure_1, 5),
        "|= ".CalculationBase::Display($calculation->weight, 5)." * 1000 / (".CalculationBase::Display($calculation->density_1, 5)." + ".CalculationBase::Display($calculation->density_2, 5)." + ".CalculationBase::Display($calculation->density_3, 5).")",
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3)"));
        
    array_push($file_data, array("М2 чистые 2, м2",
        CalculationBase::Display($calculation->area_pure_2, 5),
        "|= ".CalculationBase::Display($calculation->weight, 5)." * 1000 / (".CalculationBase::Display($calculation->density_1, 5)." + ".CalculationBase::Display($calculation->density_2, 5)." + ".CalculationBase::Display($calculation->density_3, 5).") * ".$calculation->uk2,
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК2"));
        
    array_push($file_data, array("М2 чистые 3, м2",
        CalculationBase::Display($calculation->area_pure_3, 5),
        "|= ".CalculationBase::Display($calculation->weight, 5)." * 1000 / (".CalculationBase::Display($calculation->density_1, 5)." + ".CalculationBase::Display($calculation->density_2, 5)." + ".CalculationBase::Display($calculation->density_3, 5).") * ".$calculation->uk3,
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК3"));
        
    array_push($file_data, array("М пог чистые 1, м",
        CalculationBase::Display($calculation->length_pure_start_1, 5),
        "|= ".CalculationBase::Display($calculation->area_pure_1, 5)." / (".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." / 1000)",
        "м2 чистые 1 / (количество ручьёв * ширина ручья / 1000)"));
        
    array_push($file_data, array("М пог чистые 2, м",
        CalculationBase::Display($calculation->length_pure_start_2, 5),
        "|= ".CalculationBase::Display($calculation->area_pure_2, 5)." / (".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." / 1000)",
        "м2 чистые 2 / (количество ручьёв * ширина ручья / 1000)"));
        
    array_push($file_data, array("М пог чистые 2, м",
        CalculationBase::Display($calculation->length_pure_start_3, 5),
        "|= ".CalculationBase::Display($calculation->area_pure_3, 5)." / (".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." / 1000)",
        "м2 чистые 3 / (количество ручьёв * ширина ручья / 1000)"));
        
    array_push($file_data, array("СтартСтопОтход 1",
        CalculationBase::Display($calculation->waste_length_1, 5),
        "|= ".CalculationBase::Display($calculation->data_priladka->waste_percent, 5)." * ".CalculationBase::Display($calculation->length_pure_start_1, 5)." / 100",
        "СтартСтопОтход печати * м пог чистые 1 / 100"));
        
    array_push($file_data, array("СтартСтопОтход 2",
        CalculationBase::Display($calculation->waste_length_2, 5),
        "|= ".CalculationBase::Display($calculation->data_priladka_laminator->waste_percent, 5)." * ".CalculationBase::Display($calculation->length_pure_start_2, 5)." / 100",
        "СтартСтопОтход ламинации * м. пог. чистые 2 / 100"));
        
    array_push($file_data, array("СтартСтопОтход 3",
        CalculationBase::Display($calculation->waste_length_3, 5),
        "|= ".CalculationBase::Display($calculation->data_priladka_laminator->waste_percent, 5)." * ".CalculationBase::Display($calculation->length_pure_start_3, 5)." / 100",
        "СтартСтопОтход ламинации * м. пог. чистые 3 / 100"));
        
    array_push($file_data, array("М пог грязные 1",
        CalculationBase::Display($calculation->length_dirty_start_1, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_1, 5)." + (".CalculationBase::Display($calculation->ink_number, 5)." * ".CalculationBase::Display($calculation->data_priladka->length, 5).") + (".CalculationBase::Display($calculation->laminations_number, 5)." * ".CalculationBase::Display($calculation->data_priladka_laminator->length, 5).") + ".CalculationBase::Display($calculation->waste_length_1, 5),
        "м пог чистые 1 + (красочность * метраж приладки 1 краски) + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 1"));
        
    array_push($file_data, array("М пог грязные 2",
        CalculationBase::Display($calculation->length_dirty_start_2, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_2, 5)." + (".CalculationBase::Display($calculation->laminations_number, 5)." * ".CalculationBase::Display($calculation->data_priladka_laminator->length, 5).") + ".CalculationBase::Display($calculation->waste_length_2, 5),
        "м пог чистые 2 + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 2"));
        
    array_push($file_data, array("М пог грязные 3",
        CalculationBase::Display($calculation->length_dirty_start_3, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_3, 5)." + (".CalculationBase::Display($calculation->data_priladka_laminator->length, 5)." * ".CalculationBase::Display($calculation->uk3, 0).") + ".CalculationBase::Display($calculation->waste_length_3, 5),
        "м пог чистые 3 + (метраж приладки ламинации * УК3) + СтартСтопОтход 3"));
        
    array_push($file_data, array("М2 грязные 1",
        CalculationBase::Display($calculation->area_dirty_1, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_start_1, 5)." * ".CalculationBase::Display($calculation->width_1, 5)." / 1000",
        "м пог грязные 1 * ширина материала 1 / 1000"));
        
    array_push($file_data, array("М2 грязные 2",
        CalculationBase::Display($calculation->area_dirty_2, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_start_2, 5)." * ".CalculationBase::Display($calculation->width_2, 5)." / 1000",
        "м пог грязные 2 * ширина материала 2 / 1000"));
        
    array_push($file_data, array("М2 грязные 3",
        CalculationBase::Display($calculation->area_dirty_3, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_start_3, 5)." * ".CalculationBase::Display($calculation->width_3, 5)." / 1000",
        "м пог грязные 3 * ширина материала 3 / 1000"));
        
    //****************************************
    // Массы и длины плёнок
    //****************************************
        
    array_push($file_data, array("Масса плёнки чистая 1",
        CalculationBase::Display($calculation->weight_pure_1, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_1, 5)." * ".CalculationBase::Display($calculation->width_1, 5)." * ".CalculationBase::Display($calculation->density_1, 5)." / 1000000",
        "м пог чистые 1 * ширина материала 1 * уд вес 1 / 1000000"));
        
    array_push($file_data, array("Масса плёнки чистая 2",
        CalculationBase::Display($calculation->weight_pure_2, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_2, 5)." * ".CalculationBase::Display($calculation->width_2, 5)." * ".CalculationBase::Display($calculation->density_2, 5)." / 1000000",
        "м пог чистые 2 * ширина материала 2 * уд вес 2 / 1000000"));
        
    array_push($file_data, array("Масса плёнки чистая 3",
        CalculationBase::Display($calculation->weight_pure_3, 5),
        "|= ".CalculationBase::Display($calculation->length_pure_start_3, 5)." * ".CalculationBase::Display($calculation->width_3, 5)." * ".CalculationBase::Display($calculation->density_3, 5)." / 1000000",
        "м пог чистые 3 * ширина материала 3 * уд вес 3 / 1000000"));
        
    array_push($file_data, array("Длина пленки чистая 1, м",
        CalculationBase::Display($calculation->length_pure_1, 5),
        "|= ". CalculationBase::Display($calculation->length_pure_start_1, 5),
        "м пог чистые 1"));
        
    array_push($file_data, array("Длина пленки чистая 2, м",
        CalculationBase::Display($calculation->length_pure_2, 5),
        "|= ". CalculationBase::Display($calculation->length_pure_start_2, 5),
        "м пог чистые 2"));
        
    array_push($file_data, array("Длина пленки чистая 3, м",
        CalculationBase::Display($calculation->length_pure_3, 5),
        "|= ". CalculationBase::Display($calculation->length_pure_start_3, 5),
        "м пог чистые 3"));
        
    array_push($file_data, array("Масса плёнки грязная 1, кг",
        CalculationBase::Display($calculation->weight_dirty_1, 5),
        "|= ".CalculationBase::Display($calculation->area_dirty_1, 5)." * ".CalculationBase::Display($calculation->density_1, 5)." / 1000",
        "м2 грязные 1 * уд вес 1 / 1000"));
        
    array_push($file_data, array("Масса плёнки грязная 2, кг",
        CalculationBase::Display($calculation->weight_dirty_2, 5),
        "|= ".CalculationBase::Display($calculation->area_dirty_2, 5)." * ".CalculationBase::Display($calculation->density_2, 5)." / 1000",
        "м2 грязные 2 * уд вес 2 / 1000"));
        
    array_push($file_data, array("Масса плёнки грязная 3, кг",
        CalculationBase::Display($calculation->weight_dirty_3, 5),
        "|= ".CalculationBase::Display($calculation->area_dirty_3, 5)." * ".CalculationBase::Display($calculation->density_3, 5)." / 1000",
        "м2 грязные 3 * уд вес 3 / 1000"));
        
    array_push($file_data, array("Длина плёнки грязная 1, м",
        CalculationBase::Display($calculation->length_dirty_1, 5),
        "|= ". CalculationBase::Display($calculation->length_dirty_start_1, 5),
        "м пог грязные 1"));
        
    array_push($file_data, array("Длина плёнки грязная 2, м",
        CalculationBase::Display($calculation->length_dirty_2, 5),
        "|= ". CalculationBase::Display($calculation->length_dirty_start_2, 5),
        "м пог грязные 2"));
        
    array_push($file_data, array("Длина плёнки грязная 3, м",
        CalculationBase::Display($calculation->length_dirty_3, 5),
        "|= ". CalculationBase::Display($calculation->length_dirty_start_3, 5),
        "м пог грязные 3"));
        
    //****************************************
    // Общая стоимость плёнок
    //****************************************
        
    array_push($file_data, array("Общая стоимость грязная 1, руб",
        CalculationBase::Display($calculation->film_cost_1, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_1, 5)." * ".CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "масса пленки 1 * цена плёнки 1 * курс валюты"));
        
    array_push($file_data, array("Общая стоимость грязная 2, руб",
        CalculationBase::Display($calculation->film_cost_2, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_2, 5)." * ".CalculationBase::Display($calculation->price_2, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5),
        "масса пленки 2 * цена плёнки 2 * курс валюты"));
        
    array_push($file_data, array("Общая стоимость грязная 3, руб",
        CalculationBase::Display($calculation->film_cost_3, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_3, 5)." * ".CalculationBase::Display($calculation->price_3, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5),
        "масса пленки 3 * цена плёнки 3 * курс валюты"));
        
    array_push($file_data, array("", "", "", ""));
        
    //*****************************************
    // Время - деньги
    //*****************************************
    
    array_push($file_data, array("Время приладки 1, ч",
        CalculationBase::Display($calculation->priladka_time_1, 5),
        "|= ".CalculationBase::Display($calculation->ink_number, 5)." * ".CalculationBase::Display($calculation->data_priladka->time, 5)." / 60",
        "красочность * время приладки 1 краски / 60"));
    
    array_push($file_data, array("Время приладки 2, ч",
        CalculationBase::Display($calculation->priladka_time_2, 5),
        "|= ".CalculationBase::Display($calculation->data_priladka_laminator->time, 5)." * ".CalculationBase::Display($calculation->uk2, 0)." / 60",
        "время приладки ламинатора * УК2 / 60"));
        
    array_push($file_data, array("Время приладки 3, ч",
        CalculationBase::Display($calculation->priladka_time_3, 5),
        "|= ".CalculationBase::Display($calculation->data_priladka_laminator->time, 5)." * ".CalculationBase::Display($calculation->uk3, 0)." / 60",
        "время приладки ламинатора * УК3 / 60"));
        
    array_push($file_data, array("Время печати (без приладки) 1, ч",
        CalculationBase::Display($calculation->print_time_1, 5),
        $calculation->data_machine->speed == 0 ? "|= 0" : "|= (".CalculationBase::Display($calculation->length_pure_start_1, 5)." + ".CalculationBase::Display($calculation->waste_length_1, 5).") / ".CalculationBase::Display($calculation->data_machine->speed, 5)." / 1000 * ".CalculationBase::Display($calculation->uk1, 0),
        $calculation->data_machine->speed == 0 ? "печати нет" : "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы машины / 1000 * УК1"));
        
    array_push($file_data, array("Время ламинации (без приладки) 2, ч",
        CalculationBase::Display($calculation->lamination_time_2, 5),
        $calculation->data_laminator->speed == 0 ? "|= 0" : "|= (".CalculationBase::Display($calculation->length_pure_start_2, 5)." + ".CalculationBase::Display($calculation->waste_length_2, 5).") / ".CalculationBase::Display($calculation->data_laminator->speed, 5)." / 1000 * ".CalculationBase::Display($calculation->uk2, 0),
        $calculation->data_laminator->speed == 0 ? "ламинации нет" : "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы ламинатора /1000 * УК2"));
        
    array_push($file_data, array("Время ламинации (без приладки) 3, ч",
        CalculationBase::Display($calculation->lamination_time_3, 5),
        $calculation->data_laminator->speed == 0 ? "|= 0" :"|= (".CalculationBase::Display($calculation->length_pure_start_3, 5)." + ".CalculationBase::Display($calculation->waste_length_3, 5).") / ".CalculationBase::Display($calculation->data_laminator->speed, 5)." / 1000 * ".CalculationBase::Display($calculation->uk3, 0),
        $calculation->data_laminator->speed == 0 ? "ламинации нет" : "(м пог чистые 1 + СтартСтопОтход 1) / скорость работы ламинатора / 1000 * УК3"));
        
    array_push($file_data, array("Общее время выполнения тиража 1, ч",
        CalculationBase::Display($calculation->work_time_1, 5),
        "|= ".CalculationBase::Display($calculation->priladka_time_1, 5)." + ".CalculationBase::Display($calculation->print_time_1, 5),
        "время приладки 1 + время печати"));
        
    array_push($file_data, array("Общее время выполнения тиража 2, ч",
        CalculationBase::Display($calculation->work_time_2, 5),
        "|= ".CalculationBase::Display($calculation->priladka_time_2, 5)." + ".CalculationBase::Display($calculation->lamination_time_2, 5),
        "время приладки 2 + время ламинации 1"));
        
    array_push($file_data, array("Общее время выполнения тиража 3, ч",
        CalculationBase::Display($calculation->work_time_3, 5),
        "|= ".CalculationBase::Display($calculation->priladka_time_3, 5)." + ".CalculationBase::Display($calculation->lamination_time_3, 5),
        "время приладки 3 + время ламинации 2"));
        
    array_push($file_data, array("Стоимость выполнения тиража 1, руб",
        CalculationBase::Display($calculation->work_cost_1, 5),
        "|= ".CalculationBase::Display($calculation->work_time_1, 5)." * ".CalculationBase::Display($calculation->data_machine->price, 5),
        "общее время выполнения 1 * цена работы оборудования 1"));
        
    array_push($file_data, array("Стоимость выполнения тиража 2, руб",
        CalculationBase::Display($calculation->work_cost_2, 5),
        "|= ".CalculationBase::Display($calculation->work_time_2, 5)." * ".CalculationBase::Display($calculation->data_laminator->price, 5),
        "общее время выполнения 2 * цена работы оборудования 2"));
        
    array_push($file_data, array("Стоимость выполнения тиража 3, руб",
        CalculationBase::Display($calculation->work_cost_3, 5),
        "|= ".CalculationBase::Display($calculation->work_time_3, 5)." * ".CalculationBase::Display($calculation->data_laminator->price, 5),
        "общее время выполнения 3 * цена работы оборудования 3"));
        
    array_push($file_data, array("", "", "", ""));
        
    //****************************************
    // Расход краски
    //****************************************
        
    array_push($file_data, array("Площадь запечатки, м2",
        CalculationBase::Display($calculation->print_area, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_1, 5)." * (".CalculationBase::Display($calculation->stream_width, 5)." * ".CalculationBase::Display($calculation->streams_number, 5)." + 10) / 1000",
        "м пог грязные 1 * (ширина ручья * кол-во ручьёв + 10 мм) / 1000"));
        
    array_push($file_data, array("Расход КраскаСмеси на 1 кг краски, кг",
        CalculationBase::Display($calculation->ink_1kg_mix_weight, 5),
        "|= 1 + ".CalculationBase::Display($calculation->data_ink->solvent_part, 5),
        "1 + расход растворителя на 1 кг краски"));
        
    array_push($file_data, array("Цена 1 кг чистого флексоля 82, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_flexol82_currency),
        CalculationBase::Display($calculation->ink_flexol82_kg_price, 5),
        "|= ".CalculationBase::Display($calculation->data_ink->solvent_flexol82_price, 5),
        "цена 1 кг флексоля 82, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_flexol82_currency)));
        
    array_push($file_data, array("Цена 1 кг чистого этоксипропанола, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_etoxipropanol_currency),
        CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 5),
        "|= ". CalculationBase::Display($calculation->data_ink->solvent_etoxipropanol_price, 5),
        "цена 1 кг этоксипропанола, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_etoxipropanol_currency)));
        
    $ink_solvent_kg_price = 0;
    $ink_solvent_currency = 1;
            
    if($calculation->machine_id == PRINTER_COMIFLEX) {
        $ink_solvent_kg_price = $calculation->ink_flexol82_kg_price;
        $ink_solvent_currency = $calculation->GetCurrencyRate($calculation->data_ink->solvent_flexol82_currency, $calculation->usd, $calculation->euro);
    }
    else {
        $ink_solvent_kg_price = $calculation->ink_etoxypropanol_kg_price;
        $ink_solvent_currency = $calculation->GetCurrencyRate($calculation->data_ink->solvent_etoxipropanol_currency, $calculation->usd, $calculation->euro);
    }
        
    array_push($file_data, array("М2 испарения растворителя грязная, м2",
        CalculationBase::Display($calculation->vaporization_area_dirty, 5),
        "|= ". CalculationBase::Display($calculation->data_machine->width, 0)." * ". CalculationBase::Display($calculation->length_dirty_start_1, 5)." / 1000",
        "Ширина машины * м. пог грязные / 1000"));
        
    array_push($file_data, array("", "", "", ""));
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        $ink = "ink_$i";
        $cmyk = "cmyk_$i";
        $lacquer = "lacquer_$i";
        $percent = "percent_$i";
        $price = $calculation->GetInkPrice(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_price, $calculation->data_ink->c_currency, $calculation->data_ink->m_price, $calculation->data_ink->m_currency, $calculation->data_ink->y_price, $calculation->data_ink->y_currency, $calculation->data_ink->k_price, $calculation->data_ink->k_currency, $calculation->data_ink->panton_price, $calculation->data_ink->panton_currency, $calculation->data_ink->white_price, $calculation->data_ink->white_currency, $calculation->data_ink->lacquer_glossy_price, $calculation->data_ink->lacquer_glossy_currency, $calculation->data_ink->lacquer_matte_price, $calculation->data_ink->lacquer_matte_currency);
            
        array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
            CalculationBase::Display($calculation->ink_kg_prices[$i], 5),
            "|= ". CalculationBase::Display($price->value, 5)." * ". CalculationBase::Display($calculation->GetCurrencyRate($price->currency, $calculation->usd, $calculation->euro), 5),
            "цена 1 кг чистой краски $i * курс валюты"));
            
        array_push($file_data, array("Цена 1 кг КраскаСмеси $i, руб",
            CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 5),
            "|= ((".CalculationBase::Display($calculation->ink_kg_prices[$i], 5)." * 1) + (".CalculationBase::Display($ink_solvent_kg_price, 5)." * ".CalculationBase::Display($calculation->data_ink->solvent_part, 5).")) / ".CalculationBase::Display($calculation->ink_1kg_mix_weight, 5),
            "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски"));
            
        array_push($file_data, array("Расход КраскаСмеси $i, кг",
            CalculationBase::Display($calculation->ink_expenses[$i], 5),
            "|= ".CalculationBase::Display($calculation->print_area, 5)." * ".CalculationBase::Display($calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense), 5)." * ".CalculationBase::Display(get_object_vars($calculation)[$percent], 5)." / 1000 / 100",
            "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100"));
            
        array_push($file_data, array("Стоимость КраскаСмеси $i, руб",
            CalculationBase::Display($calculation->ink_costs[$i], 5),
            "|= ". CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 5)." * ". CalculationBase::Display($calculation->ink_expenses[$i], 5),
            "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i"));
            
        array_push($file_data, array("М2 испарения растворителя чистая КраскаСмеси $i, м2",
            CalculationBase::Display($calculation->vaporization_areas_pure[$i], 5),
            "|= ". CalculationBase::Display($calculation->vaporization_area_dirty, 5)." - (". CalculationBase::Display($calculation->print_area, 5)." * ".CalculationBase::Display(get_object_vars($calculation)[$percent], 5)." / 100)",
            "М2 испарения растворителя грязное - (М2 запечатки * процент запечатки / 100)"));
        
        array_push($file_data, array("Расход испарения растворителя КраскаСмеси $i, кг",
            CalculationBase::Display($calculation->vaporization_expenses[$i], 5),
            "|= ". CalculationBase::Display($calculation->vaporization_areas_pure[$i], 5)." * ". CalculationBase::Display($calculation->data_machine->vaporization_expense, 5)." / 1000",
            "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2) / 1000"));
        
        array_push($file_data, array("Стоимость испарения растворителя КраскаСмеси $i, руб",
            CalculationBase::Display($calculation->vaporization_costs[$i], 5),
            "|= ". CalculationBase::Display($calculation->vaporization_expenses[$i], 5)." * ". CalculationBase::Display($ink_solvent_kg_price, 5)." * ".CalculationBase::Display($ink_solvent_currency, 5),
            "Расход испарения растворителя КГ * стоимость растворителя за КГ * валюту"));
            
        array_push($file_data, array("Расход (краска + растворитель на одну краску) КраскаСмеси $i, руб",
            CalculationBase::Display($calculation->ink_costs_mix[$i], 5),
            "|= ". CalculationBase::Display($calculation->ink_costs[$i], 5)." + ". CalculationBase::Display($calculation->vaporization_costs[$i], 5),
            "Стоимость КраскаСмеси на тираж, ₽ + Стоимость испарения растворителя, ₽"));
            
        array_push($file_data, array("Стоимость КраскаСмеси $i финальная, руб",
            CalculationBase::Display($calculation->ink_costs_final[$i], 5),
            "|= ЕСЛИ(".CalculationBase::Display($calculation->ink_costs_mix[$i], 5)." < ".CalculationBase::Display($calculation->data_ink->min_price_per_ink, 5)." ; ".CalculationBase::Display($calculation->data_ink->min_price_per_ink, 5)." ; ".CalculationBase::Display($calculation->ink_costs_mix[$i], 5).")",
            "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - Расход (краска + растворитель на одну краску)"));
    }
        
    array_push($file_data, array("", "", "", ""));
        
    //********************************************
    // Расход клея
    //********************************************
        
    array_push($file_data, array("Расход КлеяСмеси на 1 кг клея, кг",
        CalculationBase::Display($calculation->glue_kg_weight, 5),
        "|= 1 + ".CalculationBase::Display($calculation->data_glue->solvent_part, 5),
        "1 + расход растворителя на 1 кг клея"));
        
    array_push($file_data, array("Цена 1 кг чистого клея, руб",
        CalculationBase::Display($calculation->glue_kg_price, 5),
        "|= ".CalculationBase::Display($calculation->data_glue->glue_price, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->data_glue->glue_currency, $calculation->usd, $calculation->euro), 5),
        "цена 1 кг клея * курс валюты"));
        
    array_push($file_data, array("Цена 1 кг чистого растворителя для клея, руб",
        CalculationBase::Display($calculation->glue_solvent_kg_price, 5),
        "|= ".CalculationBase::Display($calculation->data_glue->solvent_price, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->data_glue->solvent_currency, $calculation->usd, $calculation->euro), 5),
        "цена 1 кг растворителя для клея * курс валюты"));
        
    array_push($file_data, array("Цена 1 кг КлеяСмеси, руб",
        CalculationBase::Display($calculation->mix_glue_kg_price, 5),
        "|= ((1 * ".CalculationBase::Display($calculation->glue_kg_price, 5).") + (".CalculationBase::Display($calculation->data_glue->solvent_part, 5)." * ".CalculationBase::Display($calculation->glue_solvent_kg_price, 5).")) / ".CalculationBase::Display($calculation->glue_kg_weight, 5),
        "((1 * цена 1 кг чистого клея) + (расход растворителя на 1 кг клея * цена 1 кг чистого растворителя)) / расход КлеяСмеси на 1 кг клея"));
        
    array_push($file_data, array("Площадь заклейки 2, м2",
        CalculationBase::Display($calculation->glue_area2, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_2, 5)." * ".CalculationBase::Display($calculation->lamination_roller_width, 5)." / 1000",
        "м пог грязные 2 * ширина ламинирующего вала / 1000"));
        
    array_push($file_data, array("Площадь заклейки 3, м2",
        CalculationBase::Display($calculation->glue_area3, 5),
        "|= ".CalculationBase::Display($calculation->length_dirty_3, 5)." * ".CalculationBase::Display($calculation->lamination_roller_width, 5)." / 1000",
        "м пог грязные 2 * ширина ламинирующего вала / 1000"));
        
    $glue_expense2_formula = CalculationBase::Display($calculation->glue_area2, 5)." * ".CalculationBase::Display($calculation->data_glue->glue_expense, 5)." / 1000";
    $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси в 1 м2 / 1000";
        
    if((strlen($calculation->film_1) > 3 && substr($calculation->film_1, 0, 3) == "Pet") || (strlen($calculation->film_2) > 3 && substr($calculation->film_2, 0, 3) == "Pet")) {
        $glue_expense2_formula = CalculationBase::Display($calculation->glue_area2, 5)." * ".CalculationBase::Display($calculation->data_glue->glue_expense_pet, 5)." / 1000";
        $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
    }
        
    array_push($file_data, array("Расход КлеяСмеси 2, кг",
        CalculationBase::Display($calculation->glue_expense2, 5),
        "|= ".$glue_expense2_formula,
        $glue_expense2_comment));
        
    $glue_expense3_formula = CalculationBase::Display($calculation->glue_area3, 5)." * ".CalculationBase::Display($calculation->data_glue->glue_expense, 5)." / 1000";
    $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси в 1 м2 / 1000";
    
    if((strlen($calculation->film_2) > 3 && substr($calculation->film_2, 0, 3) == "Pet") || (strlen($calculation->film_3) > 3 && substr($calculation->film_3, 0, 3) == "Pet")) {
        $glue_expense3_formula = CalculationBase::Display($calculation->glue_area3, 5)." * ".CalculationBase::Display($calculation->data_glue->glue_expense_pet, 5)." / 1000";
        $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
    }
        
    array_push($file_data, array("Расход КлеяСмеси 3, кг",
        CalculationBase::Display($calculation->glue_expense3, 5),
        "|= ".$glue_expense3_formula,
        $glue_expense3_comment));
        
    array_push($file_data, array("Стоимость КлеяСмеси 2, руб",
        CalculationBase::Display($calculation->glue_cost2, 5),
        "|= ".CalculationBase::Display($calculation->glue_expense2, 5)." * ".CalculationBase::Display($calculation->mix_glue_kg_price, 5),
        "расход КлеяСмеси 2 * цена 1 кг КлеяСмеси"));
    
    array_push($file_data, array("Стоимость КлеяСмеси 3, руб",
        CalculationBase::Display($calculation->glue_cost3, 5),
        "|= ".CalculationBase::Display($calculation->glue_expense3, 5)." * ".CalculationBase::Display($calculation->mix_glue_kg_price, 5),
        "расход КлеяСмеси 3 * цена 1 кг КлеяСмеси"));
        
    array_push($file_data, array("", "", "", ""));
        
    //***********************************
    // Стоимость форм
    //***********************************
        
    array_push($file_data, array("Высота форм, м",
        CalculationBase::Display($calculation->cliche_height, 5),
        "|= ".CalculationBase::Display($calculation->raport, 5)." + 20",
        "(рапорт + 20мм) / 1000"));
        
    array_push($file_data, array("Ширина форм, м",
        CalculationBase::Display($calculation->cliche_width, 5),
        "|= (".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->stream_width, 5)." + 20) + ".((!empty($calculation->ski_1) && $calculation->ski_1 == Calculation::NO_SKI) ? 0 : 20),
        "((кол-во ручьёв * ширина ручьёв + 20 мм), если есть лыжи (стандартные или нестандартные), то ещё + 20 мм) / 1000"));
        
    array_push($file_data, array("Площадь форм, м2",
        CalculationBase::Display($calculation->cliche_area, 5),
        "|= ".CalculationBase::Display($calculation->cliche_height, 5)." * ".CalculationBase::Display($calculation->cliche_width, 5),
        "высота форм * ширина форм"));
        
    array_push($file_data, array("Количество новых форм",
        CalculationBase::Display($calculation->cliche_new_number, 5),"", ""));
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        $cliche = "cliche_$i";
            
        $cliche_sm_price = 0;
        $cliche_currency = "";
            
        switch (get_object_vars($calculation)[$cliche]) {
            case Calculation::FLINT:
                $cliche_sm_price = $calculation->data_cliche->flint_price;
                $cliche_currency = $calculation->data_cliche->flint_currency;
                break;
                
            case Calculation::KODAK:
                $cliche_sm_price = $calculation->data_cliche->kodak_price;
                $cliche_currency = $calculation->data_cliche->kodak_currency;
                break;
        }
            
        array_push($file_data, array("Цена формы $i, руб",
            CalculationBase::Display($calculation->cliche_costs[$i], 5),
            "|= ".CalculationBase::Display($calculation->cliche_area, 5)." * ".CalculationBase::Display($cliche_sm_price, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($cliche_currency, $calculation->usd, $calculation->euro), 5),
            "площадь формы, м2 * цена формы за 1 м2 * курс валюты"));
    }
        
    array_push($file_data, array("", "", "", ""));
        
    //*******************************************
    // Стоимость скотча
    //*******************************************
        
    $scotch_formula = "";
    $scotch_comment = "";
        
    for($i = 1; $i <= 8; $i++) {
        if(!empty($scotch_formula)) {
            $scotch_formula .= " + ";
        }
            
        if(!empty($scotch_comment)) {
            $scotch_comment .= " + ";
        }
            
        $scotch_formula .= CalculationBase::Display($calculation->scotch_costs[$i], 5);
        $scotch_comment .= "стоимость скотча цвет $i";
            
        $cliche_area = 0;
            
        if($i <= $calculation->ink_number) {
            $cliche_area = $calculation->cliche_area;
        }
            
        array_push($file_data, array("Стоимость скотча Цвет $i, руб",
            CalculationBase::Display($calculation->scotch_costs[$i], 5),
            "|= ".CalculationBase::Display($calculation->cliche_area, 5)." * ".CalculationBase::Display($calculation->data_cliche->scotch_price, 5)." * ".CalculationBase::Display($calculation->GetCurrencyRate($calculation->data_cliche->scotch_currency, $calculation->usd, $calculation->euro), 5),
            "площадь формы цвет $i, м2 * цена скотча за м2 * курс валюты"));
    }
        
    array_push($file_data, array("Общая себестоимость скотча, руб",
        CalculationBase::Display($calculation->scotch_cost, 5),
        "|= ".$scotch_formula,
        $scotch_comment));
        
    array_push($file_data, array("", "", "", ""));
        
    //*******************************************
    // Наценка
    //*******************************************
        
    array_push($file_data, array("Наценка на тираж, %", CalculationBase::Display($calculation->extracharge, 5), "", ""));
    array_push($file_data, array("Наценка на ПФ, %", CalculationBase::Display($calculation->extracharge_cliche, 5), "", "Если УКПФ = 1, то наценка на ПФ всегда 0"));
    array_push($file_data, array("", "", "", ""));
        
    //*******************************************
    // Данные для правой панели
    //*******************************************
        
    array_push($file_data, array("Общая стоимость всех плёнок, руб",
        CalculationBase::Display($calculation->film_cost, 5),
        "|= ".CalculationBase::Display($calculation->film_cost_1, 5)." + ".CalculationBase::Display($calculation->film_cost_2, 5)." + ".CalculationBase::Display($calculation->film_cost_3, 5),
        "стоимость плёнки грязная 1 + стоимость плёнки грязная 2 + стоимость плёнки грязная 3"));
        
    array_push($file_data, array("Общая стоимость работ, руб",
        CalculationBase::Display($calculation->work_cost, 5),
        "|= ".CalculationBase::Display($calculation->work_cost_1, 5)." + ".CalculationBase::Display($calculation->work_cost_2, 5)." + ".CalculationBase::Display($calculation->work_cost_3, 5),
        "стоимость выполнения тиража 1 + стоимость выполнения тиража 2 + стоимость выполнения тиража 3"));
        
    $total_ink_cost_formula = "";
    $total_ink_expense_formula = "";
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        if(!empty($total_ink_cost_formula)) {
            $total_ink_cost_formula .= " + ";
        }
        $total_ink_cost_formula .= CalculationBase::Display($calculation->ink_costs_final[$i], 5);
            
        if(!empty($total_ink_expense_formula)) {
            $total_ink_expense_formula .= " + ";
        }
        $total_ink_expense_formula .= CalculationBase::Display($calculation->ink_expenses[$i], 5);
    }
        
    array_push($file_data, array("Стоимость краски, руб",
        CalculationBase::Display($calculation->ink_cost, 5),
        "|= ".$total_ink_cost_formula,
        "Сумма стоимость всех красок"));
        
    array_push($file_data, array("Расход краски, кг",
        CalculationBase::Display($calculation->ink_expense, 5),
        "|= ".$total_ink_expense_formula,
        "Сумма расход всех красок"));
        
    array_push($file_data, array("Стоимость клея, руб",
        CalculationBase::Display($calculation->glue_cost, 5),
        "|= ".CalculationBase::Display($calculation->glue_cost2, 5)." + ".CalculationBase::Display($calculation->glue_cost3, 5),
        "стоимость клея 2 + стоимость клея 3"));
        
    $total_cliche_cost_formula = "";
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        if(!empty($total_cliche_cost_formula)) {
            $total_cliche_cost_formula .= " + ";
        }
        $total_cliche_cost_formula .= CalculationBase::Display($calculation->cliche_costs[$i], 5);
    }
        
    array_push($file_data, array("Себестоимость ПФ, руб",
        CalculationBase::Display($calculation->cliche_cost, 5),
        "|= ".$total_cliche_cost_formula,
        "сумма стоимости всех форм"));
        
    array_push($file_data, array("Себестоимость, руб",
        CalculationBase::Display($calculation->cost, 5),
        "|= ". CalculationBase::Display($calculation->film_cost, 5)." + ". CalculationBase::Display($calculation->work_cost, 5)." + ". CalculationBase::Display($calculation->ink_cost, 5)." + ". CalculationBase::Display($calculation->glue_cost, 5)." + (". CalculationBase::Display($calculation->cliche_cost, 5)." * ". CalculationBase::Display($calculation->ukpf, 0).") + ".CalculationBase::Display($calculation->scotch_cost, 5),
        "стоимость плёнки + стоимость работы + стоимость краски + стоимость клея + (стоимость форм * УКПФ) + стоимость скотча"));
        
    array_push($file_data, array("Себестоимость за ". $calculation->GetUnitName($calculation->unit).", руб",
        CalculationBase::Display($calculation->cost_per_unit, 5),
        "|= ". CalculationBase::Display($calculation->cost, 5)." / ". CalculationBase::Display($calculation->quantity, 5),
        "себестоимость / размер тиража"));
        
    array_push($file_data, array("Отгрузочная стоимость, руб",
        CalculationBase::Display($calculation->shipping_cost, 5),
        "|= ".CalculationBase::Display($calculation->cost, 5)." * (1 + (".CalculationBase::Display($calculation->extracharge, 5)." / 100))",
        "себестоимость * (1 + (наценка на тираж / 100))"));
            
    array_push($file_data, array("Отгрузочная стоимость за ".$calculation->GetUnitName($calculation->unit).", руб",
        CalculationBase::Display($calculation->shipping_cost_per_unit, 5),
        "|= ".CalculationBase::Display($calculation->shipping_cost, 5)." / ".CalculationBase::Display($calculation->quantity, 0),
        "отгрузочная стоимость / размер тиража"));
            
    array_push($file_data, array("Прибыль, руб",
        CalculationBase::Display($calculation->income, 5),
        "|= (".CalculationBase::Display($calculation->shipping_cost, 5)." - ".CalculationBase::Display($calculation->cost, 5).") - (".CalculationBase::Display($calculation->extra_expense, 5)." * ".$calculation->quantity.")",
        "(отгрузочная стоимость - себестоимость) - (доп. расходы на кг / шт * объём заказа, кг/шт)"));
            
    array_push($file_data, array("Прибыль за ".$calculation->GetUnitName($calculation->unit).", руб",
        CalculationBase::Display($calculation->income_per_unit, 5),
        "|= ".CalculationBase::Display($calculation->shipping_cost_per_unit, 5)." - ".CalculationBase::Display($calculation->cost_per_unit, 5)." - ".CalculationBase::Display($calculation->extra_expense, 5),
        "отгрузочная стоимость за ". $calculation->GetUnitName($calculation->unit)." - себестоимость за ". $calculation->GetUnitName($calculation->unit)." - дополнительные расходы с ".$calculation->GetUnitName($calculation->unit)));
            
    array_push($file_data, array("Отгрузочная стоимость ПФ, руб",
        CalculationBase::Display($calculation->shipping_cliche_cost, 5),
        "|= ".CalculationBase::Display($calculation->cliche_cost, 5)." * (1 + (".CalculationBase::Display($calculation->extracharge_cliche, 5)." / 100)) * ((".$calculation->ukpf." - 1) / -1)",
        "сумма стоимости всех форм * (1 + (наценка на ПФ / 100)) * CusPayPF * ((КоэфПФ - 1) / -1)"));
        
    array_push($file_data, array("Прибыль ПФ, руб",
        CalculationBase::Display($calculation->income_cliche, 5),
        "|= (".CalculationBase::Display($calculation->shipping_cliche_cost, 5)." - ".CalculationBase::Display($calculation->cliche_cost, 5).") * ((".$calculation->ukpf." - 1) / -1)",
        "(отгрузочная стоимость ПФ - себестоимость ПФ) * ((КоэфПФ - 1) / -1)"));
        
    array_push($file_data, array("Общий вес всех плёнок с приладкой, кг",
        CalculationBase::Display($calculation->total_weight_dirty, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_1, 5)." + ".CalculationBase::Display($calculation->weight_dirty_2, 5)." + ".CalculationBase::Display($calculation->weight_dirty_3, 5),
        "масса плёнки грязная 1 + масса плёнки грязная 2 + масса плёнки грязная 3"));
        
    array_push($file_data, array("Стоимость за кг 1, руб",
        CalculationBase::Display($calculation->film_cost_per_unit_1, 5),
        "|= ".CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "цена плёнки 1 * курс валюты"));
        
    array_push($file_data, array("Стоимость за кг 2, руб",
        CalculationBase::Display($calculation->film_cost_per_unit_2, 5),
        "|= ".CalculationBase::Display($calculation->price_2, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5),
        "цена плёнки 2 * курс валюты"));
        
    array_push($file_data, array("Стоимость за кг 3, руб",
        CalculationBase::Display($calculation->film_cost_per_unit_3, 5),
        "|= ".CalculationBase::Display($calculation->price_3, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5),
        "цена плёнки 3 * курс валюты"));
        
    array_push($file_data, array("Отходы 1, руб",
        CalculationBase::Display($calculation->film_waste_cost_1, 5),
        "|= ".CalculationBase::Display($calculation->film_waste_weight_1, 5)." * ".CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "отходы 1, кг * цена плёнки 1 * курс валюты"));
        
    array_push($file_data, array("Отходы 2, руб",
        CalculationBase::Display($calculation->film_waste_cost_2, 5),
        "|= ".CalculationBase::Display($calculation->film_waste_weight_2, 5)." * ".CalculationBase::Display($calculation->price_2, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5),
        "отходы 2, кг * цена плёнки 2 * курс валюты"));
        
    array_push($file_data, array("Отходы 3, руб",
        CalculationBase::Display($calculation->film_waste_cost_3, 5),
        "|= ".CalculationBase::Display($calculation->film_waste_weight_3, 5)." * ".CalculationBase::Display($calculation->price_3, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5),
        "отходы 3, кг * цена плёнки 3 * курс валюты"));
        
    array_push($file_data, array("Отходы 1, кг",
        CalculationBase::Display($calculation->film_waste_weight_1, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_1, 5)." - ".CalculationBase::Display($calculation->weight_pure_1, 5),
        "масса плёнки грязная 1 - масса плёнки чистая 1"));
        
    array_push($file_data, array("Отходы 2, кг",
        CalculationBase::Display($calculation->film_waste_weight_2, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_2, 5)." - ".CalculationBase::Display($calculation->weight_pure_2, 5),
        "масса плёнки грязная 2 - масса плёнки чистая 2"));
        
    array_push($file_data, array("Отходы 3, кг",
        CalculationBase::Display($calculation->film_waste_weight_3, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty_3, 5)." - ".CalculationBase::Display($calculation->weight_pure_3, 5),
        "масса плёнки грязная 3 - масса плёнки чистая 3"));
        
    //***************************************************
    // Сохранение в файл
    $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y').' '.str_replace(',', '_', $calculation->name).".csv";
        
    DownloadSendHeaders($file_name);
    echo Array2Csv($file_data, $titles);
    die();
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы экспортировать в CSV надо нажать на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>