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
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$comiflex_status ?>" href="<?=APPLICATION ?>/grafik.php?id=1<?=$query_string ?>">Comiflex</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs1_status ?>" href="<?=APPLICATION ?>/grafik.php?id=2<?=$query_string ?>">ZBS-1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs2_status ?>" href="<?=APPLICATION ?>/grafik.php?id=3<?=$query_string ?>">ZBS-2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs3_status ?>" href="<?=APPLICATION ?>/grafik.php?id=4<?=$query_string ?>">ZBS-3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$atlas_status ?>" href="<?=APPLICATION ?>/grafik.php?id=5<?=$query_string ?>">Атлас</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators1_status ?>" href="<?=APPLICATION ?>/grafik.php?id=6<?=$query_string ?>">Ламинатор 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators2_status ?>" href="<?=APPLICATION ?>/grafik.php?id=13<?=$query_string ?>">Ламинатор 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters1_status ?>" href="<?=APPLICATION ?>/grafik.php?id=7<?=$query_string ?>">Резка 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters2_status ?>" href="<?=APPLICATION ?>/grafik.php?id=9<?=$query_string ?>">Резка 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters3_status ?>" href="<?=APPLICATION ?>/grafik.php?id=10<?=$query_string ?>">Резка 3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters4_status ?>" href="<?=APPLICATION ?>/grafik.php?id=14<?=$query_string ?>">Резка 4</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_atlas_status ?>" href="<?=APPLICATION ?>/grafik.php?id=11<?=$query_string ?>">Резка &laquo;Атлас&raquo;</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_soma_status ?>" href="<?=APPLICATION ?>/grafik.php?id=12<?=$query_string ?>">Резка &laquo;Сома&raquo;</a>
            </li>
            <?php
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