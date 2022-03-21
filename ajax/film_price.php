<?php
include '../include/topscripts.php';

$film_variation_id = filter_input(INPUT_GET, 'film_variation_id');
$result = array("text" => "нет данных", "text_ext" => "(Нет данных)", "currency" => "");

if(!empty($film_variation_id)) {
    $sql = "select price, currency from film_price where film_variation_id=$film_variation_id order by id desc limit 1";
    $fetcher = new Fetcher($sql);
            
    if($row = $fetcher->Fetch()) {
        $price_final = rtrim(rtrim(number_format($row['price'], 2, ",", " "), "0"), ",");
        $currency_final = "";
        switch($row['currency']) {
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
        $result = array("text" => "от $price_final $currency_final", "text_ext" => "($price_final&nbsp;$currency_final&nbsp;&nbsp;&nbsp;34&nbsp;кг&nbsp;&nbsp;&nbsp;&nbsp;23&nbsp;000&nbsp;м)", "currency" => $row['currency']);
    }
}

echo json_encode($result);
?>