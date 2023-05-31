<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?= PLAN_ROLE_NAMES[WORK_PLAN_ROLES[$this->work_id]] ?></th>
        <th class="fordrag"></th>
        <th>Заказ</th>
        <th class="storekeeper_hidden">Метраж</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Вал</th>
        <th class="cutting_hidden storekeeper_hidden">Лам-я</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Краски</th>
        <th class="storekeeper_hidden">Время</th>
        <th class="not_storekeeper_hidden">Нужно подготовить</th>
        <th class="not_storekeeper_hidden">Марка мат-ла</th>
        <th>Менеджер</th>
        <th class="comment_cell comment_invisible">Комментарий</th>
        <th class="storekeeper_hidden"></th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Show();
    }
    ?>
</table>