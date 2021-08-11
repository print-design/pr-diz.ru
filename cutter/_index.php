<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$user_id = GetUserId();
$sql = "update user set request_uri='$request_uri' where id=$user_id";
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
                <a class="nav-link mr-0" id="logout-submit" href="javascript: void(0);"><i class="fa fa-cog" aria-hidden="true""></i></a>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <button id="mat-submit" class="btn btn-dark w-100 mt-4">Приступить к раскрою</button>
</div>
<script>
    // Переход на страницу с выбором материала для резки
    function Submit() {
        OpenAjaxPage('_material.php?supplier_id=&film_brand_id=&thickness=&width=');
        submit = true;
    }
    
    $('#mat-submit').click(function() {
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
    
    // Переход на страницу для разлогинивания
    function Logout() {
        OpenAjaxPage("_logout.php");
    }
    
    $('#logout-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Logout();
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
<?php
// Проверка, имеются ли нарезки, у которых нет исходного ролика
include '_check_unclosed_cut.php';
?>