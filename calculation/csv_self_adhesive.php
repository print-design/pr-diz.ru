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
    array_push($file_data, array("Машина", $calculation->machine, "", ""));
    array_push($file_data, array("Количество тиражей", count($calculation->quantities), "", ""));
        
    $i = 1;
    foreach($calculation->quantities as $key => $quantity) {
        array_push($file_data, array("Тираж $i, шт", CalculationBase::Display(intval($quantity), 0), "", ""));
        $i++;
    }
        
    array_push($file_data, array("Суммарное количество этикеток, шт", CalculationBase::Display($calculation->quantity, 0), "", ""));
    array_push($file_data, array("Марка", $calculation->film_1, "", ""));
    array_push($file_data, array("Толщина", CalculationBase::Display($calculation->thickness_1, 5), "", ""));
    array_push($file_data, array("Плотность", CalculationBase::Display($calculation->density_1, 5), "", ""));
    array_push($file_data, array("Лыжи", $calculation->GetSkiName($calculation->ski_1), "", ""));
    if($calculation->ski_1 == CalculationBase::NONSTANDARD_SKI) array_push ($file_data, array("Ширина материала, мм", CalculationBase::Display ($calculation->width_ski_1, 5), "", ""));
    if($calculation->customers_material_1 == true) array_push ($file_data, array("Материал заказчика", "", "", ""));
    else array_push ($file_data, array("Цена", CalculationBase::Display ($calculation->price_1, 5)." ".$calculation->GetCurrencyName ($calculation->currency_1).($calculation->currency_1 == CalculationBase::USD ? " (".CalculationBase::Display ($calculation->price_1 * $calculation->usd, 5)." руб)" : "").($calculation->currency_1 == CalculationBase::EURO ? " (".CalculationBase::Display ($calculation->price_1 * $calculation->euro, 5)." руб)" : ""), "", ""));
        
    array_push($file_data, array("Ширина ручья, мм", $calculation->stream_width, "", ""));
    array_push($file_data, array("Количество ручьёв", $calculation->streams_number, "", ""));
    array_push($file_data, array("Рапорт", CalculationBase::Display($calculation->raport, 5), "", ""));
        
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
    
    array_push($file_data, array("Дополнительные расходы с шт, руб", CalculationBase::Display($calculation->extra_expense, 5), "", ""));
        
    array_push($file_data, array("ЗазорРапорт", CalculationBase::Display($calculation->data_gap->gap_raport, 5), "", ""));
    array_push($file_data, array("ЗазорРучей", CalculationBase::Display($calculation->data_gap->gap_stream, 5), "", ""));
        
    array_push($file_data, array("", "", "", ""));
    
    // Если материал заказчика, то его цена = 0
    if($calculation->customers_material_1 == true) $calculation->price_1 = 0;
        
    // Результаты вычислений
    array_push($file_data, array("Ширина материала, мм",
        CalculationBase::Display($calculation->width_mat, 5),
        $calculation->ski_1 == CalculationBase::NONSTANDARD_SKI ? "|= ".CalculationBase::Display($calculation->width_ski_1, 5) : "|= ($calculation->streams_number * (".CalculationBase::Display($calculation->stream_width, 5)." + ".CalculationBase::Display($calculation->data_gap->gap_stream, 5).")) + (".CalculationBase::Display($calculation->data_gap->ski, 5)." * 2)",
        $calculation->ski_1 == CalculationBase::NONSTANDARD_SKI ? "вводится вручную" : "(количество ручьёв * (ширина этикетки + ЗазорРучей)) + (ширина одной лыжи * 2)"));
        
    array_push($file_data, array("Высота этикетки грязная, мм",
        CalculationBase::Display($calculation->length_label_dirty, 5),
        "|= ". CalculationBase::Display($calculation->length, 5)." + ". CalculationBase::Display($calculation->data_gap->gap_raport, 5),
        "высота этикетки + ЗазорРапорт"));
        
    array_push($file_data, array("Ширина этикетки грязная, мм",
        CalculationBase::Display($calculation->width_dirty, 5),
        "|= ". CalculationBase::Display($calculation->stream_width, 5)." + ". CalculationBase::Display($calculation->data_gap->gap_stream, 5),
        "ширина этикетки + ЗазорРучей"));
        
    array_push($file_data, array("Количество этикеток в рапорте грязное",
        CalculationBase::Display($calculation->number_in_raport_dirty, 5),
        "|= ". CalculationBase::Display($calculation->raport, 5)." / ". CalculationBase::Display($calculation->length_label_dirty, 5),
        "рапорт / высота этикетки грязная"));
        
    array_push($file_data, array("Количество этикеток в рапорте чистое",
        CalculationBase::Display($calculation->number_in_raport_pure, 5),
        "|= ОКРВНИЗ(".CalculationBase::Display($calculation->number_in_raport_dirty, 5).";1)",
        "количество этикеток в рапорте грязное - округление в меньшую сторону"));
        
    array_push($file_data, array("Фактический зазор, мм",
        CalculationBase::Display($calculation->gap, 5),
        "|= (".CalculationBase::Display($calculation->raport, 5)." - (".CalculationBase::Display($calculation->length, 5)." * ".CalculationBase::Display($calculation->number_in_raport_pure, 5).")) / ".$calculation->number_in_raport_pure,
        "(рапорт - (высота этикетки чистая * количество этикеток в рапорте чистое)) / количество этикеток в рапорте чистое"));
        
    //***************************
    // Рассчёт по КГ
    //***************************
        
    array_push($file_data, array("Метраж приладки одного тиража",
        CalculationBase::Display($calculation->priladka_printing, 5),
        "|= ($calculation->ink_number * ". CalculationBase::Display($calculation->data_priladka->length, 5).") + ".CalculationBase::Display($calculation->data_priladka->stamp, 5),
        "(красочность * метраж приладки 1 краски) + метраж приладки штампа"));
        
    array_push($file_data, array("М2 чистые, м2", 
        CalculationBase::Display($calculation->area_pure, 5),
        "|= (".CalculationBase::Display($calculation->length, 5)." + ".CalculationBase::Display($calculation->gap, 5).") * (".CalculationBase::Display($calculation->stream_width, 5)." + ".CalculationBase::Display($calculation->data_gap->gap_stream, 5).") * ". CalculationBase::Display($calculation->quantity, 0)." / 1 000 000",
        "(длина этикетки чистая + фактический зазор) * (ширина этикетки + ЗазорРучей) * суммарное кол-во этикеток всех тиражей / 1 000 000"));
        
    array_push($file_data, array("М. пог. чистые, м",
        CalculationBase::Display($calculation->length_pog_pure, 5),
        "|= ".CalculationBase::Display($calculation->area_pure, 5)." / (".CalculationBase::Display($calculation->width_dirty, 5)." * $calculation->streams_number / 1000)",
        "м2 чистые / (ширина этикетки грязная * кол-во ручьев / 1000)"));
        
    array_push($file_data, array("СтартСтопОтход, м",
        CalculationBase::Display($calculation->waste_length, 5),
        "|= ". CalculationBase::Display($calculation->data_priladka->waste_percent, 5)." * ". CalculationBase::Display($calculation->length_pog_pure, 5)." / 100",
        "процент отходов на СтартСтоп * м.пог чистые / 100"));
        
    array_push($file_data, array("М пог. грязные, м",
        CalculationBase::Display($calculation->length_pog_dirty, 5),
        "|= ". CalculationBase::Display($calculation->length_pog_pure, 5)." + (".$calculation->quantities_count." * ". CalculationBase::Display($calculation->priladka_printing, 5).") + ". CalculationBase::Display($calculation->waste_length, 5),
        "м. пог чистые + (количество тиражей * метраж приладки 1 тиража) + СтартСтопОтход"));
        
    array_push($file_data, array("М2 грязные, m2",
        CalculationBase::Display($calculation->area_dirty, 5),
        "|= ". CalculationBase::Display($calculation->length_pog_dirty, 5)." * ". CalculationBase::Display($calculation->width_mat, 5)." / 1000",
        "м. пог грязные * ширина материала / 1000"));
        
    //***************************
    // Массы и длины плёнок
    //***************************
        
    array_push($file_data, array("Масса материала чистая (без приладки), кг",
        CalculationBase::Display($calculation->weight_pure, 5),
        "|= ". CalculationBase::Display($calculation->length_pog_pure, 5)." * ". CalculationBase::Display($calculation->width_mat, 5)." * ". CalculationBase::Display($calculation->density_1, 5)." / 1 000 000",
        "м. пог чистые * ширина материала * уд. вес / 1 000 000"));
        
    array_push($file_data, array("Длина материала чистая, м",
        CalculationBase::Display($calculation->length_pure, 5),
        "|= ". CalculationBase::Display($calculation->length_pog_pure, 5),
        "м. пог. чистые"));
        
    array_push($file_data, array("Масса материала грязная (с приладкой), кг",
        CalculationBase::Display($calculation->weight_dirty, 5),
        "|= ". CalculationBase::Display($calculation->area_dirty, 5)." * ". CalculationBase::Display($calculation->density_1, 5)." / 1000",
        "м2 грязные * удельный вес / 1000"));
        
    array_push($file_data, array("Длина материала грязная, м",
        CalculationBase::Display($calculation->length_dirty, 5),
        "|= ".CalculationBase::Display($calculation->length_pog_dirty, 5),
        "м. пог. чистые"));
        
    //*****************************
    // Себестоимость плёнок
    //*****************************
        
    array_push($file_data, array("Себестоимость материала грязная (с приладкой), руб",
        CalculationBase::Display($calculation->film_cost, 5),
        "|= ". CalculationBase::Display($calculation->area_dirty, 5)." * ". CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "м2 грязные * цена * курс валюты"));
        
    array_push($file_data, array("", "", "", ""));
        
    //*****************************
    // Время - деньги
    //*****************************
        
    array_push($file_data, array("Время приладки, ч",
        CalculationBase::Display($calculation->priladka_time, 5),
        "|= $calculation->ink_number"." * ".CalculationBase::Display($calculation->data_priladka->time, 5)." / 60 * ".$calculation->quantities_count,
        "красочность * время приладки 1 краски, мин / 60 * количество тиражей"));
        
    array_push($file_data, array("Время печати тиража, без приладки, ч",
        CalculationBase::Display($calculation->print_time, 5),
        "|= (". CalculationBase::Display($calculation->length_pog_pure, 5)." + ". CalculationBase::Display($calculation->waste_length, 5).") / ". CalculationBase::Display($calculation->data_machine->speed, 5)." / 1000",
        "(м. пог. чистые + СтартСтопОтход) / скорость работы машины / 1000"));
        
    array_push($file_data, array("Общее время выполнения тиража, ч",
        CalculationBase::Display($calculation->work_time, 5),
        "|= ". CalculationBase::Display($calculation->priladka_time, 5)." + ". CalculationBase::Display($calculation->print_time, 5),
        "время приладки + время печати тиража"));
        
    array_push($file_data, array("Стоимость выполнения, руб",
        CalculationBase::Display($calculation->work_cost, 5),
        "|= ". CalculationBase::Display($calculation->work_time, 5)." * ". CalculationBase::Display($calculation->data_machine->price, 5),
        "общее время выполнения тиража * стоимость работы машины"));
        
    array_push($file_data, array("", "", "", ""));
        
    //************************
    // Расход краски
    //************************
        
    array_push($file_data, array("М2 запечатки, м2",
        CalculationBase::Display($calculation->print_area, 5),
        "|= ((". CalculationBase::Display($calculation->stream_width, 5)." + ". CalculationBase::Display($calculation->data_gap->gap_stream, 5).") * (". CalculationBase::Display($calculation->length, 5)." + ". CalculationBase::Display($calculation->data_gap->gap_raport, 5).") * ". CalculationBase::Display($calculation->quantity, 0)." / 1 000 000".") + (". CalculationBase::Display($calculation->length_pog_dirty, 5)." * 0,01)",
        "((ширина этикетки + ЗазорРучей) * (длина этикетки + ЗазорРапорт) * суммарное кол-во этикеток всех тиражей / 1 000 000) + (м. пог. грязные * 0,01)"));
        
    array_push($file_data, array("Масса краски в смеси, кг",
        CalculationBase::Display($calculation->ink_1kg_mix_weight, 5),
        "|= 1 + ". CalculationBase::Display($calculation->data_ink->solvent_part, 5),
        "1 + доля растворителя в смеси"));
        
    array_push($file_data, array("Цена 1 кг чистого этоксипропанола, руб",
        CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 5),
        "|= ". CalculationBase::Display($calculation->data_ink->solvent_etoxipropanol_price, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->data_ink->solvent_etoxipropanol_currency, $calculation->usd, $calculation->euro), 5),
        "цена этоксипропанола * курс валюты"));
        
    array_push($file_data, array("М2 испарения грязная, м2",
        CalculationBase::Display($calculation->vaporization_area_dirty, 5),
        "|= ". CalculationBase::Display($calculation->data_machine->width, 0)." * ". CalculationBase::Display($calculation->length_pog_dirty, 5)." / 1000",
        "Ширина машины * м. пог грязные / 1000"));
        
    array_push($file_data, array("М2 испарения чистая, м2",
        CalculationBase::Display($calculation->vaporization_area_pure, 5),
        "|= ". CalculationBase::Display($calculation->vaporization_area_dirty, 5)." - ". CalculationBase::Display($calculation->print_area, 5),
        "М2 испарения грязное - М2 запечатки"));
        
    array_push($file_data, array("Расход испарения растворителя, кг",
        CalculationBase::Display($calculation->vaporization_expense, 5),
        "|= ". CalculationBase::Display($calculation->vaporization_area_pure, 5)." * ". CalculationBase::Display($calculation->data_machine->vaporization_expense, 5),
        "М2 испарения растворителя чистое * расход Растворителя на испарения (г/м2)"));
        
    for($i=1; $i<=$calculation->ink_number; $i++) {
        $ink = "ink_$i";
        $cmyk = "cmyk_$i";
        $lacquer = "lacquer_$i";
        $percent = "percent_$i";
            
        // Поскольку в самоклейке лак используется без растворителя, для лака используем другой расчёт
        if(get_object_vars($calculation)[$ink] == CalculationBase::LACQUER) {
            array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                CalculationBase::Display($calculation->ink_kg_prices[$i], 5),
                "|= ". CalculationBase::Display($calculation->data_ink->self_adhesive_laquer_price, 5)." * ". CalculationBase::Display($calculation->GetCurrencyRate($calculation->data_ink->self_adhesive_laquer_currency, $calculation->usd, $calculation->euro), 5),
                "цена 1 кг чистой краски $i * курс валюты"));
                
            array_push($file_data, array("Расход чистой краски $i, кг",
                CalculationBase::Display($calculation->ink_expenses[$i], 5),
                "|= ".CalculationBase::Display($calculation->print_area, 5)." * ".CalculationBase::Display($calculation->data_ink->self_adhesive_laquer_expense, 5)." * ".CalculationBase::Display(get_object_vars($calculation)[$percent], 5)." / 1000 / 100",
                "площадь запечатки * расход чистой краски за 1 м2 * процент краски $i / 1000 / 100"));
                
            array_push($file_data, array("Стоимость чистой краски $i, руб",
                CalculationBase::Display($calculation->ink_costs[$i], 5),
                "|= ". CalculationBase::Display($calculation->ink_expenses[$i], 5)." * ".CalculationBase::Display($calculation->ink_kg_prices[$i], 5),
                "Расход чистой краски $i * цена 1 кг чистой краски $i"));
        }
        else {
            $price1 = $calculation->GetInkPrice(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_price, $calculation->data_ink->c_currency, $calculation->data_ink->m_price, $calculation->data_ink->m_currency, $calculation->data_ink->y_price, $calculation->data_ink->y_currency, $calculation->data_ink->k_price, $calculation->data_ink->k_currency, $calculation->data_ink->panton_price, $calculation->data_ink->panton_currency, $calculation->data_ink->white_price, $calculation->data_ink->white_currency, $calculation->data_ink->lacquer_glossy_price, $calculation->data_ink->lacquer_glossy_currency, $calculation->data_ink->lacquer_matte_price, $calculation->data_ink->lacquer_matte_currency);
            
            array_push($file_data, array("Цена 1 кг чистой краски $i, руб",
                CalculationBase::Display($calculation->ink_kg_prices[$i], 5),
                "|= ". CalculationBase::Display($price1->value, 5)." * ". CalculationBase::Display($calculation->GetCurrencyRate($price1->currency, $calculation->usd, $calculation->euro), 5),
                "цена 1 кг чистой краски $i * курс валюты"));
            
            array_push($file_data, array("Цена 1 кг КраскаСмеси $i, руб",
                CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 5),
                "|= ((".CalculationBase::Display($calculation->ink_kg_prices[$i], 5)." * 1) + (".CalculationBase::Display($calculation->ink_etoxypropanol_kg_price, 5)." * ".CalculationBase::Display($calculation->data_ink->solvent_part, 5).")) / ".CalculationBase::Display($calculation->ink_1kg_mix_weight, 5),
                "((цена 1 кг чистой краски $i * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски"));
            
            array_push($file_data, array("Расход КраскаСмеси $i, кг",
                CalculationBase::Display($calculation->ink_expenses[$i], 5),
                "|= ".CalculationBase::Display($calculation->print_area, 5)." * ".CalculationBase::Display($calculation->GetInkExpense(get_object_vars($calculation)[$ink], get_object_vars($calculation)[$cmyk], get_object_vars($calculation)[$lacquer], $calculation->data_ink->c_expense, $calculation->data_ink->m_expense, $calculation->data_ink->y_expense, $calculation->data_ink->k_expense, $calculation->data_ink->panton_expense, $calculation->data_ink->white_expense, $calculation->data_ink->lacquer_glossy_expense, $calculation->data_ink->lacquer_matte_expense), 5)." * ".CalculationBase::Display(get_object_vars($calculation)[$percent], 5)." / 1000 / 100",
                "площадь запечатки * расход КраскаСмеси за 1 м2 * процент краски $i / 1000 / 100"));
            
            array_push($file_data, array("Стоимость КраскаСмеси $i, руб",
                CalculationBase::Display($calculation->ink_costs[$i], 5),
                "|= ". CalculationBase::Display($calculation->ink_expenses[$i], 5)." * ".CalculationBase::Display($calculation->mix_ink_kg_prices[$i], 5),
                "Расход КраскаСмеси $i * цена 1 кг КраскаСмеси $i"));
                
            array_push($file_data, array("Расход (краска + растворитель на одну краску), руб",
                CalculationBase::Display($calculation->ink_costs_mix[$i], 5),
                "|= ". CalculationBase::Display($calculation->ink_costs[$i], 5),
                "Стоимость КраскаСмеси на тираж ₽"));
                
            array_push($file_data, array("Стоимость КраскаСмеси $i финальная, руб",
                CalculationBase::Display($calculation->ink_costs_final[$i], 5),
                "|= ЕСЛИ(".CalculationBase::Display($calculation->ink_costs_mix[$i], 5)." < ".CalculationBase::Display($calculation->data_ink->min_price_per_ink, 5)." ; ".CalculationBase::Display($calculation->data_ink->min_price_per_ink, 5)." ; ".CalculationBase::Display($calculation->ink_costs_mix[$i], 5).")",
                "Если расход (краска + растворитель на одну краску) меньше, чем мин. стоимость 1 цвета, то мин. стоимость 1 цвета, иначе - расход (краска + растворитель на одну краску)"));
        }
    }
        
    array_push($file_data, array("", "", "", ""));
        
    //***********************************
    // Стоимость форм
    //***********************************
        
    array_push($file_data, array("Высота форм, м",
        CalculationBase::Display($calculation->cliche_height, 5),
        "|= (".CalculationBase::Display($calculation->raport, 5)." + 20) / 1000",
        "(рапорт + 20мм) / 1000"));
        
    array_push($file_data, array("Ширина форм, м",
        CalculationBase::Display($calculation->cliche_width, 5),
        "|= (".CalculationBase::Display($calculation->streams_number, 5)." * ".CalculationBase::Display($calculation->width_dirty, 5)." + 20 + 20) / 1000",
        "(кол-во ручьёв * ширина этикетки грязная + 20 мм + 20 мм) / 1000 (для самоклейки без лыж не бывает)"));
        
    array_push($file_data, array("Площадь форм, м2",
        CalculationBase::Display($calculation->cliche_area, 5),
        "|= ".CalculationBase::Display($calculation->cliche_height, 5)." * ".CalculationBase::Display($calculation->cliche_width, 5),
        "высота форм * ширина форм"));
        
    array_push($file_data, array("Себестоимость 1 формы Флинт, руб",
        CalculationBase::Display($calculation->cliche_flint_price, 5),
        "|= ".CalculationBase::Display($calculation->cliche_area, 5)." * ".CalculationBase::Display($calculation->data_cliche->flint_price, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->data_cliche->flint_currency, $calculation->usd, $calculation->euro), 5),
        "площадь формы * стоимиость формы Флинт * валюта"));
        
    array_push($file_data, array("Себестоимость 1 формы Кодак, руб",
        CalculationBase::Display($calculation->cliche_kodak_price, 5),
        "|= ".CalculationBase::Display($calculation->cliche_area, 5)." * ".CalculationBase::Display($calculation->data_cliche->kodak_price, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->data_cliche->kodak_currency, $calculation->usd, $calculation->euro), 5),
        "площадь формы * стоимость формы Кодак * валюта"));
        
    array_push($file_data, array("Себестоимость всех форм Флинт, руб",
        CalculationBase::Display($calculation->cliche_all_flint_price, 5),
        "|= $calculation->cliches_count_flint * ".CalculationBase::Display($calculation->cliche_flint_price, 5),
        "количество форм Флинт * себестоимость 1 формы Флинт"));
        
    array_push($file_data, array("Себестоимость всех форм Кодак, руб",
        CalculationBase::Display($calculation->cliche_all_kodak_price, 5),
        "|= $calculation->cliches_count_kodak * ".CalculationBase::Display($calculation->cliche_kodak_price, 5),
        "количество форм Кодак * себестоимость 1 формы Кодак"));
        
    array_push($file_data, array("Количество новых форм",
        CalculationBase::Display($calculation->cliche_new_number, 5),
        "|= $calculation->cliches_count_flint + $calculation->cliches_count_kodak",
        "количество форм Флинт + количество форм Кодак"));
        
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
    array_push($file_data, array("Наценка на нож, %", CalculationBase::Display($calculation->extracharge_knife, 5), "", "Если УКНОЖ = 1, то наценка на нож всегда 0"));
    array_push($file_data, array("", "", "", ""));
        
    //*******************************************
    // Данные для правой панели
    //*******************************************
        
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
        
    array_push($file_data, array("Себестоимость, руб",
        CalculationBase::Display($calculation->cost, 5),
        "|= ". CalculationBase::Display($calculation->film_cost, 5)." + ". CalculationBase::Display($calculation->work_cost, 5)." + ". CalculationBase::Display($calculation->ink_cost, 5)." + (". CalculationBase::Display($calculation->cliche_cost, 5)." * ". CalculationBase::Display($calculation->ukpf, 0).") + (".CalculationBase::Display($calculation->knife_cost, 5)." * ".CalculationBase::Display($calculation->ukknife, 0).") + ".CalculationBase::Display($calculation->scotch_cost, 5),
        "стоимость материала + стоимость работы + стоимость краски + (стоимость форм * УКПФ) + (стоимость ножа * УКНОЖ) + стоимость скотча"));
        
    array_push($file_data, array("Себестоимость за шт, руб",
        CalculationBase::Display($calculation->cost_per_unit, 5),
        "|= ". CalculationBase::Display($calculation->cost, 5)." / ". CalculationBase::Display($calculation->quantity, 5),
        "себестоимость / суммарное кол-во этикеток всех тиражей"));
        
    array_push($file_data, array("Себестоимость форм, руб",
        CalculationBase::Display($calculation->cliche_cost, 5),
        "|= ".CalculationBase::Display($calculation->cliche_all_flint_price, 5)." + ".CalculationBase::Display($calculation->cliche_all_kodak_price, 5),
        "себестоимость всех форм Флинт + себестоимость всех форм Кодак"));
        
    array_push($file_data, array("Себестоимость ножа, руб",
        CalculationBase::Display($calculation->knife_cost, 5),
        "|= ".CalculationBase::Display($calculation->knife, 5),
        "вводится пользователем"));
        
    array_push($file_data, array("Отгрузочная стоимость, руб",
        CalculationBase::Display($calculation->shipping_cost, 5),
        "|= ".CalculationBase::Display($calculation->cost, 5)." + (".CalculationBase::Display($calculation->cost, 5)." * ".CalculationBase::Display($calculation->extracharge, 5)." / 100)",
        "себестоимость + (себестоимость * наценка на тираж / 100)"));
            
    array_push($file_data, array("Отгрузочная стоимость за шт, руб",
        CalculationBase::Display($calculation->shipping_cost_per_unit, 5),
        "|= ".CalculationBase::Display($calculation->shipping_cost, 5)." / ".CalculationBase::Display($calculation->quantity, 0),
        "отгрузочная стоимость / суммарное кол-во этикеток всех тиражей"));
        
    array_push($file_data, array("Отгрузочная стоимость ПФ, руб",
        CalculationBase::Display($calculation->shipping_cliche_cost, 5),
        "|= (".CalculationBase::Display($calculation->cliche_cost, 5)." + (".CalculationBase::Display($calculation->cliche_cost, 5)." * ".CalculationBase::Display($calculation->extracharge_cliche, 5)." / 100)) * ".$calculation->ukcuspaypf." * ((".$calculation->ukpf." - 1) / -1)",
        "(сумма стоимости всех форм + (сумма стоимости всех форм * наценка на ПФ / 100)) * CusPayPF * ((КоэфПФ - 1) / -1)"));
        
    array_push($file_data, array("Отгрузочная стоимость ножа, руб",
        CalculationBase::Display($calculation->shipping_knife_cost, 5),
        "|= (".CalculationBase::Display($calculation->knife_cost, 5)." + (".CalculationBase::Display($calculation->knife_cost, 5)." * ".CalculationBase::Display($calculation->extracharge_knife, 5)." / 100)) * ".$calculation->ukcuspayknife." * ((".$calculation->ukknife." - 1) / -1)",
        "(себестоимость ножа + (себестоимость ножа * наценка на нож / 100)) * CusPayKnife * ((КоэфНож - 1) / -1)"));
        
    array_push($file_data, array("Прибыль, руб",
        CalculationBase::Display($calculation->income, 5),
        "|= (".CalculationBase::Display($calculation->shipping_cost, 5)." - ".CalculationBase::Display($calculation->cost, 5).") - (".CalculationBase::Display($calculation->extra_expense, 5)." * ".$calculation->quantity.")",
        "(отгрузочная стоимость - себестоимость) - (доп. расходы на кг / шт * объём заказа, кг/шт)"));
            
    array_push($file_data, array("Прибыль за шт, руб",
        CalculationBase::Display($calculation->income_per_unit, 5),
        "|= ".CalculationBase::Display($calculation->shipping_cost_per_unit, 5)." - ".CalculationBase::Display($calculation->cost_per_unit, 5)." - ".CalculationBase::Display($calculation->extra_expense, 5),
        "отгрузочная стоимость за шт - себестоимость за шт - дополнительные расходы за шт"));
        
    array_push($file_data, array("Прибыль ПФ, руб",
        CalculationBase::Display($calculation->income_cliche, 5),
        "|= (".CalculationBase::Display($calculation->shipping_cliche_cost, 5)." - ".CalculationBase::Display($calculation->cliche_cost, 5).") * ((".$calculation->ukpf." - 1) / -1)",
        "(отгрузочная стоимость ПФ - себестоимость ПФ) * ((КоэфПФ - 1) / -1)"));
        
    array_push($file_data, array("Прибыль на нож, руб",
        CalculationBase::Display($calculation->income_knife, 5),
        "|= (".CalculationBase::Display($calculation->shipping_knife_cost, 5)." - ".CalculationBase::Display($calculation->knife_cost, 5).") * ((".$calculation->ukknife." - 1) / -1)",
        "(отгрузочная стоимость ножа - себестоимость ножа) * ((КоэфНож - 1) / -1)"));
        
    array_push($file_data, array("Общий вес всех материала с приладкой, кг",
        CalculationBase::Display($calculation->total_weight_dirty, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty, 5),
        "масса материала грязная"));
        
    array_push($file_data, array("Стоимость за м2 1, руб",
        CalculationBase::Display($calculation->film_cost_per_unit, 5),
        "|= ".CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "цена материала * курс валюты"));
        
    array_push($file_data, array("Отходы, руб",
        CalculationBase::Display($calculation->film_waste_cost, 5),
        "|= ".CalculationBase::Display($calculation->film_waste_weight, 5)." * ".CalculationBase::Display($calculation->price_1, 5)." * ".CalculationBase::Display(CalculationBase::GetCurrencyRate($calculation->currency_1, $calculation->usd, $calculation->euro), 5),
        "отходы, кг * цена материала * курс валюты"));
        
    array_push($file_data, array("Отходы, кг",
        CalculationBase::Display($calculation->film_waste_weight, 5),
        "|= ".CalculationBase::Display($calculation->weight_dirty, 5)." - ".CalculationBase::Display($calculation->weight_pure, 5),
        "масса материала грязная - масса материала чистая"));
        
    array_push($file_data, array("", "", "", ""));
        
    $i = 1;
        
    foreach($calculation->quantities as $key => $quantity) {
        array_push($file_data, array("Длина тиража $i, м",
            CalculationBase::Display($calculation->lengths[$key], 5),
            "|= (".CalculationBase::Display(intval($calculation->length), 5)." + ".CalculationBase::Display($calculation->gap, 5).") * ".CalculationBase::Display(intval($calculation->quantities[$key]), 0)." / $calculation->streams_number / 1000",
            "(длина этикетки + фактический зазор) * кол-во этикеток этого тиража / кол-во ручьёв / 1000"));
        $i++;
    }
        
    //****************************************
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