<div class="text-nowrap nav2">
    <?php
    $employees_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/grafik_employees.php')) == APPLICATION.'/admin/grafik_employees.php' ? ' active' : '';
    ?>
    <a href="grafik_employees.php" class="mr-4<?=$employees_class ?>">Сотрудники</a>
</div>
<hr />