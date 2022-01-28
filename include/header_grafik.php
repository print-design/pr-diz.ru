<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
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