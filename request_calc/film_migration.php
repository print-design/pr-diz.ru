<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Общего количества рулонов
$total_count = 0;
$sql = "select count(id) from calculation where brand_name != 'other'";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $total_count = $row[0];
}

$total_count_1 = 0;
$sql = "select count(id) from calculation where lamination1_brand_name != 'other' and lamination1_brand_name is not null and lamination1_brand_name != ''";
$fetcher = new Fetcher($sql);
if($row = $fetcher ->Fetch()) {
    $total_count_1 = $row[0];
}

$total_count_2 = 0;
$sql = "select count(id) from calculation where lamination2_brand_name != 'other' and lamination2_brand_name is not null and lamination2_brand_name != ''";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $total_count_2 = $row[0];
}

// Количество мигрированных рулонов
$ok_count = 0;
$sql = "select count(c.id) "
        . "from calculation c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where c.brand_name = f.name and c.thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count = $row[0];
}

$ok_count_1 = 0;
$sql = "select count(c.id) "
        . "from calculation c "
        . "left join film_variation fv on c.lamination1_film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where c.lamination1_brand_name = f.name and c.lamination1_thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count_1 = $row[0];
}

$ok_count_2 = 0;
$sql = "select count(c.id) "
        . "from calculation c "
        . "left join film_variation fv on c.lamination2_film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "where c.lamination2_brand_name = f.name and lamination2_thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count_2 = $row[0];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            #progress, #progress_1, #progress_2 {
                font-family: sans-serif;
                font-weight: bold;
                font-size: 100px;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <h1>Миграция (расчёты)</h1>
            <div id="progress"><?=$ok_count ?> из <?=$total_count ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" onclick="javascript: Start(); $(this).hide();">Запуск</button>
            <br /><br /><br />
            <div id="progress_1"><?=$ok_count_1 ?> из <?=$total_count_1 ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" onclick="javascript: Start_1(); $(this).hide();">Запуск</button>
            <br /><br /><br />
            <div id="progress_2"><?=$ok_count_2 ?> из <?=$total_count_2 ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" onclick="javascript: Start_2(); $(this).hide();">Запуск</button>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function Start() {
                $.ajax({ url: 'film_migration_ajax.php' })
                        .done(function(data) {
                            $('#progress').text(data + ' из <?=$total_count ?>');
                    
                            if(data > 0) {
                                Start();
                            }
                        })
                        .fail(function() {
                            $('$progress').text("Ошибка");
                        });
            }
            
            function Start_1() {
                $.ajax({ url: 'film_migration_ajax_1.php' })
                        .done(function(data) {
                            $('#progress_1').text(data + ' из <?=$total_count_1 ?>');
                    
                            if(data > 0) {
                                Start_1();
                            }
                        })
                        .fail(function() {
                            $('$progress_1').text("Ошибка");
                        });
            }
            
            function Start_2() {
                $.ajax({ url: 'film_migration_ajax_2.php' })
                        .done(function(data) {
                            $('#progress_2').text(data + ' из <?=$total_count_2 ?>');
                    
                            if(data > 0) {
                                Start_2();
                            }
                        })
                        .fail(function() {
                            $('$progress_2').text("Ошибка");
                        });
            }
        </script>
    </body>
</html>