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

// Если есть незакрытая нарезка, где нет ни одного исходного ролика, переводим на страницу создания исходного ролика.
if(!empty($opened_roll['id']) && empty($opened_roll['is_from_pallet']) && empty($opened_roll['roll_id'])) {
    header("Location: source.php");
}
// Если есть незакрытая заявка, где есть исходный ролик без ручьёв, переводим на страницу "Как резать"
elseif (!empty ($opened_roll['id']) && !empty ($opened_roll['no_streams_source'])) {
    header("Location: streams.php");
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
            <a class="btn btn-dark w-100 mt-4" href="material.php">Приступить к раскрою</a>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>