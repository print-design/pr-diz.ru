<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include 'style.php';
        ?>
    </head>
    <body>
        <?php
        include 'header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <h1>Хранение плёнки на складе</h1>
            <table class="table table-hover">
                <tr>
                    <th style="width: 13%;">Номер плёнки</th>
                    <th style="width: 13%;">Нач. дата</th>
                    <th style="width: 13%;">Откуда</th>
                    <th style="width: 13%;">Кон. дата</th>
                    <th style="width: 13%;">Куда</th>
                    <th style="width: 22%;">Менеджер</th>
                    <th style="width: 13%;">Хранился</th>
                </tr>
                <?php
                $sql = "select count(id) from roll";
                $fetcher = new Fetcher($sql);
                if($roll = $fetcher->Fetch()) {
                    $pager_total_count = $roll[0];
                }
                
                $sql = "select concat('Р', r.id) nomer, DATE_FORMAT(r.date, '%d.%m.%Y') admission, r.cut_wind_id, rsh_max.max_id, DATE_FORMAT(rsh.date, '%d.%m.%Y') utilization, datediff(rsh.date, r.date) datediff, rsh.status_id, r.comment "
                        . "from roll r "
                        . "left join (select roll_id, max(id) max_id from roll_status_history group by roll_id) rsh_max on rsh_max.roll_id = r.id "
                        . "inner join roll_status_history rsh on rsh_max.max_id = rsh.id "
                        . "union "
                        . "select concat('П', p.id, 'Р', pr.ordinal), DATE_FORMAT(p.date, '%d.%m.%Y') admission, null cut_wind_id, prsh_max.max_id, DATE_FORMAT(prsh.date, '%d.%m.%Y') utilization, datediff(prsh.date, p.date) datediff, prsh.status_id, p.comment "
                        . "from pallet_roll pr inner join pallet p on pr.pallet_id = p.id "
                        . "left join (select pallet_roll_id, max(id) max_id from pallet_roll_status_history group by pallet_roll_id) prsh_max on prsh_max.pallet_roll_id = pr.id "
                        . "inner join pallet_roll_status_history prsh on prsh_max.max_id = prsh.id "
                        . "order by datediff desc "
                        . "limit $pager_skip, $pager_take";
                $fetcher = new Fetcher($sql);
                while($row = $fetcher->Fetch()):
                $in_text = "Поступление";
                $in_color = "lightgray";
                if(!empty($row['cut_wind_id'])) {
                    $in_text = "Из раскроя";
                    $in_color = "lightgreen";
                }
                $out_text = "Хранится";
                $out_color = "lightgray";
                if($row['status_id'] == 3) {
                    $out_text = "Раскроили";
                    $out_color = "lightgreen";
                }
                elseif ($row['status_id'] == 2) {
                    $out_text = "Сработали";
                    $out_color = "pink";
                }
                ?>
                <tr>
                    <td><?=$row['nomer'] ?></td>
                    <td><?=$row['admission'] ?></td>
                    <td style="background-color: <?=$in_color ?>"><?=$in_text ?></td>
                    <td><?=$row['utilization'] ?></td>
                    <td style="background-color: <?=$out_color ?>"><?=$out_text ?></td>
                    <td><?= trim($row['comment']) ?></td>
                    <td><?=$row['datediff'] ?> дней</td>
                </tr>
                <?php
                endwhile;
                ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
    </body>
</html>