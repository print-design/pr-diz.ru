<?php
include '../include/topscripts.php';

$brand_name = filter_input(INPUT_GET, 'brand_name');
$thickness = filter_input(INPUT_GET, 'thickness');
$price = filter_input(INPUT_GET, 'price');
$currency = filter_input(INPUT_GET, 'currency');

if(!empty($brand_name) && !empty($thickness) && !empty($price) && !empty($currency)) {
    $sql = "insert into film_price (brand_name, thickness, price, currency) values ('$brand_name', $thickness, $price, '$currency')";
    $executer = new Executer($sql);
    if(empty($executer->error)) {
        $sql = "select price from film_price where brand_name='$brand_name' and thickness=$thickness order by id desc limit 1";
        $fetcher = new Fetcher($sql);
            
        if($row = $fetcher->Fetch()) {
            echo $row[0];
        }
        else {
            echo 0;
        }
    }
    else {
        echo 0;
    }
}
else {
    echo 0;
}
?>