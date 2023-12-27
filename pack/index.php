<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
                    . "where c.status_id = ".ORDER_STATUS_PACK_READY." and e.work_id = ".WORK_CUTTING;
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                $pager_total_count = $row[0];
            }
            ?>
            <h1>Готовые резки&nbsp;&nbsp;<span style="font-size: smaller; color: #999999;"><?=$pager_total_count ?></span></h1>
            <table class="table typography">
                <tr>
                    <th>Дата<i class='fas fa-arrow-down ml-2' style="color: #BBBBBB;"></i></th>
                    <th>Резчик</th>
                    <th>№</th>
                    <th>Заказ</th>
                    <th>Метраж</th>
                    <th>Масса</th>
                    <th>Менеджер</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            <?php
            $sql = "select c.id, ct.time, c.customer_id, e.machine_id, cus.name as customer, c.name as calculation, cr.length_pure_1, concat(u.last_name, ' ', left(first_name, 1), '.') as manager, c.status_id, "
                    . "(select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) as weight, "
                    . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cutted, "
                    . "(select concat(last_name, ' ', left(first_name, 1), '.') from plan_employee where id = (select employee1_id from plan_workshift1 where work_id = ".WORK_CUTTING." and machine_id = e.machine_id and date = date(ct.time) and shift = 'day')) as day_cutter, "
                    . "(select concat(last_name, ' ', left(first_name, 1), '.') from plan_employee where id = (select employee1_id from plan_workshift1 where work_id = ".WORK_CUTTING." and machine_id = e.machine_id and date = date(ct.time) and shift = 'night')) as night_cutter, "
                    . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                    . "from calculation c "
                    . "inner join plan_edition e on e.calculation_id = c.id "
                    . "inner join customer cus on c.customer_id = cus.id "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "inner join user u on c.manager_id = u.id "
                    . "inner join (select calculation_id, max(timestamp) as time from calculation_take group by calculation_id) ct on ct.calculation_id = c.id "
                    . "where c.status_id = ".ORDER_STATUS_PACK_READY." and e.work_id = ".WORK_CUTTING
                    . " order by ct.time asc";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $row['time']);
            $hour = $datetime->format('G');
            ?>
                <tr>
                    <td><?=$datetime->format('d.m') ?><br /><span style="font-size: smaller;"><?=$datetime->format('H:i') ?></span></td>
                    <td><?=($hour < 8 || $hour > 19) ? $row['night_cutter'] : $row['day_cutter'] ?></td>
                    <td><?=$row['customer_id'].'-'.$row['num_for_customer'] ?></td>
                    <td><?=$row['calculation'] ?><br /><span style="font-size: smaller;"><?=$row['customer'] ?></span></td>
                    <td><?= DisplayNumber(floatval($row['length_pure_1']), 0) ?> м</td>
                    <td><?= DisplayNumber(floatval($row['weight']), 1) ?> кг</td>
                    <td><?=$row['manager'] ?></td>
                    <td>
                        <i class="fas fa-circle" style="color: <?=ORDER_STATUS_COLORS[$row['status_id']] ?>;"></i>&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$row['status_id']] ?>
                        <?php
                        if($row['status_id'] == ORDER_STATUS_CUTTING || $row['status_id'] == ORDER_STATUS_CUTTED || $row['status_id'] == ORDER_STATUS_PACK_READY) {
                            echo "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".DisplayNumber(floatval($row['length_cutted']), 0)." м из ".DisplayNumber(floatval($row['length_pure_1']), 0);
                        }
                        ?>
                    </td>
                    <td>
                        <a href="start.php?id=<?=$row['id'] ?>" class="btn btn-light" style="width: 150px;">Приступить</a>
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