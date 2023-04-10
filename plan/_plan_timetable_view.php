<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th>Печатник</th>
        <?php if($this->machine == CalculationBase::COMIFLEX): ?>
        <th>Помощник</th>
        <?php endif; ?>
        <th>Заказ</th>
        <th>Метраж</th>
        <th>Вал</th>
        <th>Ламинация</th>
        <th>Краски</th>
        <th>Менеджер</th>
        <th></th>
    </tr>
    <?php
    foreach($this->plan_dates as $plan_date) {
        $plan_date->Show();
    }
    ?>
</table>