<hr class="pb-0 mb-0" />
<div class="d-flex justify-content-start">
    <div class="p-1">
        <div class="text-nowrap nav2">
            <?php
            $cut_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/history/cut.php')) == APPLICATION.'/history/cut.php' ? " active" : "";
            ?>
            <a href="cut.php" class="mr-4<?=$cut_class ?>">Раскрой</a>
        </div>
    </div>
</div>