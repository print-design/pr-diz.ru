<?php
include '../include/topscripts.php';
?>
<html>
    <body>
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
    </body>
</html>