<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $request_calc_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/request_calc')) == APPLICATION.'/request_calc' ? ' disabled' : '';
            $techmap_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/techmap')) == APPLICATION.'/techmap' ? ' disabled' : '';
            $schedule_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/schedule')) == APPLICATION.'/schedule' ? ' disabled' : '';
            
            if(IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$request_calc_status ?>" href="<?=APPLICATION ?>/request_calc/<?= IsInRole('manager') ? BuildQuery("manager", GetUserId()) : "" ?>">Расчеты</a>
            </li>
            <?php
            endif;
            if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$techmap_status ?>" href="<?=APPLICATION ?>/techmap/<?= IsInRole('manager') ? BuildQuery("manager", GetUserId()) : "" ?>">Технологические карты</a>
            </li>
            <?php
            endif;
            if(IsInRole(array('technologist', 'dev', 'manager', 'administrator'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$schedule_status ?>" href="<?=APPLICATION ?>/schedule/">Расписание</a>
            </li>
            <?php endif; ?>
        </ul>
        <?php
        if(substr(filter_input(INPUT_SERVER, 'PHP_SELF'), (strlen(filter_input(INPUT_SERVER, 'PHP_SELF')) - strlen('index.php'))) == 'index.php' && file_exists('filter.php')) {
            include 'filter.php';
        }
        else {
            echo "<div class='ml-auto'></div>";
        }
        
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>