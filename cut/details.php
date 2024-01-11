<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';

// Авторизация
if(!IsInRole(CUTTER_USERS) && !IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id или машина, направляем к списку заданий
$id = filter_input(INPUT_GET, 'id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
if(empty($id) || empty($machine_id)) {
    header('Location: '.APPLICATION.'/cut/');
}

// Расчёт
$calculation = CalculationBase::Create($id);

// Начало резки
if(null !== filter_input(INPUT_POST, 'start_cut_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    
    $sql = "update calculation set status_id = ".ORDER_STATUS_CUT_PRILADKA." where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        header('Location: priladka.php?id='.$id.(empty($machine_id) ? '' : '&machine_id='.$machine_id));
    }
}

// Получение объекта
$date = '';
$name = '';
$unit = '';
$work_type_id = '';
$status_id = '';

$length = '';
$customer_id = '';
$customer = '';
$length_pure_1 = '';
$last_name = '';
$first_name = '';

$techmap_date = '';
$side = '';
$winding = '';
$winding_unit = '';
$spool = '';
$labels = '';
$package = '';

$num_for_customer = '';

$sql = "select c.date, c.name, c.unit, c.work_type_id, c.status_id, c.length, c.customer_id, cus.name customer, "
        . "cr.length_pure_1, u.last_name, u.first_name, "
        . "tm.date techmap_date, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "inner join techmap tm on tm.calculation_id = c.id "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
    $name = $row['name'];
    $unit = $row['unit'];
    $work_type_id = $row['work_type_id'];
    $status_id = $row['status_id'];
    
    $length = $row['length'];
    $customer_id = $row['customer_id'];
    $customer = $row['customer'];
    $length_pure_1 = $row['length_pure_1'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    
    $techmap_date = $row['techmap_date'];
    $side = $row['side'];
    $winding = $row['winding'];
    $winding_unit = $row['winding_unit'];
    $spool = $row['spool'];
    $labels = $row['labels'];
    $package = $row['package'];
    
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
            
            .cutter_info {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 20px;
                padding-top: 5px;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_cut.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-4">
                    <a class="btn btn-light backlink" href="<?= APPLICATION.'/cut/?machine_id='.$machine_id ?>">К списку резок</a>
                    <h1><?= $name ?></h1>
                    <div class="name"><?=$customer ?></div>
                    <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
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
                    <div style="position: absolute; left: 0px; bottom: 0px; margin: 15px;">
                        <form method="post">
                            <input type="hidden" name="id" value="<?=$id ?>" />
                            <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                            <button type="submit" name="start_cut_submit" class="btn btn-dark" style="width: 175px;">Начать работу</button>
                        </form>
                    </div>
                </div>
                <div class="col-4" style="padding-left: 20px;"></div>
                <div class="col-4" style="padding-left: 20px;">
                    <?php include './_cut_right.php'; ?>
                </div>
            </div>
        </div>            
        <?php
        include '../include/footer.php';
        include '../include/footer_cut.php';
        ?>
    </body>
</html>