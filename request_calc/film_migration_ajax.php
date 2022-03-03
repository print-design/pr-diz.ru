<?php
include '../include/topscripts.php';

$error_message = '';

// Находим первый ролик, у которого новые параметры плёнки отличаются от старых
$calculation_id = 0;
$film_brand_name = '';
$thickness = 0;
$sql = "select c.id, c.brand_name film_brand_name, c.thickness "
        . "from calculation c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where c.brand_name != 'other' and (f.name is null or fv.thickness is null or f.name != c.brand_name or fv.thickness != c.thickness) "
        . "limit 1";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;
if($row = $fetcher->Fetch()) {
    $calculation_id = $row['id'];
    $film_brand_name = $row['film_brand_name'];
    $thickness = $row['thickness'];
}

if(!empty($error_message)) {
    exit(-1);
}

$film_variation_id = 0;

if(!empty($calculation_id) && !empty($film_brand_name) && !empty($thickness)) {
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

$sql = "update calculation set film_variation_id = $film_variation_id where id = $calculation_id";
$executer = new Executer($sql);
$error_message = $executer->error;

if(!empty($error_message)) {
    exit(-3);
}

// Количество мигрированных рулонов
$ok_count = 0;
$sql = "select count(c.id) "
        . "from calculation c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where f.name = c.brand_name and fv.thickness = c.thickness";
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