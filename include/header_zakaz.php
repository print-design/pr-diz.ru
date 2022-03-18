<?php
include 'left_bar.php';

$php_self = $_SERVER['PHP_SELF'];
$substrings = mb_split("/", $php_self);
$count = count($substrings);
$folder = '';
$file = '';

if($count > 1) {
    $folder = $substrings[$count - 2];
    $file = $substrings[$count - 1];
}

$request_calc_status = '';
$techmap_status = '';
$schedule_status = '';

if($folder == 'request_calc') {
    $request_calc_status = ' disabled';
}
elseif($folder == 'techmap') {
    $techmap_status = ' disabled';
}
elseif($folder == 'schedule') {
    $schedule_status = ' disabled';
}
            
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            if(IsInRole(array('technologist', 'dev', 'manager', 'administrator', 'designer'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$request_calc_status ?>" href="<?=APPLICATION ?>/request_calc/<?= IsInRole('manager') ? BuildQuery("manager", GetUserId()) : "" ?>">Расчеты</a>
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