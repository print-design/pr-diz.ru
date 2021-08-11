<?php
$sql = "insert into cut_history (user_id, page_db) values($user_id, '$request_uri')";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}

$sql = "update cut_history set datetime = now(), page_real = '$request_uri' where user_id = $user_id and page_real = ''";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}
?>