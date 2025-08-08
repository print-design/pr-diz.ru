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
    
    /*if(empty($calculation->stream_width)) {
        foreach($calculation->stream_widths as $key => $value) {
            $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Ширина ручья $key, мм", $value, "", ""));
        }
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Ширина ручья, мм", $calculation->stream_width, "", ""));
    }
    
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Количество ручьёв", $calculation->streams_number, "", ""));
        
    if(!empty($calculation->machine_id)) {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Рапорт", DisplayNumber($calculation->raport, 5), "", ""));
    }
        
    if($calculation->laminations_number > 0) {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Ширина ламинирующего вала, мм", DisplayNumber($calculation->lamination_roller_width, 5), "", ""));
    }
        
    if(!empty($calculation->machine_id)) {
        for($i = 1; $i <= $calculation->ink_number; $i++) {
            $ink = "ink_$i";
            $color = "color_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            $cliche = "cliche_$i";
            $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Краска $i:", $calculation->GetInkName(get_object_vars($calculation)[$ink]).(empty(get_object_vars($calculation)[$color]) ? "" : " ".get_object_vars($calculation)[$color]).(empty(get_object_vars($calculation)[$cmyk]) ? "" : " ".get_object_vars($calculation)[$cmyk])." ".get_object_vars($calculation)[$percent]."% ".$calculation->GetClicheName(get_object_vars($calculation)[$cliche]), "", ""));
        }
    }
        
    if($calculation->cliche_in_price == 1) {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Включить ПФ в себестоимость", "", "", ""));
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Не включать ПФ в себестоимость", "", "", ""));
    }
        
    if($calculation->customer_pays_for_cliche == 1) {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Заказчик платит за ПФ", "", "", ""));
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Мы платим за ПФ", "", "", ""));
    }
    
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("Дополнительные расходы с ".$calculation->GetUnitName($calculation->unit).", руб", DisplayNumber($calculation->extra_expense, 5), "", ""));
    
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("", "", "", ""));
        
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
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("УК1", $calculation->uk1, "", "нет печати - 0, есть печать - 1"));
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("УК2", $calculation->uk2, "", "нет ламинации - 0, есть ламинация - 1"));
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("УК3", $calculation->uk3, "", "нет второй ламинации - 0, есть вторая ламинация - 1"));
    $sheet->setCellValue('A'.(++$rowindex), ""); $sheet->setCellValue("B$rowindex", $calculation); array_push($file_data, array("УКПФ", $calculation->ukpf, "", "ПФ не включен в себестоимость - 0, ПФ включен в себестоимость - 1"));*/
    
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