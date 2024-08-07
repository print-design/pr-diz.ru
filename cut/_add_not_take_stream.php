<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'add_not_take_stream_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $calculation_stream_id = filter_input(INPUT_POST, 'calculation_stream_id');
    $weight = filter_input(INPUT_POST, 'weight');
    $length = filter_input(INPUT_POST, 'length');
    $location = filter_input(INPUT_POST, 'php_self');
    $location_get = array();
    
    foreach($_POST as $key=>$value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4) {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    unset($location_get['stream_id']);
    unset($location_get['take_stream_id']);
    unset($location_get['take_id']);
    unset($location_get['invalid_take']);
    unset($location_get['invalid_not_take']);
    unset($location_get['error_message']);
    
    if(empty($weight)) {
        unset($location_get['not_take_stream_id']);
        $location_get['invalid_not_take'] = 1;
        header('Location: '.$location."?".http_build_query($location_get)."&error_message=".urlencode('Невалидные данные'));
        exit();
    }
    
    // Если не было сделано ни одного съёма, то нет исходных данных, по которым рассчитывать длину нового ролика.
    // Значит выдаём ошибку: Сначала создайте хотя бы один ролик из съёма с таким названием.
    if($length == 0) {
        unset($location_get['not_take_stream_id']);
        header('Location: '.$location."?". http_build_query($location_get)."&error_message=". urlencode('Сначала создайте хотя бы один ролик из съёма с таким названием').'#');
        exit();
    }
    
    $sql = "insert into calculation_not_take_stream (calculation_stream_id, weight, length, printed) values ($calculation_stream_id, $weight, $length, now())";
    $executer = new Executer($sql);
    $not_take_stream_id = $executer->insert_id;
    
    $location_get['not_take_stream_id'] = $not_take_stream_id;
    
    header('Location: '.$location."?".http_build_query($location_get).'#not_take');
}
?>