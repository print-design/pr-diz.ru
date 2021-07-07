<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $pallets_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/pallet')) == APPLICATION.'/pallet' ? ' disabled' : '';
            $rolls_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/roll')) == APPLICATION.'/roll' ? ' disabled' : '';
            $utilized_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/utilized')) == APPLICATION.'/utilized' ? ' disabled' : '';
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/user/index.php' ? ' disabled' : '';
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/personal/index.php' ? ' disabled' : '';
            
            // СТАТУС "СРАБОТАННЫЙ" ДЛЯ ПАЛЛЕТА
            $utilized_status_pallet_id = 2;
            
            // СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
            $utilized_status_roll_id = 2;
            
            // На странице рулона:
            // Если он сработан, то выделяем пункт меню "Сработанная плёнка",
            // Иначе выделяем пункт меню "Рулоны"
            if(substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/roll/roll.php')) == APPLICATION.'/roll/roll.php') {
                if(isset($status_id) && $status_id == $utilized_status_roll_id) {
                    $rolls_status = '';
                    $utilized_status = ' disabled';
                }
            }
            
            // На странице рулона из паллета:
            // Если паллет сработан, то выделяем пункт меню "Сработанная плёнка",
            // Иначе выделяем пункт меню "Паллеты".
            if(substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/pallet/roll.php')) == APPLICATION.'/pallet/roll.php') {
                if(isset($status_id) && $status_id == $utilized_status_pallet_id) {
                    $pallets_status = '';
                    $utilized_status = ' disabled';
                }
            }
            
            if(IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$pallets_status ?>" href="<?=APPLICATION ?>/pallet/">Паллеты</a>
            </li>
            <li class='nav-item'>
                <a class="nav-link<?=$rolls_status ?>" href="<?=APPLICATION ?>/roll/">Рулоны</a>
            </li>
            <?php
            endif;
            if(IsInRole(array('technologist', 'dev'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$utilized_status ?> text-nowrap" href="<?=APPLICATION ?>/utilized/">Сработанная пленка</a>
            </li>
            <?php
            endif;
            
            if($_SERVER['HTTP_HOST'] == "pr-diz-test.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>ТЕСТОВАЯ</li>";
            }
            else if($_SERVER['HTTP_HOST'] == "pr-diz-develop.ru") {
                echo "<li style='font-weight: bold; font-size: large; margin-left: 50px;'>РАЗРАБОТКА</li>";
            }
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