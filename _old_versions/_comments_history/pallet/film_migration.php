<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Общего количества
$total_count = 0;
$sql = "select count(id) from pallet";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $total_count = $row[0];
}

// Количествл мигрированных паллетов
$ok_count = 0;
$sql = "select count(p.id) "
        . "from pallet p "
        . "left join film_variation fv on p.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "where f.name = fb.name and p.thickness = fv.thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ok_count = $row[0];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            #progress {
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
            <h1>Миграция (паллеты)</h1>
            <div id="progress"><?=$ok_count ?> из <?=$total_count ?></div>
            <br /><br /><br />
            <button type="button" class="btn btn-primary" id="start_btn" onclick="javascript: Start(); $(this).hide();">Запуск</button>
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
                            $('#progress').text("Ошибка");
                        });
            }
        </script>
    </body>
</html>