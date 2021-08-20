<?php
include '../include/topscripts.php';

// Текущий пользователь
$user_id = GetUserId();

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Определяем ID незакрытого ролика
$cut_id = null;
$sql = "select id from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row[0];
}

// Создаём намотку
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

for($i=1; $i<=19; $i++) {
    if(key_exists('stream_'.$i, $_GET)) {
        $width = filter_input(INPUT_GET, 'stream_'.$i);
        $net_weight = filter_input(INPUT_GET, 'net_weight_'.$i);
        
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
}

if(!empty($error_message)) {
    exit($error_message);
}

echo $cut_wind_id;
?>