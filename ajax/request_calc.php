<?php
include '../include/topscripts.php';

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

// Вывод сообщения об ошибке
if(!empty($error_message)) {
    echo $error_message;
}
?>