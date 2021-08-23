<?php
function IsValid($previous, $current) {
    if(empty($current)) {
        return 1;
    }
    
    $result = 0;
    
    // Разбиваем предыдущую страницу на сегменты (сама страница плюс параметры)
    $previous_substrings = mb_split("[\?\&\=]", $previous);
    $current_substrings = mb_split("[\?\&\=]", $current);
    
    // Проверяем, чтобы переход из предыдущей страницы в последующую был валидным
    $valid_jumps = array();
    $valid_jumps['_index.php'] = array("_logout.php", "_material.php");
    $valid_jumps['_logout.php'] = array("_index.php");
    $valid_jumps['_material.php'] = array("_index.php", "_cut.php");
    $valid_jumps['_cut.php'] = array("_material.php", "_wind.php");
    $valid_jumps['_wind.php'] = array("_cut.php", "_print.php");
    $valid_jumps['_print.php'] = array("_next.php");
    $valid_jumps['_next.php'] = array("_print.php", "_close.php");
    $valid_jumps['_close.php'] = array("_next.php", "_remain.php");
    $valid_jumps['_remain.php'] = array("_finish.php", "_print_remain.php");
    $valid_jumps['_print_remain.php'] = array("_finish.php");
    $valid_jumps['_finish.php'] = array("_index.php");

    // В ту же самую страницу переходить можно (например, если перезагрузят страницу)
    if($previous_substrings[0] == $current_substrings[0]
            || (array_key_exists($previous_substrings[0], $valid_jumps) && in_array($current_substrings[0], $valid_jumps[$previous_substrings[0]]))) {
        $result = 1;
    }
    
    // Если вторым параметром является cut_id, проверяем, чтобы третьим параметром было значение незакрытой резки
    if(count($current_substrings) > 2 && ($current_substrings[0] == "_next.php" || $current_substrings[0] == "_close.php") && $current_substrings[1] == "cut_id") {
        $sql = "select count(id) from cut_source where cut_id = ".$current_substrings[2];
        $fetcher = new Fetcher($sql);
        $row = $fetcher->Fetch();
        
        if($row[0] > 0) {
            $result = 0;
        }
    }
    
    // Если вторым параметром является cut_wind_id, 
    // проверяем, чтобы третьим параметром было значение последней намотки незакрытой нарезки
    if(count($current_substrings) > 2 && $current_substrings[0] == "_print.php" && $current_substrings[1] == "cut_wind_id") {
        $sql = "select count(id) from cut_source where cut_id = (select cut_id from cut_wind where id = ".$current_substrings[2].")";
        $fetcher = new Fetcher($sql);
        $row = $fetcher->Fetch();
        
        if($row[0] > 0) {
            $result = 0;
        }
        
        $sql = "select count(id) from cut_wind where cut_id = (select cut_id from cut_wind where id = ".$current_substrings[2].") and id > ".$current_substrings[2];
        $fetcher = new Fetcher($sql);
        $row = $fetcher->Fetch();
        
        if($row[0] > 0) {
            $result = 0;
        }
    }
    
    return $result;
}

$page_db = '';
$sql = "select page_db from cut_history where user_id = $user_id and page_real = ''";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $page_db = $row[0];
}

if(!empty($error_message)) {
    exit($error_message);
}

$valid = 0;

if(!empty($page_db)) {
    $valid = IsValid($page_db, $request_uri);   
}

$sql = "update cut_history set datetime = now(), page_real = '$request_uri', valid = $valid where user_id = $user_id and page_real = ''";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}

$sql = "insert into cut_history (user_id, page_db) values($user_id, (select request_uri from user where id = $user_id))";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}
?>