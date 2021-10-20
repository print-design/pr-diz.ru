<?php
include '../include/topscripts.php';

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

if(null !== filter_input(INPUT_POST, 'export_calculation_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $file_name = "calculation_$id.txm";
    DownloadSendHeaders($file_name);
    
    $sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.quantity, c.unit, "
            . "c.brand_name, c.thickness, other_brand_name, other_price, other_thickness, other_weight, c.customers_material, "
            . "c.lamination1_brand_name, c.lamination1_thickness, lamination1_other_brand_name, lamination1_other_price, lamination1_other_thickness, lamination1_other_weight, c.lamination1_roller, c.lamination1_customers_material, "
            . "c.lamination2_brand_name, c.lamination2_thickness, lamination2_other_brand_name, lamination2_other_price, lamination2_other_thickness, lamination2_other_weight, c.lamination2_roller, c.lamination2_customers_material, "
            . "c.length, c.stream_width, c.streams_count, c.machine_id, c.raport, c.paints_count, "
            . "c.paint_1, c.paint_2, c.paint_3, paint_4, paint_5, paint_6, paint_7, paint_8, "
            . "c.color_1, c.color_2, c.color_3, color_4, color_5, color_6, color_7, color_8, "
            . "c.cmyk_1, c.cmyk_2, c.cmyk_3, cmyk_4, cmyk_5, cmyk_6, cmyk_7, cmyk_8, "
            . "c.percent_1, c.percent_2, c.percent_3, percent_4, percent_5, percent_6, percent_7, percent_8, "
            . "c.form_1, c.form_2, c.form_3, form_4, form_5, form_6, form_7, form_8, "
            . "c.status_id, c.extracharge, c.ski, c.no_ski, "
            . "cs.name status, cs.colour, cs.colour2, cs.image, "
            . "cu.name customer, cu.phone customer_phone, cu.extension customer_extension, cu.email customer_email, cu.person customer_person, "
            . "wt.name work_type, "
            . "mt.shortname machine, mt.colorfulness, "
            . "u.last_name, u.first_name, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.brand_name and fbw.thickness = c.thickness limit 1) weight, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination1_brand_name and fbw.thickness = c.lamination1_thickness limit 1) lamination1_weight, "
            . "(select fbw.weight from film_brand_variation fbw inner join film_brand fb on fbw.film_brand_id = fb.id where fb.name = c.lamination2_brand_name and fbw.thickness = c.lamination2_thickness limit 1) lamination2_weight "
            . "from calculation c "
            . "left join calculation_status cs on c.status_id = cs.id "
            . "left join customer cu on c.customer_id = cu.id "
            . "left join work_type wt on c.work_type_id = wt.id "
            . "left join machine mt on c.machine_id = mt.id "
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
    $lamination1_roller = $row['lamination1_roller'];
    $lamination1_customers_material = $row['lamination1_customers_material'];
    $lamination2_brand_name = $row['lamination2_brand_name'];
    $lamination2_thickness = $row['lamination2_thickness'];
    $lamination2_weight = $row['lamination2_weight'];
    $lamination2_other_brand_name = $row['lamination2_other_brand_name'];
    $lamination2_other_price = $row['lamination2_other_price'];
    $lamination2_other_thickness = $row['lamination2_other_thickness'];
    $lamination2_other_weight = $row['lamination2_other_weight'];
    $lamination2_roller = $row['lamination2_roller'];
    $lamination2_customers_material = $row['lamination2_customers_material'];
    $length = $row['length'];
    $stream_width = $row['stream_width'];
    $streams_count = $row['streams_count'];
    $machine_id = $row['machine_id'];
    $raport = $row['raport'];
    $paints_count = $row['paints_count'];
    
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
    }
    
    $status_id = $row['status_id'];
    $extracharge = $row['extracharge'];
    $ski = $row['ski'];
    $no_ski = $row['no_ski'];
    
    $status = $row['status'];
    $colour = $row['colour'];
    $colour2 = $row['colour2'];
    $image = $row['image'];
    
    $customer = $row['customer'];
    $customer_phone = $row['customer_phone'];
    $customer_extension = $row['customer_extension'];
    $customer_email = $row['customer_email'];
    $customer_person = $row['customer_person'];
    
    $work_type = $row['work_type'];
    
    $machine = $row['machine'];
    $machine_full = "";
    $machine_number = 0;
    
    switch ($machine) {
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
    
    $colorfulness = $row['colorfulness'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    
    // Толщина материала
    $thickness_final = 0;
    
    if(!empty($thickness)) {
        $thickness_final = $thickness;
    }
    elseif(!empty ($other_thickness)) {
        $thickness_final = $other_thickness;
    }
    
    // Вес материала
    $weight_final = 0;
    
    if(!empty($weight)) {
        $weight_final = $weight;
    }
    elseif(!empty ($other_weight)) {
        $weight_final = $other_weight;
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
    
    // Помещаем данные в файл
    echo mb_convert_encoding("НАИМЕНОВАНИЕ ЗАКАЗА :$name;\n", "cp1251");
    echo mb_convert_encoding("ЗАКАЗЧИК :$customer;\n", "cp1251");
    echo mb_convert_encoding("МЕНЕДЖЕР :$last_name $first_name;\n", "cp1251");
    echo mb_convert_encoding("РАЗМЕР ЭТИКЕТКИ :;\n", "cp1251");
    echo mb_convert_encoding("ДАТА :18.10.21;\n", "cp1251");
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
    echo mb_convert_encoding("Название типа материала :Другие материалы;\n", "cp1251");
    echo mb_convert_encoding("Тип материала (номер):9;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала,мкм :     $thickness_final;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес бумаги,грамм/м2 :     $weight_final;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг,руб :    $price_final;\n", "cp1251");
    echo mb_convert_encoding("Средний курс рубля за 1 евро :     $euro;\n", "cp1251");
    echo mb_convert_encoding("Число красок :         2;\n", "cp1251");
    echo mb_convert_encoding("Число новых форм :         0;\n", "cp1251");
    echo mb_convert_encoding("Название изготовителя новых форм :Москва Флинт;\n", "cp1251");
    echo mb_convert_encoding("Изготовителя новых форм (номер):2;\n", "cp1251");
    echo mb_convert_encoding("Печать с лыжами :1;\n", "cp1251");
    echo mb_convert_encoding("Ширина лыж,м :      0.02;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentC :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentM :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentY :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK :     30.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel :     30.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP1 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP2 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP3 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP4 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP5 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP6 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP7 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP8 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража чистая,м2 : 21978.022;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража обрезная,мм :   720.000;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража с отходами,мм :   740.000;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража чистая,м : 30525.031;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража с отходами,м : 32040.781;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража с отходами,м2 : 23710.178;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати чистый,кг :   500.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати с отходами,кг :   539.407;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции чистый,кг :   500.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции с отходами,кг :   539.407;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала печати,руб:135930.450;\n", "cp1251");
    echo mb_convert_encoding("Время печати тиража без приладки,ч:       3.1;\n", "cp1251");
    echo mb_convert_encoding("Время приладки,ч :       1.3;\n", "cp1251");
    echo mb_convert_encoding("Время печати с приладкой,ч :       4.4;\n", "cp1251");
    echo mb_convert_encoding("Стоимость печати,руб :   6140.17;\n", "cp1251");
    echo mb_convert_encoding("Площадь печатной формы,см2 :   3344.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость 1 печатной формы,руб :   5294.89;\n", "cp1251");
    echo mb_convert_encoding("Стоимость комплекта печатной формы,руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость всех красок + лак + растворитель,руб:   9461.75;\n", "cp1251");
    echo mb_convert_encoding("Количество ламинаций:0;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала первой ламинации: ;\n", "cp1251");
    echo mb_convert_encoding("Тип материала первой ламинации:0;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала первой ламинации,мкм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала первой ламинации,грамм/м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала первой ламинации,мм:    740.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала первой ламинации,мм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала второй ламинации: ;\n", "cp1251");
    echo mb_convert_encoding("Тип материала второй ламинации:0;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала второй ламинации,мкм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала второй ламинации,грамм/м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала второй ламинации,мм:    740.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала второй ламинации,мм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации чистый,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации с отходами,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации чистый,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации с отходами,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО себестоимость ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("Номер вала первой ламинации:1;\n", "cp1251");
    echo mb_convert_encoding("Номер вала второй ламинации:3;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг без форм, руб :    305.51;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг с формами, руб :    305.51;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт без форм, руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт с формами, руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход лака, ProcentLak :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 1 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 2 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel2 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK2 :      0.00;\n", "cp1251");
    
    die();
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать рассчёт надо нажать на кнопку "Экспорт" еа странице расчёта.</h1>
    </body>
</html>