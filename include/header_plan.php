<?php
include '../include/left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            foreach(WORKS as $work):
                $work_class = '';
                if($work_id == $work) {
                    $work_class = ' disabled';
                }
            ?>
            <?php /*ВРЕМЕННО*/ if(!(GetUserId() == CUTTER_SOMA && $work !== WORK_CUTTING)): ?>
            <li class="nav-item">
                <a class="nav-link<?=$work_class ?>" href="<?= BuildQueryAddRemove('work_id', $work, 'machine_id') ?>"><?=WORK_NAMES[$work] ?></a>
            </li>
            <?php /*ВРЕМЕННО*/ endif; ?>
            <?php endforeach; ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>