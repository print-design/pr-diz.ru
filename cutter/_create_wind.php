<?php
include '../include/topscripts.php';

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

$cut_id = filter_input(INPUT_GET, 'cut_id');
$length = filter_input(INPUT_GET, 'length');
$radius = filter_input(INPUT_GET, 'radius');
$net_weight = filter_input(INPUT_GET, 'net_weight');
$cell = "Цех";
$comment = "";

$sql = "insert into cut_wind (cut_id, length, radius) values($cut_id, $length, $radius)";
$executer = new Executer($sql);
$error_message = $executer->error;
$cut_wind_id = $executer->insert_id;

$supplier_id = 0;
$film_brand_id = 0;
$thickness = 0;
$sql = "select supplier_id, film_brand_id, thickness from cut where id=$cut_id";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

if(!empty($error_message)) {
    exit($error_message);
}

if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
}

// Создание рулона на каждый ручей
$id_from_supplier = "Из раскроя";
$user_id = GetUserId();

$sql = "select width from cut_stream where cut_id=$cut_id";
$grabber = new Grabber($sql);
$result = $grabber->result;
$error_message = $grabber->error;

if(!empty($error_message)) {
    exit($error_message);
}

foreach($result as $row) {
    $width = $row['width'];
    $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id, cut_wind_id) "
            . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id', $cut_wind_id)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    $roll_id = $executer->insert_id;
    
    if(empty($error_message)) {
        $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($roll_id, $free_status_id, $user_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

if(!empty($error_message)) {
    exit($error_message);
}

echo $cut_wind_id;
?>