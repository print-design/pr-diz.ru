<?php
include '../include/topscripts.php';

$cut_id = filter_input(INPUT_GET, 'cut_id');

// Статус "СВОБОДНЫЙ"
$free_status_id = 1;

// Статус "РАСКРОИЛИ"
$cut_status_id = 3;

// Список исходных роликов
$cut_sources = array();

// Массив сообщений об ошибке для каждого исходного ролика
$result = array();

// Получаем параметры раскроя
$sql = " select fb.name film_brand, c.thickness, c.width "
        . "from cut c inner join film_brand fb on c.film_brand_id = fb.id "
        . "where c.id=$cut_id";
$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;
$row = $fetcher->Fetch();
$film_brand = $row['film_brand'];
$thickness = $row['thickness'];
$width = $row['width'];

if(!empty($error_message)) {
    $result['error'] = $error_message;
}

for($i=1; $i<=19; $i++) {
    $source = filter_input(INPUT_GET, 'source_'.$i);
    
    if(!empty($source)) {
        $message = "";
        
        // Проверяем, чтобы номер рулона соответствовал реальному рулону и имел такие же параметры
        if(mb_substr($source, 0, 1) == "р" || mb_substr($source, 0, 1) == "Р") {
            // Ищем такой среди свободных роликов
            $roll_id = mb_substr($source, 1);
            
            $sql = "select fb.name film_brand, r.thickness, r.width, r.length "
                    . "from roll r "
                    . "inner join film_brand fb on r.film_brand_id = fb.id "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "where r.id = $roll_id and (rsh.status_id is null or rsh.status_id = $free_status_id)";
            $fetcher = new Fetcher($sql);
            $error_message = $fetcher->error;
            
            if(!empty($error_message)) {
                $result['error'] = $error_message;
            }
            
            if($row = $fetcher->Fetch()) {
                if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                // Валидацию по ширине временно отключаем
                // if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness) {
                    $cut_source = array();
                    $cut_source['cut_id'] = $cut_id;
                    $cut_source['is_from_pallet'] = 0;
                    $cut_source['roll_id'] = $roll_id;
                    $cut_source['length'] = $row['length'];
                    array_push($cut_sources, $cut_source);
                }
                else {
                    $message = "Марка/толщина/ширина не совпадают";
                    // Валидацию по ширине временно отключаем
                    // $message = "Марка/толщина не совпадают";
                }
            }
            else {
                $message = "Нет ролика с таким номером";
            }
        }
        elseif(mb_substr($source, 0, 1) == "п" || mb_substr ($source, 0, 1) == "П") {
            // Ищем среди роликов в паллетах
            $pallet_trim = mb_substr($source, 1);
            $substrings = mb_split("\D", $pallet_trim);
            
            if(count($substrings) == 2 && !empty($substrings[0]) && !empty($substrings[1])) {
                $pallet_id = $substrings[0];
                $ordinal = $substrings[1];
                
                $sql = "select pr.id roll_id, fb.name film_brand, p.thickness, p.width, pr.length "
                        . "from pallet p "
                        . "inner join pallet_roll pr on pr.pallet_id = p.id "
                        . "inner join film_brand fb on p.film_brand_id = fb.id "
                        . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                        . "where p.id = $pallet_id and pr.ordinal = $ordinal and (prsh.status_id is null or prsh.status_id = $free_status_id)";
                $fetcher = new Fetcher($sql);
                $error_message = $fetcher->error;
                
                if(!empty($error_message)) {
                    $result['error'] = $error_message;
                }
                
                if($row = $fetcher->Fetch()) {
                    if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                    // Валидацию по ширине временно отключаем
                    // if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness) {
                        $cut_source = array();
                        $cut_source['cut_id'] = $cut_id;
                        $cut_source['is_from_pallet'] = 1;
                        $cut_source['roll_id'] = $row['roll_id'];
                        $cut_source['length'] = $row['length'];
                        array_push($cut_sources, $cut_source);
                    }
                    else {
                        $message = "Марка/толщина/ширина не совпадают";
                        // Валидацию по ширине временно отключаем
                        // $message = "Марка/толщина не совпадают";
                    }
                }
                else {
                    $message = "Нет ролика с таким номером";
                }
            }
            else {
                $message = "Нет ролика с таким номером";
            }
        }
        else {
            $message = "Нет ролика с таким номером";
        }
        
        $result['source_'.$i] = $message;
    }
}

// Проверка на валидность, идти ли дальше
$valid = true;

foreach ($result as $key => $value) {
    if(!empty($value)) {
        $valid = false;
    }
}

// Проверка сумм длин исходных роликов и намоток
// (Временно отключаем)
/*if($valid) {
    // Общая длина исходных роллей
    $source_sum = 0;
    
    foreach ($cut_sources as $cut_source) {
        $source_sum += $cut_source['length'];
    }
    
    // Общая длина намоток
    $wind_sum = 0;
    $sql = "select sum(length) sum from cut_wind where cut_id = $cut_id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        $wind_sum = $row['sum'];
    }
    
    if($wind_sum > $source_sum) {
        $valid = false;
        
        foreach ($result as $key => $value) {
            $result[$key] = "Сумма длин намоток больше суммы длин исходных роликов";
        }
    }
}*/

// Проверяем, чтобы не ввели два одинаковых ролика
if($valid) {
    $existing_rolls = array();
    
    for($i=1; $i<=19; $i++) {
        $source = filter_input(INPUT_GET, 'source_'.$i);
        
        if(!empty($source)) {
            if(in_array($source, $existing_rolls)) {
                $result['source_'.$i] = "Ролик введён два раза";
                $valid = false;
            }
            
            array_push($existing_rolls, $source);
        }
    }
}

// Меняем статусы исходных роликов
if($valid) {
    foreach ($cut_sources as $cut_source) {
        $user_id = GetUserId();
        $cut_id = $cut_source['cut_id'];
        $is_from_pallet = $cut_source['is_from_pallet'];
        $roll_id = $cut_source['roll_id'];
        $sql = "insert into cut_source (cut_id, is_from_pallet, roll_id) values ($cut_id, $is_from_pallet, $roll_id)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
            
        if(empty($error_message)) {
            if($is_from_pallet == 0) {
                $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
            else {
                $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
            
            if(!empty($error_message)) {
                $result['error'] = $error_message;
            }
        }
    }
}

echo json_encode($result);
?>