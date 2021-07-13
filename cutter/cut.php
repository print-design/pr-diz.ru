<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение supplier_id, film_brand_id, thickness, width, перенаправляем на Главную
$supplier_id = filter_input(INPUT_GET, 'supplier_id');
$film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
$thickness = filter_input(INPUT_GET, 'thickness');
$width = filter_input(INPUT_GET, 'width');
if(empty($supplier_id) || empty($film_brand_id) || empty($thickness) || empty($width)) {
    header('Location: '.APPLICATION.'/cutter/');
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
        <form method="post" action="material.php" id="back_form">
            <input type="hidden" name="supplier_id" value="<?=$supplier_id ?>" />
            <input type="hidden" name="film_brand_id" value="<?=$film_brand_id ?>" />
            <input type="hidden" name="thickness" value="<?=$thickness ?>" />
            <input type="hidden" name="width" value="<?=$width ?>" />
            <div class="container-fluid header">
                <nav class="navbar navbar-expand-sm justify-content-start">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="javascript: $('form#back_form').submit();"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </form>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка 1 / <?=date('d.m.Y') ?></h1>
            <p style="font-size: large;">Как режем?</p>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>