<?php
include '../include/topscripts.php';

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

$supplier_id = filter_input(INPUT_GET, 'supplier_id');
$film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
$width = filter_input(INPUT_GET, 'width');
$thickness = filter_input(INPUT_GET, 'thickness');
$radius = filter_input(INPUT_GET, 'radius');
$spool = filter_input(INPUT_GET, 'spool');
$net_weight = filter_input(INPUT_GET, 'net_weight');
$length = filter_input(INPUT_GET, 'length');

$id_from_supplier = "Из раскроя";
$cell = "Цех";
$comment = "";
$user_id = GetUserId();

$sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, storekeeper_id) "
        . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$user_id')";
$executer = new Executer($sql);
$error_message = $executer->error;
$roll_id = $executer->insert_id;

if(!empty($error_message)) {
    exit($error_message);
}
            
$sql = "insert into roll_status_history (roll_id, status_id, user_id) values ($roll_id, $free_status_id, $user_id)";
$executer = new Executer($sql);
$error_message = $executer->error;
                
if(!empty($error_message)) {
    exit($error_message);
}

echo $roll_id;
?>