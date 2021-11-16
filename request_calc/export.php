<?php
include '../include/topscripts.php';

// Машины
const ZBS = "zbs";
const COMIFLEX = "comiflex";

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

if(null !== filter_input(INPUT_POST, 'export_request_calc_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $file_name = "request_calc_$id.txm";
    DownloadSendHeaders($file_name);
    
    $sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
            . "c.brand_name, c.thickness, other_brand_name, other_price, other_thickness, other_weight, c.customers_material, "
            . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, c.lamination1_customers_material, "
            . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, c.lamination2_customers_material, "
            . "c.length, c.stream_width, c.streams_count, c.machine, c.raport, c.lamination_roller, c.paints_count, "
            . "c.paint_1, c.paint_2, c.paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
            . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "c.form_1, c.form_2, c.form_3, form_4, form_5, form_6, form_7, form_8, "
            . "c.extracharge, c.ski_width, c.no_ski, "
            . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
            . "wt.name work_type, "
            . "u.last_name, u.first_name, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
            . "from request_calc c "
            . "left join customer cu on c.customer_id = cu.id "
            . "left join work_type wt on c.work_type_id = wt.id "
            . "left join user u on c.manager_id = u.id "
            . "where c.id=$id";
    $row = (new Fetcher($sql))->Fetch();
    
    $date = $row['date'];
    $customer_id = $row['customer_id'];
    $name = $row['name'];
    $work_type_id = $row['work_type_id'];
    $quantity = $row['quantity'];
    $unit = $row['unit'];
    $brand_name = $row['brand_name'];
    $thickness = $row['thickness'];
    $weight = $row['weight'];
    $other_brand_name = $row['other_brand_name'];
    $other_price = $row['other_price'];
    $other_thickness = $row['other_thickness'];
    $other_weight = $row['other_weight'];
    $customers_material = $row['customers_material'];
    $lamination1_brand_name = $row['lamination1_brand_name'];
    $lamination1_thickness = $row['lamination1_thickness'];
    $lamination1_weight = $row['lamination1_weight'];
    $lamination1_other_brand_name = $row['lamination1_other_brand_name'];
    $lamination1_other_price = $row['lamination1_other_price'];
    $lamination1_other_thickness = $row['lamination1_other_thickness'];
    $lamination1_other_weight = $row['lamination1_other_weight'];
    $lamination1_customers_material = $row['lamination1_customers_material'];
    $lamination2_brand_name = $row['lamination2_brand_name'];
    $lamination2_thickness = $row['lamination2_thickness'];
    $lamination2_weight = $row['lamination2_weight'];
    $lamination2_other_brand_name = $row['lamination2_other_brand_name'];
    $lamination2_other_price = $row['lamination2_other_price'];
    $lamination2_other_thickness = $row['lamination2_other_thickness'];
    $lamination2_other_weight = $row['lamination2_other_weight'];
    $lamination2_customers_material = $row['lamination2_customers_material'];
    $length = $row['length'];
    $stream_width = $row['stream_width'];
    $streams_count = $row['streams_count'];
    $machine = $row['machine'];
    $raport = $row['raport'];
    $lamination_roller = $row['lamination_roller'];
    $paints_count = $row['paints_count'];
    
    $extracharge = $row['extracharge'];
    $ski_width = $row['ski_width'];
    $no_ski = $row['no_ski'];
    
    $customer = $row['customer'];
    $customer_phone = $row['customer_phone'];
    $customer_extension = $row['customer_extension'];
    $customer_email = $row['customer_email'];
    $customer_person = $row['customer_person'];
    
    $work_type = $row['work_type'];
    
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    
    // Формы
    $new_forms_count = 0;
    $new_forms_vendor = "";
    $new_forms_vendor_id = 0;
    
    // Краски
    $procentc = 0;
    $procentm = 0;
    $procenty = 0;
    $procentk = 0;
    $procentk2 = 0;
    $procentbel = 0;
    $procentbel2 = 0;
    $procentp1 = 0;
    $procentp2 = 0;
    $procentp3 = 0;
    $procentp4 = 0;
    $procentp5 = 0;
    $procentp6 = 0;
    $procentp7 = 0;
    $procentp8 = 0;
    $procentlak = 0;
    
    for($i=1; $i<=$paints_count; $i++) {
        $paint_var = "paint_$i";
        $$paint_var = $row[$paint_var];
        
        $color_var = "color_$i";
        $$color_var = $row[$color_var];
        
        $cmyk_var = "cmyk_$i";
        $$cmyk_var = $row[$cmyk_var];
        
        $percent_var = "percent_$i";
        $$percent_var = $row[$percent_var];
        
        $form_var = "form_$i";
        $$form_var = $row[$form_var];
        
        if(!empty($$form_var) && $$form_var != "old") {
            $new_forms_count++;
            
            switch ($$form_var) {
                case "flint":
                    $new_forms_vendor = "Москва Флинт";
                    $new_forms_vendor_id = 2;
                    break;
                
                case "kodak":
                    $new_forms_vendor = "Москва Кодак";
                    $new_forms_vendor_id = 3;
                    break;
                
                case "tver":
                    $new_forms_vendor = "Тверь (наши)";
                    $new_forms_vendor_id = 1;
                    break;
            }
        }
        
        switch($$paint_var) {
            case "cmyk":
                if($$cmyk_var == "cyan" && empty($procentc)) {
                    $procentc = $$percent_var;
                }
                elseif($$cmyk_var == "magenta" && empty ($procentm)) {
                    $procentm = $$percent_var;
                }
                elseif($$cmyk_var == "yellow" && empty ($procenty)) {
                    $procenty = $$percent_var;
                }
                elseif($$cmyk_var == "kontur" && empty ($procentk)) {
                    $procentk = $$percent_var;
                }
                elseif($$cmyk_var == "kontur" && empty ($procentk2)) {
                    $procentk2 = $$percent_var;
                }
                break;
            
            case "panton":
                if(empty($procentp1)) {
                    $procentp1 = $$percent_var;
                }
                elseif(empty ($procentp2)) {
                    $procentp2 = $$percent_var;
                }
                elseif(empty ($procentp3)) {
                    $procentp3 = $$percent_var;
                }
                elseif(empty ($procentp4)) {
                    $procentp4 = $$percent_var;
                }
                elseif(empty ($procentp5)) {
                    $procentp5 = $$percent_var;
                }
                elseif(empty ($procentp6)) {
                    $procentp6 = $$percent_var;
                }
                elseif(empty($procentp7)) {
                    $procentp7 = $$percent_var;
                }
                elseif(empty ($procentp8)) {
                    $procentp8 = $$percent_var;
                }
                break;
            
            case "white":
                if(empty($procentbel)) {
                    $procentbel = $$percent_var;
                }
                elseif(empty ($procentbel2)) {
                    $procentbel2 = $$percent_var;
                }
                break;
            
            case "lacquer":
                if(empty($procentlak)) {
                    $procentlak = $$percent_var;
                }
                break;
        }
    }
    
    // Номер машины
    $machine_ids = array();
    $machine_shortnames = array();
    
    $sql = "select id, shortname from machine";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $machine_ids[$row['shortname']] = $row['id'];
        $machine_shortnames[$row['id']] = $row['shortname'];
    }
    
    $machine_id = null;
    
    if(!empty($machine) && !empty($paints_count)) {
        if($machine == COMIFLEX) {
            $machine_id = $machine_ids[COMIFLEX];
        }
        elseif($paints_count > 6) {
            $machine_id = $machine_ids['zbs3'];
        }
        else {
            $machine_id = $machine_ids['zbs1'];
        }
    }
    
    $machine_shortname = null;
    
    if(!empty($machine_id)) {
        $machine_shortname = $machine_shortnames[$machine_id];
    }
    
    $machine_full = "";
    $machine_number = 0;
    
    switch ($machine_shortname) {
        case "zbs1":
            $machine_full = "ZBS1/6color";
            $machine_number = 1;
            break;
        
        case "zbs2":
            $machine_full = "ZBS2/6color";
            $machine_number = 2;
            break;
        
        case "zbs3":
            $machine_full = "ZBS3/8color";
            $machine_number = 3;
            break;
        
        case "comiflex":
            $machine_full = "Comiflex";
            $machine_number = 4;
            break;
    }
    
    $quantity_type = 0;
    $order_weight = 0;
    $order_number = 0;
    
    if($unit == "kg") {
        $quantity_type = 1;
        $order_weight = $quantity;
    }
    else {
        $quantity_type = 2;
        $order_number = $quantity;
    }
    
    // Марка плёнки
    $brand_name_final = "";
    $brand_type_final = 0;
    
    if(!empty($brand_name)) {
        if(stripos($brand_name, 'pet') === 0) {
            $brand_name_final = "ПЭТ";
            $brand_type_final = 5;
        }
        else {
            $brand_name_final = "Другие материалы";
            $brand_type_final = 9;
        }
    }
    
    // Марка плёнки 1 ламинации
    $lamination1_brand_name_final = "";
    $lamination1_brand_type_final = 0;
    
    if(!empty($lamination1_brand_name)) {
        if(stripos($lamination1_brand_name, 'pet') === 0) {
            $lamination1_brand_name_final = "ПЭТ";
            $lamination1_brand_type_final = 5;
        }
        else {
            $lamination1_brand_name_final = "Другие материалы";
            $lamination1_brand_type_final = 9;
        }
    }
    
    // Марка плёнки 2 ламинации
    $lamination2_brand_name_final = "";
    $lamination2_brand_type_final = 0;
    
    if(!empty($lamination2_brand_name)) {
        if(stripos($lamination2_brand_name, 'pet') === 0) {
            $lamination2_brand_name_final = "ПЭТ";
            $lamination2_brand_type_final = 5;
        }
        else {
            $lamination2_brand_name_final = "Другие материалы";
            $lamination2_brand_type_final = 9;
        }
    }
    
    // Толщина материала
    $thickness_final = 0;
    
    if(!empty($thickness)) {
        $thickness_final = $thickness;
    }
    elseif(!empty ($other_thickness)) {
        $thickness_final = $other_thickness;
    }
    
    // Толщина материала 1 ламинации
    $lamination1_thickness_final = 0;
    
    if(!empty($lamination1_thickness)) {
        $lamination1_thickness_final = $lamination1_thickness;
    }
    elseif(!empty ($lamination1_other_thickness)) {
        $lamination1_thickness_final = $lamination1_other_thickness;
    }
    
    // Толщина материала 2 ламинации
    $lamination2_thickness_final = 0;
    
    if(!empty($lamination2_thickness)) {
        $lamination2_thickness_final = $lamination2_thickness;
    }
    elseif(!empty ($lamination2_other_thickness)) {
        $lamination2_thickness_final = $lamination2_other_thickness;
    }
    
    // Вес материала
    $weight_final = 0;
    
    if(!empty($weight)) {
        $weight_final = $weight;
    }
    elseif(!empty ($other_weight)) {
        $weight_final = $other_weight;
    }
    
    // Вес материала 1 ламинации
    $lamination1_weight_final = 0;
    
    if(!empty($lamination1_weight)) {
        $lamination1_weight_final = $lamination1_weight;
    }
    elseif(!empty ($lamination1_other_weight)) {
        $lamination1_weight_final = $lamination1_other_weight;
    }
    
    // Вес материала 2 ламинации
    $lamination2_weight_final = 0;
    
    if(!empty($lamination2_weight)) {
        $lamination2_weight_final = $lamination2_weight;
    }
    elseif(!empty ($lamination2_other_weight)) {
        $lamination2_weight_final = $lamination2_other_weight;
    }
    
    // Цена материала
    $price_final = 0;
    
    if(!empty($other_price)) {
        $price_final = $other_price;
    }
    elseif(!empty ($brand_name) && !empty ($thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$brand_name' and thickness = $thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $price_final = $row['price'];
                    
            if($row['currency'] == USD) {
                $price_final *= $usd;
            }
            elseif($row['currency'] == EURO) {
                $price_final *= $euro;
            }
        }
    }
    
    // Цена материала 1 ламинации
    $lamination1_price_final = 0;
    
    if(!empty($lamination1_other_price)) {
        $lamination1_price_final = $lamination1_other_price;
    }
    elseif(!empty ($lamination1_brand_name) && !empty ($lamination1_thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$lamination1_brand_name' and thickness = $lamination1_thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $lamination1_price_final = $row['price'];
                    
            if($row['currency'] == USD) {
                $lamination1_price_final *= $usd;
            }
            elseif($row['currency'] == EURO) {
                $lamination1_price_final *= $euro;
            }
        }
    }
    
    // Цена материала 2 ламинации
    $lamination2_price_final = 0;
    
    if(!empty($lamination2_other_price)) {
        $lamination2_price_final = $lamination2_other_price;
    }
    elseif(!empty ($lamination2_brand_name) && !empty ($lamination2_thickness)) {
        $sql = "select price, currency from film_price where brand_name = '$lamination2_brand_name' and thickness = $lamination2_thickness and date <= '$date' order by date desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $lamination2_price_final = $row['price'];
                    
            if($row['currency'] == USD) {
                $lamination2_price_final *= $usd;
            }
            elseif($row['currency'] == EURO) {
                $lamination2_price_final *= $euro;
            }
        }
    }
    
    // Курс доллара и евро
    $euro = 0;
    $usd = 0;
        
    $sql = "select euro, usd from currency where date <= '$date' order by date desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $euro = $row['euro'];
        $usd = $row['usd'];
    }
            
    if(empty($euro) || empty($usd)) {
        $error_message = "Не заданы курсы валют";
    }
    
    // Печать с лыжами
    $with_ski = 0;
    
    if(!$no_ski) {
        $with_ski = 1;
    }
    
    // Результаты расчёта
    $pure_area = null;    $pure_width = null;    $pure_length = null;
    $pure_length_lam = null;    $dirty_length = null;    $dirty_width = null;
    $dirty_area = null;    $pure_weight = null;    $dirty_weight = null;
    $material_price = null;    $print_time = null;    $tuning_time = null;
    $print_tuning_time = null;    $print_price = null;    $cliche_area = null;
    $cliche_flint_price = null;    $cliche_kodak_price = null;    $cliche_tver_price = null;
    $cliche_price = null;    $paint_price = null;    $pure_weight_lam1 = null;
    $dirty_weight_lam1 = null;    $price_lam1_material = null;    $price_lam1_glue = null;
    $price_lam1_work = null;    $pure_weight_lam2 = null;    $dirty_weight_lam2 = null;
    $price_lam2_material = null;    $price_lam2_glue = null;    $price_lam2_work = null;
    $price_lam_total = null;    $pure_weight_total = null;    $dirty_weight_total = null;
    $cost_no_cliche = null;    $cost_with_cliche = null;    $cost_no_cliche_kg = null;
    $cost_with_cliche_kg = null;    $cost_no_cliche_pieces = null;    $cost_with_cliche_pieces = null;

    $sql = "select pure_area, pure_width, pure_length, pure_length_lam, "
            . "dirty_length, dirty_width, dirty_area, pure_weight, dirty_weight, material_price, print_time, tuning_time, "
            . "print_tuning_time, print_price, cliche_area, cliche_flint_price, cliche_kodak_price, cliche_tver_price, "
            . "cliche_price, paint_price, pure_weight_lam1, dirty_weight_lam1, "
            . "price_lam1_material, price_lam1_glue, price_lam1_work, pure_weight_lam2, dirty_weight_lam2, price_lam2_material, "
            . "price_lam2_glue, price_lam2_work, price_lam_total, pure_weight_total, dirty_weight_total, cost_no_cliche, "
            . "cost_with_cliche, cost_no_cliche_kg, cost_with_cliche_kg, cost_no_cliche_pieces, cost_with_cliche_pieces"
            . " from request_calc_result where request_calc_id = $id order by id desc limit 1";
    $fetcher = new Fetcher($sql);

    if($row = $fetcher->Fetch()) {
        $pure_area = $row['pure_area'];
        $pure_width = $row['pure_width'];
        $pure_length = $row['pure_length'];
        $pure_length_lam = $row['pure_length_lam'];
        $dirty_length = $row['dirty_length'];
        $dirty_width = $row['dirty_width'];
        $dirty_area = $row['dirty_area'];
        $pure_weight = $row['pure_weight'];
        $dirty_weight = $row['dirty_weight'];
        $material_price = $row['material_price'];
        $print_time = $row['print_time'];
        $tuning_time = $row['tuning_time'];
        $print_tuning_time = $row['print_tuning_time'];
        $print_price = $row['print_price'];
        $cliche_area = $row['cliche_area'];
        $cliche_flint_price = $row['cliche_flint_price'];
        $cliche_kodak_price = $row['cliche_kodak_price'];
        $cliche_tver_price = $row['cliche_tver_price'];
        $cliche_price = $row['cliche_price'];
        $paint_price = $row['paint_price'];
        $pure_weight_lam1 = $row['pure_weight_lam1'];
        $dirty_weight_lam1 = $row['dirty_weight_lam1'];
        $price_lam1_material = $row['price_lam1_material'];
        $price_lam1_glue = $row['price_lam1_glue'];
        $price_lam1_work = $row['price_lam1_work'];
        $pure_weight_lam2 = $row['pure_weight_lam2'];
        $dirty_weight_lam2 = $row['dirty_weight_lam2'];
        $price_lam2_material = $row['price_lam2_material'];
        $price_lam2_glue = $row['price_lam2_glue'];
        $price_lam2_work = $row['price_lam2_work'];
        $price_lam_total = $row['price_lam_total'];
        $pure_weight_total = $row['pure_weight_total'];
        $dirty_weight_total = $row['dirty_weight_total'];
        $cost_no_cliche = $row['cost_no_cliche'];
        $cost_with_cliche = $row['cost_with_cliche'];
        $cost_no_cliche_kg = $row['cost_no_cliche_kg'];
        $cost_with_cliche_kg = $row['cost_with_cliche_kg'];
        $cost_no_cliche_pieces = $row['cost_no_cliche_pieces'];
        $cost_with_cliche_pieces = $row['cost_with_cliche_pieces'];
    }
    
    // Стоимость новой формы
    $new_cliche_price = 0;
    
    switch($new_forms_vendor_id) {
        case 1:
            $new_cliche_price = $cliche_tver_price;
            break;
        
        case 2:
            $new_cliche_price = $cliche_flint_price;
            break;
        
        case 3:
            $new_cliche_price = $cliche_kodak_price;
            break;
    }
    
    // Количество ламинаций
    $laminations_count = 0;
    
    if(!empty($lamination1_brand_name)) {
        $laminations_count = 1;
    }
    
    if(!empty($lamination2_brand_name)) {
        $laminations_count = 2;
    }
    
    // Помещаем данные в файл
    echo mb_convert_encoding("НАИМЕНОВАНИЕ ЗАКАЗА :$name;\n", "cp1251");
    echo mb_convert_encoding("ЗАКАЗЧИК :$customer;\n", "cp1251");
    echo mb_convert_encoding("МЕНЕДЖЕР :$last_name $first_name;\n", "cp1251");
    echo mb_convert_encoding("РАЗМЕР ЭТИКЕТКИ :;\n", "cp1251");
    echo mb_convert_encoding("ДАТА :".DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y').";\n", "cp1251");
    echo mb_convert_encoding("ПЕЧАТЬ ЕСТЬ/НЕТ:".(empty($machine_id) ? 0 : 1).";\n", "cp1251");
    echo mb_convert_encoding("ТИП ФЛЕКСМАШИНЫ :$machine_full;\n", "cp1251");
    echo mb_convert_encoding("ТИП ФЛЕКСМАШИНЫ НОМЕР:$machine_number;\n", "cp1251");
    echo mb_convert_encoding("Вес заказа,кг :    $order_weight;\n", "cp1251");
    echo mb_convert_encoding("Количество этикеток в заказе,шт :         $order_number;\n", "cp1251");
    echo mb_convert_encoding("ТИП ЗАКАЗА вес/количество:$quantity_type;\n", "cp1251");
    echo mb_convert_encoding("Количество ручьев,шт :         $streams_count;\n", "cp1251");
    echo mb_convert_encoding("Количество зтикеток в одном ручье на рапорте,шт :         0;\n", "cp1251");
    echo mb_convert_encoding("Ширина ручья,мм :    $stream_width;\n", "cp1251");
    echo mb_convert_encoding("Длина этикетки вдоль рапорта вала,мм :      $length;\n", "cp1251");
    echo mb_convert_encoding("Рапорт вала,мм :   $raport;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала :$brand_name_final;\n", "cp1251");
    echo mb_convert_encoding("Тип материала (номер):$brand_type_final;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала,мкм :     $thickness_final;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес бумаги,грамм/м2 :     $weight_final;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг,руб :    $price_final;\n", "cp1251");
    echo mb_convert_encoding("Средний курс рубля за 1 евро :     $euro;\n", "cp1251");
    echo mb_convert_encoding("Число красок :         $paints_count;\n", "cp1251");
    echo mb_convert_encoding("Число новых форм :         $new_forms_count;\n", "cp1251");
    echo mb_convert_encoding("Название изготовителя новых форм :$new_forms_vendor;\n", "cp1251");
    echo mb_convert_encoding("Изготовителя новых форм (номер):$new_forms_vendor_id;\n", "cp1251");
    echo mb_convert_encoding("Печать с лыжами :$with_ski;\n", "cp1251");
    echo mb_convert_encoding("Ширина лыж,м :      ".($ski_width / 1000).";\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentC :      $procentc;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentM :      $procentm;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentY :      $procenty;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK :     $procentk;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel :     $procentbel;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP1 :      $procentp1;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP2 :      $procentp2;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP3 :      $procentp3;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP4 :      $procentp4;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP5 :      $procentp5;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP6 :      $procentp6;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP7 :      $procentp7;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP8 :      $procentp8;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража чистая,м2 : $pure_area;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража обрезная,мм :   $pure_width;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража с отходами,мм :   $dirty_width;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража чистая,м : $pure_length;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража с отходами,м : $dirty_length;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража с отходами,м2 : $dirty_area;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати чистый,кг :   $pure_weight;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати с отходами,кг :   $dirty_weight;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции чистый,кг :   $pure_weight_total;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции с отходами,кг :   $dirty_weight_total;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала печати,руб:$material_price;\n", "cp1251");
    echo mb_convert_encoding("Время печати тиража без приладки,ч:       $print_time;\n", "cp1251");
    echo mb_convert_encoding("Время приладки,ч :       $tuning_time;\n", "cp1251");
    echo mb_convert_encoding("Время печати с приладкой,ч :       $print_tuning_time;\n", "cp1251");
    echo mb_convert_encoding("Стоимость печати,руб :   $print_price;\n", "cp1251");
    echo mb_convert_encoding("Площадь печатной формы,см2 :   $cliche_area;\n", "cp1251");
    echo mb_convert_encoding("Стоимость 1 печатной формы,руб :   $new_cliche_price;\n", "cp1251");
    echo mb_convert_encoding("Стоимость комплекта печатной формы,руб :      $cliche_price;\n", "cp1251");
    echo mb_convert_encoding("Стоимость всех красок + лак + растворитель,руб:   $paint_price;\n", "cp1251");
    echo mb_convert_encoding("Количество ламинаций:$laminations_count;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : $cost_no_cliche;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : $cost_with_cliche;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала первой ламинации: $lamination1_brand_name_final;\n", "cp1251");
    echo mb_convert_encoding("Тип материала первой ламинации:$lamination1_brand_type_final;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала первой ламинации,мкм:      $lamination1_thickness_final;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала первой ламинации,грамм/м2:      $lamination1_weight_final;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала первой ламинации,мм:    0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала первой ламинации,мм:      $lamination_roller;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг первой ламинации,руб:      $lamination1_price_final;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала второй ламинации: $lamination2_brand_name_final;\n", "cp1251");
    echo mb_convert_encoding("Тип материала второй ламинации:$lamination2_brand_type_final;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала второй ламинации,мкм:      $lamination2_thickness_final;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала второй ламинации,грамм/м2:      $lamination2_weight_final;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала второй ламинации,мм:    0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала второй ламинации,мм:      $lamination_roller;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг второй ламинации,руб:      $lamination2_price_final;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации чистый,кг:     $pure_weight_lam1;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации с отходами,кг:     $dirty_weight_lam1;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала первой ламинации,руб:      $price_lam1_material;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора первой ламинации,руб:      $price_lam1_glue;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса первой ламинации,руб:      $price_lam1_work;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации чистый,кг:     $pure_weight_lam2;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации с отходами,кг:     $dirty_weight_lam2;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала второй ламинации,руб:      $price_lam2_material;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора второй ламинации,руб:      $price_lam2_glue;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса второй ламинации,руб:      $price_lam2_work;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО себестоимость ламинации,руб:      $price_lam_total;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : $cost_no_cliche;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : $cost_with_cliche;\n", "cp1251");
    echo mb_convert_encoding("Номер вала первой ламинации:1;\n", "cp1251");
    echo mb_convert_encoding("Номер вала второй ламинации:1;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг без форм, руб :    $cost_no_cliche_kg;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг с формами, руб :    $cost_with_cliche_kg;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт без форм, руб :      $cost_no_cliche_pieces;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт с формами, руб :      $cost_with_cliche_pieces;\n", "cp1251");
    echo mb_convert_encoding("Расход лака, ProcentLak :      $procentlak;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 1 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 2 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel2 :      $procentbel2;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK2 :      $procentk2;\n", "cp1251");
    
    die();
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать рассчёт надо нажать на кнопку "Экспорт" еа странице расчёта.</h1>
    </body>
</html>