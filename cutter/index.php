<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();

// Проверяем, имеются ли незакрытые нарезки.
include '_check_rolls.php';
$opened_roll = CheckOpenedRolls($user_id);
$cutting_id = $opened_roll['id'];
$last_source = $opened_roll['last_source'];
$streams_count = $opened_roll['streams_count'];

// Если есть незакрытая нарезка, но нет ни одного исходного ролика, переводим на страницу создания исходного ролика.
if(!empty($cutting_id) && empty($last_source)) {
    header("Location: source.php");
}
// Если есть незакрытая нарезка, но нет ручьёв, переводим на страницу "Как резать"
elseif (!empty($cutting_id) && empty($streams_count)) {
    header("Location: streams.php");
}
// Если есть незакрытая заявка, где есть исходный ролик и ручьи, переводим на страницу намотки
elseif (!empty ($cutting_id)) {
    header("Location: wind.php");
}
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
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="">Склад</a>
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-dark w-100 mt-5" href="material.php">Приступить к раскрою</a>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>