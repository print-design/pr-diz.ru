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
            <h1>Edition laminate</h1>
            <?php
            $count = 0;
            $sql = "select count(csh.id) from calculation_status_history csh where csh.status_id = ". ORDER_STATUS_PLAN_LAMINATE
                    ." and (select count(id) from plan_edition where calculation_id = csh.calculation_id and work_id = ". WORK_LAMINATION.") = 0";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $count = $row[0];
            }
            ?>
            <div id="result" style="font-size: xx-large;"><?=$count ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Migrate() {
            $.ajax({ url: 'calculation_status_edition_laminate_migration_ajax.php' })
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