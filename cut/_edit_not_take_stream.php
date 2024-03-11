<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'edit_not_take_stream_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $weight = filter_input(INPUT_POST, 'weight');
    $location = filter_input(INPUT_POST, 'php_self');
    $location_get = array();
    
    foreach($_POST as $key => $value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4) {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    unset($location_get['stream_id']);
    unset($location_get['take_stream_id']);
    unset($location_get['take_id']);
    unset($location_get['invalid_take']);
    unset($location_get['invalid_not_take']);
    $location_get['not_take_stream_id'] = $id;
    
    $sql = "select weight, length from calculation_not_take_stream where id = $id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        $old_weight = $row['weight'];
        $old_length = $row['length'];
        
        if(empty($weight)) {
            unset($location_get['not_take_stream_id']);
            $location_get['invalid_not_take'] = 1;
            header('Location: '.$location."?". http_build_query($location_get).'#not_take');
            exit();
        }
        
        $length = $weight * $old_length / $old_weight;
        
        $sql = "update calculation_not_take_stream set weight = $weight, length = $length where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header('Location: '.$location."?". http_build_query($location_get).'#not_take');
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