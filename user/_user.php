<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) && null !== filter_input(INPUT_GET, 'active')) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $active = filter_input(INPUT_GET, 'active') === 'true' ? 1 : 0;
    $sql = "update user set active = ? where id = ?";
    $executer = new Executer($sql, [$active, $id]);
    $error_message = $executer->error;
    
    if(!empty($error_message)) {
        echo $error_message;
    }
}
?>