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
            <h2>В плане печати</h2>
            <?php
            $confirmed = 0;
            $sql = "select count(csh.id) from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_PRINT
                    ." and (select count(id) from calculation_status_history where calculation_id = csh.calculation_id and status_id = ". ORDER_STATUS_PLAN_PRINT." and id > csh.id) > 0";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $confirmed = $row[0];
            }
            ?>
            <div id="result" style="font-size: xx-large;"><?=$confirmed ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Migrate() {
            $.ajax({ url: 'calculation_status_plan_print_migration_ajax.php' })
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