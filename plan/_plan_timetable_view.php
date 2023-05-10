<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th>Печатник</th>
        <th class="fordrag"></th>
        <th>Заказ</th>
        <th>Метраж</th>
        <th>Вал</th>
        <th>Лам-я</th>
        <th>Краски</th>
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