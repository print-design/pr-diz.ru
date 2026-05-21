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
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <h1>Миграция дублирующих полей для ускорения загрузки</h1>
            <?php
            $sql = "select count(id) from calculation where id not in (select calculation_id from calculation_status_history)";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                echo $row[0];
            }
            
            $without = 0;
            $sql = "select count(id) from calculation where duplicate_status_id is not null";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $without = $row[0];
            }
            
            $total = 0;
            $sql = "select count(id) from calculation";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $total = $row[0];
            }
            ?>
            <div id="result" style="font-size: xx-large;"><?=$without.' из '.$total ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Migrate() {
            $.ajax({ url: 'duplicate_migration_ajax.php' })
                    .done(function(data) {
                        $('#result').text(data + ' из <?=$total ?>');
                
                        if(data < <?=$total ?>) {
                            Migrate();
                        }
                    })
                    .fail(function() {
                        $('#result').text('ERROR');
                    });
        }
    </script>
</html>