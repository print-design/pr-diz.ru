<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <style>
        th {
            font-weight: bold!important;
            font-size: large!important;
        }
    </style>
    <body>
        <h1>Резка миграция</h1>
        <table class="table">
            <tr>
                <th>id</th>
                <th>supplier_id</th>
                <th>film_variation_id</th>
                <th>width</th>
                <th>length</th>
                <th>net_weight</th>
                <th>cell</th>
                <th>comment</th>
                <th>date</th>
                <th>storekeeper_id</th>
                <th>cut_wind_id</th>
                <th>cutting_wind_id</th>
            </tr>
            <?php
            $sql = "select id, supplier_id, film_variation_id, width, length, net_weight, cell, comment, date, storekeeper_id, cut_wind_id, cutting_wind_id "
                    . "from roll limit 100";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            ?>
            <tr>
                <td><?=$row['id'] ?></td>
                <td><?=$row['supplier_id'] ?></td>
                <td><?=$row['film_variation_id'] ?></td>
                <td><?=$row['width'] ?></td>
                <td><?=$row['length'] ?></td>
                <td><?=$row['net_weight'] ?></td>
                <td><?=$row['cell'] ?></td>
                <td><?=$row['comment'] ?></td>
                <td><?=$row['date'] ?></td>
                <td><?=$row['storekeeper_id'] ?></td>
                <td><?=$row['cut_wind_id'] ?></td>
                <td><?=$row['cutting_wind_id'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>