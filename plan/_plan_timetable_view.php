<?php
require_once './roles.php';
?>
<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?= $role_names[$work_roles[$this->work_id]] ?></th>
        <th class="fordrag"></th>
        <th>Заказ</th>
        <th>Метраж</th>
        <?php if($this->work_id == WORK_PRINTING): ?>
        <th>Вал</th>
        <?php endif; ?>
        <th>Лам-я</th>
        <?php if($this->work_id == WORK_PRINTING): ?>
        <th>Краски</th>
        <?php endif; ?>
        <th>Время</th>
        <th>Менеджер</th>
        <th></th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Show();
    }
    ?>
</table>