<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html lang="ru">
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
            <h2>Дублирование общей стоимости</h2>
            <?php
            $result = 0;
            $sql = "select count(distinct c.id) "
                    . "from calculation c "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "where c.duplicate_shipping_cost = 0 and cr.shipping_cost > 0 and c.duplicate_status_id <> ". ORDER_STATUS_DRAFT." and c.duplicate_status_id <> ". ORDER_STATUS_TRASH;
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $result = $row[0];
            }
            ?>
            <div id="result" style="font-size: xx-large;"><?=$result ?></div>
            <button type="button" class="btn" onclick="javascript: Migrate();">Старт</button>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            function Migrate() {
                $.ajax({ url: 'shipping_cost1_migration_ajax.php' })
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
    </body>
</html>