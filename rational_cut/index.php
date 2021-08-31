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
        include '../include/header_analytics.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1 class="mb-4">Рациональный раскрой</h1>
                </div>
                <div class="p-1">
                    <a href="new.php" class="btn btn-outline-dark"><i class="fas fa-plus"></i>&nbsp;Новый раскрой</a>
                </div>
            </div>
            <table class="table table-hover">
                <?php
                $sql = "select rcs.id, DATE_FORMAT(rc.date, '%d.%m.%Y') date,rc.brand_name, rc.thickness "
                        . "from rational_cut_stage rcs "
                        . "inner join rational_cut rc on rcs.rational_cut_id = rc.id "
                        . "order by rcs.id desc";
                $fetcher = new Fetcher($sql);
                while ($row = $fetcher->Fetch()):
                    ?>
                <tr>
                    <td><?=$row['date'] ?></td>
                    <td><?=$row['brand_name'] ?></td>
                    <td><?=$row['thickness'] ?> мм</td>
                    <td><a href="stage.php<?= BuildQuery('id', $row['id']) ?>">Перейти</a></td>
                </tr>
                    <?php endwhile; ?>
            </table>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>