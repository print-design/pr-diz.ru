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
        
    /*if(!empty($calculation->machine_id)) {
        array_push($file_data, array("Машина", PRINTER_NAMES[$calculation->machine_id], "", ""));
    }
        
    if(!empty($calculation->laminator_id)) {
        array_push($file_data, array("Ламинатор", LAMINATOR_NAMES[$calculation->laminator_id], "", ""));
    }
        
    array_push($file_data, array("Размер тиража", $calculation->quantity.' '. $calculation->GetUnitName($calculation->unit), "", ""));
    array_push($file_data, array("Марка 1", $calculation->film_1, "", ""));
    array_push($file_data, array("Толщина 1, мкм", $calculation->thickness_1, "", ""));
    array_push($file_data, array("Плотность 1, г/м2", DisplayNumber($calculation->density_1, 5), "", ""));
    array_push($file_data, array("Лыжи 1", $calculation->GetSkiName($calculation->ski_1), "", ""));
    if($calculation->ski_1 == SKI_NONSTANDARD) array_push ($file_data, array("Ширина плёнки 1, мм", DisplayNumber ($calculation->width_ski_1, 5), "", ""));
    if($calculation->customers_material_1 == true) array_push ($file_data, array("Материал заказчика 1", "", "", ""));
    else array_push ($file_data, array("Цена 1", DisplayNumber ($calculation->price_1, 5)." ". $calculation->GetCurrencyName($calculation->currency_1).($calculation->currency_1 == CURRENCY_USD ? " (". DisplayNumber ($calculation->price_1 * $calculation->usd, 5)." руб)" : "").($calculation->currency_1 == CURRENCY_EURO ? " (". DisplayNumber ($calculation->price_1 * $calculation->euro, 5)." руб)" : ""), "", ""));
    array_push($file_data, array("Экосбор 1", DisplayNumber($calculation->eco_price_1, 5)." ".$calculation->GetCurrencyName($calculation->eco_currency_1).($calculation->eco_currency_1 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_1 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_1 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_1 * $calculation->euro, 5)." руб)" : ""), "", ""));
        
    if($calculation->laminations_number > 0) {
        array_push($file_data, array("Марка 2", $calculation->film_2, "", ""));
        array_push($file_data, array("Толщина 2, мкм", $calculation->thickness_2, "", ""));
        array_push($file_data, array("Плотность 2, г/м2", DisplayNumber($calculation->density_2, 5), "", ""));
        array_push($file_data, array("Лыжи 2", $calculation->GetSkiName($calculation->ski_2), "", ""));
        if($calculation->ski_2 == SKI_NONSTANDARD) array_push($file_data, array("Ширина пленки 2, мм", DisplayNumber ($calculation->width_ski_2, 5), "", ""));
        if($calculation->customers_material_2 == true) array_push ($file_data, array("Материал заказчика 2", "", "", ""));
        else array_push ($file_data, array("Цена 2", DisplayNumber ($calculation->price_2, 5)." ". $calculation->GetCurrencyName($calculation->currency_2).($calculation->currency_2 == CURRENCY_USD ? " (".DisplayNumber ($calculation->price_2 * $calculation->usd, 5)." руб)" : "").($calculation->currency_2 == CURRENCY_EURO ? " (".DisplayNumber ($calculation->price_2 * $calculation->euro, 5)." руб)" : ""), "", ""));
        array_push($file_data, array("Экосбор 2", DisplayNumber($calculation->eco_price_2, 5)." ".$calculation->GetCurrencyName($calculation->eco_currency_2).($calculation->eco_currency_2 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_2 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_2 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_2 * $calculation->euro, 5)." руб)" : ""), "", ""));
    }
        
    if($calculation->laminations_number > 1) {
        array_push($file_data, array("Марка 3", $calculation->film_3, "", ""));
        array_push($file_data, array("Толщина 3, мкм", $calculation->thickness_3, "", ""));
        array_push($file_data, array("Плотность 3, г/м2", DisplayNumber($calculation->density_3, 5), "", ""));
        array_push($file_data, array("Лыжи 3", $calculation->GetSkiName($calculation->ski_3), "", ""));
        if($calculation->ski_3 == SKI_NONSTANDARD) array_push ($file_data, array("Ширина плёнки 3, мм", DisplayNumber ($calculation->width_ski_3, 5), "", ""));
        if($calculation->customers_material_3 == true) array_push ($file_data, array("Материал заказчика (лам 2)", "", "", ""));
        else array_push ($file_data, array("Цена 3", DisplayNumber ($calculation->price_3, 5)." ". $calculation->GetCurrencyName($calculation->currency_3).($calculation->currency_3 == CURRENCY_USD ? " (".DisplayNumber ($calculation->price_3 * $calculation->usd, 5)." руб)" : "").($calculation->currency_3 == CURRENCY_EURO ? " (".DisplayNumber ($calculation->price_3 * $calculation->euro, 5)." руб)" : ""), "", ""));
        array_push($file_data, array("Экосбор 3", DisplayNumber($calculation->eco_price_3, 5)." ".$calculation->GetCurrencyName($calculation->eco_currency_3).($calculation->eco_currency_3 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_3 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_3 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_3 * $calculation->euro, 5)." руб)" : ""), "", ""));
    }
    
    if(empty($calculation->stream_width)) {
        foreach($calculation->stream_widths as $key => $value) {
            array_push($file_data, array("Ширина ручья $key, мм", $value, "", ""));
        }
    }
    else {
        array_push($file_data, array("Ширина ручья, мм", $calculation->stream_width, "", ""));
    }
    
    array_push($file_data, array("Количество ручьёв", $calculation->streams_number, "", ""));
        
    if(!empty($calculation->machine_id)) {
        array_push($file_data, array("Рапорт", DisplayNumber($calculation->raport, 5), "", ""));
    }
        
    if($calculation->laminations_number > 0) {
        array_push($file_data, array("Ширина ламинирующего вала, мм", DisplayNumber($calculation->lamination_roller_width, 5), "", ""));
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
        
    array_push($file_data, array("Дополнительные расходы с ".$calculation->GetUnitName($calculation->unit).", руб", DisplayNumber($calculation->extra_expense, 5), "", ""));
    
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
    array_push($file_data, array("УКПФ", $calculation->ukpf, "", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1"));*/
    
    // Результаты вычислений
    
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