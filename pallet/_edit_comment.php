<?php
require_once '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$text = filter_input(INPUT_GET, 'text') ?? '';
$error = '';
$result = '';

$sql = "update pallet set comment = ? where id = ?";
$executer = new Executer($sql, [$text, $id]);
$error = $executer->error;

if(empty($error)) {
    $sql = "select comment from pallet where id = ?";
    $fetcher = new Fetcher($sql, [$id]);
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