<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$sql = "update user set request_uri='$request_uri' where id=". GetUserId();
$error_message = (new Executer($sql))->error;
if(!empty($error_message)) {
    exit($error_message);
}
?>
OK
<?php print_r($_GET); ?>