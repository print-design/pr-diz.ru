<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
$error_message = (new Executer($sql))->error;

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
    <button id="index-submit" type="button" class="btn btn-dark form-control">Вернуться в заявки</button>
</div>
<script>
    function Submit() {
        OpenAjaxPage("_index.php");
        submit = true;
    }
    
    $('#index-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Submit();
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
    });
</script>