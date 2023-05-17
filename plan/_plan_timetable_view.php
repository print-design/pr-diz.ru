<?php
require_once './roles.php';
?>
<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?= ROLE_NAMES[WORK_ROLES[$this->work_id]] ?></th>
        <th class="fordrag"></th>
        <th>Заказ</th>
        <th>Метраж</th>
        <?php if($this->work_id == WORK_PRINTING): ?>
        <th>Вал</th>
        <?php endif; ?>
        <?php if($this->work_id == WORK_PRINTING || $this->work_id == WORK_LAMINATION): ?>
        <th>Лам-я</th>
        <?php endif; ?>
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