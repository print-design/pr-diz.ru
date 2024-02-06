<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку
if(null === filter_input(INPUT_GET, 'id')) {
    header('Location: '.APPLICATION.'/calculation/');
}

// ПОЛУЧЕНИЕ ОБЪЕКТА
$id = filter_input(INPUT_GET, 'id');
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <div class="text-nowrap nav2">
                <a href="details.php?<?= http_build_query($_GET) ?>" class="mr-4">Расчёт</a>
                <a href="techmap.php?<?= http_build_query($_GET) ?>" class="mr-4">Тех. карта</a>
                <a href="cut.php?<?= http_build_query($_GET) ?>" class="mr-4 active">Результаты</a>
            </div>
            <hr />
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>