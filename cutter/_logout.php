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
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <li class="nav-item">
                <button class="btn btn-link nav-link" id="index-submit"><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<div class="container-fluid">
    <?php
    if(!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
            
    $position = "Работник";
    if(IsInRole('electrocarist')) {
        $position = "Водитель погрузчика";
    }
    elseif(IsInRole('cutter')) {
        $position = "Резчик раскрой";
    }
    ?>
    <p class="mt-4" style="font-size: 18px; line-height: 24px; font-weight: 600;"><?=$position ?>:</p>
        <?php
        $sql = "select last_name, first_name from user where id=". GetUserId();
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()):
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        ?>
    <p class="mt-2 mb-5" style="font-size: 24px; line-height: 32px; font-weight: 600;"><?=$last_name.' '.$first_name ?></p>
        <?php
        endif;
        ?>
    <form method="post">
        <button type="button" class="btn btn-outline-danger form-control" id="logout_submit" name="logout_submit">Выйти</button>
    </form>
</div>
<script>
    // Выход из системы
    $('#logout_submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        $(this).form().submit();
                    }
                    else {
                        OpenAjaxPage(data);
                    }
                })
                .fail(function() {
                    alert('Ошибка при переходе на страницу.');
                });
    });
    
    // Возврат на главную страницу
    function Index() {
        OpenAjaxPage("_index.php");
    }
    
    $('#index-submit').click(function() {
        $.ajax({ url: "_check_db_uri.php?uri=<?= urlencode($request_uri) ?>" })
                .done(function(data) {
                    if(data == "OK") {
                        Index();
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