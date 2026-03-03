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
    $sheet->setTitle("Расчёт");
    
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
        
        if($calculation->machine_id == PRINTER_SOMA_OPTIMA) {
            for($i = 1; $i <= $calculation->ink_run2_number; $i++) {
                $ink_run2 = "ink_run2_$i";
                $color_run2 = "color_run2_$i";
                $cmyk_run2 = "cmyk_run2_$i";
                $percent_run2 = "percent_run2_$i";
                $cliche_run2 = "cliche_run2_$i";
                $sheet->setCellValue('A'.(++$rowindex), "Краска $i Второй прогон"); $sheet->setCellValue("B$rowindex", $calculation->GetInkName(get_object_vars($calculation)[$ink_run2]).(empty(get_object_vars($calculation)[$color_run2]) ? "" : " ".get_object_vars($calculation)[$color_run2]).(empty(get_object_vars($calculation)[$cmyk_run2]) ? "" : " ".get_object_vars($calculation)[$cmyk_run2])." ".get_object_vars($calculation)[$percent]."% ".$calculation->GetClicheName(get_object_vars($calculation)[$cliche_run2]));
            }
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
    if(empty($calculation->ink_run2_number)) $calculation->ink_run2_number = 0;
        
    // Если материал заказчика, то его цена = 0
    if($calculation->customers_material_1 == true) $calculation->price_1 = 0;
    if($calculation->customers_material_2 == true) $calculation->price_2 = 0;
    if($calculation->customers_material_3 == true) $calculation->price_3 = 0;
        
    // Уравнивающий коэффициент
    $sheet->setCellValue('A'.(++$rowindex), "УК1"); $sheet->setCellValue("B$rowindex", $calculation->uk1); $sheet->setCellValue("C$rowindex", "нет печати - 0, есть печать - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УК2"); $sheet->setCellValue("B$rowindex", $calculation->uk2); $sheet->setCellValue("C$rowindex", "нет ламинации - 0, есть ламинация - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УК3"); $sheet->setCellValue("B$rowindex", $calculation->uk3); $sheet->setCellValue("C$rowindex", "нет второй ламинации - 0, есть вторая ламинация - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УК4"); $sheet->setCellValue("B$rowindex", $calculation->uk4); $sheet->setCellValue("C$rowindex", "нет второго прогона - 0, есть второй прогон - 1");
    $sheet->setCellValue('A'.(++$rowindex), "УКПФ"); $sheet->setCellValue("B$rowindex", $calculation->ukpf); $sheet->setCellValue("C$rowindex", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1");
    
    ++$rowindex;
    
    // Результаты вычислений
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "М2 чистые, м2");
        $sheet->setCellValue("B$rowindex", $calculation->area_pure_start);
        $sheet->setCellValue("C$rowindex", $calculation->unit == KG ? "" : "|= ".DisplayNumber($calculation->length, 5)." * (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / ".DisplayNumber($calculation->streams_number, 5).") * ".DisplayNumber($calculation->quantity, 5)." / 1000000");
        $sheet->setCellValue("D$rowindex", $calculation->unit == KG ? "" : "=".$calculation->length."*(".array_sum($calculation->stream_widths)."/".$calculation->streams_number.")*".$calculation->quantity."/1000000");
        $sheet->setCellValue("E$rowindex", $calculation->unit == KG ? "Считается только при размере тиража в штуках" : "длина этикетки * (суммарная ширина ручьёв / кол-во ручьёв) * кол-во штук / 1 000 000");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "М2 чистые, м2");
        $sheet->setCellValue("B$rowindex", $calculation->area_pure_start);
        $sheet->setCellValue("C$rowindex", $calculation->unit == KG ? "" : "|= ".DisplayNumber($calculation->length, 5)." * ".DisplayNumber($calculation->stream_width, 5)." * ".DisplayNumber($calculation->quantity, 5)." / 1000000");
        $sheet->setCellValue("D$rowindex", $calculation->unit == KG ? "" : "=".$calculation->length."*".$calculation->stream_width."*".$calculation->quantity."/1000000");
        $sheet->setCellValue("E$rowindex", $calculation->unit == KG ? "Считается только при размере тиража в штуках" : "длина этикетки * ширина ручья * количество штук / 1 000 000");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса тиража, кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight);
    $sheet->setCellValue("C$rowindex", $calculation->unit == KG ? "|= ".$calculation->quantity : "|= ".DisplayNumber($calculation->area_pure_start, 5)." * (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") / 1000");
    $sheet->setCellValue("D$rowindex", $calculation->unit == KG ? $calculation->quantity : "=".$calculation->area_pure_start."*(".$calculation->density_1."+".$calculation->density_2."+".$calculation->density_3.")/1000");
    $sheet->setCellValue("E$rowindex", $calculation->unit == KG ? "размер тиража в кг" : "м2 чистые * (уд. вес 1 + уд. вес 2 + уд. вес 3) / 1000");

    $width_1_formula = "";
    $width_1_result = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_1) {
            case SKI_NO:
                $width_1_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                $width_1_result = "=".array_sum($calculation->stream_widths);
                break;
            
            case SKI_STANDARD:
                $width_1_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                $width_1_result = "=".array_sum($calculation->stream_widths)."+20";
                break;
            
            case SKI_NONSTANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->width_ski_1, 5);
                $width_1_result = "=".$calculation->width_ski_1;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 1, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_1);
        $sheet->setCellValue("C$rowindex", $width_1_formula);
        $sheet->setCellValue("D$rowindex", $width_1_result);
        $sheet->setCellValue("E$rowindex", "без лыж 1: суммарная ширина ручьёв, стандартные лыжи 1: суммарная ширина ручьёв + 20, нестандартные лыжи 1: вводится вручную");
    }
    else {
        switch ($calculation->ski_1) {
            case SKI_NO:
                $width_1_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                $width_1_result = "=".$calculation->streams_number."*".$calculation->stream_width;
                break;
            
            case SKI_STANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                $width_1_result = "=".$calculation->streams_number."*".$calculation->stream_width."+20";
                break;
            
            case SKI_NONSTANDARD:
                $width_1_formula = "|= ".DisplayNumber($calculation->width_ski_1, 5);
                $width_1_result = "=".$calculation->width_ski_1;
                break;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 1, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_1);
        $sheet->setCellValue("C$rowindex", $width_1_formula);
        $sheet->setCellValue("D$rowindex", $width_1_result);
        $sheet->setCellValue("E$rowindex", "без лыж 1: количество ручьёв * ширина ручья, стандартные лыжи 1: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 1: вводится вручную");
    }
        
    $width_2_formula = "";
    $width_2_result = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_2) {
            case SKI_NO:
                $width_2_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                $width_2_result = "=".array_sum($calculation->stream_widths);
                break;
            
            case SKI_STANDARD:
                $width_2_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                $width_2_result = "=".array_sum($calculation->stream_widths);
                break;
            
            case SKI_NONSTANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->width_ski_2, 5);
                $width_2_result = "=".$calculation->width_ski_2;
                break;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 2, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_2);
        $sheet->setCellValue("C$rowindex", $width_2_formula);
        $sheet->setCellValue("D$rowindex", $width_2_result);
        $sheet->setCellValue("E$rowindex", "без лыж 2: суммарная ширина ручьёв, стандартные лыжи 2: стандартная ширина ручьёв + 20 мм, нестандартные лыжи 2: вводится вручную");
    }
    else {
        switch ($calculation->ski_2) {
            case SKI_NO:
                $width_2_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                $width_2_result = "=".$calculation->streams_number."*".$calculation->stream_width;
                break;
            
            case SKI_STANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                $width_2_result = "=".$calculation->streams_number."*".$calculation->stream_width."+20";
                break;
            
            case SKI_NONSTANDARD:
                $width_2_formula = "|= ".DisplayNumber($calculation->width_ski_2, 5);
                $width_2_result = "=".$calculation->width_ski_2;
                break;
            
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 2, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_2);
        $sheet->setCellValue("C$rowindex", $width_2_formula);
        $sheet->setCellValue("D$rowindex", $width_2_result);
        $sheet->setCellValue("E$rowindex", "без лыж 2: количество ручьёв * ширина ручья, стандартные лыжи 2: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 2: вводится вручную");
    }
        
    $width_3_formula = "";
    $width_3_result = "";
    
    if(empty($calculation->stream_width)) {
        switch ($calculation->ski_3) {
            case SKI_NO:
                $width_3_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5);
                $width_3_result = "=".array_sum($calculation->stream_widths);
                break;
            
            case SKI_STANDARD:
                $width_3_formula = "|= ".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20";
                $width_3_result = "=".array_sum($calculation->stream_widths)."+20";
                break;
            
            case SKI_NONSTANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->width_ski_3, 5);
                $width_3_result = "=".$calculation->width_ski_3;
                break;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 3, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_3);
        $sheet->setCellValue("C$rowindex", $width_3_formula);
        $sheet->setCellValue("D$rowindex", $width_3_result);
        $sheet->setCellValue("E$rowindex", "");
    }
    else {
        switch ($calculation->ski_3) {
            case SKI_NO:
                $width_3_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5);
                $width_3_result = "=".$calculation->streams_number."*".$calculation->stream_width;
                break;
            
            case SKI_STANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20";
                $width_3_result = "=".$calculation->streams_number."*".$calculation->stream_width."+20";
                break;
            
            case SKI_NONSTANDARD:
                $width_3_formula = "|= ".DisplayNumber($calculation->width_ski_3, 5);
                $width_3_result = "=".$calculation->width_ski_3;
                break;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная) 3, мм");
        $sheet->setCellValue("B$rowindex", $calculation->width_start_3);
        $sheet->setCellValue("C$rowindex", $width_3_formula);
        $sheet->setCellValue("D$rowindex", $width_3_result);
        $sheet->setCellValue("E$rowindex", "без лыж 3: количество ручьёв * ширина ручья, стандартные лыжи 3: количество ручьёв * ширина ручья + 20 мм, нестандартные лыжи 3: вводится вручную");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (кратная 5) 1, мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_1);
    $sheet->setCellValue("C$rowindex", "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_1, 5)." / 5; 1) * 5");
    $sheet->setCellValue("D$rowindex", "=CEILING(".$calculation->width_start_1."/5,1)*5");
    $sheet->setCellValue("E$rowindex", "окрвверх(ширина материала начальная 1 / 5) * 5");
    
    $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (кратная 5) 2, мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_2);
    $sheet->setCellValue("C$rowindex", "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_2, 5)." / 5; 1) * 5");
    $sheet->setCellValue("D$rowindex", "=CEILING(".$calculation->width_start_2."/5,1)*5");
    $sheet->setCellValue("E$rowindex", "окрвверх(ширина материала начальная 2 / 5) * 5");
    
    $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (кратная 5) 3, мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_3);
    $sheet->setCellValue("C$rowindex", "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start_3, 5)." / 5; 1) * 5");
    $sheet->setCellValue("D$rowindex", "=CEILING(".$calculation->width_start_3."/5,1)*5");
    $sheet->setCellValue("E$rowindex", "окрвверх(ширина материала начальная 3 / 5) * 5");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 чистые 1, м2");
    $sheet->setCellValue("B$rowindex", $calculation->area_pure_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).")");
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight."*1000/(".$calculation->density_1."+".$calculation->density_2."+".$calculation->density_3.")");
    $sheet->setCellValue("E$rowindex", "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3)");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 чистые 2, м2");
    $sheet->setCellValue("B$rowindex", $calculation->area_pure_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") * ".$calculation->uk2);
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight."*1000/(".$calculation->density_1."+".$calculation->density_2."+".$calculation->density_3.")*".$calculation->uk2);
    $sheet->setCellValue("E$rowindex", "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК2");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 чистые 3, м2");
    $sheet->setCellValue("B$rowindex", $calculation->area_pure_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight, 5)." * 1000 / (".DisplayNumber($calculation->density_1, 5)." + ".DisplayNumber($calculation->density_2, 5)." + ".DisplayNumber($calculation->density_3, 5).") * ".$calculation->uk3);
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight."*1000/(".$calculation->density_1."+".$calculation->density_2."+".$calculation->density_3.")*".$calculation->uk3);
    $sheet->setCellValue("E$rowindex", "масса тиража * 1000 / (уд. вес 1 + уд. вес 2 + уд. вес 3) * УК3");
    
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 1, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_1);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_1, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_1."/(".array_sum($calculation->stream_widths)."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 1 / (суммарная ширина ручьёв / 1000)");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 1, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_1);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_1, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_1."/(".$calculation->streams_number."*".$calculation->stream_width."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 1 / (количество ручьёв * ширина ручья / 1000)");
    }
    
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 2, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_2);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_2, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_2."/(".array_sum($calculation->stream_widths)."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 2 / (суммарная ширина ручьёв / 1000)");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 2, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_2);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_2, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_2."/(".$calculation->streams_number."*".$calculation->stream_width."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 2 / (количество ручьёв * ширина ручья / 1000)");
    }
    
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 2, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_3);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_3, 5)." / (".DisplayNumber(array_sum($calculation->stream_widths), 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_3."/(".array_sum($calculation->stream_widths)."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 3 / (суммарная ширина ручьёв / 1000)");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "М пог чистые 2, м");
        $sheet->setCellValue("B$rowindex", $calculation->length_pure_start_3);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure_3, 5)." / (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." / 1000)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure_3."/(".$calculation->streams_number."*".$calculation->stream_width."/1000)");
        $sheet->setCellValue("E$rowindex", "м2 чистые 3 / (количество ручьёв * ширина ручья / 1000)");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "СтартСтопОтход 1, м");
    $sheet->setCellValue("B$rowindex", $calculation->waste_length_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_1, 5)." / 100");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka->waste_percent."*".$calculation->length_pure_start_1."/100");
    $sheet->setCellValue("E$rowindex", "СтартСтопОтход печати * м пог чистые 1 / 100");
    
    $sheet->setCellValue('A'.(++$rowindex), "СтартСтопОтход 1 Второй Прогон, м");
    $sheet->setCellValue("B$rowindex", $calculation->waste_length_1_run2);
    $sheet->setCellValue("C$rowindex", "|= ". DisplayNumber(($calculation->data_priladka->waste_percent_run2 ?? 0), 5)." * ".DisplayNumber($calculation->length_pure_start_1, 5)." / 100 * ".$calculation->uk4);
    $sheet->setCellValue("D$rowindex", "=".($calculation->data_priladka->waste_percent_run2 ?? 0)."*".$calculation->length_pure_start_1."/100*".$calculation->uk4);
    $sheet->setCellValue("E$rowindex", "СтартСтопОтход печати Второй Прогон * м пог чистые 1 / 100 * УК4");
    
    $sheet->setCellValue('A'.(++$rowindex), "СтартСтопОтход 2, м");
    $sheet->setCellValue("B$rowindex", $calculation->waste_length_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka_laminator->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_2, 5)." / 100");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka_laminator->waste_percent."*".$calculation->length_pure_start_2."/100");
    $sheet->setCellValue("E$rowindex", "СтартСтопОтход ламинации * м. пог. чистые 2 / 100");
    
    $sheet->setCellValue('A'.(++$rowindex), "СтартСтопОтход 3, м");
    $sheet->setCellValue("B$rowindex", $calculation->waste_length_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka_laminator->waste_percent, 5)." * ".DisplayNumber($calculation->length_pure_start_3, 5)." / 100");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka_laminator->waste_percent."*".$calculation->length_pure_start_3."/100");
    $sheet->setCellValue("E$rowindex", "СтартСтопОтход ламинации * м. пог. чистые 3 / 100");
    
    $sheet->setCellValue('A'.(++$rowindex), "М пог грязные 1");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_start_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_1, 5)." + (".$calculation->ink_number." * ".DisplayNumber($calculation->data_priladka->length, 5).") + (".$calculation->laminations_number." * ".DisplayNumber($calculation->data_priladka_laminator->length, 5).") + ".DisplayNumber($calculation->waste_length_1, 5)." + (".$calculation->ink_run2_number." * ". DisplayNumber($calculation->data_priladka->length_run2 ?? 0, 5).") + ". DisplayNumber($calculation->waste_length_1_run2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_1."+(".$calculation->ink_number."*".$calculation->data_priladka->length.")+(".$calculation->laminations_number."*".$calculation->data_priladka_laminator->length.")+".$calculation->waste_length_1."+(".$calculation->ink_run2_number."*".($calculation->data_priladka->length_run2 ?? 0).")+".$calculation->waste_length_1_run2);
    $sheet->setCellValue("E$rowindex", "м пог чистые 1 + (красочность * метраж приладки 1 краски) + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 1 + (красочность второй прогон * метраж приладки одной краски второй прогон) + СтартСтопОтход 1 второй прогон");
    
    $sheet->setCellValue('A'.(++$rowindex), "М пог грязные 1 второй прогон");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_start_1_run2);
    $sheet->setCellValue("C$rowindex", "|= ". DisplayNumber($calculation->length_pure_start_1, 5)." + (".$calculation->laminations_number." * ". DisplayNumber($calculation->data_priladka_laminator->length, 5).") + ". DisplayNumber($calculation->waste_length_1, 5)." + (".$calculation->ink_run2_number." * ". DisplayNumber($calculation->data_priladka->length_run2 ?? 0, 5).") + ".DisplayNumber($calculation->waste_length_1_run2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_1." + (".$calculation->laminations_number." * ".$calculation->data_priladka_laminator->length.") + ".$calculation->waste_length_1." + (".$calculation->ink_run2_number." * ".($calculation->data_priladka->length_run2 ?? 0).") + ".$calculation->waste_length_1_run2);
    $sheet->setCellValue("E$rowindex", "м пог чистые 1 + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 1 + (красочность прогон 2 * метраж приладки одной краски второй прогон) + СтартСтопОтход 1 второй прогон");
    
    $sheet->setCellValue('A'.(++$rowindex), "М пог грязные 2");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_start_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_2, 5)." + (".DisplayNumber($calculation->laminations_number, 5)." * ".DisplayNumber($calculation->data_priladka_laminator->length, 5).") + ".DisplayNumber($calculation->waste_length_2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_2."+(".$calculation->laminations_number."*".$calculation->data_priladka_laminator->length.")+".$calculation->waste_length_2);
    $sheet->setCellValue("E$rowindex", "м пог чистые 2 + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "М пог грязные 3");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_start_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_3, 5)." + (".DisplayNumber($calculation->data_priladka_laminator->length, 5)." * ".DisplayNumber($calculation->uk3, 0).") + ".DisplayNumber($calculation->waste_length_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_3."+(".$calculation->data_priladka_laminator->length."*".$calculation->uk3.")+".$calculation->waste_length_3);
    $sheet->setCellValue("E$rowindex", "м пог чистые 3 + (метраж приладки ламинации * УК3) + СтартСтопОтход 3");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 грязные 1");
    $sheet->setCellValue("B$rowindex", $calculation->area_dirty_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_1, 5)." * ".DisplayNumber($calculation->width_1, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_1."*".$calculation->width_1."/1000");
    $sheet->setCellValue("E$rowindex", "м пог грязные 1 * ширина материала 1 / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 грязные 2");
    $sheet->setCellValue("B$rowindex", $calculation->area_dirty_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_2, 5)." * ".DisplayNumber($calculation->width_2, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_2."*".$calculation->width_2."/1000");
    $sheet->setCellValue("E$rowindex", "м пог грязные 2 * ширина материала 2 / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 грязные 3");
    $sheet->setCellValue("B$rowindex", $calculation->area_dirty_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_3, 5)." * ".DisplayNumber($calculation->width_3, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_3."*".$calculation->width_3."/1000");
    $sheet->setCellValue("E$rowindex", "м пог грязные 3 * ширина материала 3 / 1000");
    
    //****************************************
    // Массы и длины плёнок
    //****************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки чистая 1");
    $sheet->setCellValue("B$rowindex", $calculation->weight_pure_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_1, 5)." * ".DisplayNumber($calculation->width_1, 5)." * ".DisplayNumber($calculation->density_1, 5)." / 1000000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_1."*".$calculation->width_1."*".$calculation->density_1."/1000000");
    $sheet->setCellValue("E$rowindex", "м пог чистые 1 * ширина материала 1 * уд вес 1 / 1000000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки чистая 2");
    $sheet->setCellValue("B$rowindex", $calculation->weight_pure_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_2, 5)." * ".DisplayNumber($calculation->width_2, 5)." * ".DisplayNumber($calculation->density_2, 5)." / 1000000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_2."*".$calculation->width_2."*".$calculation->density_2."/1000000");
    $sheet->setCellValue("E$rowindex", "м пог чистые 2 * ширина материала 2 * уд вес 2 / 1000000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки чистая 3");
    $sheet->setCellValue("B$rowindex", $calculation->weight_pure_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_3, 5)." * ".DisplayNumber($calculation->width_3, 5)." * ".DisplayNumber($calculation->density_3, 5)." / 1000000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_3."*".$calculation->width_3."*".$calculation->density_3."/1000000");
    $sheet->setCellValue("E$rowindex", "м пог чистые 3 * ширина материала 3 * уд вес 3 / 1000000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина пленки чистая 1, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pure_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_1, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_1);
    $sheet->setCellValue("E$rowindex", "м пог чистые 1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина пленки чистая 2, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pure_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_2);
    $sheet->setCellValue("E$rowindex", "м пог чистые 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина пленки чистая 3, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pure_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pure_start_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pure_start_3);
    $sheet->setCellValue("E$rowindex", "м пог чистые 3");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки грязная 1, кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight_dirty_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_dirty_1, 5)." * ".DisplayNumber($calculation->density_1, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->area_dirty_1."*".$calculation->density_1."/1000");
    $sheet->setCellValue("E$rowindex", "м2 грязные 1 * уд вес 1 / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки грязная 2, кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight_dirty_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_dirty_2, 5)." * ".DisplayNumber($calculation->density_2, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->area_dirty_2."*".$calculation->density_2."/1000");
    $sheet->setCellValue("E$rowindex", "м2 грязные 2 * уд вес 2 / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса плёнки грязная 3, кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight_dirty_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_dirty_3, 5)." * ".DisplayNumber($calculation->density_3, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->area_dirty_3."*".$calculation->density_3."/1000");
    $sheet->setCellValue("E$rowindex", "м2 грязные 3 * уд вес 3 / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина плёнки грязная 1, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_1, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_1);
    $sheet->setCellValue("E$rowindex", "м пог грязные 1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина плёнки грязная 2, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_2);
    $sheet->setCellValue("E$rowindex", "м пог грязные 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина плёнки грязная 3, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_start_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_start_3);
    $sheet->setCellValue("E$rowindex", "м пог грязные 3");
        
    //****************************************
    // Общая стоимость плёнок
    //****************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая стоимость грязная 1, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_1);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->weight_dirty_1, 5)." * ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5).") + (".DisplayNumber($calculation->weight_dirty_1, 5)." * ".DisplayNumber($calculation->eco_price_1, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->eco_currency_1, $calculation->usd, $calculation->euro), 5).")");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->weight_dirty_1."*".$calculation->price_1."*".$calculation->GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro).")+(".$calculation->weight_dirty_1."*".$calculation->eco_price_1."*".$calculation->GetCurrencyRate($calculation->eco_currency_1, $calculation->usd, $calculation->euro).")");
    $sheet->setCellValue("E$rowindex", "(масса пленки грязная 1 * цена плёнки 1 * курс валюты) + (масса пленки грязная 1 * цена из экосбора плёнки 1 * курс валюты)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая стоимость грязная 2, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_2);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->weight_dirty_2, 5)." * ".DisplayNumber($calculation->price_2, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5).") + (".DisplayNumber($calculation->weight_dirty_2, 5)." * ".DisplayNumber($calculation->eco_price_2, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->eco_currency_2, $calculation->usd, $calculation->euro), 5).")");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->weight_dirty_2."*".$calculation->price_2."*".$calculation->GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro).")+(".$calculation->weight_dirty_2."*".$calculation->eco_price_2."*".$calculation->GetCurrencyRate($calculation->eco_currency_2, $calculation->usd, $calculation->euro).")");
    $sheet->setCellValue("E$rowindex", "(масса пленки грязняа 2 * цена плёнки 2 * курс валюты) + (масса пленки грязняа 2 * цена из экосбора плёнки 2 * курс валюты)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая стоимость грязная 3, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_3);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->weight_dirty_3, 5)." * ".DisplayNumber($calculation->price_3, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5).") + (".DisplayNumber($calculation->weight_dirty_3, 5)." * ".DisplayNumber($calculation->eco_price_3, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->eco_currency_3, $calculation->usd, $calculation->euro), 5).")");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->weight_dirty_3."*".$calculation->price_3."*".$calculation->GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro).")+(".$calculation->weight_dirty_3."*".$calculation->eco_price_3."*".$calculation->GetCurrencyRate($calculation->eco_currency_3, $calculation->usd, $calculation->euro).")");
    $sheet->setCellValue("E$rowindex", "(масса пленки грязная 3 * цена плёнки 3 * курс валюты) + (масса пленки грязная 3 * цена из экосбора плёнки 3 * курс валюты)");
     
    ++$rowindex;
        
    //*****************************************
    // Время - деньги
    //*****************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Время приладки 1, ч");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_time_1);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->ink_number." * ".DisplayNumber($calculation->data_priladka->time, 5)." / 60");
    $sheet->setCellValue("D$rowindex", "=".$calculation->ink_number."*".$calculation->data_priladka->time."/60");
    $sheet->setCellValue("E$rowindex", "красочность * время приладки 1 краски / 60");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время приладки 1 Второй прогон, ч");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_time_1_run2);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->ink_run2_number." * ".DisplayNumber($calculation->data_priladka->time_run2 ?? 0, 5)." / 60 * ".$calculation->uk4);
    $sheet->setCellValue("D$rowindex", "=".$calculation->ink_run2_number."*".($calculation->data_priladka->time_run2 ?? 0)."/60*".$calculation->uk4);
    $sheet->setCellValue("E$rowindex", "красочность второй прогон * время приладки 1 краски второй прогон / 60 * УК4");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время приладки 2, ч");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_time_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka_laminator->time, 5)." * ".DisplayNumber($calculation->uk2, 0)." / 60");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka_laminator->time."*".$calculation->uk2."/60");
    $sheet->setCellValue("E$rowindex", "время приладки ламинатора * УК2 / 60");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время приладки 3, ч");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_time_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka_laminator->time, 5)." * ".DisplayNumber($calculation->uk3, 0)." / 60");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka_laminator->time."*".$calculation->uk3."/60");
    $sheet->setCellValue("E$rowindex", "время приладки ламинатора * УК3 / 60");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время печати (без приладки) 1, ч");
    $sheet->setCellValue("B$rowindex", $calculation->print_time_1);
    $sheet->setCellValue("C$rowindex", $calculation->data_machine->speed == 0 ? "|= 0" : "|= (".DisplayNumber($calculation->length_pure_start_1, 5)." + ".DisplayNumber($calculation->waste_length_1, 5)." + ". DisplayNumber($calculation->waste_length_1_run2, 5).") / ".DisplayNumber($calculation->data_machine->speed, 5)." / 1000 * ".DisplayNumber($calculation->uk1, 0));
    $sheet->setCellValue("D$rowindex", $calculation->data_machine->speed == 0 ? "=0" : "=(".$calculation->length_pure_start_1."+".$calculation->waste_length_1."+".$calculation->waste_length_1_run2.")/".$calculation->data_machine->speed."/1000*".$calculation->uk1);
    $sheet->setCellValue("E$rowindex", $calculation->data_machine->speed == 0 ? "печати нет" : "(м пог чистые 1 + СтартСтопОтход 1 + СтартСтопОтход 1 второй прогон) / скорость работы машины / 1000 * УК1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время печати (без приладки) 1 второй прогон, ч");
    $sheet->setCellValue("B$rowindex", $calculation->print_time_1_run2);
    $sheet->setCellValue("C$rowindex", $calculation->data_machine->speed_run2 == 0 ? "|= 0" : "|= (". DisplayNumber($calculation->length_pure_start_1, 5)." + ". DisplayNumber($calculation->waste_length_1, 5)." + ". DisplayNumber($calculation->waste_length_1_run2, 5).") / ".DisplayNumber($calculation->data_machine->speed_run2, 5)." / 1000 * ".$calculation->uk1." * ".$calculation->uk4);
    $sheet->setCellValue("D$rowindex", $calculation->data_machine->speed_run2 == 0 ? "=0" : "=(".$calculation->length_pure_start_1." + ".$calculation->waste_length_1." + ".$calculation->waste_length_1_run2.") / ".$calculation->data_machine->speed_run2." / 1000 * ".$calculation->uk1." * ".$calculation->uk4);
    $sheet->setCellValue("E$rowindex", $calculation->data_machine->speed_run2 == 0 ? "второго прогона нет" : "(м пог чистые 1 + СтартСтопОтход 1 + СтартСтопОтход 1 второй прогон) / скорость работы машины второй прогон / 1000 * УК1 * УК4");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время ламинации (без приладки) 2, ч");
    $sheet->setCellValue("B$rowindex", $calculation->lamination_time_2);
    $sheet->setCellValue("C$rowindex", $calculation->data_laminator->speed == 0 ? "|= 0" : "|= (".DisplayNumber($calculation->length_pure_start_2, 5)." + ".DisplayNumber($calculation->waste_length_2, 5).") / ".DisplayNumber($calculation->data_laminator->speed, 5)." / 1000 * ".DisplayNumber($calculation->uk2, 0));
    $sheet->setCellValue("D$rowindex", $calculation->data_laminator->speed == 0 ? "=0" : "=(".$calculation->length_pure_start_2."+".$calculation->waste_length_2.")/".$calculation->data_laminator->speed."/1000*".$calculation->uk2);
    $sheet->setCellValue("E$rowindex", $calculation->data_laminator->speed == 0 ? "ламинации нет" : "(м пог чистые 2 + СтартСтопОтход 2) / скорость работы ламинатора /1000 * УК2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время ламинации (без приладки) 3, ч");
    $sheet->setCellValue("B$rowindex", $calculation->lamination_time_3);
    $sheet->setCellValue("C$rowindex", $calculation->data_laminator->speed == 0 ? "|= 0" :"|= (".DisplayNumber($calculation->length_pure_start_3, 5)." + ".DisplayNumber($calculation->waste_length_3, 5).") / ".DisplayNumber($calculation->data_laminator->speed, 5)." / 1000 * ".DisplayNumber($calculation->uk3, 0));
    $sheet->setCellValue("D$rowindex", $calculation->data_laminator->speed == 0 ? "=0" :"=(".$calculation->length_pure_start_3."+".$calculation->waste_length_3.")/".$calculation->data_laminator->speed."/1000*".$calculation->uk3);
    $sheet->setCellValue("E$rowindex", $calculation->data_laminator->speed == 0 ? "ламинации нет" : "(м пог чистые 3 + СтартСтопОтход 3) / скорость работы ламинатора / 1000 * УК3");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общее время выполнения тиража 1, ч");
    $sheet->setCellValue("B$rowindex", $calculation->work_time_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->priladka_time_1, 5)." + ".DisplayNumber($calculation->print_time_1, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->priladka_time_1."+".$calculation->print_time_1);
    $sheet->setCellValue("E$rowindex", "время приладки 1 + время печати");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общее время выполнения тиража 1 второй прогон, ч");
    $sheet->setCellValue("B$rowindex", $calculation->work_time_1_run2);
    $sheet->setCellValue("C$rowindex", "|= ". DisplayNumber($calculation->priladka_time_1_run2, 5)." + ". DisplayNumber($calculation->print_time_1_run2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->priladka_time_1_run2."+".$calculation->print_time_1_run2);
    $sheet->setCellValue("E$rowindex", "время приладки 1 второй прогон + время печати второй прогон");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общее время выполнения тиража 2, ч");
    $sheet->setCellValue("B$rowindex", $calculation->work_time_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->priladka_time_2, 5)." + ".DisplayNumber($calculation->lamination_time_2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->priladka_time_2."+".$calculation->lamination_time_2);
    $sheet->setCellValue("E$rowindex", "время приладки 2 + время ламинации 1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общее время выполнения тиража 3, ч");
    $sheet->setCellValue("B$rowindex", $calculation->work_time_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->priladka_time_3, 5)." + ".DisplayNumber($calculation->lamination_time_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->priladka_time_3."+".$calculation->lamination_time_3);
    $sheet->setCellValue("E$rowindex", "время приладки 3 + время ламинации 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость выполнения тиража 1, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_time_1, 5)." * ".DisplayNumber($calculation->data_machine->price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_time_1."*".$calculation->data_machine->price);
    $sheet->setCellValue("E$rowindex", "общее время выполнения 1 * цена работы оборудования 1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость выполнения тиража 1 второй прогон, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost_1_run2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_time_1_run2, 5)." * ".DisplayNumber($calculation->data_machine->price_run2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_time_1_run2."*".$calculation->data_machine->price_run2);
    $sheet->setCellValue("E$rowindex", "общее время выполнения 1 второй прогон * цена работы оборудования 1 второй прогон");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость выполнения тиража 2, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_time_2, 5)." * ".DisplayNumber($calculation->data_laminator->price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_time_2."*".$calculation->data_laminator->price);
    $sheet->setCellValue("E$rowindex", "общее время выполнения 2 * цена работы оборудования 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость выполнения тиража 3, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_time_3, 5)." * ".DisplayNumber($calculation->data_laminator->price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_time_3."*".$calculation->data_laminator->price);
    $sheet->setCellValue("E$rowindex", "общее время выполнения 3 * цена работы оборудования 3");
    
    ++$rowindex;
        
    //****************************************
    // Расход краски
    //****************************************
    
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "Площадь запечатки, м2");
        $sheet->setCellValue("B$rowindex", $calculation->print_area);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_1, 5)." * (".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 10) / 1000");
        $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_1."*(".array_sum($calculation->stream_widths)."+10)/1000");
        $sheet->setCellValue("E$rowindex", "м пог грязные 1 * (суммарная ширина ручьёв + 10 мм) / 1000");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Площадь запечатки, м2");
        $sheet->setCellValue("B$rowindex", $calculation->print_area);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_1, 5)." * (".DisplayNumber($calculation->stream_width, 5)." * ".DisplayNumber($calculation->streams_number, 5)." + 10) / 1000");
        $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_1."*(".$calculation->stream_width."*".$calculation->streams_number."+10)/1000");
        $sheet->setCellValue("E$rowindex", "м пог грязные 1 * (ширина ручья * кол-во ручьёв + 10 мм) / 1000");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход КраскаСмеси на 1 кг краски, кг");
    $sheet->setCellValue("B$rowindex", $calculation->ink_1kg_mix_weight);
    $sheet->setCellValue("C$rowindex", "|= 1 + ".DisplayNumber($calculation->data_ink->solvent_part, 5));
    $sheet->setCellValue("D$rowindex", "=1+".$calculation->data_ink->solvent_part);
    $sheet->setCellValue("E$rowindex", "1 + расход растворителя на 1 кг краски");
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистого флексоля 82, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_flexol82_currency));
    $sheet->setCellValue("B$rowindex", $calculation->ink_flexol82_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_ink->solvent_flexol82_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_ink->solvent_flexol82_price);
    $sheet->setCellValue("E$rowindex", "цена 1 кг флексоля 82, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_flexol82_currency));
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистого этоксипропанола, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_etoxipropanol_currency));
    $sheet->setCellValue("B$rowindex", $calculation->ink_etoxypropanol_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_ink->solvent_etoxipropanol_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_ink->solvent_etoxipropanol_price);
    $sheet->setCellValue("E$rowindex", "цена 1 кг этоксипропанола, ".$calculation->GetCurrencyName($calculation->data_ink->solvent_etoxipropanol_currency));
        
    $ink_solvent_kg_price = 0;
    $ink_solvent_currency = 1;
            
    if($calculation->machine_id == PRINTER_COMIFLEX || $calculation->machine_id == PRINTER_SOMA_OPTIMA) {
        $ink_solvent_kg_price = $calculation->ink_flexol82_kg_price;
        $ink_solvent_currency = $calculation->GetCurrencyRate($calculation->data_ink->solvent_flexol82_currency, $calculation->usd, $calculation->euro);
    }
    else {
        $ink_solvent_kg_price = $calculation->ink_etoxypropanol_kg_price;
        $ink_solvent_currency = $calculation->GetCurrencyRate($calculation->data_ink->solvent_etoxipropanol_currency, $calculation->usd, $calculation->euro);
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 испарения растворителя грязная, м2");
    $sheet->setCellValue("B$rowindex", $calculation->vaporization_area_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_machine->width, 0)." * ".DisplayNumber($calculation->length_dirty_start_1, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_machine->width."*".$calculation->length_dirty_start_1."/1000");
    $sheet->setCellValue("E$rowindex", "Ширина машины * м. пог грязные / 1000");

    ++$rowindex;
    
    for($i = 1; $i <= $calculation->ink_number; $i++) {
        $ink = "ink_$i";
        $cmyk = "cmyk_$i";
        $lacquer = "lacquer_$i";
        $percent = "percent_$i";
        $price = $calculation->GetInkPrice(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_price, $calculation->data_ink->c_currency, $calculation->data_ink->m_price, $calculation->data_ink->m_currency, $calculation->data_ink->y_price, $calculation->data_ink->y_currency, $calculation->data_ink->k_price, $calculation->data_ink->k_currency, $calculation->data_ink->panton_price, $calculation->data_ink->panton_currency, $calculation->data_ink->white_price, $calculation->data_ink->white_currency, $calculation->data_ink->lacquer_glossy_price, $calculation->data_ink->lacquer_glossy_currency, $calculation->data_ink->lacquer_matte_price, $calculation->data_ink->lacquer_matte_currency);
        
        $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистой краски $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_kg_prices[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($price->value, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($price->currency, $calculation->usd, $calculation->euro), 5));
        $sheet->setCellValue("D$rowindex", "=".$price->value."*".$calculation->GetCurrencyRate($price->currency, $calculation->usd, $calculation->euro));
        $sheet->setCellValue("E$rowindex", "цена 1 кг чистой краски $i * курс валюты");
        
        $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг КраскаСмеси $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->mix_ink_kg_prices[$i]);
        $sheet->setCellValue("C$rowindex", "|= ((".DisplayNumber($calculation->ink_kg_prices[$i], 5)." * 1) + (".DisplayNumber($ink_solvent_kg_price, 5)." * ".DisplayNumber($calculation->data_ink->solvent_part, 5).")) / ".DisplayNumber($calculation->ink_1kg_mix_weight, 5));
        $sheet->setCellValue("D$rowindex", "=((".$calculation->ink_kg_prices[$i]."*1)+(".$ink_solvent_kg_price."*".$calculation->data_ink->solvent_part."))/".$calculation->ink_1kg_mix_weight);
        $sheet->setCellValue("E$rowindex", "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход КраскаСмеси $i, кг");
        $sheet->setCellValue("B$rowindex", $calculation->ink_expenses[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber($calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense), 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent], 5)." / 1000 / 100");
        $sheet->setCellValue("D$rowindex", "=".$calculation->print_area."*".$calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense)."*".get_object_vars($calculation)[$percent]."/1000/100");
        $sheet->setCellValue("E$rowindex", "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->mix_ink_kg_prices[$i], 5)." * ".DisplayNumber($calculation->ink_expenses[$i], 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->mix_ink_kg_prices[$i]."*".$calculation->ink_expenses[$i]);
        $sheet->setCellValue("E$rowindex", "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i");
        
        $sheet->setCellValue('A'.(++$rowindex), "М2 испарения растворителя чистая КраскаСмеси $i, м2");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_areas_pure[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_area_dirty, 5)." - (".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent], 5)." / 100)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_area_dirty."-(".$calculation->print_area."*".get_object_vars($calculation)[$percent]."/100)");
        $sheet->setCellValue("E$rowindex", "М2 испарения растворителя грязное - (М2 запечатки * процент запечатки / 100)");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход испарения растворителя КраскаСмеси $i, кг");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_expenses[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_areas_pure[$i], 5)." * ".DisplayNumber($calculation->data_machine->vaporization_expense, 5)." / 1000");
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_areas_pure[$i]."*".$calculation->data_machine->vaporization_expense."/1000");
        $sheet->setCellValue("E$rowindex", "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2) / 1000");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость испарения растворителя КраскаСмеси $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_costs[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_expenses[$i], 5)." * ".DisplayNumber($ink_solvent_kg_price, 5)." * ".DisplayNumber($ink_solvent_currency, 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_expenses[$i]."*".$ink_solvent_kg_price."*".$ink_solvent_currency);
        $sheet->setCellValue("E$rowindex", "Расход испарения растворителя КГ * стоимость растворителя за КГ * валюту");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход (краска + растворитель на одну краску) КраскаСмеси $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs_mix[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->ink_costs[$i], 5)." + ".DisplayNumber($calculation->vaporization_costs[$i], 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->ink_costs[$i]."+".$calculation->vaporization_costs[$i]);
        $sheet->setCellValue("E$rowindex", "Стоимость КраскаСмеси на тираж, ₽ + Стоимость испарения растворителя, ₽");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси $i финальная, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs_final[$i]);
        $sheet->setCellValue("C$rowindex", "|= ЕСЛИ(".DisplayNumber($calculation->ink_costs_mix[$i], 5)." < ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->ink_costs_mix[$i], 5).")");
        $sheet->setCellValue("D$rowindex", "=IF(".$calculation->ink_costs_mix[$i]."<".$calculation->data_ink->min_price_per_ink.",".$calculation->data_ink->min_price_per_ink.",".$calculation->ink_costs_mix[$i].")");
        $sheet->setCellValue("E$rowindex", "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - Расход (краска + растворитель на одну краску)");
    }
    
    ++$rowindex;
    
    for($i = 1; $i <= $calculation->ink_run2_number; $i++) {
        $ink_run2 = "ink_run2_$i";
        $cmyk_run2 = "cmyk_run2_$i";
        $lacquer_run2 = "lacquer_run2_$i";
        $percent_run2 = "percent_run2_$i";
        $price_run2 = $calculation->GetInkPrice(get_object_vars($calculation)[$ink_run2], get_object_vars($calculation)[$cmyk_run2], get_object_vars($calculation)[$lacquer_run2], $calculation->data_ink->c_price, $calculation->data_ink->c_currency, $calculation->data_ink->m_price, $calculation->data_ink->m_currency, $calculation->data_ink->y_price, $calculation->data_ink->y_currency, $calculation->data_ink->k_price, $calculation->data_ink->k_currency, $calculation->data_ink->panton_price, $calculation->data_ink->panton_currency, $calculation->data_ink->white_price, $calculation->data_ink->white_currency, $calculation->data_ink->lacquer_glossy_price, $calculation->data_ink->lacquer_glossy_currency, $calculation->data_ink->lacquer_matte_price, $calculation->data_ink->lacquer_matte_currency);
        
        $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистой краски $i Второй прогон, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_kg_prices_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($price_run2->value, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($price_run2->currency, $calculation->usd, $calculation->euro), 5));
        $sheet->setCellValue("D$rowindex", "=".$price_run2->value."*".$calculation->GetCurrencyRate($price_run2->currency, $calculation->usd, $calculation->euro));
        $sheet->setCellValue("E$rowindex", "цена 1 кг чистой краски $i * курс валюты");
        
        $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг КраскаСмеси $i Второй прогон, руб");
        $sheet->setCellValue("B$rowindex", $calculation->mix_ink_kg_prices_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ((".DisplayNumber($calculation->ink_kg_prices_run2[$i], 5)." * 1) + (".DisplayNumber($ink_solvent_kg_price, 5)." * ".DisplayNumber($calculation->data_ink->solvent_part, 5).")) / ".DisplayNumber($calculation->ink_1kg_mix_weight, 5));
        $sheet->setCellValue("D$rowindex", "=((".$calculation->ink_kg_prices_run2[$i]."*1)+(".$ink_solvent_kg_price."*".$calculation->data_ink->solvent_part."))/".$calculation->ink_1kg_mix_weight);
        $sheet->setCellValue("E$rowindex", "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход КраскаСмеси $i Второй прогон, кг");
        $sheet->setCellValue("B$rowindex", $calculation->ink_expenses_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber($calculation->GetInkExpense(get_object_vars($calculation)[$ink_run2], get_object_vars($calculation)[$cmyk_run2], get_object_vars($calculation)[$lacquer_run2], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense), 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent_run2], 5)." / 1000 / 100");
        $sheet->setCellValue("D$rowindex", "=".$calculation->print_area."*".$calculation->GetInkExpense(get_object_vars($calculation)[$ink_run2], get_object_vars($calculation)[$cmyk_run2], get_object_vars($calculation)[$lacquer_run2], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense)."*".get_object_vars($calculation)[$percent_run2]."/1000/100");
        $sheet->setCellValue("E$rowindex", "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси Второй прогон $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->mix_ink_kg_prices_run2[$i], 5)." * ".DisplayNumber($calculation->ink_expenses_run2[$i], 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->mix_ink_kg_prices_run2[$i]."*".$calculation->ink_expenses_run2[$i]);
        $sheet->setCellValue("E$rowindex", "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i");
        
        $sheet->setCellValue('A'.(++$rowindex), "М2 испарения растворителя чистая КраскаСмеси Второй прогон $i, м2");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_areas_pure_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_area_dirty, 5)." - (".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent_run2], 5)." / 100)");
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_area_dirty."-(".$calculation->print_area."*".get_object_vars($calculation)[$percent_run2]."/100)");
        $sheet->setCellValue("E$rowindex", "М2 испарения растворителя грязное - (М2 запечатки * процент запечатки / 100)");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход испарения растворителя КраскаСмеси Второй прогон $i, кг");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_expenses_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_areas_pure_run2[$i], 5)." * ".DisplayNumber($calculation->data_machine->vaporization_expense, 5)." / 1000");
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_areas_pure_run2[$i]."*".$calculation->data_machine->vaporization_expense."/1000");
        $sheet->setCellValue("E$rowindex", "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2) / 1000");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость испарения растворителя КраскаСмеси $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->vaporization_costs_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_expenses_run2[$i], 5)." * ".DisplayNumber($ink_solvent_kg_price, 5)." * ".DisplayNumber($ink_solvent_currency, 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_expenses_run2[$i]."*".$ink_solvent_kg_price."*".$ink_solvent_currency);
        $sheet->setCellValue("E$rowindex", "Расход испарения растворителя КГ * стоимость растворителя за КГ * валюту");
        
        $sheet->setCellValue('A'.(++$rowindex), "Расход (краска + растворитель на одну краску) КраскаСмеси $i Второй прогон, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs_mix_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->ink_costs_run2[$i], 5)." + ".DisplayNumber($calculation->vaporization_costs_run2[$i], 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->ink_costs_run2[$i]."+".$calculation->vaporization_costs_run2[$i]);
        $sheet->setCellValue("E$rowindex", "Стоимость КраскаСмеси на тираж, ₽ + Стоимость испарения растворителя, ₽");
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси $i финальная Второй прогон, руб");
        $sheet->setCellValue("B$rowindex", $calculation->ink_costs_final_run2[$i]);
        $sheet->setCellValue("C$rowindex", "|= ЕСЛИ(".DisplayNumber($calculation->ink_costs_mix_run2[$i], 5)." < ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->ink_costs_mix_run2[$i], 5).")");
        $sheet->setCellValue("D$rowindex", "=IF(".$calculation->ink_costs_mix_run2[$i]."<".$calculation->data_ink->min_price_per_ink.",".$calculation->data_ink->min_price_per_ink.",".$calculation->ink_costs_mix_run2[$i].")");
        $sheet->setCellValue("E$rowindex", "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - Расход (краска + растворитель на одну краску)");
    }

    ++$rowindex;
        
    //********************************************
    // Расход клея
    //********************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход КлеяСмеси на 1 кг клея, кг");
    $sheet->setCellValue("B$rowindex", $calculation->glue_kg_weight);
    $sheet->setCellValue("C$rowindex", "|= 1 + ".DisplayNumber($calculation->data_glue->solvent_part, 5));
    $sheet->setCellValue("D$rowindex", "=1+".$calculation->data_glue->solvent_part);
    $sheet->setCellValue("E$rowindex", "1 + расход растворителя на 1 кг клея");
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистого клея, руб");
    $sheet->setCellValue("B$rowindex", $calculation->glue_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_glue->glue_price, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->data_glue->glue_currency, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_glue->glue_price."*".$calculation->GetCurrencyRate($calculation->data_glue->glue_currency, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена 1 кг клея * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистого растворителя для клея, руб");
    $sheet->setCellValue("B$rowindex", $calculation->glue_solvent_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_glue->solvent_price, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->data_glue->solvent_currency, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_glue->solvent_price."*".$calculation->GetCurrencyRate($calculation->data_glue->solvent_currency, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена 1 кг растворителя для клея * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг КлеяСмеси, руб");
    $sheet->setCellValue("B$rowindex", $calculation->mix_glue_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ((1 * ".DisplayNumber($calculation->glue_kg_price, 5).") + (".DisplayNumber($calculation->data_glue->solvent_part, 5)." * ".DisplayNumber($calculation->glue_solvent_kg_price, 5).")) / ".DisplayNumber($calculation->glue_kg_weight, 5));
    $sheet->setCellValue("D$rowindex", "=((1*".$calculation->glue_kg_price.")+(".$calculation->data_glue->solvent_part."*".$calculation->glue_solvent_kg_price."))/".$calculation->glue_kg_weight);
    $sheet->setCellValue("E$rowindex", "((1 * цена 1 кг чистого клея) + (расход растворителя на 1 кг клея * цена 1 кг чистого растворителя)) / расход КлеяСмеси на 1 кг клея");
    
    $sheet->setCellValue('A'.(++$rowindex), "Площадь заклейки 2, м2");
    $sheet->setCellValue("B$rowindex", $calculation->glue_area2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_2, 5)." * ".DisplayNumber($calculation->lamination_roller_width, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_2."*".$calculation->lamination_roller_width."/1000");
    $sheet->setCellValue("E$rowindex", "м пог грязные 2 * ширина ламинирующего вала / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Площадь заклейки 3, м2");
    $sheet->setCellValue("B$rowindex", $calculation->glue_area3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_dirty_3, 5)." * ".DisplayNumber($calculation->lamination_roller_width, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_dirty_3."*".$calculation->lamination_roller_width."/1000");
    $sheet->setCellValue("E$rowindex", "м пог грязные 3 * ширина ламинирующего вала / 1000");
        
    $glue_expense2_formula = DisplayNumber($calculation->glue_area2, 5)." * ".DisplayNumber($calculation->data_glue->glue_expense, 5)." / 1000";
    $glue_expense2_result = $calculation->glue_area2."*".$calculation->data_glue->glue_expense."/1000";
    $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси в 1 м2 / 1000";
        
    if((strlen($calculation->film_1) > 3 && substr($calculation->film_1, 0, 3) == "Pet") || (strlen($calculation->film_2) > 3 && substr($calculation->film_2, 0, 3) == "Pet")) {
        $glue_expense2_formula = DisplayNumber($calculation->glue_area2, 5)." * ".DisplayNumber($calculation->data_glue->glue_expense_pet, 5)." / 1000";
        $glue_expense2_result = $calculation->glue_area2."*".$calculation->data_glue->glue_expense_pet."/1000";
        $glue_expense2_comment = "площадь заклейки 2 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход КлеяСмеси 2, кг");
    $sheet->setCellValue("B$rowindex", $calculation->glue_expense2);
    $sheet->setCellValue("C$rowindex", "|= ".$glue_expense2_formula);
    $sheet->setCellValue("D$rowindex", "=".$glue_expense2_result);
    $sheet->setCellValue("E$rowindex", $glue_expense2_comment);
        
    $glue_expense3_formula = DisplayNumber($calculation->glue_area3, 5)." * ".DisplayNumber($calculation->data_glue->glue_expense, 5)." / 1000";
    $glue_expense3_result = $calculation->glue_area3."*".$calculation->data_glue->glue_expense."/1000";
    $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси в 1 м2 / 1000";
    
    if((strlen($calculation->film_2) > 3 && substr($calculation->film_2, 0, 3) == "Pet") || (strlen($calculation->film_3) > 3 && substr($calculation->film_3, 0, 3) == "Pet")) {
        $glue_expense3_formula = DisplayNumber($calculation->glue_area3, 5)." * ".DisplayNumber($calculation->data_glue->glue_expense_pet, 5)." / 1000";
        $glue_expense3_result = $calculation->glue_area3."*".$calculation->data_glue->glue_expense_pet."/1000";
        $glue_expense3_comment = "площадь заклейки 3 * расход КлеяСмеси для ПЭТ в 1 м2 / 1000";
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход КлеяСмеси 3, кг");
    $sheet->setCellValue("B$rowindex", $calculation->glue_expense3);
    $sheet->setCellValue("C$rowindex", "|= ".$glue_expense3_formula);
    $sheet->setCellValue("D$rowindex", "=".$glue_expense3_result);
    $sheet->setCellValue("E$rowindex", $glue_expense3_comment);
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость КлеяСмеси 2, руб");
    $sheet->setCellValue("B$rowindex", $calculation->glue_cost2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->glue_expense2, 5)." * ".DisplayNumber($calculation->mix_glue_kg_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->glue_expense2."*".$calculation->mix_glue_kg_price);
    $sheet->setCellValue("E$rowindex", "расход КлеяСмеси 2 * цена 1 кг КлеяСмеси");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость КлеяСмеси 3, руб");
    $sheet->setCellValue("B$rowindex", $calculation->glue_cost3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->glue_expense3, 5)." * ".DisplayNumber($calculation->mix_glue_kg_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->glue_expense3."*".$calculation->mix_glue_kg_price);
    $sheet->setCellValue("E$rowindex", "расход КлеяСмеси 3 * цена 1 кг КлеяСмеси");
        
    ++$rowindex;
        
    //***********************************
    // Стоимость форм
    //***********************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Высота форм, м");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_height);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->raport, 5)." + 20) / 1000");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->raport."+20)/1000");
    $sheet->setCellValue("E$rowindex", "(рапорт + 20 мм) / 1000");
    
    if(empty($calculation->stream_width)) {
        $sheet->setCellValue('A'.(++$rowindex), "Ширина форм, м");
        $sheet->setCellValue("B$rowindex", $calculation->cliche_width);
        $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber(array_sum($calculation->stream_widths), 5)." + 20 + ".((!empty($calculation->ski_1) && $calculation->ski_1 == SKI_NO) ? 0 : 20).") / 1000");
        $sheet->setCellValue("D$rowindex", "=(".array_sum($calculation->stream_widths)."+20+".((!empty($calculation->ski_1) && $calculation->ski_1 == SKI_NO) ? 0 : 20).")/1000");
        $sheet->setCellValue("E$rowindex", "(суммарная ширина ручьёв + 20 мм, если есть лыжи (стандартные или нестандартные), то ещё + 20 мм) / 1000");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Ширина форм, м");
        $sheet->setCellValue("B$rowindex", $calculation->cliche_width);
        $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->stream_width, 5)." + 20 + ".((!empty($calculation->ski_1) && $calculation->ski_1 == SKI_NO) ? 0 : 20).") / 1000");
        $sheet->setCellValue("D$rowindex", "=(".$calculation->streams_number."*".$calculation->stream_width."+20+".((!empty($calculation->ski_1) && $calculation->ski_1 == SKI_NO) ? 0 : 20).")/1000");
        $sheet->setCellValue("E$rowindex", "(кол-во ручьёв * ширина ручьёв + 20 мм, если есть лыжи (стандартные или нестандартные), то ещё + 20 мм) / 1000");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Площадь форм, м2");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_area);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_height, 5)." * ".DisplayNumber($calculation->cliche_width, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_height."*".$calculation->cliche_width);
    $sheet->setCellValue("E$rowindex", "высота форм * ширина форм");
    
    $sheet->setCellValue('A'.(++$rowindex), "Количество новых форм");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_new_number);
    $sheet->setCellValue("C$rowindex", "");
    $sheet->setCellValue("D$rowindex", "");
    $sheet->setCellValue("E$rowindex", "");
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        $cliche = "cliche_$i";
            
        $cliche_sm_price = 0;
        $cliche_currency = "";
            
        switch (get_object_vars($calculation)[$cliche]) {
            case CLICHE_FLINT:
                $cliche_sm_price = $calculation->data_cliche->flint_price;
                $cliche_currency = $calculation->data_cliche->flint_currency;
                break;
                
            case CLICHE_KODAK:
                $cliche_sm_price = $calculation->data_cliche->kodak_price;
                $cliche_currency = $calculation->data_cliche->kodak_currency;
                break;
        }
        
        $sheet->setCellValue('A'.(++$rowindex), "Цена формы $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->cliche_costs[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_area, 5)." * ".DisplayNumber($cliche_sm_price, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($cliche_currency, $calculation->usd, $calculation->euro), 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_area."*".$cliche_sm_price."*".$calculation->GetCurrencyRate($cliche_currency, $calculation->usd, $calculation->euro));
        $sheet->setCellValue("E$rowindex", "площадь формы, м2 * цена формы за 1 м2 * курс валюты");
    }
        
    ++$rowindex;
        
    //*******************************************
    // Стоимость скотча
    //*******************************************
    
    $scotch_formula = "";
    $scotch_result = "";
    $scotch_comment = "";
        
    for($i = 1; $i <= $calculation->ink_number; $i++) {
        if(!empty($scotch_formula)) {
            $scotch_formula .= " + ";
        }
        
        if(!empty($scotch_result)) {
            $scotch_result .= "+";
        }
            
        if(!empty($scotch_comment)) {
            $scotch_comment .= " + ";
        }
        
        $scotch_formula .= DisplayNumber($calculation->scotch_costs[$i], 5);
        $scotch_result .= $calculation->scotch_costs[$i];
        $scotch_comment .= "стоимость скотча цвет $i";
        
        $cliche_area = $calculation->cliche_area;
        
        $sheet->setCellValue('A'.(++$rowindex), "Стоимость скотча Цвет $i, руб");
        $sheet->setCellValue("B$rowindex", $calculation->scotch_costs[$i]);
        $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_area, 5)." * ".DisplayNumber($calculation->data_cliche->scotch_price, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->data_cliche->scotch_currency, $calculation->usd, $calculation->euro), 5));
        $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_area."*".$calculation->data_cliche->scotch_price."*".$calculation->GetCurrencyRate($calculation->data_cliche->scotch_currency, $calculation->usd, $calculation->euro));
        $sheet->setCellValue("E$rowindex", "площадь формы цвет $i, м2 * цена скотча за м2 * курс валюты");
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая себестоимость скотча, руб");
    $sheet->setCellValue("B$rowindex", $calculation->scotch_cost);
    $sheet->setCellValue("C$rowindex", "|= ".$scotch_formula);
    $sheet->setCellValue("D$rowindex", "=".$scotch_result);
    $sheet->setCellValue("E$rowindex", $scotch_comment);    
        
    ++$rowindex;
        
    //*******************************************
    // Наценка
    //*******************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Наценка на тираж, %");
    $sheet->setCellValue("B$rowindex", $calculation->extracharge);
    
    $sheet->setCellValue('A'.(++$rowindex), "Наценка на ПФ, %");
    $sheet->setCellValue("B$rowindex", $calculation->extracharge_cliche);
    
    ++$rowindex;
        
    //*******************************************
    // Данные для правой панели
    //*******************************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая стоимость всех плёнок, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_cost_1, 5)." + ".DisplayNumber($calculation->film_cost_2, 5)." + ".DisplayNumber($calculation->film_cost_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_cost_1."+".$calculation->film_cost_2."+".$calculation->film_cost_3);
    $sheet->setCellValue("E$rowindex", "стоимость плёнки грязная 1 + стоимость плёнки грязная 2 + стоимость плёнки грязная 3");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общая стоимость работ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_cost_1, 5)." + ".DisplayNumber($calculation->work_cost_2, 5)." + ".DisplayNumber($calculation->work_cost_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_cost_1."+".$calculation->work_cost_2."+".$calculation->work_cost_3);
    $sheet->setCellValue("E$rowindex", "стоимость выполнения тиража 1 + стоимость выполнения тиража 2 + стоимость выполнения тиража 3");
        
    $total_ink_cost_formula = "";
    $total_ink_cost_result = "";
    $total_ink_expense_formula = "";
    $total_ink_expense_result = "";
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        if(!empty($total_ink_cost_formula)) {
            $total_ink_cost_formula .= " + ";
            $total_ink_cost_result .= "+";
        }
        $total_ink_cost_formula .= DisplayNumber($calculation->ink_costs_final[$i], 5);
        $total_ink_cost_result .= $calculation->ink_costs_final[$i];
            
        if(!empty($total_ink_expense_formula)) {
            $total_ink_expense_formula .= " + ";
            $total_ink_expense_result .= "+";
        }
        $total_ink_expense_formula .= DisplayNumber($calculation->ink_expenses[$i], 5);
        $total_ink_expense_result .= $calculation->ink_expenses[$i];
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость краски, руб");
    $sheet->setCellValue("B$rowindex", $calculation->ink_cost);
    $sheet->setCellValue("C$rowindex", "|= ".$total_ink_cost_formula);
    $sheet->setCellValue("D$rowindex", "=".$total_ink_cost_result);
    $sheet->setCellValue("E$rowindex", "Сумма стоимость всех красок");
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход краски, кг");
    $sheet->setCellValue("B$rowindex", $calculation->ink_expense);
    $sheet->setCellValue("C$rowindex", "|= ".$total_ink_expense_formula);
    $sheet->setCellValue("D$rowindex", "=".$total_ink_expense_result);
    $sheet->setCellValue("E$rowindex", "Сумма расход всех красок");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость клея, руб");
    $sheet->setCellValue("B$rowindex", $calculation->glue_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->glue_cost2, 5)." + ".DisplayNumber($calculation->glue_cost3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->glue_cost2."+".$calculation->glue_cost3);
    $sheet->setCellValue("E$rowindex", "стоимость клея 2 + стоимость клея 3");
        
    $total_cliche_cost_formula = "";
    $total_cliche_cost_result = "";
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        if(!empty($total_cliche_cost_formula)) {
            $total_cliche_cost_formula .= " + ";
        }
        
        if(!empty($total_cliche_cost_result)) {
            $total_cliche_cost_result .= "+";
        }
        
        $total_cliche_cost_formula .= DisplayNumber($calculation->cliche_costs[$i], 5);
        $total_cliche_cost_result .= $calculation->cliche_costs[$i];
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость ПФ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_cost);
    $sheet->setCellValue("C$rowindex", "|= ".$total_cliche_cost_formula);
    $sheet->setCellValue("D$rowindex", "=".$total_cliche_cost_result);
    $sheet->setCellValue("E$rowindex", "сумма стоимости всех форм");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_cost, 5)." + ".DisplayNumber($calculation->work_cost, 5)." + ".DisplayNumber($calculation->ink_cost, 5)." + ".DisplayNumber($calculation->glue_cost, 5)." + (".DisplayNumber($calculation->cliche_cost, 5)." * ".DisplayNumber($calculation->ukpf, 0).") + ".DisplayNumber($calculation->scotch_cost, 5)." + (".$calculation->quantity." * ".DisplayNumber($calculation->extra_expense, 5).")");
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_cost."+".$calculation->work_cost."+".$calculation->ink_cost."+".$calculation->glue_cost."+(".$calculation->cliche_cost."*".$calculation->ukpf.")+".$calculation->scotch_cost."+(".$calculation->quantity."*".$calculation->extra_expense.")");
    $sheet->setCellValue("E$rowindex", "стоимость плёнки + стоимость работы + стоимость краски + стоимость клея + (стоимость форм * УКПФ) + стоимость скотча + (объём заказа, кг/шт * доп. расходы на кг / шт)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость за ". $calculation->GetUnitName($calculation->unit).", руб");
    $sheet->setCellValue("B$rowindex", $calculation->cost_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cost, 5)." / ".DisplayNumber($calculation->quantity, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cost."/".$calculation->quantity);
    $sheet->setCellValue("E$rowindex", "себестоимость / размер тиража");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cost, 5)." * (1 + (".DisplayNumber($calculation->extracharge, 5)." / 100))");
    $sheet->setCellValue("D$rowindex", "=".$calculation->cost."*(1+(".$calculation->extracharge."/100))");
    $sheet->setCellValue("E$rowindex", "себестоимость * (1 + (наценка на тираж / 100))");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость за ".$calculation->GetUnitName($calculation->unit).", руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cost_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->shipping_cost, 5)." / ".DisplayNumber($calculation->quantity, 0));
    $sheet->setCellValue("D$rowindex", "=".$calculation->shipping_cost."/".$calculation->quantity);
    $sheet->setCellValue("E$rowindex", "отгрузочная стоимость / размер тиража");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->shipping_cost, 5)." - ".DisplayNumber($calculation->cost, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->shipping_cost."-".$calculation->cost);
    $sheet->setCellValue("E$rowindex", "отгрузочная стоимость - себестоимость");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль за ".$calculation->GetUnitName($calculation->unit).", руб");
    $sheet->setCellValue("B$rowindex", $calculation->income_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->shipping_cost_per_unit, 5)." - ".DisplayNumber($calculation->cost_per_unit, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->shipping_cost_per_unit."-".$calculation->cost_per_unit);
    $sheet->setCellValue("E$rowindex", "отгрузочная стоимость за ". $calculation->GetUnitName($calculation->unit)." - себестоимость за ". $calculation->GetUnitName($calculation->unit));
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость ПФ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cliche_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_cost, 5)." * (1 + (".DisplayNumber($calculation->extracharge_cliche, 5)." / 100)) * ((".$calculation->ukpf." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_cost."*(1+(".$calculation->extracharge_cliche."/100))*((".$calculation->ukpf."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "сумма стоимости всех форм * (1 + (наценка на ПФ / 100)) * CusPayPF * ((КоэфПФ - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль ПФ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income_cliche);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->shipping_cliche_cost, 5)." - ".DisplayNumber($calculation->cliche_cost, 5).") * ((".$calculation->ukpf." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->shipping_cliche_cost."-".$calculation->cliche_cost.")*((".$calculation->ukpf."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "(отгрузочная стоимость ПФ - себестоимость ПФ) * ((КоэфПФ - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общий вес всех плёнок с приладкой, кг");
    $sheet->setCellValue("B$rowindex", $calculation->total_weight_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty_1, 5)." + ".DisplayNumber($calculation->weight_dirty_2, 5)." + ".DisplayNumber($calculation->weight_dirty_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty_1."+".$calculation->weight_dirty_2."+".$calculation->weight_dirty_3);
    $sheet->setCellValue("E$rowindex", "масса плёнки грязная 1 + масса плёнки грязная 2 + масса плёнки грязная 3");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость за кг 1, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_per_unit_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->price_1."*".CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена плёнки 1 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость за кг 2, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_per_unit_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->price_2, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->price_2."*".CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена плёнки 2 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость за кг 3, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_per_unit_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->price_3, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->price_3."*".CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена плёнки 3 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 1, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_cost_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_waste_weight_1, 5)." * ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_waste_weight_1."*".$calculation->price_1."*".CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "отходы 1, кг * цена плёнки 1 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 2, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_cost_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_waste_weight_2, 5)." * ".DisplayNumber($calculation->price_2, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_waste_weight_2."*".$calculation->price_2."*".CalculationBase::GetCurrencyRate($calculation->currency_2, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "отходы 2, кг * цена плёнки 2 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 3, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_cost_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_waste_weight_3, 5)." * ".DisplayNumber($calculation->price_3, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_waste_weight_3."*".$calculation->price_3."*".CalculationBase::GetCurrencyRate($calculation->currency_3, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "отходы 3, кг * цена плёнки 3 * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 1, кг");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_weight_1);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty_1, 5)." - ".DisplayNumber($calculation->weight_pure_1, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty_1."-".$calculation->weight_pure_1);
    $sheet->setCellValue("E$rowindex", "масса плёнки грязная 1 - масса плёнки чистая 1");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 2, кг");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_weight_2);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty_2, 5)." - ".DisplayNumber($calculation->weight_pure_2, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty_2."-".$calculation->weight_pure_2);
    $sheet->setCellValue("E$rowindex", "масса плёнки грязная 2 - масса плёнки чистая 2");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы 3, кг");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_weight_3);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty_3, 5)." - ".DisplayNumber($calculation->weight_pure_3, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty_3."-".$calculation->weight_pure_3);
    $sheet->setCellValue("E$rowindex", "масса плёнки грязная 3 - масса плёнки чистая 3");
    
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
<!DOCTYPE html>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>