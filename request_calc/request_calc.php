<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Машины
const ZBS = "zbs";
const COMIFLEX = "comiflex";

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Валюты
const USD = "usd";
const EURO = "euro";

// Краски
const CMYK = "cmyk";
const CYAN = "cyan";
const MAGENTA = "magenta";
const YELLOW = "yellow";
const KONTUR = "kontur";
const PANTON = "panton";
const WHITE = "white";
const LACQUER = "lacquer";

$form_valid = true;

// Заполнение красочности
if(null !== filter_input(INPUT_POST, 'percent-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $color_id = filter_input(INPUT_POST, 'color_id');
    $percent = filter_input(INPUT_POST, 'percent');
    
    if(empty($percent)) {
        $error_message = "Процент обязательно";
        $form_valid = false;
    }
    
    if($form_valid) {
        $sql = "update request_calc set percent_$color_id = $percent where id=$id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Ввод комментария
if(null !== filter_input(INPUT_POST, 'comment-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    
    $sql = "update request_calc set comment='$comment' where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Расчёт
if(null !== filter_input(INPUT_POST, 'calculate-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
            . "c.brand_name, c.thickness, individual_brand_name, individual_price, individual_thickness, individual_density, c.customers_material, "
            . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_individual_brand_name, lamination1_individual_price, lamination1_individual_thickness, lamination1_individual_density, c.lamination1_customers_material, "
            . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_individual_brand_name, lamination2_individual_price, lamination2_individual_thickness, lamination2_individual_density, c.lamination2_customers_material, "
            . "c.label_length, c.stream_width, c.streams_number, c.machine_type, c.raport, c.number_on_raport, c.lamination_roller_width, c.ink_number, c.manager_id, "
            . "c.ink_1, c.ink_2, c.ink_3, ink_4, ink_5, ink_6, ink_7, ink_8, "
            . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "c.cliche_1, c.cliche_2, c.cliche_3, cliche_4, cliche_5, cliche_6, cliche_7, cliche_8, "
            . "c.extracharge, c.ski_width, c.no_ski, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) density, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_density, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_density "
            . "from request_calc c "
            . "where c.id=$id";
    if($row = (new Fetcher($sql))->Fetch())
    {
        $date = $row['date']; // Дата создания расчёта
        $customer_id = $row['customer_id']; // ID заказчика
        $request_name = $row['name']; // Наименование расчёта
        $work_type_id = $row['work_type_id']; // Тип работы (Плёнка с печатью / Плёнка без печати)
        $quantity = $row['quantity']; // Объём заказа (в рублях или штуках)
        $unit = $row['unit']; // Единица объёма заказа ('kg' или 'pieces', соотв. рубли или штуки)
        $brand_name = $row['brand_name']; // Марка плёнки (если выбиралась из списка)
        $thickness = $row['thickness']; // Толщина плёнки (если выбиралась из списка)
        $density = $row['density']; // Удельный вес плёнки (если выбиралась из списка)
        $individual_brand_name = $row['individual_brand_name']; // Марка плёнки (если вводилась вручную)
        $individual_price = $row['individual_price']; // Цена плёнки (если вводилась вручную)
        $individual_thickness = $row['individual_thickness']; // Толщина плёнки (если вводилась вручную)
        $individual_density = $row['individual_density']; // Удельный вес плёнки (если вводилась вручную)
        $customers_material = $row['customers_material']; // Материал заказчика (ДА/НЕТ)
        $lamination1_brand_name = $row['lamination1_brand_name']; // Марка плёнки ламинации 1 (если выбиралась из списка)
        $lamination1_thickness = $row['lamination1_thickness']; // Толщина плёнки ламинации 1 (если выбиралась из списка)
        $lamination1_density = $row['lamination1_density']; // Удельный вес плёнки ламинации 1 (если выбиралась из списка)
        $lamination1_individual_brand_name = $row['lamination1_individual_brand_name']; // Марка плёнки ламинации 1 (если вводилась вручную)
        $lamination1_individual_price = $row['lamination1_individual_price']; // Цена плёнки ламинации 1 (если вводилась вручную)
        $lamination1_individual_thickness = $row['lamination1_individual_thickness']; // Толщина плёнки ламинации 1 (если вводилась вручную)
        $lamination1_individual_density = $row['lamination1_individual_density']; // Удельный вес плёнки ламинации 1 (если вводилась вручную)
        $lamination1_customers_material = $row['lamination1_customers_material']; // Ламинация 1 - материал заказчика (ДА/НЕТ)
        $lamination2_brand_name = $row['lamination2_brand_name']; // Марка плёнки ламинации 2 (если выбиралась из списка)
        $lamination2_thickness = $row['lamination2_thickness']; // Толщина плёнки ламинации 2 (если выбиралась из списка)
        $lamination2_density = $row['lamination2_density']; // Удельный вес плёнки ламинации 2 (если выбиралась из списка)
        $lamination2_individual_brand_name = $row['lamination2_individual_brand_name']; // Марка плёнки ламинации 2 (если вводилась вручную)
        $lamination2_individual_price = $row['lamination2_individual_price']; // Цена плёнки ламинации 2 (если вводилась вручную)
        $lamination2_individual_thickness = $row['lamination2_individual_thickness']; // Толщина плёнки ламинации 2 (если вводилась вручную)
        $lamination2_individual_density = $row['lamination2_individual_density']; // Удельный вес плёнки ламинации 2 (если вводилась вручную)
        $lamination2_customers_material = $row['lamination2_customers_material']; // Ламинация 2 - материал заказчика (ДА/НЕТ)
        $label_length = $row['label_length']; // Длина этикетки вдоль рапорта вала
        $stream_width = $row['stream_width']; // Ширина ручья
        $streams_number = $row['streams_number']; // Количество ручьёв
        $machine_type = $row['machine_type']; // Тип машины ('zbs' или 'comiflex')
        $raport = $row['raport']; // Рапорт
        $number_on_raport = $row['number_on_raport']; // Количество этикеток на ручье
        $lamination_roller_width = $row['lamination_roller_width']; // Ширина вала ламинации
        $ink_number = $row['ink_number']; // Количество красок
        $manager_id = $row['manager_id']; // ID менеджера

        // Заполнение переменных для красок:
        // $ink_1, ..., $ink_8 - тип краски (CMYK / Пантон / Белый / Лак)
        // $color_1, ..., $color_8 - номер пантона
        // $cmyk_1, ..., $cmyk_8 - компонент CMYK (Cyan / Magenta / Yellow / Contour)
        // $percent_1, ..., $percent_8 - процент краски
        // $cliche_1, ..., $cliche_8 - форма (Старая / Новая Флинт / Новая Кодак / Новая Тверь)
        for($i=1; $i<=8; $i++) {
            $ink_var = "ink_$i";
            if($i <= $ink_number) {
                $$ink_var = $row[$ink_var];
            }
            else {
                $$ink_var = null;
            }
        
            $color_var = "color_$i";
            if($i <= $ink_number) {
                $$color_var = $row[$color_var];
            }
            else {
                $$color_var = null;
            }
        
            $cmyk_var = "cmyk_$i";
            if($i <= $ink_number) {
                $$cmyk_var = $row[$cmyk_var];
            }
            else {
                $$cmyk_var = null;
            }
        
            $percent_var = "percent_$i";
            if($i <= $ink_number) {
                $$percent_var = $row[$percent_var];
            }
            else {
                $$percent_var = null;
            }
        
            $cliche_var = "cliche_$i";
            if($i <= $ink_number) {
                $$cliche_var = $row["cliche_$i"];
            }
            else {
                $$cliche_var = null;
            }
        }

        $extracharge = $row['extracharge']; // Наценка
        $ski_width = $row['ski_width']; // Ширина лыж
        $no_ski = $row['no_ski']; // Печать без лыж (ДА/НЕТ)
    }
    else {
        $error_message = "Ошибка при получении из базы исходных данных для расчёта";
    }
    
    // Курс доллара и евро
    $euro = null;
    $usd = null;
        
    if(empty($error_message)) {
        $sql = "select euro, usd from currency order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $euro = $row['euro'];
            $usd = $row['usd'];
        }
            
        if(empty($euro) || empty($usd)) {
            $error_message = "Не заданы курсы валют";
        }
    }
        
    // Удельный вес
    $c_density = null;
        
    if(empty($error_message)) {
        if(!empty($individual_density)) {
            $c_density = $individual_density;
        }
        else {
            $c_density = $density;
        }
        
        if(empty($c_density)) {
            $error_message = "Для данной толщины плёнки не задан удельный вес";
        }
    }
    
    // Цена материала
    $c_price = null;
        
    if(empty($error_message)) {
        if(!empty($individual_price)) { // Если материал вводился вручную, цена также введена вручную
            $c_price = $individual_price;
        }
        else if(!empty ($brand_name) && !empty ($thickness)) { // Если материал выбирался из списка, цену берём из базы
            $sql = "select price, currency from film_price where brand_name = '$brand_name' and thickness = $thickness and date <= current_timestamp() order by date desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $c_price = $row['price'];
                
                // Если цена не в рублях, переводим её в рубли
                if($row['currency'] == USD) {
                    $c_price *= $usd;
                }
                else if($row['currency'] == EURO) {
                    $c_price *= $euro;
                }
            }
        }
            
        if(empty($c_price)) {
            $error_message = "Для данной толщины плёнки не указана цена";
        }
    }
        
    // Удельный вес ламинации 1
    $c_density_lam1 = null;
        
    if(empty($error_message)) {
        if(!empty($lamination1_individual_density)) { // Если материал ламинации 1 введён вручную
            $c_density_lam1 = $lamination1_individual_density;
        }
        else { // Если материал ламинации 1 выбран из списка
            $c_density_lam1 = $lamination1_density;
        }
            
        if(!empty($lamination1_brand_name) && !empty($lamination1_thickness) && empty($c_density_lam1)) {
            $error_message = "Для данной толщина ламинации 1 не задан удельный вес";
        }
    }
        
    // Цена ламинации 1
    $c_price_lam1 = null;
        
    if(empty($error_message)) {
        if(!empty($lamination1_individual_price)) { // Если материал ламинации 1 введён вручную, цена также введена вручную
            $c_price_lam1 = $lamination1_individual_price;
        }
        else if(!empty ($lamination1_brand_name) && !empty ($lamination1_thickness)) { // Если материал ламинации 1 выбран из списка, цену берём из базы
            $sql = "select price, currency from film_price where brand_name = '$lamination1_brand_name' and thickness = $lamination1_thickness and date <= current_timestamp() order by date desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $c_price_lam1 = $row['price'];
                    
                if($row['currency'] == USD) {
                    $c_price_lam1 *= $usd;
                }
                else if($row['currency'] == EURO) {
                    $c_price_lam1 *= $euro;
                }
            }
        }
            
        if(empty($c_price_lam1) && !empty($c_density_lam1)) {
            $error_message = "Для данной толщины ламинации 1 не указана цена";
        }
    }
        
    // Удельный вес ламинации 2
    $c_density_lam2 = null;
        
    if(empty($error_message)) {
        if(!empty($lamination2_individual_density)) { // Удельный вес ламинации 2 введён вручную
            $c_density_lam2 = $lamination2_individual_density;
        }
        else { // Удельный вес ламинации 1 выбран из списка
            $c_density_lam2 = $lamination2_density;
        }
            
        if(!empty($lamination2_brand_name) && !empty($lamination2_thickness) && empty($c_density_lam2)) {
            $error_message = "Для данной толщины ламинации 2 не задан удельный вес";
        }
    }
    
    // Цена ламинации 2
    $c_price_lam2 = null;
        
    if(empty($error_message)) { // Если материал ламинации 2 введён вручную, цена также введена вручную
        if(!empty($lamination2_individual_price)) {
            $c_price_lam2 = $lamination2_individual_price;
        }
        else if(!empty ($lamination2_brand_name) && !empty ($lamination2_thickness)) { // Если материал ламинации 2 выбран из списка, цену получаем из базы
            $sql = "select price, currency from film_price where brand_name = '$lamination2_brand_name' and thickness = $lamination2_thickness and date <= current_timestamp() order by date desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $c_price_lam2 = $row['price'];
                    
                if($row['currency'] == USD) {
                    $c_price_lam2 *= $usd;
                }
                else if($row['currency'] == EURO) {
                    $c_price_lam2 *= $euro;
                }
            }
        }
            
        if(empty($c_price_lam2) && !empty($c_density_lam2)) {
            $error_message = "Для данной толщины ламинации 2 не указана цена";
        }
    }
        
    // Данные о приладке для печати
    $tuning_times = array(); // Массив - время приладки для каждой машины
    $tuning_lengths = array(); // Массив - метраж приладки для каждой машины
    $tuning_waste_percents = array(); // Массив - процент отходов для каждой машины
        
    $sql = "select machine_id, time, length, waste_percent "
            . "from norm_tuning "
            . "where date in (select max(date) from norm_tuning group by machine_id)";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $tuning_times[$row['machine_id']] = $row['time'];
        $tuning_lengths[$row['machine_id']] = $row['length'];
        $tuning_waste_percents[$row['machine_id']] = $row['waste_percent'];
    }
    
    // Данные для приладки для ламинации
    $laminator_tuning_time = null; // Время приладки ламинатора
    $laminator_tuning_length = null; // Метраж приладки ламинатора
    $laminator_tuning_waste_percent = null; // Процент отходов ламинатора
    
    $sql = "select time, length, waste_percent from norm_laminator_tuning order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminator_tuning_time = $row['time'];
        $laminator_tuning_length = $row['length'];
        $laminator_tuning_waste_percent = $row['waste_percent'];
    }
        
    // Данные о машинах    
    $machine_speeds = array(); // Массив - скорость работы каждой машины
    $machine_prices = array(); // Массов - стоимость работы каждой машины
        
    $sql = "select machine_id, price, speed "
            . "from norm_machine "
            . "where date in (select max(date) from norm_machine group by machine_id)";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $machine_prices[$row['machine_id']] = $row['price'];
        $machine_speeds[$row['machine_id']] = $row['speed'];
    }
    
    // Данные о ламинаторе
    $laminator_price = null; // Скорость работы ламинатора
    $laminator_speed = null; // Стоимость работы ламинатора
    
    $sql = "select price, speed from norm_laminator order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminator_price = $row['price'];
        $laminator_speed = $row['speed'];
    }
    
    $machine_ids = array(); // Массив - идентификаторы каждой машины для поиска по наименованию
    $machine_shortnames = array(); // Массив - наименования каждой машины для поиска по идентификатора
    
    $sql = "select id, shortname from machine";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $machine_ids[$row['shortname']] = $row['id'];
        $machine_shortnames[$row['id']] = $row['shortname'];
    }
    
    // Идентификатор текущей машины
    // Если тип машины "comiflex", текущая машина - Comiflex
    // Если тип машины "zbs" и количество красок больше 6, то машина - ZBS3
    // Если тип машины "zbs" и количество красок меньше или равно 6, то машина - ZBS1
    $machine_id = null;
    
    if(!empty($machine_type) && !empty($ink_number)) {
        if($machine_type == COMIFLEX) {
            $machine_id = $machine_ids[COMIFLEX];
        }
        elseif($ink_number > 6) {
            $machine_id = $machine_ids['zbs3'];
        }
        else {
            $machine_id = $machine_ids['zbs1'];
        }
    }
        
    // Данные о форме
    $cliche_flint = null; // Стоимость формы Флинт за см2
    $cliche_kodak = null; // Стоимость формы Кодак за см2
    $cliche_tver = null; // Стоимость тверской формы за см2
    $cliche_film = null; // Стоимость плёнки для формы за см2
    $cliche_tver_coeff = null; // Коэффициент удорожания для тверских форм
    $cliche_additional_size = null; // Величина припуска
    $cliche_scotch = null; // Стоимость скотча за м2
        
    $sql = "select flint, flint_currency, kodak, kodak_currency, tver, tver_currency, film, film_currency, tver_coeff, overmeasure, scotch, scotch_currency "
            . "from norm_form order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $cliche_flint = $row['flint'];
                
        if($row['flint_currency'] == USD) {
            $cliche_flint *= $usd;
        }
        else if($row['flint_currency'] == EURO) {
            $cliche_flint *= $euro;
        }
                
        $cliche_kodak = $row['kodak'];
                
        if($row['kodak_currency'] == USD) {
            $cliche_kodak *= $usd;
        }
        else if($row['kodak_currency'] == EURO) {
            $cliche_kodak *= $euro;
        }
                
        $cliche_tver = $row['tver'];
                
        if($row['tver_currency'] == USD) {
            $cliche_tver *= $usd;
        }
        else if($row['tver_currency'] == EURO) {
            $cliche_tver *= $euro;
        }
                
        $cliche_film = $row['film'];
                
        if($row['film_currency'] == USD) {
            $cliche_film *= $usd;
        }
        if($row['film_currency'] == EURO) {
            $cliche_film *= $euro;
        }
                
        $cliche_tver_coeff = $row['tver_coeff'];
        $cliche_additional_size = $row['overmeasure'];
                
        $cliche_scotch = $row['scotch'];
                
        if($row['scotch_currency'] == USD) {
            $cliche_scotch *= $usd;
        }
        if($row['scotch_currency'] == EURO) {
            $cliche_scotch *= $euro;
        }
    }
        
    // Данные о красках
    $ink_c = null; // Стоимость краски Cyan
    $ink_c_expense = null; // Расход краски Cyan
    $ink_m = null; // Стоимость краски Magenta
    $ink_m_expense = null; // Расход краски Magenta
    $ink_y = null; // Стоимость краски Yellow
    $ink_y_expense = null; // Расход краски Yellow
    $ink_k = null; // Стоимость краски Kontur
    $ink_k_expense = null; // Расход краски Kontur
    $ink_white = null; // Стоимость белой краски
    $ink_white_expense = null; // Расход белой краски
    $ink_panton = null; // Стоимость пантона
    $ink_panton_expense = null; // Расход пантона
    $ink_lacquer = null; // Стоимость лака
    $ink_lacquer_expense = null; // Расход лака
    $ink_ink_solvent = null; // Отношение краски к растворителю в процентах
    $ink_solvent_etoxipropanol = null; // Стоимость этоксипропанола
    $ink_solvent_flexol82 = null; // Стоимость флексоля 82
    $ink_lacquer_solvent = null; // Отношение лака к растворителю в процентах
    $ink_min_price = null; // Ограничение на минимальную стоимость в рублях
        
    $sql = "select c, c_currency, c_expense, m, m_currency, m_expense, y, y_currency, y_expense, k, k_currency, k_expense, white, white_currency, white_expense, panton, panton_currency, panton_expense, lacquer, lacquer_currency, lacquer_expense, ink_solvent, solvent_etoxipropanol, solvent_etoxipropanol_currency, solvent_flexol82, solvent_flexol82_currency, lacquer_solvent, min_price "
            . "from norm_ink order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $ink_c = $row['c'];
                
        if($row['c_currency'] == USD) {
            $ink_c *= $usd;
        }
        else if($row['c_currency'] == EURO) {
            $ink_c *= $euro;
        }
            
        $ink_c_expense = $row['c_expense'];
        $ink_m = $row['m'];
            
        if($row['m_currency'] == USD) {
            $ink_m *= $usd;
        }
        else if($row['m_currency'] == EURO) {
            $ink_m *= $euro;
        }
            
        $ink_m_expense = $row['m_expense'];
        $ink_y = $row['y'];
                
        if($row['y_currency'] == USD) {
            $ink_y *= $usd;
        }
        else if($row['y_currency'] == EURO) {
            $ink_y *= $euro;
        }
            
        $ink_y_expense = $row['y_expense'];
        $ink_k = $row['k'];
            
        if($row['k_currency'] == USD) {
            $ink_k *= $usd;
        }
        else if($row['k_currency'] == EURO) {
            $ink_k *= $euro;
        }
            
        $ink_k_expense = $row['k_expense'];
        $ink_white = $row['white'];
            
        if($row['white_currency'] == USD) {
            $ink_white *= $usd;
        }
        else if($row['white_currency'] == EURO) {
            $ink_white *= $euro;
        }
            
        $ink_white_expense = $row['white_expense'];
        $ink_panton = $row['panton'];
            
        if($row['panton_currency'] == USD) {
            $ink_panton *= $usd;
        }
        else if($row['panton_currency'] == EURO) {
            $ink_panton *= $euro;
        }
            
        $ink_panton_expense = $row['panton_expense'];
        $ink_lacquer = $row['lacquer'];
            
        if($row['lacquer_currency'] == USD) {
            $ink_lacquer *= $usd;
        }
        else if($row['lacquer_currency'] == EURO) {
            $ink_lacquer *= $euro;
        }
            
        $ink_lacquer_expense = $row['lacquer_expense'];
        $ink_ink_solvent = $row['ink_solvent'];
        $ink_solvent_etoxipropanol = $row['solvent_etoxipropanol'];
            
        if($row['solvent_etoxipropanol_currency'] == USD) {
            $ink_solvent_etoxipropanol *= $usd;
        }
        else if($row['solvent_etoxipropanol_currency'] == EURO) {
            $ink_solvent_etoxipropanol *= $euro;
        }
            
        $ink_solvent_flexol82 = $row['solvent_flexol82'];
                
        if($row['solvent_flexol82_currency'] == USD) {
            $ink_solvent_flexol82 *= $usd;
        }
        else if($row['solvent_flexol82_currency'] == EURO) {
            $ink_solvent_flexol82 *= $euro;
        }
            
        $ink_lacquer_solvent = $row['lacquer_solvent'];
        $ink_min_price = $row['min_price'];
    }
    
    // Данные о клее при ламинации
    $glue_price = null; // Стоимость клея
    $glue_expense = null; // Расход клея
    $glue_expense_pet = null; // Расход клея для ламинации ПЭТ
    $glue_solvent_price = null; // Стоимость растворителя для клея
    $glue_glue_part = null; // Доля клея в растворе
    $glue_solvent_part = null; // Доля растворителя в растворе
        
    $sql = "select glue, glue_currency, glue_expense, glue_expense_pet, solvent, solvent_currency, glue_part, solvent_part from norm_glue order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $glue_price = $row['glue'];
            
        if($row['glue_currency'] == USD) {
            $glue_price *= $usd;
        }
        else if($row['glue_currency'] == EURO) {
            $glue_price *= $euro;
        }
        
        $glue_expense = $row['glue_expense'];
        $glue_expense_pet = $row['glue_expense_pet'];
        $glue_solvent_price = $row['solvent'];
        
        if($row['solvent_currency'] == USD) {
            $glue_solvent_price *= $usd;
        }
        else if($row['solvent_currency'] == EURO) {
            $glue_solvent_price *= $euro;
        }
        
        $glue_glue_part = $row['glue_part'];
        $glue_solvent_part = $row['solvent_part'];
    }
    
    //********************************************************
    // НАЧАЛО РАСЧЁТОВ
        
    // Площадь тиража чистая, м2
    // если в кг: 1000 * объём заказа / удельный вес материала
    // если в шт: ширина ручья / 1000 * длина этикетки вдоль рапорта вала / 1000 * количество этикеток в заказе
    $pure_area = 0;
        
    if($unit == 'kg' && !empty($quantity) && !empty($c_density)) {
        $pure_area = 1000 * $quantity / ($c_density + (empty($c_density_lam1) ? 0 : $c_density_lam1) + (empty($c_density_lam2) ? 0 : $c_density_lam2));
    }
    else if($unit == 'pieces' && !empty ($stream_width) && !empty ($label_length) && !empty ($quantity)) {
        $pure_area = $stream_width / 1000 * $label_length / 1000 * $quantity;
    }
    else {
        $error_message = "Отсутствуют данные об объёме заказа";
    }
        
    // Ширина тиража обрезная, мм
    // ширина ручья * количество ручьёв
    $pure_width = 0;
        
    if(!empty($stream_width) && !empty($streams_number)) {
        $pure_width = $stream_width * $streams_number;
    }
    else {
        $error_message = "Отсутствуют данные о ширине ручья и количестве ручьёв";
    }
        
    // Длина тиража чистая, м
    // площадь тиража чистая / ширина тиража обрезная
    if(!empty($pure_width) && $pure_width > 0) {
        $pure_length = ($pure_area ?? 0) / $pure_width * 1000;
    }
    else {
        $error_message = "Отсутствуют данные о ширине тиража";
    }
        
    // Длина тиража чистая с ламинацией, м
    // длина тиража чистая * (процент отходов для ламинатора + 100) / 100;
    $pure_length_lam = ($pure_length ?? 0) * ($laminator_tuning_waste_percent + 100) / 100;
        
    // Длина тиража с отходами, м
    // если есть печать: длина тиража чистая + (длина тиража чистая * процент отхода машины) / 100 + длина приладки для машины * число красок
    // если нет печати, но есть ламинация: длина тиража чистая с ламинацией + длина приладки ламинации
    $dirty_length = 0;
        
    if(!empty($machine_id) && !empty($ink_number)) {
        $dirty_length = ($pure_length ?? 0) + (($pure_length ?? 0) * $tuning_waste_percents[$machine_id] / 100 + $tuning_lengths[$machine_id] * $ink_number);
    }
    elseif(!empty ($lamination1_brand_name)) {
        $dirty_length = ($pure_length_lam ?? 0) + $laminator_tuning_length;
    }
    else {
        $error_message = "Если не указана печатная машина, должна быть добавлена хоть одна ламинация";
    }
        
    // Ширина тиража с отходами, мм
    // с лыжами: ширина лыж + ширина тиража обрезная
    // без лыж: ширина тиража обрезная
    // затем отругляем ширину тиража с отходами до возможности деления на 5 без остатка
    $dirty_width = null;
    
    if($no_ski) {
        $dirty_width = $pure_width / 1000;
    }
    elseif(!empty ($ski_width)) {
        $dirty_width = ($pure_width + $ski_width) / 1000;
    }
    else {
        $error_message = "Отсутствуют данные о ширине лыж";
    }
    
    if(!empty($dirty_width)) {
        $vari = intval($dirty_width * 1000);
        $varcc = $vari % 5;
        $numiterazij = 0;
            
        if($varcc > 0) {
            while ($varcc > 0) {
                $vari++;
                $varcc = $vari % 5;
                $numiterazij++;
                if($numiterazij > 500) break;
            }
            
            $varid = doubleval($vari);
            
            if($varid !== null) {
                $dirty_width = $varid / 1000;
            }
        }
        
        if($dirty_width !== null) {
            $dirty_width *= 1000;
        }
    }
    else {
        $error_message = "Отсутствуют данные о ширине тиража с отходами";
    }
    
    // Площадь тиража с отходами, м2
    // длина тиража с отходами * ширина тиража с отходами
    $dirty_area = 0;
    
    if(!empty($dirty_width)) {
        $dirty_area = ($dirty_length ?? 0) * $dirty_width / 1000;
    }
    else {
        $error_message = "Отсутствуют данные о ширине тиража с отходами";
    }
    
    // Вес материала печати чистый, кг
    // площадь тиража чистая * удельный вес материала / 1000
    $pure_weight = 0;
    
    if(!empty($c_density)) {
        $pure_weight = ($pure_area ?? 0) * $c_density / 1000;
    }
    else {
        $error_message = "Отсутствуют данные об удельном весе материала";
    }
    
    // Вес материала печати с отходами, кг
    // площадь тиража с отходами * удельный вес материала / 1000
    $dirty_weight = 0;
    
    if(!empty($c_density)) {
        $dirty_weight = ($dirty_area ?? 0) * $c_density / 1000;
    }
    else {
        $error_message = "Отсутствуют данные об удельном весе материала";
    }
    
    // Стоимость материала печати, руб
    // если сырьё заказчика, то стоимость материала равна 0
    // иначе: вес материала печати с отходами * цена материала за 1 кг
    $material_price = null;
        
    if($customers_material) {
        $material_price = 0;
    }
    elseif(!empty($c_price)) {
        $material_price = ($dirty_weight ?? 0) * $c_price;
    }
    else {
        $error_message = "Отсутствуют данные о стоимости материала";
    }
    
    //***************************************************************************
    // СТОИМОСТЬ ПЕЧАТИ
    
    $print_time = null; // Время печати тиража без приладки, ч
    $tuning_time = null; // Время приладки, ч
    $print_tuning_time = null; // Время печати с приладкой, ч
    $print_price = null; // Стоимость печати, руб
    
    $cliche_area = null; // Площадь печатной формы, см2
    $cliche_flint_price = null; // Стоимость 1 новой формы Флинт, руб
    $cliche_kodak_price = null; // Стоимость 1 новой формы Кодак, руб
    $cliche_tver_price = null; // Стоимость 1 новой формы Тверь, руб
    $cliche_price = null; // Стоимость комплекта печатных форм
    
    $ink_price = null; // Стоимость краски + лака + растворителя, руб
    
    if(!empty($machine_id)) {
        // Время печати тиража без приладки, ч
        // длина тиража чистая / 1000 / скорость работы флекс машины
        $print_time = ($pure_length ?? 0) / 1000 / $machine_speeds[$machine_id];
    
        // Время приладки, ч
        // время приладки каждой краски * число красок
        if(!empty($ink_number)) {
            $tuning_time = $tuning_times[$machine_id] / 60 * $ink_number;
        }
        else {
            $error_message = "Отсутствуют данные о количестве красок";
        }
    
        // Время печати с приладкой, ч
        // время печати + время приладки
        $print_tuning_time = ($print_time ?? 0) + ($tuning_time ?? 0);
        
        // Стоимость печати, руб
        // время печати с приладкой * стоимость работы машины
        $print_price = ($print_tuning_time ?? 0) * $machine_prices[$machine_id];
        
        //***************************************************************
        
        // Площадь печатной формы, см2
        // (припуск * 2 + ширина тиража с отходами * 100) * (припуск * 2 + рапорт вала / 10)
        if(!empty($raport)) {
            $cliche_area = (($cliche_additional_size ?? 0) * 2 + ($dirty_width ?? 0) / 1000 * 100) * (($cliche_additional_size ?? 0) * 2 + $raport / 10);
        }
        else {
            $error_message = "Отсутствуют данные о рапорте";
        }
        
        // Стоимость 1 новой формы Флинт, руб
        // площадь печатной формы * стоимость 1 см2 формы
        $cliche_flint_price = ($cliche_area ?? 0) * ($cliche_flint ?? 0);
        
        // Стоимость 1 новой формы Кодак, руб
        // площадь печатной формы * стоимость 1 см2 формы 
        $cliche_kodak_price = ($cliche_area ?? 0) * ($cliche_kodak ?? 0);
        
        // Стоимость 1 новой формы Тверь, руб
        // площадь печатной формы * (стоимость 1 см2 формы + стоимость 1 см2 плёнок * коэфф. удорожания для тверских форм)
        $cliche_tver_price = ($cliche_area ?? 0) * (($cliche_tver ?? 0) + ($cliche_film ?? 0) * ($cliche_tver_coeff ?? 0));
        
        // Стоимость комплекта печатных форм
        // сумма стоимости форм для каждой краски
        if(!empty($cliche_flint_price) && !empty($cliche_kodak_price) && !empty($cliche_tver_price)) {
            // Перебираем все используемые краски
            if(!empty($ink_number)){
                for($i=1; $i<=8; $i++) {
                    if($ink_number >= $i) {
                        $ink_var = "ink_$i";
                        $cliche_var = "cliche_$i";
                        if(!empty($$ink_var)) {        
                            if($$cliche_var == 'old') {
                                $cliche_price += 0;
                            }
                            elseif($$cliche_var == 'flint') {
                                $cliche_price += $cliche_flint_price;
                            }
                            elseif($$cliche_var == 'kodak') {
                                $cliche_price += $cliche_kodak_price;
                            }
                            elseif($$cliche_var == 'tver') {
                                $cliche_price += $cliche_tver_price;
                            }
                        }
                    }
                }
            }
            else {
                $error_message = "Отсутствуют данные о количестве красок";
            }
        }
        else {
            $error_message = "Отсутствуют или неполные данные о стоимости форм";
        }
        
        // Стоимость краски + лака + растворителя, руб
        if(!empty($dirty_area)) {
            $ink_price = 0;
            
            // Перебираем все используемые краски
            for($i=1; $i<=8; $i++) {
                if(!empty($ink_number) && $ink_number >= $i) {
                    $ink_var = "ink_$i";
                    $percent_var = "percent_$i";
                    $cmyk_var = "cmyk_$i";
                
                    if(!empty($$ink_var)) {
                        // Площадь запечатки, м2
                        // площадь тиража с отходами * процент краски / 100
                        $ink_area = $dirty_area * $$percent_var / 100;
                    
                        // Расход краски, г/м2
                        $ink_expense_final = 0;
                    
                        // Стоимость краски за 1 кг, руб
                        $ink_price_final = 0;
                    
                        // Стоимость растворителя за 1 кг, руб
                        $solvent_price_final = 0;
                    
                        // Процент краски по отношению к растворителю
                        $ink_solvent_final = 0;
                    
                        switch ($$ink_var) {
                            case CMYK:
                                switch ($$cmyk_var) {
                                    case CYAN:
                                        $ink_expense_final = $ink_c_expense;
                                        $ink_price_final = $ink_c;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                        $ink_solvent_final = $ink_ink_solvent;
                                        break;
                                    case MAGENTA:
                                        $ink_expense_final = $ink_m_expense;
                                        $ink_price_final = $ink_m;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                        $ink_solvent_final = $ink_ink_solvent;
                                        break;
                                    case YELLOW:
                                        $ink_expense_final = $ink_y_expense;
                                        $ink_price_final = $ink_y;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                        $ink_solvent_final = $ink_ink_solvent;
                                        break;
                                    case KONTUR:
                                        $ink_expense_final = $ink_k_expense;
                                        $ink_price_final = $ink_k;
                                        $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                        $ink_solvent_final = $ink_ink_solvent;
                                        break;
                                };
                                break;
                            case PANTON:
                                $ink_expense_final = $ink_panton_expense;
                                $ink_price_final = $ink_panton;
                                $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                $ink_solvent_final = $ink_ink_solvent;
                                break;
                            case WHITE:
                                $ink_expense_final = $ink_white_expense;
                                $ink_price_final = $ink_white;
                                $solvent_price_final = $machine_shortnames[$machine_id] == COMIFLEX ? $ink_solvent_flexol82 : $ink_solvent_etoxipropanol;
                                $ink_solvent_final = $ink_ink_solvent;
                                break;
                            case LACQUER:
                                $ink_expense_final = $ink_lacquer_expense;
                                $ink_price_final = $ink_lacquer;
                                $solvent_price_final = $ink_solvent_flexol82;
                                $ink_solvent_final = $ink_lacquer_solvent;
                                break;
                        }
                
                        // Количество краски, кг
                        // площадь запечатки * расход краски / 1000
                        $ink_quantity = $ink_area * $ink_expense_final / 1000;
                    
                        // Стоимость неразведённой краски, руб
                        // количество краски * стоимость краски за 1 кг
                        $ink_price_sum = $ink_quantity * $ink_price_final;
                    
                        // Проверяем, чтобы стоимость была не меньше минимальной стоимости
                        // Если меньше, то присваиваем стоимости значение минимальной стоимости
                        if($ink_price_sum < $ink_min_price) {
                            $ink_price_sum = $ink_min_price;
                        }
                    
                        // Стоимость растворителя
                        // количество краски * стоимость растворителя за 1 кг
                        $solvent_price_sum = $ink_quantity * $solvent_price_final;
                    
                        // Стоимость разведённой краски
                        // (стоимость краски * процент краски / 100) + (стоимость краски * (100 - процент краски) / 100)
                        $ink_solvent_price_sum = ($ink_price_sum * $ink_solvent_final / 100) + ($solvent_price_sum * (100 - $ink_solvent_final) / 100);
                    
                        // Итого стоимость краски + лака + растворителя, руб
                        $ink_price += $ink_solvent_price_sum;
                    }
                }
            }
        }
        else {
            $error_message = "Отсутствуют данные о площади тиража с отходами";
        }
    }
        
    //***************************************************
    // СТОИМОСТЬ ЛАМИНАЦИИ
    
    $price_lam_total = 0; // Итого стоимость ламинации, руб
        
    $pure_weight_lam1 = null; // Вес материала ламинации 1 чистый, кг
    $dirty_weight_lam1 = null; // Вес материала ламинации 1 с отходами, кг
    $price_lam1_material = null; // Стоимость материала ламинации 1, руб
    $price_lam1_glue = null; // Стоимость клеевого раствора 1, руб
    $price_lam1_work = null; // Стоимость процесса ламинации 1, руб
                    
    if(!empty($lamination1_brand_name)) {
        // Вес материала ламинации 1 чистый, кг
        // площадь тиража чистая * удельный вес ламинации 1 / 1000
        $pure_weight_lam1 = ($pure_area ?? 0) * ($c_density_lam1 ?? 0) / 1000;
                        
        // Вес материала ламинации 1 с отходами, кг
        // (длина тиража с ламинацией + длина материала для приладки при ламинации) * ширина тиража с отходами (в метрах) * удельный вес ламинации 1 / 1000
        $dirty_weight_lam1 = (($pure_length_lam ?? 0) + $laminator_tuning_length) * ($dirty_width ?? 0) / 1000 * ($c_density_lam1 ?? 0) / 1000;
            
        // Стоимость материала ламинации 1, руб
        // если материал заказчика, то стоимость равна 0
        // иначе удельная стоимость материала ламинации * вес материала с отходами
        if($lamination1_customers_material) {
            $price_lam1_material = 0;
        }
        else {
            $price_lam1_material = ($c_price_lam1 ?? 0) * ($dirty_weight_lam1 ?? 0);
        }
            
        // Удельная стоимость клеевого раствора 1, руб
        // (стоимость клея * доля клея / (доля клея + доля раствора)) + (стоимость растворителя для клея * доля раствора / (доля клея + доля раствора))
        $glue_solvent_g = ($glue_price * $glue_glue_part / ($glue_glue_part + $glue_solvent_part)) + ($glue_solvent_price * $glue_solvent_part / ($glue_glue_part + $glue_solvent_part));
            
        // Стоимость клеевого раствора 1, руб
        // удельная стоимость клеевого раствора кг/м2 * расход клея кг/м2 * (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)
        // Если марка плёнки начинается на pet
        // удельная стоимость клеевого раствора кг/м2 * расход клея кг/м2 * (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)
        $price_lam1_glue = null;
            
        if(stripos($brand_name, 'pet') === 0 || stripos($lamination1_brand_name, 'pet') === 0) {
            $price_lam1_glue = $glue_solvent_g / 1000 * $glue_expense_pet * (($pure_length_lam ?? 0) * $lamination_roller_width / 1000 + $laminator_tuning_length);
        }
        else {
            $price_lam1_glue = $glue_solvent_g / 1000 * $glue_expense * (($pure_length_lam ?? 0) * $lamination_roller_width / 1000 + $laminator_tuning_length);
        }
            
        // Стоимость процесса ламинации 1, руб
        // стоимость работы оборудования + (длина чистая с ламинацией / скорость работы оборудования) * стоимость работы оборудования
        $price_lam1_work = $laminator_price + (($pure_length_lam ?? 0) / 1000 / $laminator_speed) * $laminator_price;
            
        // Итого стоимость ламинации 1, руб
        // материал1 + клей1 + процесс1
        $price_lam_total += ($price_lam1_material ?? 0) + ($price_lam1_glue ?? 0) + ($price_lam1_work ?? 0);
    }
        
    $pure_weight_lam2 = null; // Вес материала ламинации 2 чистый, кг
    $dirty_weight_lam2 = null; // Вес материала ламинации 2 с отходами 2, кг
    $price_lam2_material = null; // Стоимость материала ламинации 2, руб
    $price_lam2_glue = null; // Стоимость клеевого раствора 2, руб
    $price_lam2_work = null; // Стоимость процесса ламинации 2, руб
        
    if(!empty($lamination2_brand_name)) {
        // Вес материала ламинации 2 чистый, кг
        // площадь тиража чистая * удельный вес ламинации 1 / 1000
        $pure_weight_lam2 = ($pure_area ?? 0) * ($c_density_lam2 ?? 0) / 1000;
                        
        // Вес материала ламинации 2 с отходами 2, кг
        // (длина тиража с ламинацией + длина материала для приладки при ламинации) * ширина тиража с отходами (в метрах) * удельный вес ламинации 1 / 1000
        $dirty_weight_lam2 = (($pure_length_lam ?? 0) + $laminator_tuning_length) * $dirty_width / 1000 * $c_density_lam2 / 1000;
            
        // Стоимость материала ламинации 2, руб
        // удельная стоимость материала ламинации * вес материала с отходами
        if($lamination2_customers_material) {
            $price_lam2_material = 0;
        }
        else {
            $price_lam2_material = ($c_price_lam2 ?? 0) * ($dirty_weight_lam2 ?? 0);
        }
            
        // Удельная стоимость клеевого раствора 2, руб
        // (стоимость клея * соотношение кл/раст / 100) + (стоимость растворителя для клея * (100 - соотношение кл/раст) / 100)
        $glue_solvent_g = ($glue_price * $glue_glue_part / ($glue_glue_part + $glue_solvent_part)) + ($glue_solvent_price * $glue_solvent_part / ($glue_glue_part + $glue_solvent_part));
            
        // Стоимость клеевого раствора 2, руб
        // удельная стоимость клеевого раствора кг/м2 * расход клея кг/м2 * (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)
        // Если марка плёнки начинается на pet
        // удельная стоимость клеевого раствора кг/м2 * расход клея кг/м2 * (чистая длина с ламинацией * ширина вала / 1000 + длина материала для приладки при ламинации)
        $price_lam2_glue = null;
            
        if(stripos($lamination2_brand_name, 'pet') === 0) {
            $price_lam2_glue = $glue_solvent_g / 1000 * $glue_expense_pet * (($pure_length_lam ?? 0) * $lamination_roller_width / 1000 + $laminator_tuning_length);
        }
        else {
            $price_lam2_glue = $glue_solvent_g / 1000 * $glue_expense * (($pure_length_lam ?? 0) * $lamination_roller_width / 1000 + $laminator_tuning_length);
        }
            
        // Стоимость процесса ламинации 2, руб
        // стоимость работы оборудования + (длина чистая с ламинацией / скорость работы оборудования) * стоимость работы оборудования
        $price_lam2_work = $laminator_price + (($pure_length_lam ?? 0) / 1000 / $laminator_speed) * $laminator_price;
            
        // Итого стоимость ламинации, руб
        // материал1 + материал2 + клей1 + клей2 + процесс1 + процесс2
        $price_lam_total += ($price_lam2_material ?? 0) + ($price_lam2_glue ?? 0) + ($price_lam2_work ?? 0);
    }
        
    //***************************************************************************
        
    // Вес материала готовой продукции чистый
    // площадь тиража чистая * удельный вес материала + удельный вес ламинации 1 + удельный вес ламинации 2 / 1000
    $pure_weight_total = ($pure_area ?? 0) * (($c_density ?? 0) + ($c_density_lam1 ?? 0) + ($c_density_lam2 ?? 0)) / 1000;
        
    // Вес материала готовой продукции с отходами
    // площадь тиража с отходами * удельный вес материала + удельный вес ламинации 1 + удельный вес ламинации 2 / 1000
    $dirty_weight_total = ($dirty_area ?? 0) * (($c_density ?? 0) + ($c_density_lam1 ?? 0) + ($c_density_lam2 ?? 0)) / 1000;
        
    //***************************************************************************
        
    // Итого себестоимость без форм, руб
    // m_dbEdit42 = m_pY10 + m_pY3 + m_dbEdit6 + dbEdit7 + CostScothF
    // стоимость материала печати + стоимость печати + стоимость красок, лака и растворителя + итого себестоимость ламинации + (стоимость скотча для наклейки форм * число красок * площадь печатной формы / 10000)
    $cost_no_cliche = ($material_price ?? 0) + ($print_price ?? 0) + ($ink_price ?? 0) + ($price_lam_total ?? 0) + (($cliche_scotch ?? 0) * (empty($ink_number) ? 0 : intval($ink_number)) * ($cliche_area ?? 0) / 10000);
        
    // Итого себестоимость с формами, руб
    // итого стоимость без форм + стоимость комплекта печатных форм
    $cost_with_cliche = ($cost_no_cliche ?? 0) + ($cliche_price ?? 0);
        
    // Итого себестоимость за 1 кг без форм, руб
    // итого себестоимость без форм / j,]`v заказа
    $cost_no_cliche_kg = null;
    
    if($unit == "kg") {
        if(!empty($quantity)) {
            $cost_no_cliche_kg = ($cost_no_cliche ?? 0) / $quantity;
        }
        else {
            $error_message = "Отсутствуют данные об объёме заказа";
        }
    }
    else {
        $cost_no_cliche_kg = 0;
    }
        
    // Итого себестоимость за 1 кг с формами, руб
    // итого стоимость с формами / объём заказа
    $cost_with_cliche_kg = null;
    
    if($unit == "kg") {
        if(!empty($quantity)) {
            $cost_with_cliche_kg = ($cost_with_cliche ?? 0) / $quantity;
        }
        else {
            $error_message = "Отсутствуют данные об объёме заказа";
        }
    }
    else {
        $cost_with_cliche_kg = 0;
    }
        
    // Итого себестоимость за 1 шт без форм, руб
    // итого себестоимость без форм / объём заказа
    $cost_no_cliche_pieces = null;
    
    if($unit == "pieces") {
        if(!empty($quantity)) {
            $cost_no_cliche_pieces = ($cost_no_cliche ?? 0) / $quantity;
        }
        else {
            $error_message = "Отсутствуют данные об объёме заказа";
        }
    }
    else {
        $cost_no_cliche_pieces = 0;
    }
        
    // Итого себестоимость за 1 шт с формами, руб
    // итого стоимость с формами / объём заказа
    $cost_with_cliche_pieces = null;
    
    if($unit == "pieces") {
        if(!empty($quantity)) {
            $cost_with_cliche_pieces = ($cost_with_cliche ?? 0) / $quantity;
        }
        else {
            $error_message = "Отсутствуют данные об объёме заказа";
        }
    }
    else {
        $cost_with_cliche_pieces = 0;
    }
    
    // *************************************
    // Сохранение расчёта в базу
    if(empty($error_message)) {
        if($pure_area === null) $pure_area = "NULL";
        if($pure_width === null) $pure_width = "NULL";
        if($pure_length === null) $pure_length = "NULL";
        if($pure_length_lam === null) $pure_length_lam = "NULL";
        if($dirty_length === null) $dirty_length = "NULL";
        if($dirty_width === null) $dirty_width = "NULL";
        if($dirty_area === null) $dirty_area = "NULL";
        if($pure_weight === null) $pure_weight = "NULL";
        if($dirty_weight === null) $dirty_weight = "NULL";
        if($material_price === null) $material_price = "NULL";
        if($print_time === null) $print_time = "NULL";
        if($tuning_time === null) $tuning_time = "NULL";
        if($print_tuning_time === null) $print_tuning_time = "NULL";
        if($print_price === null) $print_price = "NULL";
        if($cliche_area === null) $cliche_area = "NULL";
        if($cliche_flint_price === null) $cliche_flint_price = "NULL";
        if($cliche_kodak_price === null) $cliche_kodak_price = "NULL";
        if($cliche_tver_price === null) $cliche_tver_price = "NULL";
        if($cliche_price === null) $cliche_price = "NULL";
        if($ink_price === null) $ink_price = "NULL";
        if($pure_weight_lam1 === null) $pure_weight_lam1 = "NULL";
        if($dirty_weight_lam1 === null) $dirty_weight_lam1 = "NULL";
        if($price_lam1_material === null) $price_lam1_material = "NULL";
        if($price_lam1_glue === null) $price_lam1_glue = "NULL";
        if($price_lam1_work === null) $price_lam1_work = "NULL";
        if($pure_weight_lam2 === null) $pure_weight_lam2 = "NULL";
        if($dirty_weight_lam2 === null) $dirty_weight_lam2 = "NULL";
        if($price_lam2_material === null) $price_lam2_material = "NULL";
        if($price_lam2_glue === null) $price_lam2_glue = "NULL";
        if($price_lam2_work === null) $price_lam2_work = "NULL";
        if($price_lam_total === null) $price_lam_total = "NULL";
        if($pure_weight_total === null) $pure_weight_total = "NULL";
        if($dirty_weight_total === null) $dirty_weight_total = "NULL";
        if($cost_no_cliche === null) $cost_no_cliche = "NULL";
        if($cost_with_cliche === null) $cost_with_cliche = "NULL";
        if($cost_no_cliche_kg === null) $cost_no_cliche_kg = "NULL";
        if($cost_with_cliche_kg === null) $cost_with_cliche_kg = "NULL";
        if($cost_no_cliche_pieces === null) $cost_no_cliche_pieces = "NULL";
        if($cost_with_cliche_pieces === null) $cost_with_cliche_pieces = "NULL";
                        
        $sql = "insert into request_calc_result (request_calc_id, pure_area, pure_width, pure_length, pure_length_lam, "
                . "dirty_length, dirty_width, dirty_area, pure_weight, dirty_weight, material_price, print_time, tuning_time, "
                . "print_tuning_time, print_price, cliche_area, cliche_flint_price, cliche_kodak_price, cliche_tver_price, cliche_price, "
                . "ink_price, pure_weight_lam1, dirty_weight_lam1, "
                . "price_lam1_material, price_lam1_glue, price_lam1_work, pure_weight_lam2, dirty_weight_lam2, price_lam2_material, "
                . "price_lam2_glue, price_lam2_work, price_lam_total, pure_weight_total, dirty_weight_total, cost_no_cliche, "
                . "cost_with_cliche, cost_no_cliche_kg, cost_with_cliche_kg, cost_no_cliche_pieces, cost_with_cliche_pieces) "
                . "values ($id, $pure_area, $pure_width, $pure_length, $pure_length_lam, "
                . "$dirty_length, $dirty_width, $dirty_area, $pure_weight, $dirty_weight, $material_price, $print_time, $tuning_time, "
                . "$print_tuning_time, $print_price, $cliche_area, $cliche_flint_price, $cliche_kodak_price, $cliche_tver_price, $cliche_price, "
                . "$ink_price, $pure_weight_lam1, $dirty_weight_lam1, "
                . "$price_lam1_material, $price_lam1_glue, $price_lam1_work, $pure_weight_lam2, $dirty_weight_lam2, $price_lam2_material, "
                . "$price_lam2_glue, $price_lam2_work, $price_lam_total, $pure_weight_total, $dirty_weight_total, $cost_no_cliche, "
                . "$cost_with_cliche, $cost_no_cliche_kg, $cost_with_cliche_kg, $cost_no_cliche_pieces, $cost_with_cliche_pieces)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$id = filter_input(INPUT_GET, 'id');

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
        . "c.brand_name, c.thickness, individual_brand_name, individual_price, individual_thickness, individual_density, c.customers_material, "
        . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_individual_brand_name, lamination1_individual_price, lamination1_individual_thickness, lamination1_individual_density, c.lamination1_customers_material, "
        . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_individual_brand_name, lamination2_individual_price, lamination2_individual_thickness, lamination2_individual_density, c.lamination2_customers_material, "
        . "c.label_length, c.stream_width, c.streams_number, c.machine_type, c.raport, c.number_on_raport, c.lamination_roller_width, c.ink_number, "
        . "c.ink_1, c.ink_2, c.ink_3, ink_4, ink_5, ink_6, ink_7, ink_8, "
        . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
        . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
        . "c.cliche_1, c.cliche_2, c.cliche_3, cliche_4, cliche_5, cliche_6, cliche_7, cliche_8, "
        . "comment, "
        . "c.extracharge, c.ski_width, c.no_ski, "
        . "(select id from techmap where request_calc_id = $id order by id desc limit 1) techmap_id, "
        . "(select id from request_calc_result where request_calc_id = $id order by id desc limit 1) request_calc_result_id, "
        . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
        . "wt.name work_type, "
        . "(select count(id) from request_calc where customer_id = c.customer_id and id <= c.id) num_for_customer, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) density, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_density, "
        . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_density "
        . "from request_calc c "
        . "left join customer cu on c.customer_id = cu.id "
        . "left join work_type wt on c.work_type_id = wt.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$request_name = $row['name'];
$work_type_id = $row['work_type_id'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$density = $row['density'];
$individual_brand_name = $row['individual_brand_name'];
$individual_price = $row['individual_price'];
$individual_thickness = $row['individual_thickness'];
$individual_density = $row['individual_density'];
$customers_material = $row['customers_material'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_density = $row['lamination1_density'];
$lamination1_individual_brand_name = $row['lamination1_individual_brand_name'];
$lamination1_individual_price = $row['lamination1_individual_price'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination1_individual_density = $row['lamination1_individual_density'];
$lamination1_customers_material = $row['lamination1_customers_material'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_density = $row['lamination2_density'];
$lamination2_individual_brand_name = $row['lamination2_individual_brand_name'];
$lamination2_individual_price = $row['lamination2_individual_price'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];
$lamination2_individual_density = $row['lamination2_individual_density'];
$lamination2_customers_material = $row['lamination2_customers_material'];
$label_length = $row['label_length'];
$stream_width = $row['stream_width'];
$streams_number = $row['streams_number'];
$machine_type = $row['machine_type'];
$raport = $row['raport'];
$number_on_raport = $row['number_on_raport'];
$lamination_roller_width = $row['lamination_roller_width'];
$ink_number = $row['ink_number'];

for($i=1; $i<=$ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $row[$ink_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $percent_var = "percent_$i";
    $$percent_var = $row[$percent_var];
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $row[$cliche_var];
}

$comment = $row['comment'];

$extracharge = $row['extracharge'];
$ski_width = $row['ski_width'];
$no_ski = $row['no_ski'];
$techmap_id = $row['techmap_id'];

$customer = $row['customer'];
$customer_phone = $row['customer_phone'];
$customer_extension = $row['customer_extension'];
$customer_email = $row['customer_email'];
$customer_person = $row['customer_person'];

$work_type = $row['work_type'];

$techmap_id = $row['techmap_id'];
$request_calc_result_id = $row['request_calc_result_id'];
$num_for_customer = $row['num_for_customer'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.calculation-table tr th, table.calculation-table tr td {
                padding-top: 5px;
                padding-right: 5px;
                padding-bottom: 5px;
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/request_calc/<?= IsInRole('manager') ? BuildQueryAddRemove('manager', GetUserId(), 'id') : BuildQueryRemove('id') ?>">К списку</a>
            <div class="d-inline-block" style="width: 150px;"></div>
            <?php if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))): ?>
            <?php if(!empty($request_calc_result_id)): ?>
            <a href="create.php<?= BuildQuery("mode", "recalc") ?>" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Пересчитать</a>
            <?php elseif(empty($row['ink_number'])): ?>
            <form method="post" class="d-inline-block">
                <input type="hidden" name="id" value="<?=$id ?>" />
                <button type="submit" name="calculate-submit" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Рассчитать</button>
            </form>
            <?php
            else:
                $percents_exist = true;
                        
                for($i=1; $i<=$ink_number; $i++) {
                    if(empty($row["percent_$i"])) {
                        $percents_exist = false;
                    }
                }
                                    
            if($percents_exist):
            ?>
            <form method="post" class="d-inline-block">
                <input type="hidden" name="id" value="<?=$id ?>" />
                <button type="submit" name="calculate-submit" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Рассчитать</button>
            </form>
            <?php 
            endif;
            endif;
            ?>
                            
            <?php if(empty($request_calc_result_id)): ?>
            <a href="create.php<?= BuildQuery("id", $id) ?>" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Редактировать</a>
            <?php endif; ?>
                    
            <?php if(!empty($techmap_id)): ?>
            <a href="<?=APPLICATION.'/techmap/details.php?id='.$techmap_id ?>" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Посмотреть тех. карту</a>
            <?php elseif (!empty($request_calc_result_id)): ?>
            <form method="post" action="<?=APPLICATION ?>/techmap/create.php" class="d-inline-block">
                <input type="hidden" name="request_calc_id" value="<?=$id ?>" />
                <button type="submit" class="btn btn-outline-dark ml-2 topbutton" style="width: 200px;">Составить тех. карту</button>
            </form>
            <?php
            endif;
            endif;
            ?>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-5" id="left_side">
                    <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($request_name) ?></h1>
                    <h2 style="font-size: 26px;">№<?=$customer_id."-".$num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></h2>
                    <?php if(!empty($techmap_id)): ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px green; color: green;">
                        <i class="fas fa-file"></i>&nbsp;&nbsp;&nbsp;Составлена технологическая карта
                    </div>
                    <?php elseif(!empty ($row['request_calc_result_id'])): ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px blue; color: blue;">
                        <i class="fas fa-calculator"></i>&nbsp;&nbsp;&nbsp;Сделан расчёт
                    </div>
                    <?php elseif(empty($row['ink_number'])): ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px brown; color: brown;">
                        <i class="far fa-clock"></i>&nbsp;&nbsp;&nbsp;Требуется расчёт
                    </div>
                    <?php
                    else:
                        $percents_exist = true;
                        
                        for($i=1; $i<=$ink_number; $i++) {
                            if(empty($row["percent_$i"])) {
                                $percents_exist = false;
                            }
                        }
                        
                        if(!$percents_exist):
                    ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px orange; color: orange;">
                        <i class="far fa-clock"></i>&nbsp;&nbsp;&nbsp;Требуется красочность
                    </div>
                    <?php else: ?>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; border: solid 2px brown; color: brown;">
                        <i class="far fa-clock"></i>&nbsp;&nbsp;&nbsp;Требуется расчёт
                    </div>
                    <?php
                        endif;
                    endif;
                    ?>
                    <table class="w-100 calculation-table">
                        <tr><th>Заказчик</th><td class="param-value"><?=$customer ?></td></tr>
                        <tr><th>Название заказа</th><td class="param-value"><?=$request_name ?></td></tr>
                        <tr><th>Тип работы</th><td class="param-value"><?=$work_type ?></td></tr>
                            <?php
                            if(!empty($quantity) && !empty($unit)):
                            ?>
                        <tr><th>Объем заказа</th><td class="param-value"><?= rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",") ?> <?=$unit == 'kg' ? "кг" : "шт" ?></td></tr>
                            <?php
                            endif;
                            if(!empty($machine_type)):
                            ?>
                        <tr><th>Печатная машина</th><td class="param-value"><?=$machine_type ?></td></tr>
                            <?php
                            endif;
                            if(!empty($stream_width)):
                            ?>
                        <tr><th>Ширина ручья</th><td class="param-value"><?= rtrim(rtrim(number_format($stream_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($streams_number)):
                            ?>
                        <tr><th>Количество ручьев</th><td class="param-value"><?= $streams_number ?></td></tr>
                            <?php
                            endif;
                            if(!empty($raport)):
                            ?>
                        <tr><th>Рапорт</th><td class="param-value"><?= rtrim(rtrim(number_format($raport, 3, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($number_on_raport)):
                            ?>
                        <tr><th>Количество этикеток на ручье</th><td class="param-value"><?=$number_on_raport ?></td></tr>
                            <?php
                            endif;
                            if(!empty($label_length)):
                            ?>
                        <tr><th>Длина этикетки вдоль рапорта вала</th><td class="param-value"><?= rtrim(rtrim(number_format($label_length, 4, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($lamination_roller_width)):
                            ?>
                        <tr><th>Ширина вала ламинации</th><td class="param-value"><?= rtrim(rtrim(number_format($lamination_roller_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                            <?php
                            endif;
                            if(!empty($machine_type)):
                            ?>
                        <tr>
                            <th>Ширина лыж</th>
                            <td class="param-value">
                                <?php
                                if($no_ski) {
                                    echo "Без лыж";
                                }
                                else {
                                    echo rtrim(rtrim(number_format($ski_width, 2, ",", " "), "0"), ",")." м";
                                }
                                ?>
                            </td>
                        </tr>
                            <?php
                            endif;
                            if(!empty($brand_name) && !empty($thickness)):
                            ?>
                        <tr>
                            <th>Пленка</th>
                            <td class="param-value">
                                <table class="w-100">
                                    <tr>
                                        <td><?=$brand_name ?></td>
                                        <td><?= number_format($thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                            <?php elseif(!empty($individual_brand_name)): ?>
                        <tr>
                            <th>Пленка</th>
                            <td class="param-value">
                                <table class="w-100">
                                    <tr>
                                        <td><?=$individual_brand_name ?></td>
                                        <td><?= number_format($individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                            <?php endif; ?>
                        <tr>
                            <?php
                            $lamination = "нет";
                            if(!empty($lamination1_brand_name)) $lamination = "1";
                            if(!empty($lamination2_brand_name)) $lamination = "2";
                            ?>
                            <th>Ламинация: <?=$lamination ?></th>
                            <td class="param-value">
                                <?php if(!empty($lamination1_brand_name) && !empty($lamination1_thickness)): ?>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$lamination1_brand_name ?></td>
                                        <td><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php
                                    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness)):
                                    ?>
                                    <tr>
                                        <td><?=$lamination2_brand_name ?></td>
                                        <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php elseif(!empty($lamination2_individual_brand_name)): ?>
                                    <tr>
                                        <td><?=$lamination2_individual_brand_name ?></td>
                                        <td><?= number_format($lamination2_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <?php elseif(!empty($lamination1_individual_brand_name)): ?>
                                <table class="w-100">
                                    <tr>
                                        <td><?=$lamination1_individual_brand_name ?></td>
                                        <td><?= number_format($lamination1_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination1_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php
                                    if(!empty($lamination2_brand_name) && !empty($lamination2_thickness)):
                                    ?>
                                    <tr>
                                        <td><?=$lamination2_brand_name ?></td>
                                        <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php elseif(!empty($lamination2_individual_brand_name)): ?>
                                    <tr>
                                        <td><?=$lamination2_individual_brand_name ?></td>
                                        <td><?= number_format($lamination2_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                                        <td class="w-25"><?=$lamination2_customers_material == 1 ? "Сырье заказчика" : "" ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <?php endif; ?>
                            </td>
                        </tr>
                            <?php
                            if(!empty($ink_number)):
                            ?>
                        <tr>
                            <th>Красочность: <?=$ink_number ?></th>
                            <td class="param-value">
                                <table class="w-100">
                                    <?php
                                    for($i=1; $i<=$ink_number; $i++):
                                    $ink_var = "ink_$i";
                                    $color_var = "color_$i";
                                    $cmyk_var = "cmyk_$i";
                                    $percent_var = "percent_$i";
                                    $cliche_var = "cliche_$i";
                                    ?>
                                    <tr>
                                        <td><?=$i ?></td>
                                        <td>
                                            <?php
                                            switch ($$ink_var) {
                                                case 'cmyk':
                                                    echo "CMYK";
                                                    break;
                                                case 'panton':
                                                    echo 'Пантон';
                                                    break;
                                                case 'lacquer':
                                                    echo 'Лак';
                                                    break;
                                                case  'white':
                                                    echo 'Белый';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($$ink_var == "cmyk") {
                                                echo $$cmyk_var;
                                            }
                                            elseif($$ink_var == "panton") {
                                                echo 'P '.$$color_var;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($request_calc_result_id)): ?>
                                            <?=$$percent_var ?>%
                                            <?php else: ?>
                                            <form method="post" class="form-inline">
                                                <input type="hidden" name="id" value="<?=$id ?>" />
                                                <input type="hidden" name="color_id" value="<?=$i ?>" />
                                                <input type="hidden" id="scroll" name="scroll" />
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" 
                                                               class="form-control int-only percent" 
                                                               style="width: 50px;" 
                                                               name="percent" 
                                                               value="<?= empty($$percent_var) ? '' : $$percent_var ?>" 
                                                               required="required"
                                                               onmousedown="javascript: $(this).removeAttr('name');" 
                                                               onmouseup="javascript: $(this).attr('name', 'percent');" 
                                                               onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); }" 
                                                               onkeyup="javascript: $(this).attr('name', 'percent');" 
                                                               onfocusout="javascript: $(this).attr('name', 'percent');" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-outline-dark invisible" name="percent-submit">Сохранить</button>
                                                </div>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($$cliche_var) {
                                                case "old":
                                                    echo 'Старая';
                                                    break;
                                                case "flint":
                                                    echo 'Новая Флинт';
                                                    break;
                                                case "kodak":
                                                    echo "Новая Кодак";
                                                    break;
                                                case "tver":
                                                    echo "Новая Тверь";
                                                    break;
                                                default:
                                                    echo $$cliche_var;
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                        <?php
                                        endfor;
                                        ?>
                                </table>
                            </td>
                        </tr>
                            <?php
                            endif;
                            ?>
                        <tr>
                            <th>Комментарий</th>
                            <td>
                                <form class="form-inline" method="post">
                                    <input type="hidden" name="id" value="<?=$id ?>" />
                                    <input type="hidden" id="scroll" name="scroll" />
                                    <div class="form-group">
                                        <textarea id="comment" name="comment" rows="3" cols="40" class="form-control"><?= htmlentities($comment) ?></textarea>
                                    </div>
                                    <?php if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))): ?>
                                    <div class="form-group">
                                        <button type="submit" name="comment-submit" class="btn btn-outline-dark invisible">Сохранить</button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-1"></div>
                <div class="col-6">
                    <?php
                    if(!empty($request_calc_result_id)) {
                        include './right_panel.php';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // В поле "процент" ограничиваем значения: целые числа от 1 до 100
            $('.percent').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 100)) {
                    return false;
                }
                
                // Делаем также доступной кнопку "Сохранить"
                $(this).form().find('button').removeClass('invisible');
            });
    
            $(".percent").change(function(){
                ChangeLimitIntValue($(this), 100);
            });
            
            // Делаем доступной кнопку "Сохранить" для коментария
            $('#comment').keydown(function() {
                $(this).form().find('button').removeClass('invisible');
            });
            
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
            }
            
            // Ограничение значений наценки
            $('#extracharge').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 999)) {
                    return false;
                }
            });
            
            $('#extracharge').change(function(){
                ChangeLimitIntValue($(this), 999);
                
                // Сохранение значения в базе
                EditExtracharge($(this));
            });
        </script>
    </body>
</html>