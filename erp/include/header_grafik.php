<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $comiflex_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/comiflex.php' ? ' disabled' : '';
            $zbs1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/zbs1.php' ? ' disabled' : '';
            $zbs2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/zbs2.php' ? ' disabled' : '';
            $zbs3_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/zbs3.php' ? ' disabled' : '';
            $atlas_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/atlas.php' ? ' disabled' : '';
            $laminators1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/laminators1.php' ? ' disabled' : '';
            $laminators2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/laminators2.php' ? ' disabled' : '';
            $cutters1_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters1.php' ? ' disabled' : '';
            $cutters2_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters2.php' ? ' disabled' : '';
            $cutters3_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters3.php' ? ' disabled' : '';
            $cutters4_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters4.php' ? ' disabled' : '';
            $cutters_atlas_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters_atlas.php' ? ' disabled' : '';
            $cutters_soma_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/cutters_soma.php' ? ' disabled' : '';
            
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
            
            if(IsInRole(array('technologist', 'dev', 'manager'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$comiflex_status ?>" href="<?=APPLICATION ?>/grafik/comiflex.php<?=$query_string ?>">Comiflex</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs1_status ?>" href="<?=APPLICATION ?>/grafik/zbs1.php<?=$query_string ?>">ZBS-1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs2_status ?>" href="<?=APPLICATION ?>/grafik/zbs2.php<?=$query_string ?>">ZBS-2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$zbs3_status ?>" href="<?=APPLICATION ?>/grafik/zbs3.php<?=$query_string ?>">ZBS-3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$atlas_status ?>" href="<?=APPLICATION ?>/grafik/atlas.php<?=$query_string ?>">Атлас</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators1_status ?>" href="<?=APPLICATION ?>/grafik/laminators1.php<?=$query_string ?>">Ламинатор 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$laminators2_status ?>" href="<?=APPLICATION ?>/grafik/laminators2.php<?=$query_string ?>">Ламинатор 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters1_status ?>" href="<?=APPLICATION ?>/grafik/cutters1.php<?=$query_string ?>">Резка 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters2_status ?>" href="<?=APPLICATION ?>/grafik/cutters2.php<?=$query_string ?>">Резка 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters3_status ?>" href="<?=APPLICATION ?>/grafik/cutters3.php<?=$query_string ?>">Резка 3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters4_status ?>" href="<?=APPLICATION ?>/grafik/cutters4.php<?=$query_string ?>">Резка 4</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_atlas_status ?>" href="<?=APPLICATION ?>/grafik/cutters_atlas.php<?=$query_string ?>">Резка &laquo;Атлас&raquo;</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cutters_soma_status ?>" href="<?=APPLICATION ?>/grafik/cutters_soma.php<?=$query_string ?>">Резка &laquo;Сома&raquo;</a>
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