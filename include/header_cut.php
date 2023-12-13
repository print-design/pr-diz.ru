<?php
include '../include/left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php if(IsInRole(CUTTER_USERS)): ?>
            <li class="nav-item">
                <a class="nav-link disabled" href="<?=APPLICATION.'/cut/' ?>"><?=filter_input(INPUT_COOKIE, ROLE_LOCAL) ?></a>
            </li>
            <?php
            else:
            foreach(CUTTERS as $cutter):
                $disabled = '';
            if(filter_input(INPUT_GET, 'machine_id') == $cutter) {
                $disabled = ' disabled';
            }
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$disabled ?>" href="<?=APPLICATION.'/cut/?machine_id='.$cutter ?>"><?=CUTTER_NAMES[$cutter] ?></a>
            </li>
            <?php
            endforeach;
            endif;
            ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>