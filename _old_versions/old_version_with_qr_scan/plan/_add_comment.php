<?php
require_once '../include/topscripts.php';

$planType = filter_input(INPUT_GET, 'plan_type');
$id = filter_input(INPUT_GET, 'id');
$text = addslashes(filter_input(INPUT_GET, 'text'));
$error = '';
$result = '';

$sql_update = '';
$sql_select = '';

switch ($planType) {
    case PLAN_TYPE_EVENT:
        $sql_update = "update plan_event set comment = '$text' where id = $id";
        $sql_select = "select comment from plan_event where id = $id";
        break;
    case PLAN_TYPE_EDITION:
        $sql_update = "update plan_edition set comment = '$text' where id = $id";
        $sql_select = "select comment from plan_edition where id = $id";
        break;
    case PLAN_TYPE_CONTINUATION:
        $sql_update = "update plan_continuation set comment = '$text' where id = $id";
        $sql_select = "select comment from plan_continuation where id = $id";
        break;
    case PLAN_TYPE_PART:
        $sql_update = "update plan_part set comment = '$text' where id = $id";
        $sql_select = "select comment from plan_part where id = $id";
        break;
    case PLAN_TYPE_PART_CONTINUATION:
        $sql_update = "update plan_part_continuation set comment = '$text' where id = $id";
        $sql_select = "select comment from plan_part_continuation where id = $id";
        break;
}

$executer = new Executer($sql_update);
$error = $executer->error;

if(empty($error)) {
    $fetcher = new Fetcher($sql_select);
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