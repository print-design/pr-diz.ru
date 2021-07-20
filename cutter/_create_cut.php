<?php
include '../include/topscripts.php';

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

$supplier_id = filter_input(INPUT_GET, 'supplier_id');
$film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
$thickness = filter_input(INPUT_GET, 'thickness');
$width = filter_input(INPUT_GET, 'width');

$sql = "insert into cut (supplier_id, film_brand_id, thickness, width) values($supplier_id, $film_brand_id, $thickness, $width)";
$executer = new Executer($sql);
$error_message = $executer->error;
$cut_id = $executer->insert_id;

if(!empty($error_message)) {
    exit($error_message);
}

for($i=1; $i<=19; $i++) {
    if(key_exists('stream_'.$i, $_GET)) {
        $width = filter_input(INPUT_GET, 'stream_'.$i);
        $sql = "insert into cut_stream (cut_id, width) values($cut_id, $width)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if(!empty($error_message)) {
    exit($error_message);
}

$length = filter_input(INPUT_GET, 'length');
$radius = filter_input(INPUT_GET, 'radius');
$net_weight = filter_input(INPUT_GET, 'net_weight');
$cell = "Цех";
$comment = "";

$sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
$executer = new Executer($sql);
$error_message = $executer->error;
$cut_wind_id = $executer->insert_id;

if(!empty($error_message)) {
    exit($error_message);
}

// Создание рулона на каждый ручей
$id_from_supplier = "Из раскроя";
$user_id = GetUserId();
    
for($i=1; $i<=19; $i++) {
    if(key_exists('stream_'.$i, $_GET)) {
        $width = filter_input(INPUT_GET, 'stream_'.$i);
    
        $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id, cut_wind_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id', $cut_wind_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
                    
        if(empty($error_message)) {
            $sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $free_status_id, $user_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
}

if(!empty($error_message)) {
    exit($error_message);
}

echo $cut_wind_id;
?>