<?php
include '../include/topscripts.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$sql = "update dialog set viewed = 1 where id = ?";
$executer = new Executer($sql, [$id]);
if(!empty($executer->error)) {
    echo $executer->error;
}
else {
    echo 1;
}
?>