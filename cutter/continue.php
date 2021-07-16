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
            $id = $row['id'];
            $supplier = $row['supplier'];
            $film_brand = $row['film_brand'];
            $thickness = $row['thickness'];
            $width = $row['width'];
            $cut_wind_id = $row['cut_wind_id'];
            $cut_streams_count = $row['cut_streams_count'];
            ?>
            <h1>Характеристики</h1>
            <p>Поставщик: <?=$supplier ?></p>
            <p>Марка пленки: <?=$film_brand ?></p>
            <p>Толщина: <?=$thickness ?> мкм</p>
            <p>Ширина: <?=$width ?> мм</p>
            <br />
            <?php
            $sql = "select length from cut_wind where cut_id";
            $fetcher = new Fetcher($sql);
            $total_length = 0;
            while($row = $fetcher->Fetch()) {
                $total_length += $row['length'];
            }
            ?>
            <p>Всего нарезали: <?=number_format($total_length, 0, ",", " ") ?> метров</p>
            <?php
            $counter = 0;
            $sql = "select width from cut_stream where cut_id = $id order by id";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            ?>
            <p>Ручей <?=++$counter ?> &ndash; <?=$row['width'] ?> мм</p>
            <?php endwhile; ?>
            <a class="btn btn-dark w-100 mt-4" href="<?=APPLICATION ?>/cutter/print.php?cut_wind_id=<?=$cut_wind_id ?>">Продолжить</a>
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