<?php
include '../include/topscripts.php';

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

$error_message = '';
$id = filter_input(INPUT_GET, 'id');

// Автозаполнение наценки
$extracharge = filter_input(INPUT_GET, 'extracharge');
if($extracharge !== null) {
    $error_message = (new Executer("update request_calc set extracharge=$extracharge where id=$id"))->error;
    
    if(empty($error_message)) {
        $fetcher = new Fetcher("select extracharge from request_calc where id=$id");
        $row = $fetcher->Fetch();
        $error_message = $fetcher->error;
        
        if(empty($error_message)) {
            echo $row['extracharge'];
        }
    }
}

// Автосохранение заказчика
$customer_id = filter_input(INPUT_GET, 'customer_id');
if($customer_id !== null) {
    $error_message = (new Executer("update request_calc set customer_id=$customer_id where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение названия заказа
$name = filter_input(INPUT_GET, 'name');
if($name !== null) {
    $name = addslashes($name);
    $error_message = (new Executer("update request_calc set name='$name' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение типа работы
$work_type_id = filter_input(INPUT_GET, 'work_type_id');
if($work_type_id !== null) {
    $work_type_id = addslashes($work_type_id);
    $error_message = (new Executer("update request_calc set work_type_id=$work_type_id where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение объёма заказа
$quantity = filter_input(INPUT_GET, 'quantity');
if($quantity !== null) {
    $quantity = preg_replace("/\D/", "", $quantity);
    $error_message = (new Executer("update request_calc set quantity=$quantity where id=$id"))->error;
    
    $sql = "select unit from request_calc where id=$id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if(empty($row[0])) {
            $error_message = (new Executer("update request_calc set unit='kg' where id=$id"))->error;
        }
    }
    
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение единицы объёма
$unit = filter_input(INPUT_GET, 'unit');
if($unit !== null) {
    $error_message = (new Executer("update request_calc set unit='$unit' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение типа машины
$machine_type = filter_input(INPUT_GET, 'machine_type');
if($machine_type !== null) {
    $error_message = (new Executer("update request_calc set machine_type='$machine_type' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение расширения/сжатия
$raport_resize = filter_input(INPUT_GET, 'raport_resize');
if($raport_resize !== null) {
    $error_message = (new Executer("update request_calc set raport_resize=$raport_resize where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение марки плёнки
$brand_name = filter_input(INPUT_GET, 'brand_name');
if($brand_name !== null) {
    $brand_name = addslashes($brand_name);
    $error_message = (new Executer("update request_calc set brand_name='$brand_name' where id=$id"))->error;
    
    // Если плёнка пользовательская, то сохраняем пустую толщину
    // Иначе сохраняем пустые пользовательские значения
    if($brand_name == INDIVIDUAL) {
        $error_message = (new Executer("update request_calc set thickness=NULL where id=$id"))->error;
    }
    else {
        $error_message = (new Executer("update request_calc set individual_brand_name='', individual_price=NULL, individual_thickness=NULL, individual_density=NULL where id=$id"))->error;
    }
    
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение толщины плёнки
$thickness = filter_input(INPUT_GET, 'thickness');
if($thickness !== null) {
    $error_message = (new Executer("update request_calc set thickness='$thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение флажка "Сырьё заказчика"
$customers_material = filter_input(INPUT_GET, 'customers_material');
if($customers_material !== null) {
    $error_message = (new Executer("update request_calc set customers_material=$customers_material where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской марки плёнки
$individual_brand_name = filter_input(INPUT_GET, 'individual_brand_name');
if($individual_brand_name !== null) {
    $error_message = (new Executer("update request_calc set individual_brand_name='$individual_brand_name' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской стоимости плёнки
$individual_price = filter_input(INPUT_GET, 'individual_price');
if($individual_price !== null) {
    $error_message = (new Executer("update request_calc set individual_price='$individual_price' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской толщины плёнки
$individual_thickness = filter_input(INPUT_GET, 'individual_thickness');
if($individual_thickness !== null) {
    $error_message = (new Executer("update request_calc set individual_thickness='$individual_thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательского удельного веса плёнки
$individual_density = filter_input(INPUT_GET, 'individual_density');
if($individual_density !== null) {
    $error_message = (new Executer("update request_calc set individual_density='$individual_density' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение марки плёнки ЛАМИНАЦИЯ 1
$lamination1_brand_name = filter_input(INPUT_GET, 'lamination1_brand_name');
if($lamination1_brand_name !== null) {
    $lamination1_brand_name = addslashes($lamination1_brand_name);
    $error_message = (new Executer("update request_calc set lamination1_brand_name='$lamination1_brand_name' where id=$id"))->error;
    
    // Если плёнка пользовательская, то сохраняем пустую толщину
    // Иначе сохраняем пустые пользовательские значения
    if($lamination1_brand_name == INDIVIDUAL) {
        $error_message = (new Executer("update request_calc set lamination1_thickness=NULL where id=$id"))->error;
    }
    else {
        $error_message = (new Executer("update request_calc set lamination1_individual_brand_name='', lamination1_individual_price=NULL, lamination1_individual_thickness=NULL, lamination1_individual_density=NULL where id=$id"))->error;
    }
    
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение толщины плёнки ЛАМИНАЦИЯ 1
$lamination1_thickness = filter_input(INPUT_GET, 'lamination1_thickness');
if($lamination1_thickness !== null) {
    $error_message = (new Executer("update request_calc set lamination1_thickness='$lamination1_thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение флажка "Сырьё заказчика" ЛАМИНАЦИЯ 1
$lamination1_customers_material = filter_input(INPUT_GET, 'lamination1_customers_material');
if($lamination1_customers_material !== null) {
    $error_message = (new Executer("update request_calc set lamination1_customers_material=$lamination1_customers_material where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской марки плёнки ЛАМИНАЦИЯ 1
$lamination1_individual_brand_name = filter_input(INPUT_GET, 'lamination1_individual_brand_name');
if($lamination1_individual_brand_name !== null) {
    $error_message = (new Executer("update request_calc set lamination1_individual_brand_name='$lamination1_individual_brand_name' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской стоимости плёнки ЛАМИНАЦИЯ 1
$lamination1_individual_price = filter_input(INPUT_GET, 'lamination1_individual_price');
if($lamination1_individual_price !== null) {
    $error_message = (new Executer("update request_calc set lamination1_individual_price='$lamination1_individual_price' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской толщины плёнки ЛАМИНАЦИЯ 1
$lamination1_individual_thickness = filter_input(INPUT_GET, 'lamination1_individual_thickness');
if($lamination1_individual_thickness !== null) {
    $error_message = (new Executer("update request_calc set lamination1_individual_thickness='$lamination1_individual_thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательского удельного веса плёнки ЛАМИНАЦИЯ 1
$lamination1_individual_density = filter_input(INPUT_GET, 'lamination1_individual_density');
if($lamination1_individual_density !== null) {
    $error_message = (new Executer("update request_calc set lamination1_individual_density='$lamination1_individual_density' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение марки плёнки ЛАМИНАЦИЯ 2
$lamination2_brand_name = filter_input(INPUT_GET, 'lamination2_brand_name');
if($lamination2_brand_name !== null) {
    $lamination2_brand_name = addslashes($lamination2_brand_name);
    $error_message = (new Executer("update request_calc set lamination2_brand_name='$lamination2_brand_name' where id=$id"))->error;
    
    // Если плёнка пользовательская, то сохраняем пустую толщину
    // Иначе сохраняем пустые пользовательские значения
    if($lamination2_brand_name == INDIVIDUAL) {
        $error_message = (new Executer("update request_calc set lamination2_thickness=NULL where id=$id"))->error;
    }
    else {
        $error_message = (new Executer("update request_calc set lamination2_individual_brand_name='', lamination2_individual_price=NULL, lamination2_individual_thickness=NULL, lamination2_individual_density=NULL where id=$id"))->error;
    }
    
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение толщины плёнки ЛАМИНАЦИЯ 2
$lamination2_thickness = filter_input(INPUT_GET, 'lamination2_thickness');
if($lamination2_thickness !== null) {
    $error_message = (new Executer("update request_calc set lamination2_thickness='$lamination2_thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение флажка "Сырьё заказчика" ЛАМИНАЦИЯ 2
$lamination2_customers_material = filter_input(INPUT_GET, 'lamination2_customers_material');
if($lamination2_customers_material !== null) {
    $error_message = (new Executer("update request_calc set lamination2_customers_material=$lamination2_customers_material where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской марки плёнки ЛАМИНАЦИЯ 2
$lamination2_individual_brand_name = filter_input(INPUT_GET, 'lamination2_individual_brand_name');
if($lamination2_individual_brand_name !== null) {
    $error_message = (new Executer("update request_calc set lamination2_individual_brand_name='$lamination2_individual_brand_name' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской стоимости плёнки ЛАМИНАЦИЯ 2
$lamination2_individual_price = filter_input(INPUT_GET, 'lamination2_individual_price');
if($lamination2_individual_price !== null) {
    $error_message = (new Executer("update request_calc set lamination2_individual_price='$lamination2_individual_price' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательской толщины плёнки ЛАМИНАЦИЯ 2
$lamination2_individual_thickness = filter_input(INPUT_GET, 'lamination2_individual_thickness');
if($lamination2_individual_thickness !== null) {
    $error_message = (new Executer("update request_calc set lamination2_individual_thickness='$lamination2_individual_thickness' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение пользовательского удельного веса плёнки ЛАМИНАЦИЯ 2
$lamination2_individual_density = filter_input(INPUT_GET, 'lamination2_individual_density');
if($lamination2_individual_density !== null) {
    $error_message = (new Executer("update request_calc set lamination2_individual_density='$lamination2_individual_density' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение ширины ручья
$stream_width = filter_input(INPUT_GET, 'stream_width');
if($stream_width !== null) {
    $error_message = (new Executer("update request_calc set stream_width=$stream_width where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение количества ручьёв
$streams_number = filter_input(INPUT_GET, 'streams_number');
if($streams_number !== null) {
    $error_message = (new Executer("update request_calc set streams_number=$streams_number where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение количества этикеток на валу
$number_on_raport = filter_input(INPUT_GET, 'number_on_raport');
if($number_on_raport !== null) {
    $error_message = (new Executer("update request_calc set number_on_raport=$number_on_raport where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение рапорта
$raport = filter_input(INPUT_GET, 'raport');
if($raport !== null) {
    $error_message = (new Executer("update request_calc set raport=$raport where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение длины этикетки вдоль рапорта
$label_length = filter_input(INPUT_GET, 'label_length');
if($label_length !== null) {
    $error_message = (new Executer("update request_calc set label_length=$label_length where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение ширины вала ламинации
$lamination_roller_width = filter_input(INPUT_GET, 'lamination_roller_width');
if($lamination_roller_width !== null) {
    $error_message = (new Executer("update request_calc set lamination_roller_width='$lamination_roller_width' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение ширины лыж
$ski_width = filter_input(INPUT_GET, 'ski_width');
if($ski_width !== null) {
    $error_message = (new Executer("update request_calc set ski_width='$ski_width' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автосохранение флажка "Печать без лыж"
$no_ski = filter_input(INPUT_GET, 'no_ski');
if($no_ski !== null) {
    $error_message = (new Executer("update request_calc set no_ski=$no_ski where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Вывод сообщения об ошибке
if(!empty($error_message)) {
    echo $error_message;
}
?>