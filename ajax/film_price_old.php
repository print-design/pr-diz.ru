<?php
include '../include/topscripts.php';

$brand_name = filter_input(INPUT_GET, 'brand_name');
$thickness = filter_input(INPUT_GET, 'thickness');
$price = filter_input(INPUT_GET, 'price');
$currency = filter_input(INPUT_GET, 'currency');

if(!empty($brand_name) && !empty($thickness) && !empty($price) && !empty($currency)) {
    $sql = "select count(id) from film_price where brand_name='$brand_name' and thickness=$thickness";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $count = $row[0];
        $sql = "";
        
        if($count == 0) {
            $sql = "insert into film_price (brand_name, thickness, price, currency) values ('$brand_name', $thickness, $price, '$currency')";
        }
        else {
            $sql = "update film_price set price=$price, currency='$currency' where brand_name='$brand_name' and thickness=$thickness";
        }
        
        $executer = new Executer($sql);
        if(empty($executer->error)) {
            $sql = "select price from film_price where brand_name='$brand_name' and thickness=$thickness";
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
}
else {
    echo 0;
}
?>