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
            <h1>История статусов заказа</h1>
                <?php
                $in_history = 0;
                $sql = "select count(c.id) from calculation c where (select count(id) from calculation_status_history where calculation_id = c.id) > 0";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $in_history = $row[0];
                }
                
                $total = 0;
                $sql = "select count(id) from calculation";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $total = $row[0];
                }
                ?>
            <div id="result" style="font-size: xx-large"><?=$in_history.' из '.$total ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Migrate() {
            $.ajax({ url: 'calculation_status_migration_ajax.php' })
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