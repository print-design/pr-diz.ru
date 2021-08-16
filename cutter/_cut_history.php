<?php
$sql = "update cut_history set datetime = now(), page_real = '$request_uri' where user_id = $user_id and page_real = ''";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}

$sql = "insert into cut_history (user_id, page_db) values($user_id, (select request_uri from user where id = $user_id))";
$error_message = (new Executer($sql))->error;

if(!empty($error_message)) {
    exit($error_message);
}
?>