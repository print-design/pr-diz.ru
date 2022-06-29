<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

$person = addslashes(filter_input(INPUT_GET, 'person'));

if(null !== $person) {
    $str_person = addslashes($person);
    $sql = "update customer set person = '$str_person' where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        echo $person;
    }
    else {
        echo "";
    }
}
?>