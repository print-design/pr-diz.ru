<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <?php
            $storage_status = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/analytics/index.php')) == APPLICATION.'/analytics/index.php' ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$storage_status ?>" href="<?=APPLICATION ?>/analytics/">Хранение</a>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>