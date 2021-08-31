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
            <?php
            $sql = "select id, rational_cut_id from rational_cut_stage order by id desc";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()):
                $id = $row['id'];
                $cut_id = $row['rational_cut_id'];
            ?>
            <p><a href="stage.php<?= BuildQuery('id', $id) ?>">Раскрой <?=$cut_id ?></a></p>
            <?php endwhile; ?>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>