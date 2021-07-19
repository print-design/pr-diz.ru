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
    <nav class="navbar navbar-expand-sm justify-content-between">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="">Склад</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item dropdown no-dropdown-arrow-after">
                <a class="nav-link mr-0" href="../user_mobile.php?link=<?= APPLICATION.'/cutter/' ?>"><i class="fa fa-cog" aria-hidden="true""></i></a>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <button class="btn btn-dark w-100 mt-4 goto_material">Приступить к раскрою</button>
</div>