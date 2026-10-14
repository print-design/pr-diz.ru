<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$i = filter_input(INPUT_GET, 'i');
$value = filter_input(INPUT_GET, 'value') ?? '';

// $i используется как часть имени колонки (requirement1 .. requirement3), поэтому его нельзя
// передать через параметр prepared statement -- вместо этого проверяем по строгому списку допустимых значений
if(!in_array($i, ['1', '2', '3'], true)) {
    $i = null;
}

$result = "";

$sql = "update calculation set requirement$i = ? where id = ?";
$executer = new Executer($sql, [$value, $id]);
$error_message = $executer->error;

if(empty($error_message)) {
    $sql = "select requirement$i from calculation where id = ?";
    $fetcher = new Fetcher($sql, [$id]);
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