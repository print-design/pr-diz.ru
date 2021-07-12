<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/cut/');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= urldecode(filter_input(INPUT_GET, 'link')) ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Нарезка по заявке №1234</h1>
                        <p class="mt-4 mb-5" style="font-size: 1.1rem;">Введите информацию об исходном ролике</p>
                        <form>
                            <div class="form-group">
                                <label for="id">ID исходного ролика</label>
                                <input type="text" 
                                       class="form-control no-latin" 
                                       id="id" 
                                       name="id" 
                                       required="required" 
                                       onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                                       onmouseup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                                       onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }" 
                                       onkeyup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                                       onfocusout="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" />
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" style="margin-top: 250px;">Далее</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>