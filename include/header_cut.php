<?php
include '../include/left_bar.php';
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link disabled" href="<?=APPLICATION.'/cut/' ?>"><?=filter_input(INPUT_COOKIE, ROLE_LOCAL) ?></a>
            </li>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include '../include/header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>