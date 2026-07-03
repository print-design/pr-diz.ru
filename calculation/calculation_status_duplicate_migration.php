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
            <h2>Дублирование статуса</h2>
            <?php
            $result = 0;
            $sql = "select count(c.id) from calculation c where duplicate_status_id <> "
                    . "(select status_id from calculation_status_history where calculation_id = c.id order by id desc limit 1)";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $result = $row[0];
            }
            ?>
            <div id="result" style="font-size: xx-large;"><?=$result ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Migrate() {
            $.ajax({ url: 'calculation_status_duplicate_migration_ajax.php' })
                    .done(function(data) {
                        $('#result').text(data);
                
                        if(data != 0 && data != '0') {
                            Migrate();
                        }
                    })
                    .fail(function() {
                        $('#result').text('ERROR');
                    });
        }
    </script>
</html>