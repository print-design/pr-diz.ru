<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/pack/');
}

// Расчёт
$calculation = Calculation::Create($id);

// Получение объекта
$date = '';
$name = '';
$unit = '';
$work_type_id = '';
$status_id = '';

$customer_id = '';
$customer = '';
$length_pure_1 = '';
$last_name = '';
$first_name = '';

$techmap_date = '';
$length_cut = '';
$num_for_customer = '';

$sql = "select c.id, c.date, c.name, c.unit, c.work_type_id, c.status_id, c.customer_id, "
        . "cus.name customer, cr.length_pure_1, u.last_name, u.first_name, tm.date techmap_date, "
        . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $unit = $row['unit'];
    $work_type_id = $row['work_type_id'];
    $status_id = $row['status_id'];
    
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    $length_pure_1 = $row['length_pure_1'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    
    $techmap_date = $row['techmap_date'];
    $length_cut = $row['length_cut'];
    $num_for_customer = $row['num_for_customer'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            h1 {
                font-size: 33px;
            }
            
            h2, .name {
                font-size: 26px;
                font-weight: bold;
                line-height: 45px;
            }
            
            h3 {
                font-size: 20px;
            }
            
            .subtitle {
                font-weight: bold;
                font-size: 20px;
                line-height: 40px
            }
            
            table {
                width: 100%;
            }
            
            tr {
                border-bottom: solid 1px #e3e3e3;
            }
            
            th {
                white-space: nowrap;
                padding-right: 30px;
                vertical-align: top;
            }
            
            td {
                line-height: 22px;
            }
            
            tr td:nth-child(2) {
                text-align: right;
                padding-left: 10px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_pack.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-4">
                    <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pack/">К списку</a>
                    <h1><?=$row['name'] ?></h1>
                    <div class="name"><?=$row['customer'] ?></div>
                    <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
                    <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; margin-top: 15px; margin-bottom: 15px; display: inline-block;">
                        <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$status_id].' '.DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval($calculation->length_pure_1), 0) ?>
                    </div>
                    <table>
                        <tr>
                            <td>Объём заказа</td>
                            <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($length_pure_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Менеджер</td>
                            <td><?=$last_name.' '.$first_name ?></td>
                        </tr>
                        <tr>
                            <td>Тип работы</td>
                            <td><?=WORK_TYPE_NAMES[$work_type_id] ?></td>
                        </tr>
                        <tr>
                            <td>Карта составлена</td>
                            <td><?= DateTime::createFromFormat('Y-m-d H:i:s', $techmap_date)->format('d.m.Y H:i') ?></td>
                        </tr>
                    </table>
                    <div class="d-flex justify-content-xl-start mt-4">
                        <div><a href="pack.php?id=<?= filter_input(INPUT_GET, 'id') ?>" class="btn btn-dark pl-4 pr-4 mr-4"><i class="fas fa-check mr-2"></i>Подтвердить</a></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4 mr-4"><i class="fas fa-plus mr-2"></i>Добавить рулон не из съёма</button></div>
                        <div><button type="button" class="btn btn-light pl-4 pr-4"><i class="fas fa-download mr-2"></i>Выгрузка</button></div>
                    </div>
                </div>
            </div>
            <?php
            include '../include/footer.php';
            ?>
        </div>
    </body>
</html>