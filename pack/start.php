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
$last_name = '';
$first_name = '';

$techmap_date = '';
$length_cut = '';
$num_for_customer = '';

$sql = "select c.id, c.date, c.name, c.unit, c.work_type_id, c.status_id, c.customer_id, "
        . "cus.name customer, u.last_name, u.first_name, tm.date techmap_date, "
        . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
        . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
        . "from calculation c "
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
            <a class="btn btn-light backlink" href="<?=APPLICATION ?>/pack/">К списку</a>
            <h1><?=$row['name'] ?></h1>
            <div class="name"><?=$row['customer'] ?></div>
            <div class="subtitle">№<?=$customer_id.'-'.$num_for_customer ?> от  <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></div>
            <div style="background-color: lightgray; padding-left: 10px; padding-right: 15px; padding-top: 2px; border-radius: 10px; margin-top: 15px; margin-bottom: 15px; display: inline-block;">
                <i class="fas fa-circle" style="font-size: x-small; vertical-align: bottom; padding-bottom: 7px; color: <?=ORDER_STATUS_COLORS[$status_id] ?>;">&nbsp;&nbsp;</i><?=ORDER_STATUS_NAMES[$status_id].' '.DisplayNumber(floatval($length_cut), 0)." м из ".DisplayNumber(floatval($calculation->length_pure_1), 0) ?>
            </div>
            <?php
            include '../include/footer.php';
            ?>
        </div>
    </body>
</html>