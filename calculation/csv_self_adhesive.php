<?php
include '../include/topscripts.php';
include './calculation.php';

$id = filter_input(INPUT_GET, 'id');

if($id !== null) {
    // Заголовки CSV-файла
    $titles = array("Параметр", "Значение", "Расчёт", "Комментарий");
    
    // ПОЛУЧЕНИЕ ИСХОДНЫХ ДАННЫХ
    $date = null;
    $name = null;
    $quantity = null;
    
    $film = null; // Самоклеящийся материал, марка
    $thickness = null; // Толщина, мкм
    $density = null; // Плотность, г/м2
    $price = null; // Цена, руб
    $currency = null; // Валюта
    $customers_material; // Материал заказчика
    $ski = null; // Лыжи
    $width_ski = null; // Ширина плёнки
    
    $machine = null;
    $machine_id = null;
    $length = null; // Длина этикетки, мм
    $stream_width = null; // Ширина этикетки, мм
    $streams_number = null; // Количество ручьёв, мм
    $raport = null; // Рапорт, мм
    $ink_number = 0; // Красочность
    
    $cliche_in_price = null; // Включить формы в стоимость
    $extracharge = null; // Наценка на тираж
    $extracharge_cliche = null; // Наценка на ПФ
    
    $sql = "select rc.date, rc.name, rc.quantity, "
            . "f.name film, fv.thickness thickness, fv.weight density, "
            . "rc.film_variation_id, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
            . "rc.customers_material, rc.ski, rc.width_ski, "
            . "m.name machine, rc.machine_id, rc.length, rc.stream_width, rc.streams_number, rc.raport, rc.ink_number, "
            . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
            . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
            . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
            . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
            . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
            . "rc.cliche_in_price, rc.extracharge, rc.extracharge_cliche "
            . "from calculation rc "
            . "left join machine m on rc.machine_id = m.id "
            . "left join film_variation fv on rc.film_variation_id = fv.id "
            . "left join film f on fv.film_id = f.id "
            . "where rc.id = $id";
    $fetcher = new Fetcher($sql);
    
    if ($row = $fetcher->Fetch()) {
        $date = $row['date'];
        $name = $row['name'];
        $quantity = $row['quantity']; // Размер тиража в кг или шт
        
        if(!empty($row['film_variation_id'])) {
            $film = $row['film']; // Основная пленка, марка
            $thickness = $row['thickness']; // Основная пленка, толщина, мкм
            $density = $row['density']; // Основная пленка, плотность, г/м2
        }
        else {
            $film = $row['individual_film_name']; // Основная пленка, марка
            $thickness = $row['individual_thickness']; // Основная пленка, толщина, мкм
            $density = $row['individual_density']; // Основная пленка, плотность, г/м2
        }
        $price = $row['price']; // Основная пленка, цена
        $currency = $row['currency']; // Основная пленка, валюта
        $customers_material = $row['customers_material']; // Основная плёнка, другая, материал заказчика
        $ski = $row['ski']; // Основная пленка, лыжи
        $width_ski = $row['width_ski']; // Основная пленка, ширина пленки, мм
        
        $machine = $row['machine'];
        $machine_id = $row['machine_id'];
        $length = $row['length']; // Длина этикетки, мм
        $stream_width = $row['stream_width']; // Ширина ручья, мм
        $streams_number = $row['streams_number']; // Количество ручьёв
        $raport = $row['raport']; // Рапорт
        $ink_number = $row['ink_number']; // Красочность
        
        $ink_1 = $row['ink_1']; $ink_2 = $row['ink_2']; $ink_3 = $row['ink_3']; $ink_4 = $row['ink_4']; $ink_5 = $row['ink_5']; $ink_6 = $row['ink_6']; $ink_7 = $row['ink_7']; $ink_8 = $row['ink_8'];
        $color_1 = $row['color_1']; $color_2 = $row['color_2']; $color_3 = $row['color_3']; $color_4 = $row['color_4']; $color_5 = $row['color_5']; $color_6 = $row['color_6']; $color_7 = $row['color_7']; $color_8 = $row['color_8'];
        $cmyk_1 = $row['cmyk_1']; $cmyk_2 = $row['cmyk_2']; $cmyk_3 = $row['cmyk_3']; $cmyk_4 = $row['cmyk_4']; $cmyk_5 = $row['cmyk_5']; $cmyk_6 = $row['cmyk_6']; $cmyk_7 = $row['cmyk_7']; $cmyk_8 = $row['cmyk_8'];
        $percent_1 = $row['percent_1']; $percent_2 = $row['percent_2']; $percent_3 = $row['percent_3']; $percent_4 = $row['percent_4']; $percent_5 = $row['percent_5']; $percent_6 = $row['percent_6']; $percent_7 = $row['percent_7']; $percent_8 = $row['percent_8'];
        $cliche_1 = $row['cliche_1']; $cliche_2 = $row['cliche_2']; $cliche_3 = $row['cliche_3']; $cliche_4 = $row['cliche_4']; $cliche_5 = $row['cliche_5']; $cliche_6 = $row['cliche_6']; $cliche_7 = $row['cliche_7']; $cliche_8 = $row['cliche_8'];
        
        $cliche_in_price = $row['cliche_in_price']; // Включать стоимиость ПФ в тираж
        $extracharge = $row['extracharge']; // Наценка на тираж
        $extracharge_cliche = $row['extracharge_cliche']; // Наценка на ПФ
    }
    
    // Курсы валют
    $usd = null;
    $euro = null;
    
    if(!empty($date)) {
        $sql = "select usd, euro from currency where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $usd = $row['usd'];
            $euro = $row['euro'];
        }
    }
    
    // ПОЛУЧЕНИЕ НОРМ
    $data_priladka = new DataPriladka(null, null, null);
    $data_machine = new DataMachine(null, null, null);
    $data_gap = new DataGap(null, null);
    $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
    $data_cliche = new DataCliche(null, null, null, null, null, null);
    $data_extracharge = array();
    
    if(!empty($date)) {
        if(empty($machine_id)) {
            $data_priladka = new DataPriladka(0, 0, 0);
        }
        else {
            $sql = "select machine_id, time, length, waste_percent from norm_priladka where id in (select max(id) from norm_priladka where date <= '$date' group by machine_id)";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                if($row['machine_id'] == $machine_id) {
                    $data_priladka = new DataPriladka($row['time'], $row['length'], $row['waste_percent']);
                }
            }
        }
        
        if(empty($machine_id)) {
            $data_machine = new DataMachine(0, 0, 0);
        }
        else {
            $sql = "select machine_id, price, speed, max_width from norm_machine where id in (select max(id) from norm_machine where date <= '$date' group by machine_id)";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                if($row['machine_id'] == $machine_id) {
                    $data_machine = new DataMachine($row['price'], $row['speed'], $row['max_width']);
                }
            }
        }
        
        $sql = "select gap_raport, gap_stream from norm_gap where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_gap = new DataGap($row['gap_raport'], $row['gap_stream']);
        }
        
        $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_price, lacquer_currency, lacquer_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense "
                . "from norm_ink where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_price'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price'], $row['self_adhesive_laquer_price'], $row['self_adhesive_laquer_currency'], $row['self_adhesive_laquer_expense']);
        }
        
        $sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency "
                . "from norm_cliche where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_cliche = new DataCliche($row['flint_price'], $row['flint_currency'], $row['kodak_price'], $row['kodak_currency'], $row['scotch_price'], $row['scotch_currency']);
        }
        
        $sql = "select extracharge_type_id, from_weight, to_weight, value from extracharge";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            array_push($data_extracharge, new DataExtracharge($row['value'], $row['extracharge_type_id'], $row['from_weight'], $row['to_weight']));
        }
    }
    
    if(!empty($date)) {
        // Расчёт
        $calculation = new CalculationSelfAdhesive($data_priladka, 
                $data_machine, 
                $data_gap, 
                $data_ink, 
                $data_cliche, 
                $data_extracharge, 
                $usd, // Курс доллара
                $euro, // Курс евро
                $quantity, // Размер тиража в шт
                
                $film, // Марка материла
                $thickness, // Толщина материала, мкм
                $density, // Плотность материала, г/м2
                $price, // Цена материала
                $currency, // Валюта цены материала
                $customers_material, // Материал заказчика
                $ski, // Лыжи
                $width_ski, // Ширина плёнки, мм
                
                $length, // Длина этикетки, мм
                $stream_width, // Ширина этикетки, мм
                $streams_number, // Количество ручьёв
                $raport, // Рапорт, мм
                $ink_number, // Красочность
                
                $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
                $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
                $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                
                $cliche_in_price, // Стоимость ПФ включается в себестоимость
                $extracharge, // Наценка на тираж
                $extracharge_cliche); // Наценка на ПФ
        
        // Данные CSV-файла
        $file_data = array();
        
        array_push($file_data, array("Курс доллара, руб", CalculationBase::Display($usd, 2), "", ""));
        array_push($file_data, array("Курс евро, руб", CalculationBase::Display($euro, 2), "", ""));
        array_push($file_data, array("Машина", $machine, "", ""));
        array_push($file_data, array("Размер тиража", $quantity." шт", "", ""));
        array_push($file_data, array("Марка", $film, "", ""));
        array_push($file_data, array("Толщина", CalculationBase::Display($thickness, 2), "", ""));
        array_push($file_data, array("Плотность", CalculationBase::Display($density, 2), "", ""));
        array_push($file_data, array("Лыжи", $calculation->GetSkiName($ski), "", ""));
        if($ski == CalculationBase::NONSTANDARD_SKI) array_push ($file_data, array("Ширина плёнки, мм", CalculationBase::Display ($width_ski, 2), "", ""));
        if($customers_material == true) array_push ($file_data, array("Материал заказчика", "", "", ""));
        else array_push ($file_data, array("Цена", CalculationBase::Display ($price, 2)." ".$calculation->GetCurrencyName ($currency).($currency == CalculationBase::USD ? " (".CalculationBase::Display ($price * $usd, 2)." руб)" : "").($currency == CalculationBase::EURO ? " (".CalculationBase::Display ($price * $euro, 2)." руб)" : ""), "", ""));
        
        array_push($file_data, array("Ширина ручья, мм", $stream_width, "", ""));
        array_push($file_data, array("Количество ручьёв", $streams_number, "", ""));
        array_push($file_data, array("Рапорт", CalculationBase::Display($raport, 2), "", ""));
        
        if(!empty($machine_id)) {
            for($i=1; $i<=$ink_number; $i++) {
                $ink = "ink_$i";
                $color = "color_$i";
                $cmyk = "cmyk_$i";
                $percent = "percent_$i";
                $cliche = "cliche_$i";
                array_push($file_data, array("Краска $i:", $calculation->GetInkName($$ink).(empty($$color) ? "" : " ".$$color).(empty($$cmyk) ? "" : " ".$$cmyk)." ".$$percent."% ".$calculation->GetClicheName($$cliche), "", ""));
            }
        }
        
        if($cliche_in_price == 1) {
            array_push($file_data, array("Включить ПФ в себестоимость", "", "", ""));
        }
        else {
            array_push($file_data, array("Не включать ПФ в себестоимость", "", "", ""));
        }
        
        array_push($file_data, array("ЗазорРапорт", CalculationBase::Display($data_gap->gap_raport, 2), "", ""));
        array_push($file_data, array("ЗазорРучей", CalculationBase::Display($data_gap->gap_stream, 2), "", ""));
        
        array_push($file_data, array("", "", "", ""));
        
        // Если материал заказчика, то его цена = 0
        if($customers_material == true) $price = 0;
        
        // Результаты вычислений
        array_push($file_data, array("Ширина материала, мм",
            CalculationBase::Display($calculation->width_mat, 2),
            $ski == CalculationBase::NONSTANDARD_SKI ? "|= ".CalculationBase::Display($width_ski, 2) : "|= $streams_number * (".CalculationBase::Display($stream_width, 2)." + ".CalculationBase::Display($data_gap->gap_stream, 2).") + 20",
            $ski == CalculationBase::NONSTANDARD_SKI ? "вводится вручную" : "количество ручьёв * (ширина этикетки + ЗазорРучей) + 20"));
        
        array_push($file_data, array("Высота этикетки грязная, мм",
            CalculationBase::Display($calculation->length_label_dirty, 2),
            "|= ". CalculationBase::Display($length, 2)." + ". CalculationBase::Display($data_gap->gap_raport, 2),
            "высота этикетки + ЗазорРапорт"));
        
        array_push($file_data, array("Ширина этикетки грязная, мм",
            CalculationBase::Display($calculation->width_dirty, 2),
            "|= ". CalculationBase::Display($stream_width, 2)." + ". CalculationBase::Display($data_gap->gap_stream, 2),
            "ширина этикетки + ЗазорРучей"));
        
        array_push($file_data, array("Количество этикеток в рапорте грязное",
            CalculationBase::Display($calculation->number_in_raport_dirty, 2),
            "|= ". CalculationBase::Display($raport, 2)." / ". CalculationBase::Display($calculation->length_label_dirty, 2),
            "рапорт / высота этикетки грязная"));
        
        array_push($file_data, array("Количество этикеток в рапорте чистое",
            CalculationBase::Display($calculation->number_in_raport_pure, 2),
            "|= ОКРВНИЗ(".CalculationBase::Display($calculation->number_in_raport_dirty, 2).";1)",
            "количество этикеток в рапорте грязное - округление в меньшую сторону"));
        
        array_push($file_data, array("Фактический зазор, мм",
            CalculationBase::Display($calculation->gap, 2),
            "|= (".CalculationBase::Display($raport, 2)." - (".CalculationBase::Display($length, 2)." * ".CalculationBase::Display($calculation->number_in_raport_pure, 2).")) / ".$calculation->number_in_raport_pure,
            "(рапорт - (высота этикетки чистая * количество этикеток в рапорте чистое)) / количество этикеток в рапорте чистое"));
        
        //***************************
        // Рассчёт по КГ
        //***************************
        
        array_push($file_data, array("М2 чистые, м2", 
            CalculationBase::Display($calculation->area_pure, 2),
            "|= (".CalculationBase::Display($length, 2)." + ".CalculationBase::Display($calculation->gap, 2).") * (".CalculationBase::Display($stream_width, 2)." + ".CalculationBase::Display($data_gap->gap_stream, 2).") * $quantity / 1000000",
            "(длина этикетки чистая + фактический зазор) * (ширина этикетки + ЗазорРучей) * количество этикеток / 1000000"));
        
        array_push($file_data, array("М. пог. чистые, м",
            CalculationBase::Display($calculation->length_pog_pure, 2),
            "|= ".CalculationBase::Display($calculation->area_pure, 2)." / (".CalculationBase::Display($calculation->width_dirty, 2)." * $streams_number / 1000)",
            "м2 чистые / (ширина этикетки грязная * кол-во ручьев / 1000)"));
        
        array_push($file_data, array("СтартСтопОтход, м",
            CalculationBase::Display($calculation->waste_length, 2),
            "|= ". CalculationBase::Display($data_priladka->waste_percent, 2)." * ". CalculationBase::Display($calculation->length_pog_pure, 2)." / 100",
            "процент отходов на СтартСтоп * м.пог чистые / 100"));
        
        array_push($file_data, array("М пог. грязные, м",
            CalculationBase::Display($calculation->length_pog_dirty, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2)." + ($ink_number * ". CalculationBase::Display($data_priladka->length, 2).") + ". CalculationBase::Display($calculation->waste_length, 2),
            "м. пог чистые + (красчность * метраж приладки 1 краски) + СтартСтопОтход"));
        
        array_push($file_data, array("М2 грязные, m2",
            CalculationBase::Display($calculation->area_dirty, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_dirty, 2)." * ". CalculationBase::Display($calculation->width_mat, 2)." / 1000",
            "м. пог грязные * ширина материала / 1000"));
        
        //***************************
        // Массы и длины плёнок
        //***************************
        
        array_push($file_data, array("Масса плёнки чистая (без приладки), кг",
            CalculationBase::Display($calculation->weight_pure, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2)." * ". CalculationBase::Display($calculation->width_mat, 2)." * ". CalculationBase::Display($density, 2)." / 1000000",
            "м. пог чистые * ширина материала * уд. вес / 1000000"));
        
        array_push($file_data, array("Длина плёнки чистая, м",
            CalculationBase::Display($calculation->length_pure, 2),
            "|= ". CalculationBase::Display($calculation->length_pog_pure, 2),
            "м. пог. чистые"));
        
        array_push($file_data, array("Масса плёнки грязная (с приладкой), кг",
            CalculationBase::Display($calculation->weight_dirty, 2),
            "|= ". CalculationBase::Display($calculation->area_dirty, 2)." * ". CalculationBase::Display($density, 2)." / 1000",
            "м2 грязные * удельный вес / 1000"));
        
        array_push($file_data, array("Длина плёнки грязная, м",
            CalculationBase::Display($calculation->length_dirty, 2),
            "|= ".CalculationBase::Display($calculation->length_pog_dirty, 2),
            "м. пог. чистые"));
        
        //*****************************
        // Себестоимость плёнок
        //*****************************
        
        array_push($file_data, array("Себестоимость плёнки грязная (с приладкой), руб",
            CalculationBase::Display($calculation->film_cost, 2),
            "|= ". CalculationBase::Display($calculation->area_dirty, 2)." * ". CalculationBase::Display($price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency, $usd, $euro), 2),
            "м2 грязные * цена * курс валюты"));
        
        array_push($file_data, array("", "", "", ""));
        
        //*****************************
        // Время - деньги
        //*****************************
        
        array_push($file_data, array("Время приладки, ч",
            CalculationBase::Display($calculation->priladka_time, 2),
            "|= $ink_number"." * ".CalculationBase::Display($data_priladka->time, 2),
            "красочность * время приладки 1 краски"));
        
        array_push($file_data, array("Время печати тиража, без приладки, ч",
            CalculationBase::Display($calculation->print_time, 2),
            "|= (". CalculationBase::Display($calculation->length_pog_pure, 2)." + ". CalculationBase::Display($calculation->waste_length, 2).") / ". CalculationBase::Display($data_machine->speed, 2)." / 1000",
            "м. пог. чистые + СтартСтопОтход) / скорость работы машины / 1000"));
        
        array_push($file_data, array("Общее время выполнения тиража, ч",
            CalculationBase::Display($calculation->work_time, 2),
            "|= ". CalculationBase::Display($calculation->priladka_time, 2)." / 60 + ". CalculationBase::Display($calculation->print_time, 2),
            "время приладки / 60 + время печати тиража"));
        
        array_push($file_data, array("Стоимость выполнения, руб",
            CalculationBase::Display($calculation->work_cost, 2),
            "|= ". CalculationBase::Display($calculation->work_time, 2)." * ". CalculationBase::Display($data_machine->price, 2),
            "общее время выполнения тиража * стоимость работы машины"));
        
        array_push($file_data, array("", "", "", ""));
        
        //************************
        // Расход краски
        //************************
        
        array_push($file_data, array("М2 запечатки, м2",
            CalculationBase::Display($calculation->print_area, 2),
            "|= ((". CalculationBase::Display($stream_width, 2)." + ". CalculationBase::Display($data_gap->gap_stream, 2).") * (". CalculationBase::Display($length, 2)." + ". CalculationBase::Display($data_gap->gap_raport, 2).") * $quantity / 1000000".") + (". CalculationBase::Display($calculation->length_pog_dirty, 2)." * 0,01)",
            "((ширина этикетки + ЗазорРучей) * (длина этикетки + ЗазорРапорт) * кол-во этикеток / 1000000) + (м. пог. грязные * 0,01)"));
        
        array_push($file_data, array("Масса краски в смеси, кг",
            CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
            "|= 1 + ". CalculationBase::Display($data_ink->solvent_part, 2),
            "1 + доля растворителя в смеси"));
        
        array_push($file_data, array("Цена 1 кг чистого этоксипропанола, руб",
            CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 2),
            "|= ". CalculationBase::Display($data_ink->solvent_etoxipropanol_price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro), 2),
            "цена этоксипропанола * курс валюты"));
        
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            
            // Поскольку в самоклейке лак используется без растворителя, для лака используем другой расчёт
            if($$ink == CalculationBase::LACQUER) {
                array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "|= ". CalculationBase::Display($data_ink->self_adhesive_laquer_price, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($data_ink->self_adhesive_laquer_currency, $usd, $euro), 2),
                    "цена 1 кг чистой краски $i * курс валюты"));
                
                array_push($file_data, array("Расход чистой краски $i, кг",
                    CalculationBase::Display($calculation->ink_expenses[$i], 2),
                    "|= ".CalculationBase::Display($calculation->print_area, 2)." * ".CalculationBase::Display($data_ink->self_adhesive_laquer_expense, 2)." * ".CalculationBase::Display($$percent, 2)." / 1000 / 100",
                    "площадь запечатки * расход чистой краски за 1 м2 * процент краски $i / 1000 / 100"));
                
                array_push($file_data, array("Стоимость чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_costs[$i], 2),
                    "|= ". CalculationBase::Display($data_ink->self_adhesive_laquer_expense, 2)." * ".CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "Расход чистой краски $i * цена 1 кг чистой краски $i"));
            }
            else {
                $price1 = $calculation->GetInkPrice($$ink, $$cmyk, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_price, $data_ink->lacquer_currency);
            
                array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                    CalculationBase::Display($calculation->ink_kg_prices[$i], 2),
                    "|= ". CalculationBase::Display($price1->value, 2)." * ". CalculationBase::Display($calculation->GetCurrencyRate($price1->currency, $usd, $euro), 2),
                    "цена 1 кг чистой краски $i * курс валюты"));
            
                array_push($file_data, array("Цена 1 кг КраскаСмеси $i, руб",
                    CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2),
                    "|= ((".CalculationBase::Display($calculation->ink_kg_prices[$i], 2)." * 1) + (".CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 2)." * ".CalculationBase::Display($data_ink->solvent_part, 2).")) / ".CalculationBase::Display($calculation->ink_1kg_mix_weight, 2),
                    "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски"));
            
                array_push($file_data, array("Расход КраскаСмеси $i, кг",
                    CalculationBase::Display($calculation->ink_expenses[$i], 2),
                    "|= ".CalculationBase::Display($calculation->print_area, 2)." * ".CalculationBase::Display($calculation->GetInkExpense($$ink, $$cmyk, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_expense), 2)." * ".CalculationBase::Display($$percent, 2)." / 1000 / 100",
                    "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100"));
            
                array_push($file_data, array("Стоимость КраскаСмеси $i, руб",
                    CalculationBase::Display($calculation->ink_costs[$i], 2),
                    "|= ". CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 2)." * ". CalculationBase::Display($calculation->ink_expenses[$i], 2),
                    "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i"));
            }
        }
        
        array_push($file_data, array("", "", "", ""));
        
        //***********************************
        // Стоимость форм
        //***********************************
        
        array_push($file_data, array("Высота форм, мм",
            CalculationBase::Display($calculation->cliche_height, 2),
            "|= ".CalculationBase::Display($raport, 2)." + 20",
            "рапорт + 20мм"));
        
        array_push($file_data, array("Ширина форм, мм",
            CalculationBase::Display($calculation->cliche_width, 2),
            "|= (".CalculationBase::Display($streams_number, 2)." * ".CalculationBase::Display($stream_width, 2)." + 20) + ".((!empty($ski_1) && $ski_1 == Calculation::NO_SKI) ? 0 : 20),
            "(кол-во ручьёв * ширина ручьёв + 20 мм), если есть лыжи (стандартные или нестандартные), то ещё + 20 мм"));
        
        array_push($file_data, array("Площадь форм, см",
            CalculationBase::Display($calculation->cliche_area, 2),
            "|= ".CalculationBase::Display($calculation->cliche_height, 2)." * ".CalculationBase::Display($calculation->cliche_width, 2)." / 100",
            "высота форм * ширина форм / 100"));
        
        array_push($file_data, array("Количество новых форм",
            CalculationBase::Display($calculation->cliche_new_number, 2),"", ""));
        
        for($i=1; $i<=$ink_number; $i++) {
            $cliche = "cliche_$i";
            
            $cliche_sm_price = 0;
            $cliche_currency = "";
            
            switch ($$cliche) {
                case Calculation::FLINT:
                    $cliche_sm_price = $data_cliche->flint_price;
                    $cliche_currency = $data_cliche->flint_currency;
                    break;
                
                case Calculation::KODAK:
                    $cliche_sm_price = $data_cliche->kodak_price;
                    $cliche_currency = $data_cliche->kodak_currency;
                    break;
            }
            
            array_push($file_data, array("Цена формы $i, руб",
                CalculationBase::Display($calculation->cliche_costs[$i], 2),
                "|= ".CalculationBase::Display($calculation->cliche_area, 2)." * ".CalculationBase::Display($cliche_sm_price, 2)." * ".CalculationBase::Display($calculation->GetCurrencyRate($cliche_currency, $usd, $euro), 2),
                "площадь формы * цена формы за 1 см * курс валюты"));
        }
        
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Наценка
        //*******************************************
        
        array_push($file_data, array("Наценка на тираж, %", CalculationBase::Display($calculation->extracharge, 2), "", ""));
        array_push($file_data, array("Наценка на ПФ, %", CalculationBase::Display($calculation->extracharge_cliche, 2), "", "Если УКПФ = 1, то наценка на ПФ всегда 0"));
        array_push($file_data, array("", "", "", ""));
        
        //*******************************************
        // Данные для правой панели
        //*******************************************
        
        $total_ink_cost_formula = "";
        $total_ink_expense_formula = "";
        
        for($i=1; $i<=$ink_number; $i++) {
            if(!empty($total_ink_cost_formula)) {
                $total_ink_cost_formula .= " + ";
            }
            $total_ink_cost_formula .= CalculationBase::Display($calculation->ink_costs[$i], 2);
            
            if(!empty($total_ink_expense_formula)) {
                $total_ink_expense_formula .= " + ";
            }
            $total_ink_expense_formula .= CalculationBase::Display($calculation->ink_expenses[$i], 2);
        }
        
        array_push($file_data, array("Стоимость краски, руб",
            CalculationBase::Display($calculation->ink_cost, 2),
            "|= ".$total_ink_cost_formula,
            "Сумма стоимость всех красок"));
        
        array_push($file_data, array("Расход краски, кг",
            CalculationBase::Display($calculation->ink_expense, 2),
            "|= ".$total_ink_expense_formula,
            "Сумма расход всех красок"));
        
        $total_cliche_cost_formula = "";
        
        for($i=1; $i<=$ink_number; $i++) {
            if(!empty($total_cliche_cost_formula)) {
                $total_cliche_cost_formula .= " + ";
            }
            $total_cliche_cost_formula .= CalculationBase::Display($calculation->cliche_costs[$i], 2);
        }
        
        array_push($file_data, array("Стоимость форм, руб",
            CalculationBase::Display($calculation->cliche_cost, 2),
            "|= ".$total_cliche_cost_formula,
            "сумма стоимости всех форм"));
        
        array_push($file_data, array("Себестоимость, руб",
            CalculationBase::Display($calculation->cost, 2),
            "|= ". CalculationBase::Display($calculation->film_cost, 2)." + ". CalculationBase::Display($calculation->work_cost, 2)." + ". CalculationBase::Display($calculation->ink_cost, 2)." + (". CalculationBase::Display($calculation->cliche_cost, 2)." * ". CalculationBase::Display($calculation->ukpf, 0).")",
            "стоимость плёнки + стоимость работы + стоимость краски + (стоимость форм * УКПФ)"));
        
        array_push($file_data, array("Себестоимость за шт, руб",
            CalculationBase::Display($calculation->cost_per_unit, 2),
            "|= ". CalculationBase::Display($calculation->cost, 2)." / ". CalculationBase::Display($quantity, 2),
            "себестоимость / количество этикеток"));
        
        array_push($file_data, array("Отгрузочная стоимость, руб",
            CalculationBase::Display($calculation->shipping_cost, 2),
            "|= ".CalculationBase::Display($calculation->cost, 1)." + (".CalculationBase::Display($calculation->cost, 2)." * ".CalculationBase::Display($calculation->extracharge, 2)." / 100)",
            "себестоимость + (себестоимость * наценка на тираж / 100)"));
            
        array_push($file_data, array("Отгрузочная стоимость за шт, руб",
            CalculationBase::Display($calculation->shipping_cost_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost, 2)." / ".CalculationBase::Display($quantity, 2),
            "отгрузочная стоимость / размер тиража"));
            
        array_push($file_data, array("Прибыль, руб",
            CalculationBase::Display($calculation->income, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost, 2)." - ".CalculationBase::Display($calculation->cost, 2),
            "отгрузочная стоимость - себестоимость"));
            
        array_push($file_data, array("Прибыль за шт, руб",
            CalculationBase::Display($calculation->income_per_unit, 2),
            "|= ".CalculationBase::Display($calculation->shipping_cost_per_unit, 2)." - ".CalculationBase::Display($calculation->cost_per_unit, 2),
            "отгрузочная стоимость за шт - себестоимость за шт"));
            
        array_push($file_data, array("Отгрузочная стоимость ПФ, руб",
            CalculationBase::Display($calculation->shipping_cliche_cost, 2),
            "|= ".CalculationBase::Display($calculation->cliche_cost, 2)." + (".CalculationBase::Display($calculation->cliche_cost, 2)." * ".CalculationBase::Display($calculation->extracharge_cliche, 2)." / 100)",
            "сумма стоимости всех форм + (сумма стоимости всех форм * наценка на ПФ / 100)"));
        
        array_push($file_data, array("Общий вес всех материала с приладкой, кг",
            CalculationBase::Display($calculation->total_weight_dirty, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty, 2),
            "масса плёнки грязная"));
        
        array_push($file_data, array("Стоимость за м2 1, руб",
            CalculationBase::Display($calculation->film_cost_per_unit, 2),
            "|= ".CalculationBase::Display($price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency, $usd, $euro), 2),
            "цена материала * курс валюты"));
        
        array_push($file_data, array("Отходы, руб",
            CalculationBase::Display($calculation->film_waste_cost, 2),
            "|= ".CalculationBase::Display($calculation->film_waste_weight, 2)." * ".CalculationBase::Display($price, 2)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($currency, $usd, $euro), 2),
            "отходы, кг * цена материала * курс валюты"));
        
        array_push($file_data, array("Отходы, кг",
            CalculationBase::Display($calculation->film_waste_weight, 2),
            "|= ".CalculationBase::Display($calculation->weight_dirty, 2)." - ".CalculationBase::Display($calculation->weight_pure, 2),
            "масса плёнки грязная - масса плёнки чистая"));
        
        //****************************************
        // Сохранение в файл
        $file_name = DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y')." $name.csv";
        
        DownloadSendHeaders($file_name);
        echo Array2Csv($file_data, $titles);
        die();
    }
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы экспортировать в CSV надо нажать на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>