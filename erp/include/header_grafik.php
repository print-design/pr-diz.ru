<?php
include 'left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            $comiflex_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/comiflex.php' ? ' disabled' : '';
            if(IsInRole(array('technologist', 'dev', 'manager'))):
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$comiflex_status ?>" href="<?=APPLICATION ?>/grafik/comiflex.php">Comiflex</a>
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