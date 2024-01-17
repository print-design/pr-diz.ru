<?php
if(null !== filter_input(INPUT_POST, 'edit_take_stream_submit')) {
    $location = filter_input(INPUT_POST, 'php_self');
    $location_get = array();
    
    foreach($_POST as $key => $value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4 && mb_substr($key, 4) != 'stream_id') {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    $location_get['scroll'] = filter_input(INPUT_POST, 'scroll');
    $location_get['take_id'] = filter_input(INPUT_POST, 'take_id');
    
    header('Location: '.$location."?". http_build_query($location_get));
}
?>