<?php
include '../include/left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <?php
            foreach($works as $work):
                $work_class = '';
                if($work_id == $work) {
                    $work_class = ' disabled';
                }
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$work_class ?>" href="?work_id=<?=$work ?>"><?=$work_names[$work] ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>