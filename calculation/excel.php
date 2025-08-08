<?php
include '../include/topscripts.php';
include './calculation.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $calculation = CalculationBase::Create($id);
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($calculation->name);
    
    // Заголовки
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    
    $sheet->getCell('A1')->getStyle()->getFont()->setBold(true); $sheet->setCellValue('A1', "Параметр");
    $sheet->getCell('B1')->getStyle()->getFont()->setBold(true); $sheet->setCellValue('B1', "Значение");
    $sheet->getCell('C1')->getStyle()->getFont()->setBold(true); $sheet->setCellValue('C1', "Расчёт");
    $sheet->getCell('D1')->getStyle()->getFont()->setBold(true); $sheet->setCellValue('D1', "Результат");
    $sheet->getCell('E1')->getStyle()->getFont()->setBold(true); $sheet->setCellValue('E1', "Комментарий");
    
    // Исходные данные
    $rowindex = 1;
    
    $sheet->setCellValue('A'.(++$rowindex), "Курс доллара, руб"); $sheet->setCellValue("B$rowindex", $calculation->usd);
    $sheet->setCellValue('A'.(++$rowindex), "Курс евро, руб"); $sheet->setCellValue("B$rowindex", $calculation->euro);
    if($calculation->work_type_id == WORK_TYPE_PRINT) { $sheet->setCellValue('A'.(++$rowindex), "Тип работы"); $sheet->setCellValue("B$rowindex", "Плёнка с печатью"); }
    elseif($calculation->work_type_id == WORK_TYPE_NOPRINT) { $sheet->setCellValue('A'.(++$rowindex), "Тип работы"); $sheet->setCellValue("B$rowindex", "Плёнка без печати"); }
        
    if(!empty($calculation->machine_id)) {
        $sheet->setCellValue('A'.(++$rowindex), "Машина"); $sheet->setCellValue("B$rowindex", PRINTER_NAMES[$calculation->machine_id]);
    }
        
    if(!empty($calculation->laminator_id)) {
        $sheet->setCellValue('A'.(++$rowindex), "Ламинатор"); $sheet->setCellValue("B$rowindex", LAMINATOR_NAMES[$calculation->laminator_id]);
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Размер тиража"); $sheet->setCellValue("B$rowindex", $calculation->quantity); $sheet->setCellValue("C$rowindex", $calculation->GetUnitName($calculation->unit));
    $sheet->setCellValue('A'.(++$rowindex), "Марка 1"); $sheet->setCellValue("B$rowindex", $calculation->film_1);
    $sheet->setCellValue('A'.(++$rowindex), "Толщина 1, мкм"); $sheet->setCellValue("B$rowindex", $calculation->thickness_1);
    $sheet->setCellValue('A'.(++$rowindex), "Плотность 1, г/м2"); $sheet->setCellValue("B$rowindex", $calculation->density_1);
    $sheet->setCellValue('A'.(++$rowindex), "Лыжи 1"); $sheet->setCellValue("B$rowindex", $calculation->GetSkiName($calculation->ski_1));
    if($calculation->ski_1 == SKI_NONSTANDARD) { $sheet->setCellValue('A'.(++$rowindex), "Ширина плёнки 1, мм"); $sheet->setCellValue("B$rowindex", $calculation->width_ski_1); }
    if($calculation->customers_material_1 == true) { $sheet->setCellValue('A'.(++$rowindex), "Материал заказчика 1"); }
    else { $sheet->setCellValue('A'.(++$rowindex), "Цена 1"); $sheet->setCellValue("B$rowindex", $calculation->price_1); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->currency_1).($calculation->currency_1 == CURRENCY_USD ? " (".DisplayNumber($calculation->price_1 * $calculation->usd, 5)." руб)" : "").($calculation->currency_1 == CURRENCY_EURO ? " (".DisplayNumber($calculation->price_1 * $calculation->euro, 5)." руб)" : "")); }
    $sheet->setCellValue('A'.(++$rowindex), "Экосбор 1"); $sheet->setCellValue("B$rowindex", $calculation->eco_price_1); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->eco_currency_1).($calculation->eco_currency_1 == CURRENCY_USD ? " (". DisplayNumber($calculation->eco_price_1 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_1 == CURRENCY_EURO ? " (". DisplayNumber($calculation->eco_price_1 * $calculation->euro, 5)." руб)" : ""));
        
    if($calculation->laminations_number > 0) {
        $sheet->setCellValue('A'.(++$rowindex), "Марка 2"); $sheet->setCellValue("B$rowindex", $calculation->film_2);
        $sheet->setCellValue('A'.(++$rowindex), "Толщина 2, мкм"); $sheet->setCellValue("B$rowindex", $calculation->thickness_2);
        $sheet->setCellValue('A'.(++$rowindex), "Плотность 2, г/м2"); $sheet->setCellValue("B$rowindex", $calculation->density_2);
        $sheet->setCellValue('A'.(++$rowindex), "Лыжи 2"); $sheet->setCellValue("B$rowindex", $calculation->GetSkiName($calculation->ski_2));
        if($calculation->ski_2 == SKI_NONSTANDARD) { $sheet->setCellValue('A'.(++$rowindex), "Ширина пленки 2, мм"); $sheet->setCellValue("B$rowindex", $calculation->width_ski_2); }
        if($calculation->customers_material_2 == true) { $sheet->setCellValue('A'.(++$rowindex), "Материал заказчика 2"); }
        else { $sheet->setCellValue('A'.(++$rowindex), "Цена 2"); $sheet->setCellValue("B$rowindex", $calculation->price_2); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->currency_2).($calculation->currency_2 == CURRENCY_USD ? " (".DisplayNumber($calculation->price_2 * $calculation->usd, 5)." руб)" : "").($calculation->currency_2 == CURRENCY_EURO ? " (".DisplayNumber($calculation->price_2 * $calculation->euro, 5)." руб)" : "")); }
        $sheet->setCellValue('A'.(++$rowindex), "Экосбор 2"); $sheet->setCellValue("B$rowindex", $calculation->eco_price_2); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->eco_currency_2).($calculation->eco_currency_2 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_2 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_2 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_2 * $calculation->euro, 5)." руб)" : ""));
    }
        
    if($calculation->laminations_number > 1) {
        $sheet->setCellValue('A'.(++$rowindex), "Марка 3"); $sheet->setCellValue("B$rowindex", $calculation->film_3);
        $sheet->setCellValue('A'.(++$rowindex), "Толщина 3, мкм"); $sheet->setCellValue("B$rowindex", $calculation->thickness_3);
        $sheet->setCellValue('A'.(++$rowindex), "Плотность 3, г/м2"); $sheet->setCellValue("B$rowindex", $calculation->density_3);
        $sheet->setCellValue('A'.(++$rowindex), "Лыжи 3"); $sheet->setCellValue("B$rowindex", $calculation->GetSkiName($calculation->ski_3));
        if($calculation->ski_3 == SKI_NONSTANDARD) { $sheet->setCellValue('A'.(++$rowindex), "Ширина плёнки 3, мм"); $sheet->setCellValue("B$rowindex", $calculation->width_ski_3); }
        if($calculation->customers_material_3 == true) { $sheet->setCellValue('A'.(++$rowindex), "Материал заказчика 3"); }
        else { $sheet->setCellValue('A'.(++$rowindex), "Цена 3"); $sheet->setCellValue("B$rowindex", $calculation->price_3); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->currency_3).($calculation->currency_3 == CURRENCY_USD ? " (".DisplayNumber($calculation->price_3 * $calculation->usd, 5)." руб)" : "").($calculation->currency_3 == CURRENCY_EURO ? " (".DisplayNumber($calculation->price_3 * $calculation->euro, 5)." руб)" : "")); }
        $sheet->setCellValue('A'.(++$rowindex), "Экосбор 3"); $sheet->setCellValue("B$rowindex", $calculation->eco_price_3); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->eco_currency_3).($calculation->eco_currency_3 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_3 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_3 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_3 * $calculation->euro, 5)." руб)" : ""));
    }
    
    if(empty($calculation->stream_width)) {
        foreach($calculation->stream_widths as $key => $value) {
            $sheet->setCellValue('A'.(++$rowindex), "Ширина ручья $key, мм"); $sheet->setCellValue("B$rowindex", $value);
        }
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Ширина ручья, мм"); $sheet->setCellValue("B$rowindex", $calculation->stream_width);
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Количество ручьёв"); $sheet->setCellValue("B$rowindex", $calculation->streams_number);
        
    if(!empty($calculation->machine_id)) {
        $sheet->setCellValue('A'.(++$rowindex), "Рапорт"); $sheet->setCellValue("B$rowindex", $calculation->raport);
    }
        
    if($calculation->laminations_number > 0) {
        $sheet->setCellValue('A'.(++$rowindex), "Ширина ламинирующего вала, мм"); $sheet->setCellValue("B$rowindex", $calculation->lamination_roller_width);
    }
        
    if(!empty($calculation->machine_id)) {
        for($i = 1; $i <= $calculation->ink_number; $i++) {
            $ink = "ink_$i";
            $color = "color_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            $cliche = "cliche_$i";
            $sheet->setCellValue('A'.(++$rowindex), "Краска $i:"); $sheet->setCellValue("B$rowindex", $calculation->GetInkName(get_object_vars($calculation)[$ink]).(empty(get_object_vars($calculation)[$color]) ? "" : " ".get_object_vars($calculation)[$color]).(empty(get_object_vars($calculation)[$cmyk]) ? "" : " ".get_object_vars($calculation)[$cmyk])." ".get_object_vars($calculation)[$percent]."% ".$calculation->GetClicheName(get_object_vars($calculation)[$cliche]));
        }
    }
        
    if($calculation->cliche_in_price == 1) {
        $sheet->setCellValue('A'.(++$rowindex), "Включить ПФ в себестоимость");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Не включать ПФ в себестоимость");
    }
        
    if($calculation->customer_pays_for_cliche == 1) {
        $sheet->setCellValue('A'.(++$rowindex), "Заказчик платит за ПФ");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Мы платим за ПФ");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Дополнительные расходы с ".$calculation->GetUnitName($calculation->unit).", руб"); $sheet->setCellValue("B$rowindex", $calculation->extra_expense);
    
    ++$rowindex;
        
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
    $sheet->setCellValue('A'.(++$rowindex), "УК1"); $sheet->setCellValue("B$rowindex", $calculation->uk1); $sheet->setCellValue("C$rowindex", "нет печати - 0, есть печать - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УК2"); $sheet->setCellValue("B$rowindex", $calculation->uk2); $sheet->setCellValue("C$rowindex", "нет ламинации - 0, есть ламинация - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УК3"); $sheet->setCellValue("B$rowindex", $calculation->uk3); $sheet->setCellValue("C$rowindex", "нет второй ламинации - 0, есть вторая ламинация - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УКПФ"); $sheet->setCellValue("B$rowindex", $calculation->ukpf); $sheet->setCellValue("C$rowindex", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1");
    
    ++$rowindex;
    
    // Результаты вычислений
    /*if(empty($calculation->stream_width)) {
        array_push($file_data, array("М2 чистые, м2", 
            DisplayNumber($calculation->area_pure_start, 5), 
            $calculation->unit == KG ? "" : "|= ".DisplayNumber($calculation->length, 5)." * (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / ".DisplayNumber($calculation->streams_number, 5).") * ".DisplayNumber($calculation->quantity, 5)." / 1000000",
            $calculation->unit == KG ? "Считается только при размере тиража в штуках" : "длина этикетки * (суммарная ширина ручьёв / кол-во ручьёв) * кол-во штук / 1 000 000"));
    }
    else {
        array_push($file_data, array("М2 чистые, м2",
            DisplayNumber($calculation->area_pure_start, 5),
            $calculation->unit == KG ? "" : "|= ".DisplayNumber($calculation->length, 5)." * ".DisplayNumber($calculation->stream_width, 5)." * ".DisplayNumber($calculation->quantity, 5)." / 1000000",
            $calculation->unit == KG ? "Считается только при размере тиража в штуках" : "длина этикетки * ширина ручья * количество штук / 1 000 000"));
    }
        
    array_push($file_data, array("Масса тиража, кг", 
        DisplayNumber($calculation->weight, 5),
        $calculation->unit == KG ? "|= ".$calculation->quantity : "|= ".DisplayNumber($calculation->area_pure_start, 5)." * (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") / 1000",
        $calculation->unit == KG ? "размер тиража в кг" : "м2 чистые * (уд. вес 1 + уд. вес 2 + уд. вес 3) / 1000"));
        
    $width_1_formula = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_1) {
            case SKI_NO:
                $width_1_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                break;
            
            case SKI_STANDARD:
                $width_1_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->width_ski_1, 5);
        }
        
        array_push($file_data, array("Ширина материала (начальная) 1, мм", DisplayNumber($calculation->width_start_1, 5),
            $width_1_formula,
            "без лыж 1: суммарная ширина ручьёв, стандартные лыжи 1: суммарная ширина ручьёв + 20, нестандартные лыжи 1: вводится вручную"));
    }
    else {
        switch ($calculation->ski_1) {
            case SKI_NO:
                $width_1_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                break;
            
            case SKI_STANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->width_ski_1, 5);
                break;
        }
        
        array_push($file_data, array("Ширина материала (начальная) 1, мм",
            DisplayNumber($calculation->width_start_1, 5),
            $width_1_formula,
            "без лыж 1: количество ручьёв * ширина ручья, стандартные лыжи 1: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 1: вводится вручную"));
    }
        
    $width_2_formula = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_2) {
            case SKI_NO:
                $width_2_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                break;
            
            case SKI_STANDARD:
                $width_2_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->width_ski_2, 5);
                break;
        }
        
        array_push($file_data, array("Ширина материала (начальная) 2, мм", 
            DisplayNumber($calculation->width_start_2, 5),
            $width_2_formula,
            "без лыж 2: суммарная ширина ручьёв, стандартные лыжи 2: стандартная ширина ручьёв + 20 мм, нестандартные лыжи 2: вводится вручную"));
    }
    else {
        switch ($calculation->ski_2) {
            case SKI_NO:
                $width_2_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                break;
            
            case SKI_STANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->width_ski_2, 5);
                break;
            
        }
        
        array_push($file_data, array("Ширина материала (начальная) 2, мм",
            DisplayNumber($calculation->width_start_2, 5),
            $width_2_formula,
            "без лыж 2: количество ручьёв * ширина ручья, стандартные лыжи 2: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 2: вводится вручную"));
    }
        
    $width_3_formula = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_3) {
            case SKI_NO:
                $width_3_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                break;
            
            case SKI_STANDARD:
                $width_3_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->width_ski_3, 5);
                break;
        }
        
        array_push($file_data, array("Ширина материала (начальная) 3, мм",
            DisplayNumber($calculation->width_start_3, 5),
            $width_3_formula,
            "без лыж 3: суммарная ширина ручьёв, стандартные лыжи 3: суммарная ширина ручьёв + 20 мм, нестандартные лыжи 3: вводится вручную"));
    }
    else {
        switch ($calculation->ski_3) {
            case SKI_NO:
                $width_3_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                break;
            
            case SKI_STANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                break;
            
            case SKI_NONSTANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->width_ski_3, 5);
                break;
        }
        
        array_push($file_data, array("Ширина материала (начальная) 3, мм",
            DisplayNumber($calculation->width_start_3, 5),
            $width_3_formula,
            "без лыж 3: количество ручьёв * ширина ручья, стандартные лыжи 3: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 3: вводится вручную"));
    }
    
    array_push($file_data, array("Ширина материала (кратная 5) 1, мм",
        DisplayNumber($calculation->width_1, 5),
        "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_1, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 1 / 5) * 5"));
    
    array_push($file_data, array("Ширина материала (кратная 5) 2, мм",
        DisplayNumber($calculation->width_2, 5),
        "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_2, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 2 / 5) * 5"));
    
    array_push($file_data, array("Ширина материала (кратная 5) 3, мм",
        DisplayNumber($calculation->width_3, 5),
        "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_3, 5)." / 5; 1) * 5",
        "окрвверх(ширина материала начальная 3 / 5) * 5"));
        
    array_push($file_data, array("М2 чистые 1, м2",
        DisplayNumber($calculation->area_pure_1, 5),
        "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).")",
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3)"));
        
    array_push($file_data, array("М2 чистые 2, м2",
        DisplayNumber($calculation->area_pure_2, 5),
        "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") * ".$calculation->uk2,
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК2"));
        
    array_push($file_data, array("М2 чистые 3, м2",
        DisplayNumber($calculation->area_pure_3, 5),
        "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") * ".$calculation->uk3,
        "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК3"));
    
    if(empty($calculation->stream_width)) {
        array_push($file_data, array("М пог чистые 1, м", 
            DisplayNumber($calculation->length_pure_start_1, 5), 
            "!= ".DisplayNumber($calculation->area_pure_1, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / 1000)",
            "м2 чистые 1 / (суммарная ширина ручьёв / 1000)"));
    }
    else {
        array_push($file_data, array("М пог чистые 1, м",
            DisplayNumber($calculation->length_pure_start_1, 5),
            "|= ".DisplayNumber($calculation->area_pure_1, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)",
            "м2 чистые 1 / (количество ручьёв * ширина ручья / 1000)"));
    }
    
    if(empty($calculation->stream_width)) {
        array_push($file_data, array("М пог чистые 2, м",
            DisplayNumber($calculation->length_pure_start_2, 5),
            "|= ".DisplayNumber($calculation->area_pure_2, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / 1000)",
            "м2 чистые 2 / (суммарная ширина ручьёв / 1000)"));
    }
    else {
        array_push($file_data, array("М пог чистые 2, м",
            DisplayNumber($calculation->length_pure_start_2, 5),
            "|= ".DisplayNumber($calculation->area_pure_2, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)",
            "м2 чистые 2 / (количество ручьёв * ширина ручья / 1000)"));
    }
    
    if(empty($calculation->stream_width)) {
        array_push($file_data, array("М пог чистые 2, м", DisplayNumber($calculation->length_pure_start_3, 5),
            "|= ".DisplayNumber($calculation->area_pure_3, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." /1000)",
            "м2 чистые 3 / (суммарная ширина ручьёв / 1000)"));
    }
    else {
        array_push($file_data, array("М пог чистые 2, м",
            DisplayNumber($calculation->length_pure_start_3, 5),
            "|= ".DisplayNumber($calculation->area_pure_3, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)",
            "м2 чистые 3 / (количество ручьёв * ширина ручья / 1000)"));
    }
        
    array_push($file_data, array("СтартСтопОтход 1",
        DisplayNumber($calculation->waste_length_1, 5),
        "|= ".DisplayNumber($calculation->data_priladka->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_1, 5)." / 100",
        "СтартСтопОтход печати * м пог чистые 1 / 100"));
        
    array_push($file_data, array("СтартСтопОтход 2",
        DisplayNumber($calculation->waste_length_2, 5),
        "|= ".DisplayNumber($calculation->data_priladka_laminator->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_2, 5)." / 100",
        "СтартСтопОтход ламинации * м. пог. чистые 2 / 100"));
        
    array_push($file_data, array("СтартСтопОтход 3",
        DisplayNumber($calculation->waste_length_3, 5),
        "|= ".DisplayNumber($calculation->data_priladka_laminator->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_3, 5)." / 100",
        "СтартСтопОтход ламинации * м. пог. чистые 3 / 100"));
        
    array_push($file_data, array("М пог грязные 1",
        DisplayNumber($calculation->length_dirty_start_1, 5),
        "|= ".DisplayNumber($calculation->length_pure_start_1, 5)." + (".DisplayNumber($calculation->ink_number, 5)." * ".DisplayNumber($calculation->data_priladka->length, 5).") + (".DisplayNumber($calculation->laminations_number, 5)." * ".DisplayNumber($calculation->data_priladka_laminator->length, 5).") + ".DisplayNumber($calculation->waste_length_1, 5),
        "м пог чистые 1 + (красочность * метраж приладки 1 краски) + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 1"));
        
    array_push($file_data, array("М пог грязные 2",
        DisplayNumber($calculation->length_dirty_start_2, 5),
        "|= ".DisplayNumber($calculation->length_pure_start_2, 5)." + (".DisplayNumber($calculation->laminations_number, 5)." * ".DisplayNumber($calculation->data_priladka_laminator->length, 5).") + ".DisplayNumber($calculation->waste_length_2, 5),
        "м пог чистые 2 + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 2"));
        
    array_push($file_data, array("М пог грязные 3",
        DisplayNumber($calculation->length_dirty_start_3, 5),
        "|= ".DisplayNumber($calculation->length_pure_start_3, 5)." + (".DisplayNumber($calculation->data_priladka_laminator->length, 5)." * ".DisplayNumber($calculation->uk3, 0).") + ".DisplayNumber($calculation->waste_length_3, 5),
        "м пог чистые 3 + (метраж приладки ламинации * УК3) + СтартСтопОтход 3"));
        
    array_push($file_data, array("М2 грязные 1",
        DisplayNumber($calculation->area_dirty_1, 5),
        "|= ".DisplayNumber($calculation->length_dirty_start_1, 5)." * ".DisplayNumber($calculation->width_1, 5)." / 1000",
        "м пог грязные 1 * ширина материала 1 / 1000"));
        
    array_push($file_data, array("М2 грязные 2",
        DisplayNumber($calculation->area_dirty_2, 5),
        "|= ".DisplayNumber($calculation->length_dirty_start_2, 5)." * ".DisplayNumber($calculation->width_2, 5)." / 1000",
        "м пог грязные 2 * ширина материала 2 / 1000"));
        
    array_push($file_data, array("М2 грязные 3",
        DisplayNumber($calculation->area_dirty_3, 5),
        "|= ".DisplayNumber($calculation->length_dirty_start_3, 5)." * ".DisplayNumber($calculation->width_3, 5)." / 1000",
        "м пог грязные 3 * ширина материала 3 / 1000"));*/
    
    //****************************************
    // Массы и длины плёнок
    //****************************************
    
    // Сохранение
    $filename = DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y').' '.str_replace(',', '_', $calculation->name).".xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>