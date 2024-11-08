<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th><?= PLAN_ROLE_NAMES[WORK_PLAN_ROLES[$this->work_id]] ?></th>
        <?php if($this->editable): ?>
        <th></th>
        <?php endif; ?>
        <th>№</th>
        <th>Заказ</th>
        <th class="storekeeper_hidden">Метраж</th>
        <?php if(IsInRole(ROLE_NAMES[ROLE_LAM_HEAD]) && $this->work_id == WORK_LAMINATION): ?>
        <th>Марка мат-ла</th>
        <th>Лам. вал</th>
        <?php endif; ?>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden">Кол-во образцов</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden colorist_hidden">Вал</th>
        <th class="cutting_hidden text-nowrap">Лам-й</th>
        <th class="cutting_hidden lamination_hidden storekeeper_hidden">Кр-ть</th>
        <th class="not_colorist_hidden">Краски</th>
        <th class="storekeeper_hidden colorist_hidden">Время</th>
        <th class="not_storekeeper_hidden">Нужно подготовить</th>
        <th class="not_storekeeper_hidden">Марка <span class="text-nowrap">мат-ла</span></th>
        <th class="not_storekeeper_hidden cutting_hidden">Ширина <span class="text-nowrap">мат-ла</span></th>
        <th>Менеджер</th>
        <th>Статус</th>
        <?php
        $comment_invisible_class = "";
        if($this->editable) {
            $comment_invisible_class = " comment_invisible";
        }
        ?>
        <th class="comment_cell<?=$comment_invisible_class ?> colorist_hidden">Комментарий</th>
        <th></th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Show();
    }
    ?>
</table>