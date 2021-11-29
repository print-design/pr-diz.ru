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

// Автозаполнение заказчика
$customer_id = filter_input(INPUT_GET, 'customer_id');
if($customer_id !== null) {
    $error_message = (new Executer("update request_calc set customer_id=$customer_id where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Автозаполнение названия заказа
$name = filter_input(INPUT_GET, 'name');
if($name !== null) {
    $name = addslashes($name);
    $error_message = (new Executer("update request_calc set name='$name' where id=$id"))->error;
    if(empty($error_message)) {
        echo 'OK';
    }
}

// Вывод сообщения об ошибке
if(!empty($error_message)) {
    echo $error_message;
}
?>