<?php
include '../include/topscripts.php';
$id = filter_input(INPUT_GET, 'id');

$sql = "update dialog set viewed = 1 where id = $id";
$executer = new Executer($sql);
if(!empty($executer->error)) {
    echo $executer->error;
}
else {
    echo 1;
}
?>