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
        
        $sql = "select gap_raport, gap_stream from norm_raport where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_gap = new DataGap($row['gap_raport'], $row['gap_stream']);
        }
        
        $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_price, lacquer_currency, lacquer_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price "
                . "from norm_ink where date <= '$date' order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_price'], $row['lacquer_currency'], $row['lacquer_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price']);
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
        array_push($file_data, array("Толщина", $thickness, "", ""));
        array_push($file_data, array("Плотность", $density, "", ""));
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
        
        array_push($file_data, array("ЗазорРапорт", CalculationBase::Display($gap_raport, 2), "", ""));
        array_push($file_data, array("ЗазорРучей", CalculationBase::Display($gap_stream, 2), "", ""));
        
        array_push($file_data, array("", "", "", ""));
        
        // Если материал заказчика, то его цена = 0
        if($customers_material == true) $price = 0;
        
        // Результаты вычислений
    }
}
?>
