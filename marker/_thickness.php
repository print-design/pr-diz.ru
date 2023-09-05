<?php
include '../include/topscripts.php';

// Получение толщин плёнки по ID марки для раскрывающегося списка
$film_id = filter_input(INPUT_GET, 'film_id');

if(!empty($film_id)) {
    echo "<option value='' hidden='hidden' selected='selected'>Выберите толщину</option>";
    $sql = "select id, thickness, weight from film_variation where film_id = $film_id and id in (select film_variation_id from supplier_film_variation) order by thickness";
    $grabber = (new Grabber($sql))->result;
    
    foreach ($grabber as $row) {
        $film_variation_id = intval($row['id']);
        $thickness = intval($row['thickness']);
        $weight = floatval($row['weight']);
        echo "<option value='$film_variation_id'>$thickness мкм $weight г/м<sup>2</sup></option>";
    }
}
?>