<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки
include '_check_cuts.php';
CheckCuts($user_id);

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Находим id раскроя
$cut_id = null;
$sql = "select id from cut where cutter_id = $user_id and id in (select cut_id from cut_source) order by id desc limit 1";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $cut_id = $row[0];
}

// Получение объекта
$supplier_id = null;
$film_brand_id = null;
$thickness = null;
$width = null;

$sql = "select supplier_id, film_brand_id, thickness, width from cut where id = $cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $supplier_id = $row['supplier_id'];
    $film_brand_id = $row['film_brand_id'];
    $thickness = $row['thickness'];
    $width = $row['width'];
}
?>
<h1>REMAIN</h1>