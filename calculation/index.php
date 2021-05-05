<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        include '../include/pager_top.php';
        $rowcounter = 0;
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Расчеты</h1>
                </div>
                <div class="p-1">
                    <a href="new.php" class="btn btn-outline-dark"><i class="fas fa-plus"></i>&nbsp;Новый расчет</a>
                </div>
            </div>
            <table class="table" id="content_table">
                <thead>
                    <tr>
                        <th>ID<a href="?sort=id">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Дата<a href="?sort=date">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Заказчик<a href="?sort=customer">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Имя заказа<a href="?sort=order">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Объем кг.<a href="?sort=weight">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Тип работы<a href="?sort=work_type">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Менеджер<a href="?sort=manager">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th>Статус<a href="?sort=status">&nbsp;<i class="fas fa-arrow-down"></i></a></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
            <?php
            if($rowcounter == 0) {
                echo '<p>Ничего не найдено.</p>';
            }
            
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
    </body>
</html>