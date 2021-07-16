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
            <?php
            $sql = "select c.id, s.name supplier, fb.name film_brand, c.thickness, c.width, cw.cut_wind_id, cs.cut_streams_count "
                    . "from cut c "
                    . "inner join supplier s on c.supplier_id = s.id "
                    . "inner join film_brand fb on c.film_brand_id = fb.id "
                    . "inner join (select max(id) cut_wind_id, cut_id from cut_wind where id in (select cut_wind_id from roll) group by cut_id) cw on cw.cut_id = c.id "
                    . "inner join (select count(id) cut_streams_count, cut_id from cut_stream group by cut_id) cs on cs.cut_id = c.id "
                    . "where c.id not in (select cut_id from cut_source) "
                    . "order by c.id asc";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            ?>
            <h1>У вас есть незакрытая заявка</h1>
            <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cutter/continue.php">Посмотреть</a>
            <?php
            else:
                header('Location: '.APPLICATION.'/cutter/');
            endif;
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>