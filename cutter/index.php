<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cutter/material.php">Приступить к раскрою</a>
            <?php /*
            $sql = "select c.id, cw.cut_wind_id "
                    . "from cut c inner join (select max(id) cut_wind_id, cut_id from cut_wind group by cut_id) cw on cw.cut_id = c.id ";
                    //. "where c.id not in (select cut_id from cut_source)";
            echo $sql;
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            ?>
            <p>EXIST</p>
            <?php else: ?>
            <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cutter/material.php">Приступить к раскрою</a>
            <?php endif; */ ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>