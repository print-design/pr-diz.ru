<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;
if(empty($error_message)) {
    $sql = "insert into history (user, request_uri) values($user_id, '$request_uri')";
    $error_message = (new Executer($sql))->error;    
}
if(!empty($error_message)) {
    exit($error_message);
}

include '_info.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item dropdown no-dropdown-arrow-after">
                <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <h1 class="text-center">Заявка закрыта</h1>
    <p class="text-center" style="font-size: x-large; color: green;">Молодец:)</p>
    <div style="height: 22rem;"></div>
    <button type="button" class="btn btn-dark form-control goto_index">Вернуться в заявки</button>
</div>