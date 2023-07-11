<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Общего количества рулонов
$total_count1 = 0;
$sql = "select count(id) from cut";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $total_count1 = $row[0];
}

$total_count2 = 0;
$sql = "select count(id) from cutting";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $total_count2 = $row[0];
}

// Количество мигрированных рулонов
$ok_count1 = 0;
$sql = "select count(c.id) "
        . "from cut c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_brand fb on c.film_brand_id = fb.id "
        . "where f.name = fb.name and c.thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count1 = $row[0];
}

$ok_count2 = 0;
$sql = "select count(c.id) "
        . "from cutting c "
        . "left join film_variation fv on c.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_brand fb on c.film_brand_id = fb.id "
        . "where f.name = fb.name and c.thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count2 = $row[0];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            #progress1, #progress2 {
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
            <h1>Миграция (нарезка)</h1>
            <div id="progress1"><?=$ok_count1 ?> из <?=$total_count1 ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" onclick="javascript: Start1(); $(this).hide();">Запуск</button>
            <br /><br /><br />
            <div id="progress2"><?=$ok_count2 ?> из <?=$total_count2 ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" onclick="javascript: Start2(); $(this).hide();">Запуск</button>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function Start1() {
                $.ajax({ url: 'film_migration_ajax1.php' })
                        .done(function(data) {
                            $('#progress1').text(data + ' из <?=$total_count1 ?>');
                    
                            if(data > 0) {
                                Start1();
                            }
                        })
                        .fail(function() {
                            $('$progress1').text("Ошибка");
                        });
            }
            
            function Start2() {
                $.ajax({ url: 'film_migration_ajax2.php' })
                        .done(function(data) {
                            $('#progress2').text(data + ' из <?=$total_count2 ?>');
                    
                            if(data > 0) {
                                Start2();
                            }
                        })
                        .fail(function() {
                            $('$progress2').text("Ошибка");
                        });
            }
        </script>
    </body>
</html>