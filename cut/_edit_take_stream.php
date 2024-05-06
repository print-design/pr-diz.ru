<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'edit_take_stream_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $weight = filter_input(INPUT_POST, 'weight');
    $length = filter_input(INPUT_POST, 'length');
    $radius = filter_input(INPUT_POST, 'radius');
    $location = filter_input(INPUT_POST, 'php_self');
    $location_get = array();
    
    foreach($_POST as $key => $value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4) {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    unset($location_get['stream_id']);
    unset($location_get['not_take_stream_id']);
    unset($location_get['invalid_take']);
    unset($location_get['invalid_not_take']);
    unset($location_get['error_message']);
    $location_get['take_stream_id'] = $id;
    $location_get['scroll'] = filter_input(INPUT_POST, 'scroll');
    
    $sql = "select calculation_take_id from calculation_take_stream where id = $id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        $location_get['take_id'] = $row['calculation_take_id'];
        $sql = "update calculation_take_stream set weight = $weight, length = $length, radius = $radius where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.$location."?". http_build_query($location_get));
        }
        else {
            header('Location: '.APPLICATION.'/cut/');
        }
    }
    else {
        header('Location: '.APPLICATION.'/cut/');
    }
}
?>