<?php
include '../include/topscripts.php';

$film_brand_id = filter_input(INPUT_GET, 'film_brand_id');

if(!empty($film_brand_id)) {
    echo "<option value=''>Выберите толщину</option>";
    $grabber = (new Grabber("select thickness, weight from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
    
    foreach ($grabber as $row) {
        $thickness = $row['thickness'];
        $weight = $row['weight'];
        echo "<option value='$thickness'>$thickness мкм $weight г/м<sup>2</sup></option>";
    }
}

$film_brand_name = addslashes(filter_input(INPUT_GET, 'film_brand_name'));

if(!empty($film_brand_name)) {
    $grabber = (new Grabber("select distinct fbv.thickness from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$film_brand_name' order by thickness"))->result;
    $result = array();
    
    foreach ($grabber as $row) {
        array_push($result, $row['thickness']);
    }
    
    echo json_encode($result);
}
?>