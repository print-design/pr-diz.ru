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