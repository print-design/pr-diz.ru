<?php
include '../include/topscripts.php';

$supplier_id = filter_input(INPUT_GET, 'supplier_id');
if(!empty($supplier_id)) {
    $fetcher = (new Fetcher("select name from supplier where id=$supplier_id"));
    $row = $fetcher->Fetch();
    $supplier_name = $row['name'];
    echo "<option value='' hidden='hidden' selected='selected'>Выберите марку от $supplier_name</option>";
    
    $film_brands = (new Grabber("select id, name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = $supplier_id)) order by name"))->result;
    
    foreach ($film_brands as $film_brand) {
        $id = $film_brand['id'];
        $name = $film_brand['name'];
        echo "<option value='$id'>$name</option>";
    }
}
?>