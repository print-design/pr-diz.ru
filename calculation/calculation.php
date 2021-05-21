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

$sql = "select c.date, c.customer_id, c.name name, c.work_type_id, c.brand_name, c.thickness, c.lamination1_brand_name, c.lamination1_thickness, c.lamination2_brand_name, c.lamination2_thickness, c.width, c.weight, c.streamscount, c.status_id, "
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
$weight = $row['weight'];
$width = $row['width'];
$streamscount = $row['streamscount'];
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
                <a href="<?=APPLICATION ?>/calculation/">Назад</a>
            </div>
            <div class="row">
                <!-- Левая половина -->
                <div class="col-6" id="left_side">
                    <h1 style="font-size: 32px; font-weight: 600;"><?= htmlentities($name) ?></h1>
                    <h2 style="font-size: 26px;">№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?></h2>
                    <div style="width: 100%; padding: 12px; margin-top: 40px; margin-bottom: 40px; border-radius: 10px; font-weight: bold; text-align: center; background-color: <?=$colour2 ?>; border: solid 2px <?=$colour ?>; color: <?=$colour ?>">
                        <?=$image ?>&nbsp;&nbsp;&nbsp;<?=$status ?>
                    </div>
                    <table>
                        <tr><th class="pr-5">Заказчик</th><td><?=$customer ?></td></tr>
                        <tr><th class="pr-5">Тип работы</th><td><?=$work_type ?></td></tr>
                        <tr><th class="pr-5">Марка пленки</th><td><?=$brand_name ?></td></tr>
                        <tr><th class="pr-5">Толщина</th><td><?= number_format($thickness, 0, ",", " ") ?> мкм</td></tr>
                        <tr><th class="pr-5">Вес нетто</th><td><?= number_format($weight, 0, ",", " ") ?></td></tr>
                        <?php
                        if(!empty($width)):
                        ?>
                        <tr><th class="pr-5">Ширина</th><td><?= number_format($width, 0, ",", " ") ?> мм</td></tr>
                        <?php
                        endif;
                        if(!empty($streamscount)):
                        ?>
                        <tr><th class="pr-5">Количество ручьев</th><td><?= number_format($streamscount, 0, ",", " ") ?></td></tr>
                        <?php
                        endif;
                        ?>
                    </table>
                    <?php if($status_id == 3): ?>
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" id="change_status_submit" name="change_status_submit" />
                        <button type="submit" id="status_id" name="status_id" value="5" class="btn btn-outline-dark mt-5 mr-2 pl-5 pr-5">Отклонить</button>
                        <button type="submit" id="status_id" name="status_id" value="4" class="btn btn-dark mt-5 mr-2 pl-5 pr-5">Одобрить</button>
                    </form>
                    <?php endif; ?>
                </div>
                <!-- Правая половина -->
                <div class="col-6 col-lg-3">
                    <!-- Расчёт -->
                    <div id="calculation">
                        <h1>Расчет</h1>
                        <div class="d-table w-100">
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    <div style="font-size: x-small;">Наценка</div>
                                    10%
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3" style="color: gray; border: solid 1px gray; border-radius: 10px;">
                                    <div style="font-size: x-small;">Курс евро</div>
                                    93
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    Себестоимость
                                    <div class="font-weight-bold" style="font-size: large;">1 200 000 руб.</div>
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3">
                                    За 1 кг
                                    <div class="font-weight-bold" style="font-size: large;">765 руб.</div>
                                </div>
                            </div>
                            <div class="d-table-row">
                                <div class="d-table-cell pb-2 pt-2">
                                    Отгрузочная стоимость
                                    <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
                                </div>
                                <div class="d-table-cell pb-2 pt-2 pl-3">
                                    За 1 кг
                                    <div class="font-weight-bold" style="font-size: large;">978 руб.</div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
                        ?>
                        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
                        <div id="costs" class="d-none">
                            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
                            <div class="d-table w-100">
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Отходы
                                        <div class="font-weight-bold" style="font-size: large;">1 280 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">4,5 кг.</div>
                                    </div>
                                </div>
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Клей
                                        <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">1,0 кг.</div>
                                    </div>
                                </div>
                                <div class="d-table-row">
                                    <div class="d-table-cell pb-2 pt-2">
                                        Работа ламинатора
                                        <div class="font-weight-bold">230 руб.</div>
                                    </div>
                                    <div class="d-table-cell pb-2 pt-2 pl-3">
                                        <br />
                                        <div class="font-weight-bold" style="font-size: large;">1,5 ч.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        endif;
                        ?>
                        <form method="post">
                            <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                            <input type="hidden" id="change_status_submit" name="change_status_submit" />
                            <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark mt-3">Сделать КП</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
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