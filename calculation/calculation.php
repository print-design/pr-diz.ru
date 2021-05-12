<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Смена статуса
if(null !== filter_input(INPUT_POST, 'change_status_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $sql = "update calculation set status_id=$status_id where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/calculation/calculation.php'. BuildQuery('id', $id));
    }
}

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.brand_name, c.thickness, c.lamination1_brand_name, c.lamination1_thickness, c.lamination2_brand_name, c.lamination2_thickness, c.width, c.weight, c.diameter, c.status_id, "
        . "cs.name status, cs.colour, cs.colour2, cs.image, cu.name customer, wt.name work_type "
        . "from calculation c "
        . "inner join calculation_status cs on c.status_id = cs.id "
        . "inner join customer cu on c.customer_id = cu.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$customer_id = $row['customer_id'];
$name = $row['name'];
$work_type_id = $row['work_type_id'];
$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$width = $row['width'];
$weight = $row['weight'];
$diameter = $row['diameter'];
$status_id = $row['status_id'];

$status = $row['status'];
$colour = $row['colour'];
$colour2 = $row['colour2'];
$image = $row['image'];
$customer = $row['customer'];
$work_type = $row['work_type'];
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
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="backlink">
                <a href="<?=APPLICATION ?>/calculation/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-6" id="left_side">
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                    <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></h2>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; background-color: <?=$colour2 ?>; border: solid 2px <?=$colour ?>; color: <?=$colour ?>">
                        <?=$image ?>&nbsp;&nbsp;&nbsp;<?=$status ?>
                    </div>
                    <table>
                        <tr><th class="pr-5">Заказчик</th><td><?=$customer ?></td></tr>
                        <tr><th class="pr-5">Тип работы</th><td><?=$work_type ?></td></tr>
                        <tr><th class="pr-5">Марка пленки</th><td><?=$brand_name ?></td></tr>
                        <tr><th class="pr-5">Толщина</th><td><?= number_format($thickness, 0, ",", " ") ?> мкм</td></tr>
                        <tr><th class="pr-5">Ширина</th><td><?= number_format($width, 0, ",", " ") ?></td> мм</tr>
                        <tr><th class="pr-5">Вес нетто</th><td><?= number_format($weight, 0, ",", " ") ?></td></tr>
                        <tr><th class="pr-5">Диаметр намотки</th><td><?= number_format($diameter, 0, ",", " ") ?> мм &nbsp;&nbsp;&nbsp;Примерно 2020 метров</td></tr>
                    </table>
                </div>
                <!-- Правая половина -->
                <div class="col-3">
                    <!-- Расчёт -->
                    <div id="calculation">
                        <h1>Расчет</h1>
                        <div class="mt-3 mb-1">Наценка</div>
                        <div class="font-weight-bold mt-1 mb-1" style="font-size: large;">10%</div>
                        <div class="mt-3 mb-1">Себестоимость</div>
                        <div class="font-weight-bold mt-1 mb-1" style="font-size: large;"><?= number_format(1200000, 0, ",", " ") ?> руб.</div>
                        <div class="mt-3 mb-1">Отгрузочная стоимость</div>
                        <div class="font-weight-bold mt-1 mb-1" style="font-size: large;"><?= number_format(800000, 0, ",", " ") ?> руб.</div>
                        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
                        <div id="costs" class="d-none">
                            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                            <div class="mt-3 mb-1">Отходы</div>
                            <div class="font-weight-bold mt-1 mb-1" style="font-size: large;"><?= number_format(200280, 0, ",", " ") ?> руб.&nbsp;&nbsp;&nbsp;<?= number_format(24.5, 1, ",", " ") ?> кг.</div>
                            <div class="mt-3 mb-1">Клей</div>
                            <div class="font-weight-bold mt-1 mb-3" style="font-size: large;"><?= number_format(800000, 0, ",", " ") ?> руб.</div>
                        </div>
                        <form method="post">
                            <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <input type="hidden" id="change_status_submit" name="change_status_submit" />
                            <button type="submit" id="status_id" name="status_id" value="3" class="btn btn-outline-dark w-75 mt-3">Ждет одобрения</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            // Показ расходов
            function ShowCosts() {
                $("#costs").removeClass("d-none");
                $("#show_costs").addClass("d-none");
            }
            
            // Скрытие расходов
            function HideCosts() {
                $("#costs").addClass("d-none");
                $("#show_costs").removeClass("d-none");
            }
        </script>
    </body>
</html>