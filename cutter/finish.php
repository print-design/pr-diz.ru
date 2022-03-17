<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

$cutting_id = filter_input(INPUT_GET, 'id');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-end">
                <ul class="navbar-nav mr-4">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" href="javascript: void(0);" data-toggle="modal" data-target="#infoModal"><img src="<?=APPLICATION ?>/images/icons/info.svg" /></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="logout.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-user-alt" aria-hidden="true""></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            include '_info.php';
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1 class="text-center">Заявка закрыта</h1>
            <p class="text-center" style="font-size: x-large; color: green;" id="molotok">Молодец:)</p>
            <div id="bottom_buttons" class="pb-4">
                <a class="btn btn-dark form-control" href="<?=APPLICATION ?>/cutter/">Вернуться в заявки</a>
            </div>
        </div>
        <?php
        include '_footer.php';
        ?>
        <script>
            // Позиционируем кнопку "Вернуться в заявки" относительно нижнего края экрана только если она не перекроет другие элементы
            function AdjustButtons() {
                if($('#molotok').offset().top + $('#bottom_buttons').outerHeight() + 30 < $(window).height()) {
                    $('#bottom_buttons').removeClass('sticky-top');
                    $('#bottom_buttons').addClass('fixed-bottom');
                    $('#bottom_buttons').addClass('container-fluid');
                }
                else {
                    $('#bottom_buttons').addClass('sticky-top');
                    $('#bottom_buttons').removeClass('fixed-bottom');
                    $('#bottom_buttons').removeClass('container-fluid');
                }
            }
            
            $(document).ready(AdjustButtons);
            
            $(window).on('resize', AdjustButtons);
        </script>
    </body>
</html>