<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $comiflex_status = filter_input(INPUT_GET, 'id') == 1 ? ' disabled' : '';
            $zbs1_status = filter_input(INPUT_GET, 'id') == 2 ? ' disabled' : '';
            $zbs2_status = filter_input(INPUT_GET, 'id') == 3 ? ' disabled' : '';
            $zbs3_status = filter_input(INPUT_GET, 'id') == 4 ? ' disabled' : '';
            $atlas_status = filter_input(INPUT_GET, 'id') == 5 ? ' disabled' : '';
            $laminators1_status = filter_input(INPUT_GET, 'id') == 6 ? ' disabled' : '';
            $laminators2_status = filter_input(INPUT_GET, 'id') == 13 ? ' disabled' : '';
            $cutters1_status = filter_input(INPUT_GET, 'id') == 7 ? ' disabled' : '';
            $cutters2_status = filter_input(INPUT_GET, 'id') == 9 ? ' disabled' : '';
            $cutters3_status = filter_input(INPUT_GET, 'id') == 10 ? ' disabled' : '';
            $cutters4_status = filter_input(INPUT_GET, 'id') == 14 ? ' disabled' : '';
            $cutters_atlas_status = filter_input(INPUT_GET, 'id') == 11 ? ' disabled' : '';
            $cutters_soma_status = filter_input(INPUT_GET, 'id') == 12 ? ' disabled' : '';
            
            $query_string = '';
            $period = array();
            
            $from = filter_input(INPUT_GET, 'from');
            if($from !== null)
                $period['from'] = $from;
            
            $to = filter_input(INPUT_POST, 'to');
            if($to !== null)
                $period['to'] = $to;
            
            if(count($period) > 0)
                $query_string = '?'.http_build_query($period);
            
            if(IsInRole(array('technologist', 'dev', 'manager', 'storekeeper'))):
            $sql = "select id, name from machine order by name";
            $fetcher = new FetcherGrafik($sql);
            while ($row = $fetcher->Fetch()):
            $status = filter_input(INPUT_GET, 'id') == $row['id'] ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$status ?>" href="<?=APPLICATION ?>/grafik.php?id=<?=$row['id'] ?>"><?=$row['name'] ?></a>
            </li>
            <?php
            endwhile;
            endif;
            ?>
        </ul>
        <?php
        if(file_exists('find.php')) {
            include 'find.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>