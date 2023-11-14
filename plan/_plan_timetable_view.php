<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?= PLAN_ROLE_NAMES[WORK_PLAN_ROLES[$this->work_id]] ?></th>
        <th class="fordrag"></th>
        <th>№</th>
        <th>Заказ</th>
        <th class="storekeeper_hidden">Метраж</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden samples_count_cell samples_count_invisible">Кол-во образцов</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Вал</th>
        <th class="cutting_hidden text-nowrap">Лам-я</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Краски</th>
        <th class="storekeeper_hidden">Время</th>
        <th class="not_storekeeper_hidden">Нужно подготовить</th>
        <th class="not_storekeeper_hidden">Марка <span class="text-nowrap">мат-ла</span></th>
        <th class="not_storekeeper_hidden cutting_hidden">Ширина <span class="text-nowrap">мат-ла</span></th>
        <th>Менеджер</th>
        <th class="comment_cell comment_invisible">Комментарий</th>
        <th></th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Show();
    }
    ?>
</table>