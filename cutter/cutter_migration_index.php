<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
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
        include '../include/header_sklad.php';
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Марка</th>
                                <th>Дата</th>
                                <th>Исходник</th>
                                <th>Длина намотки</th>
                                <th>Радиус намотки</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "select c.date, cs.id cutting_source_id, '' length, '' radius, 1 level "
                                    . "from cutting_source cs "
                                    . "inner join cutting c on cs.cutting_id = c.id "
                                    . "union "
                                    . "select c.date, cw.cutting_source_id, cw.length, cw.radius, 0 level "
                                    . "from cutting_wind cw "
                                    . "inner join cutting_source cs on cw.cutting_source_id = cs.id "
                                    . "inner join cutting c on cs.cutting_id = c.id "
                                    . "order by date asc, cutting_source_id asc, level desc";
                            $fetcher = new Fetcher($sql);
                            while($row = $fetcher->Fetch()):
                            ?>
                            <tr>
                                <td></td>
                                <td><?=$row['date'] ?></td>
                                <td><?=$row['cutting_source_id'] ?></td>
                                <td><?=$row['length'] ?></td>
                                <td><?=$row['radius'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>