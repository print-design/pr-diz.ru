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
        ?>
        <div class="container-fluid">
            <h1>Хранение плёнки на складе</h1>
            <table class="table table-hover">
                <tr>
                    <th>Номер плёнки</th>
                    <th>Нач. дата</th>
                    <th>Откуда</th>
                    <th>Кон. дата</th>
                    <th>Куда</th>
                    <th>Хранился</th>
                </tr>
                <?php
                $sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') admission, r.cut_wind_id, rsh_max.max_id, DATE_FORMAT(rsh.date, '%d.%m.%Y') utilization, datediff(rsh.date, r.date) datediff, rsh.status_id "
                        . "from roll r left join (select roll_id, max(id) max_id from roll_status_history group by roll_id) rsh_max on rsh_max.roll_id = r.id "
                        . "inner join roll_status_history rsh on rsh_max.max_id = rsh.id order by datediff desc "
                        . "limit 1000";
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
                    <td><?='Р'.$row['id'] ?></td>
                    <td><?=$row['admission'] ?></td>
                    <td style="background-color: <?=$in_color ?>"><?=$in_text ?></td>
                    <td><?=$row['utilization'] ?></td>
                    <td style="background-color: <?=$out_color ?>"><?=$out_text ?></td>
                    <td><?=$row['datediff'] ?> дней</td>
                </tr>
                <?php
                endwhile;
                ?>
            </table>
        </div>
    </body>
</html>