<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$text = addslashes(filter_input(INPUT_GET, 'text') ?? '');
$error = '';
$result = '';

$sql = "update roll set comment = '$text' where id = $id";
$executer = new Executer($sql);
$error = $executer->error;

if(empty($error)) {
    $sql = "select comment from roll where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
    else {
        $result = "Ошибка при чтении комментария из базы";
    }
}
else {
    $result = $error;
}

echo $result;
?>