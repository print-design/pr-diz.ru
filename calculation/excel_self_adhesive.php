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
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость материала грязная (с приладкой), руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->area_dirty, 5)." * ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5).") + (".DisplayNumber($calculation->area_dirty, 5)." * ". DisplayNumber($calculation->density_1, 2)." * ".DisplayNumber($calculation->eco_price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->eco_currency_1, $calculation->usd, $calculation->euro), 5)." / 1000)");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->area_dirty."*".$calculation->price_1."*".CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro).")+(".$calculation->area_dirty."*".$calculation->density_1."*".$calculation->eco_price_1."*".CalculationBase::GetCurrencyRate($calculation->eco_currency_1, $calculation->usd, $calculation->euro)."/1000)");
    $sheet->setCellValue("E$rowindex", "(м2 грязные 1 * цена * курс валюты) + (м2 грязные 1 * уд. вес плёнки 1 * цена из экосбора плёнки 1 * курс валюты / 1000)");
        
    ++$rowindex;
        
    //*****************************
    // Время - деньги
    //*****************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Время приладки, ч");
    $sheet->setCellValue("B$rowindex", $calculation->priladka_time);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->ink_number." * ".DisplayNumber($calculation->data_priladka->time, 5)." / 60 * ".$calculation->quantities_count);
    $sheet->setCellValue("D$rowindex", "=".$calculation->ink_number."*".$calculation->data_priladka->time."/60*".$calculation->quantities_count);
    $sheet->setCellValue("E$rowindex", "красочность * время приладки 1 краски, мин / 60 * количество тиражей");
    
    $sheet->setCellValue('A'.(++$rowindex), "Время печати тиража, без приладки, ч");
    $sheet->setCellValue("B$rowindex", $calculation->print_time);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->length_pog_pure, 5)." + ".DisplayNumber($calculation->waste_length, 5).") / ".DisplayNumber($calculation->data_machine->speed, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->length_pog_pure."+".$calculation->waste_length.")/".$calculation->data_machine->speed."/1000");
    $sheet->setCellValue("E$rowindex", "(м. пог. чистые + СтартСтопОтход) / скорость работы машины / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общее время выполнения тиража, ч");
    $sheet->setCellValue("B$rowindex", $calculation->work_time);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->priladka_time, 5)." + ".DisplayNumber($calculation->print_time, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->priladka_time."+".$calculation->print_time);
    $sheet->setCellValue("E$rowindex", "время приладки + время печати тиража");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость выполнения, руб");
    $sheet->setCellValue("B$rowindex", $calculation->work_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->work_time, 5)." * ".DisplayNumber($calculation->data_machine->price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->work_time."*".$calculation->data_machine->price);
    $sheet->setCellValue("E$rowindex", "общее время выполнения тиража * стоимость работы машины");
    
    ++$rowindex;
        
    //************************
    // Расход краски
    //************************
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 запечатки, м2");
    $sheet->setCellValue("B$rowindex", $calculation->print_area);
    $sheet->setCellValue("C$rowindex", "|= ((".DisplayNumber($calculation->stream_width, 5)." + ".DisplayNumber($calculation->data_gap->gap_stream, 5).") * (".DisplayNumber($calculation->length, 5)." + ".DisplayNumber($calculation->data_gap->gap_raport, 5).") * ".DisplayNumber($calculation->quantity, 0)." / 1 000 000) + (".DisplayNumber($calculation->length_pog_dirty, 5)." * 0,01)");
    $sheet->setCellValue("D$rowindex", "=((".$calculation->stream_width."+".$calculation->data_gap->gap_stream.")*(".$calculation->length."+".$calculation->data_gap->gap_raport.")*".$calculation->quantity."/1000000)+(".$calculation->length_pog_dirty."*0.01)");
    $sheet->setCellValue("E$rowindex", "((ширина этикетки + ЗазорРучей) * (длина этикетки + ЗазорРапорт) * суммарное кол-во этикеток всех тиражей / 1 000 000) + (м. пог. грязные * 0,01)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Масса краски в смеси, кг");
    $sheet->setCellValue("B$rowindex", $calculation->ink_1kg_mix_weight);
    $sheet->setCellValue("C$rowindex", "|= 1 + ".DisplayNumber($calculation->data_ink->solvent_part, 5));
    $sheet->setCellValue("D$rowindex", "=1+".$calculation->data_ink->solvent_part);
    $sheet->setCellValue("E$rowindex", "1 + доля растворителя в смеси");
    
    $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистого этоксипропанола, руб");
    $sheet->setCellValue("B$rowindex", $calculation->ink_etoxypropanol_kg_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_ink->solvent_etoxipropanol_price, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->data_ink->solvent_etoxipropanol_currency, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_ink->solvent_etoxipropanol_price."*".CalculationBase::GetCurrencyRate($calculation->data_ink->solvent_etoxipropanol_currency, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена этоксипропанола * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 испарения грязная, м2");
    $sheet->setCellValue("B$rowindex", $calculation->vaporization_area_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_machine->width, 0)." * ".DisplayNumber($calculation->length_pog_dirty, 5)." / 1000");
    $sheet->setCellValue("D$rowindex", "=".$calculation->data_machine->width."*".$calculation->length_pog_dirty."/1000");
    $sheet->setCellValue("E$rowindex", "Ширина машины * м. пог грязные / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "М2 испарения чистая, м2");
    $sheet->setCellValue("B$rowindex", $calculation->vaporization_area_pure);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_area_dirty, 5)." - ".DisplayNumber($calculation->print_area, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_area_dirty."-".$calculation->print_area);
    $sheet->setCellValue("E$rowindex", "М2 испарения грязное - М2 запечатки");
    
    $sheet->setCellValue('A'.(++$rowindex), "Расход испарения растворителя, кг");
    $sheet->setCellValue("B$rowindex", $calculation->vaporization_expense);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->vaporization_area_pure, 5)." * ".DisplayNumber($calculation->data_machine->vaporization_expense, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->vaporization_area_pure."*".$calculation->data_machine->vaporization_expense);
    $sheet->setCellValue("E$rowindex", "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2)");
    
    for($i = 1; $i <= $calculation->ink_number; $i++) {
        $ink = "ink_$i";
        $cmyk = "cmyk_$i";
        $lacquer = "lacquer_$i";
        $percent = "percent_$i";
            
        // Поскольку в самоклейке лак используется без растворителя, для лака используем другой расчёт
        if(get_object_vars($calculation)[$ink] == INK_LACQUER) {
            $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистой краски $i, руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_kg_prices[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->data_ink->self_adhesive_laquer_price, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($calculation->data_ink->self_adhesive_laquer_currency, $calculation->usd, $calculation->euro), 5));
            $sheet->setCellValue("D$rowindex", "=".$calculation->data_ink->self_adhesive_laquer_price."*".$calculation->GetCurrencyRate($calculation->data_ink->self_adhesive_laquer_currency, $calculation->usd, $calculation->euro));
            $sheet->setCellValue("E$rowindex", "цена 1 кг чистой краски $i * курс валюты");
                
            $sheet->setCellValue('A'.(++$rowindex), "Расход чистой краски $i, кг");
            $sheet->setCellValue("B$rowindex", $calculation->ink_expenses[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber($calculation->data_ink->self_adhesive_laquer_expense, 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent], 5)." / 1000 / 100");
            $sheet->setCellValue("D$rowindex", "=".$calculation->print_area."*".$calculation->data_ink->self_adhesive_laquer_expense."*".get_object_vars($calculation)[$percent]."/1000/100");
            $sheet->setCellValue("E$rowindex", "площадь запечатки * расход чистой краски за 1 м2 * процент краски $i / 1000 / 100");
            
            $sheet->setCellValue('A'.(++$rowindex), "Стоимость чистой краски $i, руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_costs[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->ink_expenses[$i], 5)." * ".DisplayNumber($calculation->ink_kg_prices[$i], 5));
            $sheet->setCellValue("D$rowindex", "=".$calculation->ink_expenses[$i]."*".$calculation->ink_kg_prices[$i]);
            $sheet->setCellValue("E$rowindex", "Расход чистой краски $i * цена 1 кг чистой краски $i");
        }
        else {
            $price1 = $calculation->GetInkPrice(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_price, $calculation->data_ink->c_currency, $calculation->data_ink->m_price, $calculation->data_ink->m_currency, $calculation->data_ink->y_price, $calculation->data_ink->y_currency, $calculation->data_ink->k_price, $calculation->data_ink->k_currency, $calculation->data_ink->panton_price, $calculation->data_ink->panton_currency, $calculation->data_ink->white_price, $calculation->data_ink->white_currency, $calculation->data_ink->lacquer_glossy_price, $calculation->data_ink->lacquer_glossy_currency, $calculation->data_ink->lacquer_matte_price, $calculation->data_ink->lacquer_matte_currency);
            
            $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг чистой краски $i, руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_kg_prices[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($price1->value, 5)." * ".DisplayNumber($calculation->GetCurrencyRate($price1->currency, $calculation->usd, $calculation->euro), 5));
            $sheet->setCellValue("D$rowindex", "=".$price1->value."*".$calculation->GetCurrencyRate($price1->currency, $calculation->usd, $calculation->euro));
            $sheet->setCellValue("E$rowindex", "цена 1 кг чистой краски $i * курс валюты");
            
            $sheet->setCellValue('A'.(++$rowindex), "Цена 1 кг КраскаСмеси $i, руб");
            $sheet->setCellValue("B$rowindex", $calculation->mix_ink_kg_prices[$i]);
            $sheet->setCellValue("C$rowindex", "|= ((".DisplayNumber($calculation->ink_kg_prices[$i], 5)." * 1) + (".DisplayNumber($calculation->ink_etoxypropanol_kg_price, 5)." * ".DisplayNumber($calculation->data_ink->solvent_part, 5).")) / ".DisplayNumber($calculation->ink_1kg_mix_weight, 5));
            $sheet->setCellValue("D$rowindex", "=((".$calculation->ink_kg_prices[$i]."*1)+(".$calculation->ink_etoxypropanol_kg_price."*".$calculation->data_ink->solvent_part."))/".$calculation->ink_1kg_mix_weight);
            $sheet->setCellValue("E$rowindex", "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски");
            
            $sheet->setCellValue('A'.(++$rowindex), "Расход КраскаСмеси $i, кг");
            $sheet->setCellValue("B$rowindex", $calculation->ink_expenses[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->print_area, 5)." * ".DisplayNumber($calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense), 5)." * ".DisplayNumber(get_object_vars($calculation)[$percent], 5)." / 1000 / 100");
            $sheet->setCellValue("D$rowindex", "=".$calculation->print_area."*".$calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense)."*".get_object_vars($calculation)[$percent]."/1000/100");
            $sheet->setCellValue("E$rowindex", "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100");
            
            $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси $i, руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_costs[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->ink_expenses[$i], 5)." * ".DisplayNumber($calculation->mix_ink_kg_prices[$i], 5));
            $sheet->setCellValue("D$rowindex", "=".$calculation->ink_expenses[$i]."*".$calculation->mix_ink_kg_prices[$i]);
            $sheet->setCellValue("E$rowindex", "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i");
            
            $sheet->setCellValue('A'.(++$rowindex), "Расход (краска + растворитель на одну краску), руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_costs_mix[$i]);
            $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->ink_costs[$i], 5));
            $sheet->setCellValue("D$rowindex", "=".$calculation->ink_costs[$i]);
            $sheet->setCellValue("E$rowindex", "Стоимость КраскаСмеси на тираж ₽");
            
            $sheet->setCellValue('A'.(++$rowindex), "Стоимость КраскаСмеси $i финальная, руб");
            $sheet->setCellValue("B$rowindex", $calculation->ink_costs_final[$i]);
            $sheet->setCellValue("C$rowindex", "|= ЕСЛИ(".DisplayNumber($calculation->ink_costs_mix[$i], 5)." < ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->data_ink->min_price_per_ink, 5)." ; ".DisplayNumber($calculation->ink_costs_mix[$i], 5).")");
            $sheet->setCellValue("D$rowindex", "=IF(".$calculation->ink_costs_mix[$i]."<".$calculation->data_ink->min_price_per_ink.",".$calculation->data_ink->min_price_per_ink.",".$calculation->ink_costs_mix[$i].")");
            $sheet->setCellValue("E$rowindex", "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - расход (краска + растворитель на одну краску)");
        } 
    }
        
    ++$rowindex;
    
    //***********************************
    // Стоимость форм
    //***********************************
    
    $sheet->setCellValue('A'.(++$rowindex), "Высота форм, м");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_height);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->raport, 5)." + 20) / 1000");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->raport."+20)/1000");
    $sheet->setCellValue("E$rowindex", "(рапорт + 20мм) / 1000");
    
    $sheet->setCellValue('A'.(++$rowindex), "Ширина форм, м");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_width);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->streams_number, 5)." * ".DisplayNumber($calculation->width_dirty, 5)." + 20 + 20) / 1000");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->streams_number."*".$calculation->width_dirty."+20+20)/1000");
    $sheet->setCellValue("E$rowindex", "(кол-во ручьёв * ширина этикетки грязная + 20 мм + 20 мм) / 1000 (для самоклейки без лыж не бывает)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Площадь форм, м2");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_area);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_height, 5)." * ".DisplayNumber($calculation->cliche_width, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_height."*".$calculation->cliche_width);
    $sheet->setCellValue("E$rowindex", "высота форм * ширина форм");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость 1 формы Флинт, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_flint_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_area, 5)." * ".DisplayNumber($calculation->data_cliche->flint_price, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->data_cliche->flint_currency, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_area."*".$calculation->data_cliche->flint_price."*".CalculationBase::GetCurrencyRate($calculation->data_cliche->flint_currency, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "площадь формы * стоимиость формы Флинт * валюта");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость 1 формы Кодак, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_kodak_price);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_area, 5)." * ".DisplayNumber($calculation->data_cliche->kodak_price, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->data_cliche->kodak_currency, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_area."*".$calculation->data_cliche->kodak_price."*".CalculationBase::GetCurrencyRate($calculation->data_cliche->kodak_currency, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "площадь формы * стоимость формы Кодак * валюта");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость всех форм Флинт, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_all_flint_price);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->cliches_count_flint." * ".DisplayNumber($calculation->cliche_flint_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliches_count_flint."*".$calculation->cliche_flint_price);
    $sheet->setCellValue("E$rowindex", "количество форм Флинт * себестоимость 1 формы Флинт");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость всех форм Кодак, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_all_kodak_price);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->cliches_count_kodak." * ".DisplayNumber($calculation->cliche_kodak_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliches_count_kodak."*".$calculation->cliche_kodak_price);
    $sheet->setCellValue("E$rowindex", "количество форм Кодак * себестоимость 1 формы Кодак");
    
    $sheet->setCellValue('A'.(++$rowindex), "Количество новых форм");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_new_number);
    $sheet->setCellValue("C$rowindex", "|= ".$calculation->cliches_count_flint." + ".$calculation->cliches_count_kodak);
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliches_count_flint."+".$calculation->cliches_count_kodak);
    $sheet->setCellValue("E$rowindex", "количество форм Флинт + количество форм Кодак");
        
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
    
    $sheet->setCellValue('A'.(++$rowindex), "Наценка на тираж, %"); $sheet->setCellValue("B$rowindex", $calculation->extracharge);
    $sheet->setCellValue('A'.(++$rowindex), "Наценка на ПФ, %"); $sheet->setCellValue("B$rowindex", $calculation->extracharge_cliche); $sheet->setCellValue("E$rowindex", "Если УКПФ = 1, то наценка на ПФ всегда 0");
    $sheet->setCellValue('A'.(++$rowindex), "Наценка на нож, %"); $sheet->setCellValue("B$rowindex", $calculation->extracharge_knife); $sheet->setCellValue("E$rowindex", "Если УКНОЖ = 1, то наценка на нож всегда 0");
    
    ++$rowindex;
        
    //*******************************************
    // Данные для правой панели
    //*******************************************
        
    $total_ink_cost_formula = "";
    $total_ink_cost_result = "";
    $total_ink_expense_formula = "";
    $total_ink_expense_result = "";
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        if(!empty($total_ink_cost_formula)) {
            $total_ink_cost_formula .= " + ";
        }
        $total_ink_cost_formula .= DisplayNumber($calculation->ink_costs_final[$i], 5);
        
        if(!empty($total_ink_cost_result)) {
            $total_ink_cost_result .= "+";
        }
        $total_ink_cost_result .= $calculation->ink_costs_final[$i];
            
        if(!empty($total_ink_expense_formula)) {
            $total_ink_expense_formula .= " + ";
        }
        $total_ink_expense_formula .= DisplayNumber($calculation->ink_expenses[$i], 5);
        
        if(!empty($total_ink_expense_result)) {
            $total_ink_expense_result .= "+";
        }
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
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_cost, 5)." + ".DisplayNumber($calculation->work_cost, 5)." + ".DisplayNumber($calculation->ink_cost, 5)." + (".DisplayNumber($calculation->cliche_cost, 5)." * ".DisplayNumber($calculation->ukpf, 0).") + (".DisplayNumber($calculation->knife_cost, 5)." * ".DisplayNumber($calculation->ukknife, 0).") + ".DisplayNumber($calculation->scotch_cost, 5)." + (".DisplayNumber($calculation->extra_expense, 5)." * ".$calculation->quantity.")");
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_cost."+".$calculation->work_cost."+".$calculation->ink_cost."+(".$calculation->cliche_cost."*".$calculation->ukpf.")+(".$calculation->knife_cost."*".$calculation->ukknife.")+".$calculation->scotch_cost."+(".$calculation->extra_expense."*".$calculation->quantity.")");
    $sheet->setCellValue("E$rowindex", "стоимость материала + стоимость работы + стоимость краски + (стоимость форм * УКПФ) + (стоимость ножа * УКНОЖ) + стоимость скотча + (доп. расходы на кг / шт * объём заказа, кг/шт)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость за шт, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cost_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cost, 5)." / ".DisplayNumber($calculation->quantity, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cost."/".$calculation->quantity);
    $sheet->setCellValue("E$rowindex", "себестоимость / суммарное кол-во этикеток всех тиражей");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость форм, руб");
    $sheet->setCellValue("B$rowindex", $calculation->cliche_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_all_flint_price, 5)." + ".DisplayNumber($calculation->cliche_all_kodak_price, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_all_flint_price."+".$calculation->cliche_all_kodak_price);
    $sheet->setCellValue("E$rowindex", "себестоимость всех форм Флинт + себестоимость всех форм Кодак");
    
    $sheet->setCellValue('A'.(++$rowindex), "Себестоимость ножа, руб");
    $sheet->setCellValue("B$rowindex", $calculation->knife_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->knife, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->knife);
    $sheet->setCellValue("E$rowindex", "вводится пользователем");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cost, 5)." * (1 + (".DisplayNumber($calculation->extracharge, 5)." / 100))");
    $sheet->setCellValue("D$rowindex", "=".$calculation->cost."*(1+(".$calculation->extracharge."/100))");
    $sheet->setCellValue("E$rowindex", "себестоимость * (1 + (наценка на тираж / 100))");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость за шт, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cost_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->shipping_cost, 5)." / ".DisplayNumber($calculation->quantity, 0));
    $sheet->setCellValue("D$rowindex", "=".$calculation->shipping_cost."/".$calculation->quantity);
    $sheet->setCellValue("E$rowindex", "отгрузочная стоимость / суммарное кол-во этикеток всех тиражей");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость ПФ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_cliche_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->cliche_cost, 5)." * (1 + (".DisplayNumber($calculation->extracharge_cliche, 5)." / 100)) * ".$calculation->ukcuspaypf." * ((".$calculation->ukpf." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=".$calculation->cliche_cost."*(1+(".$calculation->extracharge_cliche."/100))*".$calculation->ukcuspaypf."*((".$calculation->ukpf."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "сумма стоимости всех форм * (1 + (наценка на ПФ / 100)) * CusPayPF * ((КоэфПФ - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отгрузочная стоимость ножа, руб");
    $sheet->setCellValue("B$rowindex", $calculation->shipping_knife_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->knife_cost, 5)." * (1 + (".DisplayNumber($calculation->extracharge_knife, 5)." / 100)) * ".$calculation->ukcuspayknife." * ((".$calculation->ukknife." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=".$calculation->knife_cost."*(1+(".$calculation->extracharge_knife."/100))*".$calculation->ukcuspayknife."*((".$calculation->ukknife."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "себестоимость ножа * (1 + (наценка на нож / 100)) * CusPayKnife * ((КоэфНож - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->shipping_cost, 5)." - ".DisplayNumber($calculation->cost, 5).")");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->shipping_cost."-".$calculation->cost.")");
    $sheet->setCellValue("E$rowindex", "(отгрузочная стоимость - себестоимость)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль за шт, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->shipping_cost_per_unit, 5)." - ".DisplayNumber($calculation->cost_per_unit, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->shipping_cost_per_unit."-".$calculation->cost_per_unit);
    $sheet->setCellValue("E$rowindex", "отгрузочная стоимость за шт - себестоимость за шт");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль ПФ, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income_cliche);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->shipping_cliche_cost, 5)." - ".DisplayNumber($calculation->cliche_cost, 5).") * ((".$calculation->ukpf." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->shipping_cliche_cost."-".$calculation->cliche_cost.")*((".$calculation->ukpf."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "(отгрузочная стоимость ПФ - себестоимость ПФ) * ((КоэфПФ - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Прибыль на нож, руб");
    $sheet->setCellValue("B$rowindex", $calculation->income_knife);
    $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber($calculation->shipping_knife_cost, 5)." - ".DisplayNumber($calculation->knife_cost, 5).") * ((".$calculation->ukknife." - 1) / -1)");
    $sheet->setCellValue("D$rowindex", "=(".$calculation->shipping_knife_cost."-".$calculation->knife_cost.")*((".$calculation->ukknife."-1)/-1)");
    $sheet->setCellValue("E$rowindex", "(отгрузочная стоимость ножа - себестоимость ножа) * ((КоэфНож - 1) / -1)");
    
    $sheet->setCellValue('A'.(++$rowindex), "Общий вес всех материала с приладкой, кг");
    $sheet->setCellValue("B$rowindex", $calculation->total_weight_dirty);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty);
    $sheet->setCellValue("E$rowindex", "масса материала грязная");
    
    $sheet->setCellValue('A'.(++$rowindex), "Стоимость за м2 1, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_cost_per_unit);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->price_1."*".CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "цена материала * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы, руб");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_cost);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->film_waste_weight, 5)." * ".DisplayNumber($calculation->price_1, 5)." * ".DisplayNumber(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->film_waste_weight."*".$calculation->price_1."*".CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro));
    $sheet->setCellValue("E$rowindex", "отходы, кг * цена материала * курс валюты");
    
    $sheet->setCellValue('A'.(++$rowindex), "Отходы, кг");
    $sheet->setCellValue("B$rowindex", $calculation->film_waste_weight);
    $sheet->setCellValue("C$rowindex", "|= ".DisplayNumber($calculation->weight_dirty, 5)." - ".DisplayNumber($calculation->weight_pure, 5));
    $sheet->setCellValue("D$rowindex", "=".$calculation->weight_dirty."-".$calculation->weight_pure);
    $sheet->setCellValue("E$rowindex", "масса материала грязная - масса материала чистая");
        
    ++$rowindex;
        
    $i = 1;
        
    foreach($calculation->quantities as $key => $quantity) {
        $sheet->setCellValue('A'.(++$rowindex), "Длина тиража $i, м");
        $sheet->setCellValue("B$rowindex", $calculation->lengths[$key]);
        $sheet->setCellValue("C$rowindex", "|= (".DisplayNumber(intval($calculation->length), 5)." + ".DisplayNumber($calculation->gap, 5).") * ".DisplayNumber(intval($calculation->quantities[$key]), 0)." / $calculation->streams_number / 1000");
        $sheet->setCellValue("D$rowindex", "=(".$calculation->length."+".$calculation->gap.")*".$calculation->quantities[$key]."/".$calculation->streams_number."/1000");
        $sheet->setCellValue("E$rowindex", "(длина этикетки + фактический зазор) * кол-во этикеток этого тиража / кол-во ручьёв / 1000");
        $i++;
    }
    
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