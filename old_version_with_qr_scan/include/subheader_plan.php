<div class="text-nowrap nav2">
    <?php
    $employees_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/plan_employees.php')) == APPLICATION.'/admin/plan_employees.php' ? ' active' : '';
    if(!$employees_class) {
        $employees_class = substr(filter_input(INPUT_SERVER, 'PHP_SELF'), 0, strlen(APPLICATION.'/admin/plan_employees_create.php')) == APPLICATION.'/admin/plan_employees_create.php' ? ' active' : '';
    }
    ?>
    <a href="plan_employees.php" class="mr-4<?=$employees_class ?>">Сотрудники</a>
</div>
<hr />