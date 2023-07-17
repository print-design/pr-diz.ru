<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');
$i = filter_input(INPUT_GET, 'i');
$value = addslashes(filter_input(INPUT_GET, 'value'));

$result = "";

$sql = "update calculation set requirement$i = '$value' where id = $id";
$executer = new Executer($sql);
$error_message = $executer->error;

if(empty($error_message)) {
    $sql = "select requirement$i from calculation where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
    else {
        $result = "Ошибка при редактировании требования по материалу";
    }
}
else {
    $result = $error_message;
}

echo $result;
?>