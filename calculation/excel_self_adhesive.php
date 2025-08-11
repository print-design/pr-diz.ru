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
    $sheet->setCellValue('A'.(++$rowindex), "Машина"); $sheet->setCellValue("B$rowindex", PRINTER_NAMES[$calculation->machine_id]);
    $sheet->setCellValue('A'.(++$rowindex), "Количество тиражей"); $sheet->setCellValue("B$rowindex", count($calculation->quantities));
    
    $i = 1;
    foreach($calculation->quantities as $key => $quantity) {
        $sheet->setCellValue('A'.(++$rowindex), "Тираж $i, шт"); $sheet->setCellValue("B$rowindex", intval($quantity));
        $i++;
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Суммарное количество этикеток, шт"); $sheet->setCellValue("B$rowindex", $calculation->quantity);
    $sheet->setCellValue('A'.(++$rowindex), "Марка"); $sheet->setCellValue("B$rowindex", $calculation->film_1);
    $sheet->setCellValue('A'.(++$rowindex), "Толщина"); $sheet->setCellValue("B$rowindex", $calculation->thickness_1);
    $sheet->setCellValue('A'.(++$rowindex), "Плотность"); $sheet->setCellValue("B$rowindex", $calculation->density_1);
    $sheet->setCellValue('A'.(++$rowindex), "Лыжи"); $sheet->setCellValue("B$rowindex", $calculation->GetSkiName($calculation->ski_1));
    
    if($calculation->ski_1 == SKI_NONSTANDARD) {
        $sheet->setCellValue('A'.(++$rowindex), "Ширина материала, мм"); $sheet->setCellValue("B$rowindex", $calculation->width_ski_1);
    }
    
    if($calculation->customers_material_1 == true) {
        $sheet->setCellValue('A'.(++$rowindex), "Материал заказчика");
    }
    else {
        $sheet->setCellValue('A'.(++$rowindex), "Цена"); $sheet->setCellValue("B$rowindex", $calculation->price_1); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName ($calculation->currency_1).($calculation->currency_1 == CURRENCY_USD ? " (".DisplayNumber ($calculation->price_1 * $calculation->usd, 5)." руб)" : "").($calculation->currency_1 == CURRENCY_EURO ? " (".DisplayNumber ($calculation->price_1 * $calculation->euro, 5)." руб)" : ""));
    }
    
    $sheet->setCellValue('A'.(++$rowindex), "Экосбор"); $sheet->setCellValue("B$rowindex", $calculation->eco_price_1); $sheet->setCellValue("C$rowindex", $calculation->GetCurrencyName($calculation->eco_currency_1).($calculation->eco_currency_1 == CURRENCY_USD ? " (".DisplayNumber($calculation->eco_price_1 * $calculation->usd, 5)." руб)" : "").($calculation->eco_currency_1 == CURRENCY_EURO ? " (".DisplayNumber($calculation->eco_price_1 * $calculation->euro, 5)." руб)" : ""));
    $sheet->setCellValue('A'.(++$rowindex), "Ширина ручья, мм"); $sheet->setCellValue("B$rowindex", $calculation->stream_width);
    $sheet->setCellValue('A'.(++$rowindex), "Количество ручьёв"); $sheet->setCellValue("B$rowindex", $calculation->streams_number);
    $sheet->setCellValue('A'.(++$rowindex), "Рапорт"); $sheet->setCellValue("B$rowindex", $calculation->raport);
        
    if(!empty($calculation->machine_id)) {
        for($i=1; $i<=$calculation->ink_number; $i++) {
            $ink = "ink_$i";
            $color = "color_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            $cliche = "cliche_$i";
            $sheet->setCellValue('A'.(++$rowindex), "Краска $i:");
            $sheet->setCellValue("B$rowindex", $calculation->GetInkName(get_object_vars($calculation)[$ink]).(empty(get_object_vars($calculation)[$color]) ? "" : " ".get_object_vars($calculation)[$color]).(empty(get_object_vars($calculation)[$cmyk]) ? "" : " ".get_object_vars($calculation)[$cmyk])." ".get_object_vars($calculation)[$percent]."% ".$calculation->GetClicheName(get_object_vars($calculation)[$cliche]));
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
    
    $sheet->setCellValue('A'.(++$rowindex), "Дополнительные расходы с шт, руб"); $sheet->setCellValue("B$rowindex", $calculation->extra_expense);
    $sheet->setCellValue('A'.(++$rowindex), "ЗазорРапорт"); $sheet->setCellValue("B$rowindex", $calculation->data_gap->gap_raport);
    $sheet->setCellValue('A'.(++$rowindex), "ЗазорРучей"); $sheet->setCellValue("B$rowindex", $calculation->data_gap->gap_stream);
        
    ++$rowindex;
    
    // Если материал заказчика, то его цена = 0
    if($calculation->customers_material_1 == true) $calculation->price_1 = 0;
        
    // Результаты вычислений
    $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (начальная), мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_start);
    $sheet->setCellValue("C$rowindex", $calculation->ski_1 == SKI_NONSTANDARD ? "|= ".DisplayNumber($calculation->width_ski_1, 5) : "|= (".$calculation->streams_number." * (".DisplayNumber($calculation->stream_width, 5)." + ".DisplayNumber($calculation->data_gap->gap_stream, 5).")) + (".DisplayNumber($calculation->data_gap->ski, 5)." * 2)");
    $sheet->setCellValue("D$rowindex", $calculation->ski_1 == SKI_NONSTANDARD ? "=".$calculation->width_ski_1 : "=(".$calculation->streams_number."*(".$calculation->stream_width."+".$calculation->data_gap->gap_stream."))+(".$calculation->data_gap->ski."*2)");
    $sheet->setCellValue("E$rowindex", $calculation->ski_1 == SKI_NONSTANDARD ? "вводится вручную" : "(количество ручьёв * (ширина этикетки + ЗазорРучей)) + (ширина одной лыжи * 2)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Ширина материала (кратная 5), мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_mat);
    $sheet->setCellValue("C$rowindex", "|= ОКРВВЕРХ(".DisplayNumber($calculation->width_start, 5)." / 5; 1) * 5");
    $sheet->setCellValue("D$rowindex", "=CEILING(".$calculation->width_start."/5,1)*5");
    $sheet->setCellValue("E$rowindex", "окрвверх(ширина материала начальная / 5) * 5");
        
    $sheet->setCellValue('A'.(++$rowindex), "Высота этикетки грязная, мм");
    $sheet->setCellValue("B$rowindex", $calculation->length_label_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length, 5)." + ".DisplayNumber($calculation->data_gap->gap_raport, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length."+".$calculation->data_gap->gap_raport);
    $sheet->setCellValue("E$rowindex", "высота этикетки + ЗазорРапорт");
        
    $sheet->setCellValue('A'.(++$rowindex), "Ширина этикетки грязная, мм");
    $sheet->setCellValue("B$rowindex", $calculation->width_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->stream_width, 5)." + ".DisplayNumber($calculation->data_gap->gap_stream, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->stream_width."+".$calculation->data_gap->gap_stream);
    $sheet->setCellValue("E$rowindex", "ширина этикетки + ЗазорРучей");
        
    $sheet->setCellValue('A'.(++$rowindex), "Количество этикеток в рапорте грязное");
    $sheet->setCellValue("B$rowindex", $calculation->number_in_raport_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->raport, 5)." / ".DisplayNumber($calculation->length_label_dirty, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->raport."/".$calculation->length_label_dirty);
    $sheet->setCellValue("E$rowindex", "рапорт / высота этикетки грязная");
        
    $sheet->setCellValue('A'.(++$rowindex), "Количество этикеток в рапорте чистое");
    $sheet->setCellValue("B$rowindex", $calculation->number_in_raport_pure);
    $sheet->setCellValue("C$rowindex", "|= ОКРВНИЗ(".DisplayNumber($calculation->number_in_raport_dirty, 5).";1)");
    $sheet->setCellValue("D$rowindex", "=FLOOR(".$calculation->number_in_raport_dirty.",1)");
    $sheet->setCellValue("E$rowindex", "количество этикеток в рапорте грязное - округление в меньшую сторону");
        
    $sheet->setCellValue('A'.(++$rowindex), "Фактический зазор, мм");
    $sheet->setCellValue("B$rowindex", $calculation->gap);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->raport, 5)." - (".DisplayNumber($calculation->length, 5)." * ".DisplayNumber($calculation->number_in_raport_pure, 5).")) / ".$calculation->number_in_raport_pure);
    $sheet->setCellValue("D$rowindex", "=(".$calculation->raport."-(".$calculation->length."*".$calculation->number_in_raport_pure."))/".$calculation->number_in_raport_pure);
    $sheet->setCellValue("E$rowindex", "(рапорт - (высота этикетки чистая * количество этикеток в рапорте чистое)) / количество этикеток в рапорте чистое");
    
    //***************************
    // Рассчёт по КГ
    //***************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Метраж приладки одного тиража");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_printing);
    $sheet->setCellValue("C$rowindex", "|= (".$calculation->ink_number." * ".DisplayNumber($calculation->data_priladka->length, 5).") + ".DisplayNumber($calculation->data_priladka->stamp, 5));
    $sheet->setCellValue("D$rowindex", "=(".$calculation->ink_number."*".$calculation->data_priladka->length.")+".$calculation->data_priladka->stamp);
    $sheet->setCellValue("E$rowindex", "(красочность * метраж приладки 1 краски) + метраж приладки штампа");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 чистые, м2");
    $sheet->setCellValue("B$rowindex", $calculation->area_pure);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->length, 5)." + ".DisplayNumber($calculation->gap, 5).") * (".DisplayNumber($calculation->stream_width, 5)." + ".DisplayNumber($calculation->data_gap->gap_stream, 5).") * ".DisplayNumber($calculation->quantity, 0)." / 1 000 000");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->length."+".$calculation->gap.")*(".$calculation->stream_width."+".$calculation->data_gap->gap_stream.")*".$calculation->quantity."/1000000");
    $sheet->setCellValue("E$rowindex", "(длина этикетки чистая + фактический зазор) * (ширина этикетки + ЗазорРучей) * суммарное кол-во этикеток всех тиражей / 1 000 000");
    
    $sheet->setCellValue('A'.(++$rowindex), "М. пог. чистые, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pog_pure);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_pure, 5)." / (".DisplayNumber($calculation->width_dirty, 5)." * ".$calculation->streams_number." / 1000)");
    $sheet->setCellValue("D$rowindex", "=".$calculation->area_pure."/(".$calculation->width_dirty."*".$calculation->streams_number."/1000)");
    $sheet->setCellValue("E$rowindex", "м2 чистые / (ширина этикетки грязная * кол-во ручьев / 1000)");
    
    $sheet->setCellValue('A'.(++$rowindex), "СтартСтопОтход, м");
    $sheet->setCellValue("B$rowindex", $calculation->waste_length);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_priladka->waste_percent, 5)." * ".DisplayNumber($calculation->length_pog_pure, 5)." / 100");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_priladka->waste_percent."*".$calculation->length_pog_pure."/100");
    $sheet->setCellValue("E$rowindex", "процент отходов на СтартСтоп * м.пог чистые / 100");
    
    $sheet->setCellValue('A'.(++$rowindex), "М пог. грязные, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pog_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pog_pure, 5)." + (".$calculation->quantities_count." * ".DisplayNumber($calculation->priladka_printing, 5).") + ".DisplayNumber($calculation->waste_length, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pog_pure."+(".$calculation->quantities_count."*".$calculation->priladka_printing.")+".$calculation->waste_length);
    $sheet->setCellValue("E$rowindex", "м. пог чистые + (количество тиражей * метраж приладки 1 тиража) + СтартСтопОтход");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 грязные, m2");
    $sheet->setCellValue("B$rowindex", $calculation->area_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pog_dirty, 5)." * ".DisplayNumber($calculation->width_mat, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pog_dirty."*".$calculation->width_mat."/1000");
    $sheet->setCellValue("E$rowindex", "м. пог грязные * ширина материала / 1000");
        
    //***************************
    // Массы и длины плёнок
    //***************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса материала чистая (без приладки), кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight_pure);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pog_pure, 5)." * ".DisplayNumber($calculation->width_mat, 5)." * ".DisplayNumber($calculation->density_1, 5)." / 1 000 000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pog_pure."*".$calculation->width_mat."*".$calculation->density_1."/1000000");
    $sheet->setCellValue("E$rowindex", "м. пог чистые * ширина материала * уд. вес / 1 000 000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина материала чистая, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_pure);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pog_pure, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pog_pure);
    $sheet->setCellValue("E$rowindex", "м. пог. чистые");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса материала грязная (с приладкой), кг");
    $sheet->setCellValue("B$rowindex", $calculation->weight_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->area_dirty, 5)." * ".DisplayNumber($calculation->density_1, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->area_dirty."*".$calculation->density_1."/1000");
    $sheet->setCellValue("E$rowindex", "м2 грязные * удельный вес / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Длина материала грязная, м");
    $sheet->setCellValue("B$rowindex", $calculation->length_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->length_pog_dirty, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->length_pog_dirty);
    $sheet->setCellValue("E$rowindex", "м2 пог. грязные");
        
    //*****************************
    // Себестоимость плёнок $this->film_cost = ($this->area_dirty * $this->price_1 * self::GetCurrencyRate($this->currency_1, $usd, $euro)) + ($this->area_dirty * $this->density_1 * $this->eco_price_1 * self::GetCurrencyRate($this->eco_currency_1, $usd, $euro) / 1000);
    //*****************************
    
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