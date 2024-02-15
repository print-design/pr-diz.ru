<?php
include '../include/topscripts.php';

$film_variation_id = filter_input(INPUT_GET, 'film_variation_id');
$result = array("text" => "нет данных", "price" => "", "currency" => "", "currency_local" => "");

if(!empty($film_variation_id)) {
    $sql = "select price, currency from film_price where film_variation_id=$film_variation_id order by id desc limit 1";
    $fetcher = new Fetcher($sql);
            
    if($row = $fetcher->Fetch()) {
        if(!empty($row['price'])) {
            $price_final = rtrim(rtrim(number_format($row['price'], 2, ",", " "), "0"), ",");
            $currency_final = "";
            $currency_local = "";
            switch($row['currency']) {
                case CURRENCY_RUB:
                    $currency_final = "руб";
                    $currency_local = "Руб";
                    break;
                
                case CURRENCY_USD:
                    $currency_final = "USD";
                    $currency_local = "USD";
                    break;
                
                case CURRENCY_EURO:
                    $currency_final = "EUR";
                    $currency_local = "EUR";
                    break;
            }
            
            $result = array("text" => "от $price_final $currency_final", "price" => $row['price'], "currency" => $row['currency'], "currency_local" => $currency_local);
        }
    }
}

echo json_encode($result);
?>