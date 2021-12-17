<?php
include '../include/topscripts.php';

$brand_name = filter_input(INPUT_GET, 'brand_name');
$thickness = filter_input(INPUT_GET, 'thickness');

$price = filter_input(INPUT_GET, 'price');
$currency = filter_input(INPUT_GET, 'currency');

if(!empty($brand_name) && !empty($thickness) && empty($price) && empty($currency)) {
    $brand_name = addslashes($brand_name);
    $sql = "select price, currency from film_price where brand_name='$brand_name' and thickness = $thickness order by id desc limit 1";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $price_final = rtrim(rtrim(number_format($row['price'], 2, ",", " "), "0"), ",");
        $currency_final = "";
        switch ($row['currency']) {
            case 'rub':
                $currency_final = "руб";
                break;
            
            case 'usd':
                $currency_final = "USD";
                break;
            
            case 'euro':
                $currency_final = "EUR";
                break;
        }
        $result = array("text" => "($price_final&nbsp;$currency_final&nbsp;&nbsp;&nbsp;34&nbsp;кг&nbsp;&nbsp;&nbsp;23&nbsp;000&nbsp;м)",
            "currency" => $row['currency']);
    }
    else {
        $result = array("text" => "Нет данных",
            "currency" => "");
    }
    
    echo json_encode($result);
}

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
?>