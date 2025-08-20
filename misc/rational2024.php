<?php
include '../include/topscripts.php';
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        $start_width = 1200;
        $end_width = 2000;
        ?>
        <h1>Исходные ширины для нарезки ручьёв (<?=$start_width ?> &ndash; <?=$end_width ?>)</h1>
        <h2>HGPL прозрачка, 20</h2>
        <table class="table">
        <?php
        $sql = "select distinct (stream_width * streams_number + 20) width "
                . "from calculation "
                . "where status_id > 7 and work_type_id = 2 and film_variation_id = 2 "
                . "order by width";
        $grabber = new Grabber($sql);
        $widths = $grabber->result;
        $items = array();
        
        foreach($widths as $item) {
            $matches = array();
            for($current_width = $start_width; $current_width <= $end_width; $current_width++) {
                if($current_width / $item['width'] == round($current_width ?? 0 / $item['width'] ?? 1)) {
                    array_push($matches, $current_width);
                }
            }
            $item['matches'] = $matches;
            array_push($items, $item);
        }
        
        foreach($items as $item):
        ?>
            <tr>
                <td><?=$item['width'] ?></td>
                <td><?= implode(', ', $item['matches']) ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
        <?php
        $roll_widths = array();
        ?>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>