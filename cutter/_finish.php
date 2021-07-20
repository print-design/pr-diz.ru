<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$sql = "update user set request_uri='$request_uri' where id=". GetUserId();
$error_message = (new Executer($sql))->error;
if(!empty($error_message)) {
    exit($error_message);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start"></nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1 class="text-center">Заявка закрыта</h1>
    <p class="text-center" style="font-size: x-large; color: green;">Молодец:)</p>
    <div style="height: 22rem;"></div>
    <button type="button" class="btn btn-dark form-control goto_index">Вернуться в заявки</button>
</div>