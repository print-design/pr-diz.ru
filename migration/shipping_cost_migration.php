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
                    . "from calculation_take_stream cts "
                    . "inner join calculation_take ct on cts.calculation_take_id = ct.id "
                    . "inner join calculation c on ct.calculation_id = c.id "
                    . "inner join calculation_result cr on cr.calculation_id = c.id "
                    . "where c.duplicate_shipping_cost = 0 and cts.weight > 0 and cts.length > 0 and cr.shipping_cost_per_unit <> 0 ";
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
                $.ajax({ url: 'shipping_cost_migration_ajax.php' })
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