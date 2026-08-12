<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) && null !== filter_input(INPUT_GET, 'active')) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $active = filter_input(INPUT_GET, 'active');
    $sql = "update user set active=$active where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(!empty($error_message)) {
        echo $error_message;
    }
}
?>