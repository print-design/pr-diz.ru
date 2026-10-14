<?php
include '../include/topscripts.php';

$supplier_id = filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT);

// Получение толщин плёнки по ID марки для раскрывающегося списка
$film_id = filter_input(INPUT_GET, 'film_id', FILTER_VALIDATE_INT);

if(!empty($film_id)) {
    echo "<option value='' hidden='hidden' selected='selected'>Выберите толщину</option>";
    $sql = "select id, thickness, weight from film_variation where film_id = ?";
    $params = [$film_id];
    
    if(!empty($supplier_id)) {
        $sql .= " and id in (select film_variation_id from supplier_film_variation where supplier_id = ?)";
        $params[] = $supplier_id;
    }
    
    $sql .= " order by thickness";
    $grabber = (new Grabber($sql, $params))->result;
    
    foreach ($grabber as $row) {
        $film_variation_id = intval($row['id']);
        $thickness = intval($row['thickness']);
        $weight = floatval($row['weight']);
        echo "<option value='$film_variation_id'>$thickness мкм $weight г/м<sup>2</sup></option>";
    }
}

// Получение толщин плёнки по названию марки для ползунка
$film = filter_input(INPUT_GET, 'film');

if(!empty($film)) {
    $grabber = (new Grabber("select distinct fv.thickness from film_variation fv inner join film f on fv.film_id = f.id where f.id = ? order by thickness", [$film]))->result;
    $result = array();
    
    foreach ($grabber as $row) {
        array_push($result, $row['thickness']);
    }
    
    echo json_encode($result);
}

// Получение толщин плёнки для раскрывающегося списка
$film_name = filter_input(INPUT_GET, 'film_name') ?? '';

if(!empty($film_name) && !empty($supplier_id)) {
    echo "<option value='' hidden='hidden' selected='selected'>Толщина...</option>";
    $grabber = (new Grabber("select distinct fv.id, fv.thickness, fv.weight from film_variation fv inner join film f on fv.film_id = f.id where f.name = ? and fv.id in (select film_variation_id from supplier_film_variation where supplier_id = ?) order by thickness", [$film_name, $supplier_id]))->result;
    
    foreach ($grabber as $row) {
        $film_variation_id = intval($row['id']);
        $thickness = intval($row['thickness']);
        $weight = floatval($row['weight']);
        echo "<option value='$thickness'>$thickness мкм $weight г/м<sup>2</sup></option>";
    }
}
?>