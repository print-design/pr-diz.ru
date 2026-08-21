<?php
include '../include/topscripts.php';

$error_message = '';

// Находим первый паллет, у которого новые параметры плёнки отличаются от старых
$pallet_id = 0;
$film_brand_name = '';
$thickness = 0;
$sql = "select p.id, fb.name film_brand_name, p.thickness "
        . "from pallet p "
        . "left join film_variation fv on p.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "where f.name is null or fv.thickness is null or f.name != fb.name or fv.thickness != p.thickness "
        . "limit 1";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;
if($row = $fetcher->Fetch()) {
    $pallet_id = $row['id'];
    $film_brand_name = $row['film_brand_name'];
    $thickness = $row['thickness'];
}

if(!empty($error_message)) {
    exit(-1);
}

$film_variation_id = 0;

if(!empty($pallet_id) && !empty($film_brand_name) && !empty($thickness)) {
    $sql = "select fv.id "
            . "from film_variation fv "
            . "inner join film f on fv.film_id = f.id "
            . "where f.name = '$film_brand_name' and fv.thickness = $thickness";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    if($row = $fetcher->Fetch()) {
        $film_variation_id = $row[0];
    }
}

if(!empty($error_message)) {
    exit(-1);
}

if(empty($film_variation_id)) {
    exit(-2);
}

$sql = "update pallet set film_variation_id = $film_variation_id where id = $pallet_id";
$executer = new Executer($sql);
$error_message = $executer->error;

if(!empty($error_message)) {
    exit(-3);
}

// Количество мигрированных паллетов
$ok_count = 0;
$sql = "select count(p.id) "
        . "from pallet p "
        . "left join film_variation fv on p.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "where f.name = fb.name and p.thickness = fv.thickness";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;
if($row = $fetcher->Fetch()) {
    $ok_count = $row[0];
}

if(!empty($error_message)) {
    exit(-1);
}

echo $ok_count;
?>