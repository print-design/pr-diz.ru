<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Статус
$status_id = ORDER_STATUS_PACK_READY;

if(null !== filter_input(INPUT_GET, 'status_id')) {
    $status_id = filter_input(INPUT_GET, 'status_id');
}

// Ошибки при расчётах (если есть)
if(null !== filter_input(INPUT_GET, 'error_message')) {
    $error_message = filter_input(INPUT_GET, 'error_message');
}

// Отображение статуса заказа
function ShowOrderStatus($status_id, $length_cut, $weight_cut, $quantity_sum, $quantity, $unit, $raport, $length, $gap_raport, $cut_remove_cause) {
    include '../include/order_status_index.php';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }
            
            table.typography tr th {
                color: #68676C;
                border-top: 0;
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
            
            include '../include/pager_top.php';
            $rowcounter = 0;
            
            // Общее количество работ для установления количества страниц в постраничном выводе
            $sql = "select count(c.id) "
                    . "from calculation c "
                    . "inner join plan_edition e on e.calculation_id = c.id "
                    . "inner join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id "
                    . "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) = $status_id and e.work_id = ".WORK_CUTTING;
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <h1>Готовые резки&nbsp;&nbsp;<span style="font-size: smaller; color: #999999;"><?=$pager_total_count ?></span></h1>
            <table class="table typography">
                <tr>
                    <th class="text-nowrap">Дата<i class='fas fa-arrow-down ml-2' style="color: #BBBBBB;"></i></th>
                    <th>№</th>
                    <th>Заказ</th>
                    <th>Метраж</th>
                    <th>Масса</th>
                    <th>Менеджер</th>
                    <th>Статус</th>
                    <th>Комментарий</th>
                    <th></th>
                </tr>
            <?php
            $sql = "select distinct c.id, ct.time, c.customer_id, e.machine_id, e.comment, pc.comment as continuation_comment, cus.name as customer, c.name as calculation, cr.length_pure_1, concat(u.last_name, ' ', left(first_name, 1), '.') as manager, c.raport, c.length, c.cut_remove_cause, c.unit, c.quantity, "
                    . "(select sum(quantity) from calculation_quantity where calculation_id = c.id) quantity_sum, "
                    . "(select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) as weight, "
                    . "(select gap_raport from norm_gap where date <= c.date order by id desc limit 1) as gap_raport, "
                    . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                    . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                    . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                    . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                    . "(select status_id from calculation_status_history where calculation_id = c.id order by date limit 1) status_id, "
                    . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                    . "from calculation c "
                    . "inner join plan_edition e on e.calculation_id = c.id "
                    . "inner join customer cus on c.customer_id = cus.id "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "inner join user u on c.manager_id = u.id "
                    . "inner join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id "
                    . "left join plan_continuation pc on pc.plan_edition_id = e.id "
                    . "where (select status_id from calculation_status_history where calculation_id = c.id order by date desc limit 1) = $status_id and e.work_id = ".WORK_CUTTING
                    . " order by ct.time desc limit $pager_skip, $pager_take";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $row['time']);
            ?>
                <tr>
                    <td><?=$datetime->format('d.m') ?><br /><span style="font-size: smaller;"><?=$datetime->format('H:i') ?></span></td>
                    <td><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                    <td><?=$row['calculation'] ?><br /><span style="font-size: smaller;"><?=$row['customer'] ?></span></td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['length_pure_1']), 0) ?> м</td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['weight']), 1) ?> кг</td>
                    <td class="text-nowrap"><?=$row['manager'] ?></td>
                    <td><?php ShowOrderStatus($row['status_id'], $row['length_cut'], $row['weight_cut'], $row['quantity_sum'], $row['quantity'], $row['unit'], $row['raport'], $row['length'], $row['gap_raport'], $row['cut_remove_cause']); ?></td>
                    <td><?= trim($row['comment'].' '.$row['continuation_comment'], ' ') ?></td>
                    <td>
                        <a href="details.php<?= BuildQuery('id', $row['id']) ?>" class="btn btn-light" style="width: 150px;">Приступить</a>
                    </td>
                </tr>
            <?php
            endwhile;
            ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>