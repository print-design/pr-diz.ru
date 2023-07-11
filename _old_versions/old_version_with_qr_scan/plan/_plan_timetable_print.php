<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?=PLAN_ROLE_NAMES[WORK_PLAN_ROLES[$this->work_id]] ?></th>
        <th>№</th>
        <th>Заказ</th>
        <th class="storekeeper_hidden">Метраж</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Вал</th>
        <th class="cutting_hidden storekeeper_hidden">Лам-я</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Краски</th>
        <th class="storekeeper_hidden">Время</th>
        <th class="not_storekeeper_hidden">Нужно подготовить</th>
        <th class="not_storekeeper_hidden">Марка мат-ла</th>
        <th class="not_storekeeper_hidden cutting_hidden">Ширина мат-ла</th>
        <th>Менеджер</th>
        <th>Комментарий</th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Print();
    }
    ?>
</table>