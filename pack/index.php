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
function ShowOrderStatus($status_id, $length_cut, $weight_cut, $quantity_sum, $quantity, $unit, $raport, $length, $gap_raport, $status_comment) {
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
        include '../include/status_track.php';
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
                <div class="text-nowrap">
                    <h1>Готовые резки&nbsp;&nbsp;<span style="font-size: smaller; color: #999999;"><?=$pager_total_count ?></span></h1>
                    <?php
                    // Фильтр
                    $filter = '';
                    
                    $find = trim(filter_input(INPUT_GET, 'find') ?? '');
                    if(!empty($find)) {
                        $find_substrings = explode('-', $find);
                        if(count($find_substrings) != 2 || intval($find_substrings[0]) == 0 || intval($find_substrings[1]) == 0) {
                            $filter .= " and false";
                        }
                        else {
                            $filter .= " and c.customer_id = ". intval($find_substrings[0])." and num_for_customers.num_for_customer = ". intval($find_substrings[1]);
                        }
                    }
                    
                    // Общее количество работ для установления количества страниц в постраничном выводе
                    $sql = "select count(c.id) "
                            . "from calculation c "
                            . "inner join plan_edition e on e.calculation_id = c.id "
                            . "left join (select calculation_id, status_id from calculation_status_history where id in (select max(id) from calculation_status_history group by calculation_id)) cshmax on cshmax.calculation_id = c.id "
                            . "left join (select c1.id as calculation_id, (select count(id) from calculation where customer_id = c1.customer_id and id < c1.id) as num_for_customer from calculation c1) num_for_customers on num_for_customers.calculation_id = c.id "
                            . "where cshmax.status_id = $status_id and e.work_id = ".WORK_CUTTING.$filter;
                    $fetcher = new Fetcher($sql);
                    
                    if($row = $fetcher->Fetch()) {
                        $pager_total_count = $row[0];
                    }
                    ?>
                </div>
                <div></div>
            </div>            
            <table class="table typography">
                <tr>
                    <th class="text-nowrap">Дата<i class='fas fa-arrow-down ml-2' style="color: #BBBBBB;"></i></th>
                    <?php if($status_id == ORDER_STATUS_SHIPPED): ?>
                    <th>Дата отгрузки</th>
                    <?php endif; ?>
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
            $sql = "select distinct c.id, ct.time, c.customer_id, num_for_customers.num_for_customer, e.machine_id, e.comment, pc.comment as continuation_comment, cus.name as customer, c.name as calculation, cr.length_pure_1, concat(u.last_name, ' ', left(first_name, 1), '.') as manager, c.raport, c.length, c.unit, c.quantity, "
                    . ($status_id == ORDER_STATUS_SHIPPED ? "ss.shipping_date, " : "")
                    . "cq.quantity_sum, cgr.gap_raport, cshmax.status_id, cshmax.status_comment, ifnull(ctw.weight, 0) + ifnull(csw.weight, 0) weight_cut, ifnull(ctw.length, 0) + ifnull(csw.length, 0) length_cut "
                    . "from calculation c "
                    . "inner join plan_edition e on e.calculation_id = c.id "
                    . "inner join customer cus on c.customer_id = cus.id "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "inner join user u on c.manager_id = u.id "
                    . "inner join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id "
                    . ($status_id == ORDER_STATUS_SHIPPED ? "left join (select calculation_id, max(date) as shipping_date from calculation_status_history where status_id = ". ORDER_STATUS_SHIPPED." group by calculation_id) ss on ss.calculation_id = c.id " : "")
                    . "left join plan_continuation pc on pc.plan_edition_id = e.id "
                    . "left join (select calculation_id, sum(quantity) as quantity_sum from calculation_quantity group by calculation_id) cq on cq.calculation_id = c.id "
                    . "left join (select ct1.calculation_id, sum(cts1.weight) as weight, sum(cts1.length) as length from calculation_take ct1 inner join calculation_take_stream cts1 on cts1.calculation_take_id = ct1.id group by ct1.calculation_id) ctw on ctw.calculation_id = c.id "
                    . "left join (select cs1.calculation_id, sum(cnts1.weight) as weight, sum(cnts1.length) as length from calculation_stream cs1 inner join calculation_not_take_stream cnts1 on cnts1.calculation_stream_id = cs1.id group by cs1.calculation_id) csw on csw.calculation_id = c.id "
                    . "left join (select c1.id as calculation_id, (select gap_raport from norm_gap where date <= c1.date order by id desc limit 1) as gap_raport from calculation c1 group by c1.id) cgr on cgr.calculation_id = c.id "
                    . "left join (select calculation_id, status_id, comment as status_comment from calculation_status_history where id in (select max(id) from calculation_status_history group by calculation_id)) cshmax on cshmax.calculation_id = c.id "
                    . "left join (select c1.id as calculation_id, (select count(id) from calculation where customer_id = c1.customer_id and id <= c1.id) as num_for_customer from calculation c1) num_for_customers on num_for_customers.calculation_id = c.id "
                    . "where cshmax.status_id = $status_id and e.work_id = ".WORK_CUTTING.$filter
                    . " order by ct.time desc limit $pager_skip, $pager_take";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $row['time']);
            ?>
                <tr>
                    <td><?=$datetime->format('d.m') ?><br /><span style="font-size: smaller;"><?=$datetime->format('H:i') ?></span></td>
                    <?php
                    if($status_id == ORDER_STATUS_SHIPPED):
                        $shipping_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['shipping_date']);
                    ?>
                    <td><?=$shipping_date->format('d.m') ?><br /><span style="font-size: smaller;"><?=$shipping_date->format("H:i") ?></span></td>
                    <?php endif; ?>
                    <td class="text-nowrap"><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                    <td><?=$row['calculation'] ?><br /><span style="font-size: smaller;"><?=$row['customer'] ?></span></td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['length_pure_1']), 0) ?> м</td>
                    <td class="text-nowrap"><?= DisplayNumber(floatval($row['weight_cut']), 1) ?> кг</td>
                    <td class="text-nowrap"><?=$row['manager'] ?></td>
                    <td data-toggle="modal" data-target="#status_track" style="cursor: pointer;" onclick="javascript:  StatusTrack(<?=$row['id'] ?>);"><?php ShowOrderStatus($row['status_id'], $row['length_cut'], $row['weight_cut'], $row['quantity_sum'], $row['quantity'], $row['unit'], $row['raport'], $row['length'], $row['gap_raport'], $row['status_comment']); ?></td>
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