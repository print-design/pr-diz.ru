<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?=PLAN_ROLE_NAMES[WORK_PLAN_ROLES[$this->work_id]] ?></th>
        <th>Заказ</th>
        <th>Метраж</th>
        <th class="cutting_hidden lamination_hidden">Вал</th>
        <th class="cutting_hidden">Лам-я</th>
        <th class="cutting_hidden lamination_hidden">Краски</th>
        <th>Время</th>
        <th>Менеджер</th>
        <th>Комментарий</th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Print();
    }
    ?>
</table>