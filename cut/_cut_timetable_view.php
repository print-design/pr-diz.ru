<table class="table typography">
    <tr>
        <th>Дата</th>
        <th>Смена</th>
        <th>Резчик</th>
        <th>№</th>
        <th>Заказ</th>
        <th>Метраж</th>
        <th>Время</th>
        <th>Менеджер</th>
        <th>Статус</th>
        <th>Комментарий</th>
        <th style="width: 154px;"></th>
    </tr>
    <?php
    foreach ($this->cut_dates as $cut_date) {
        $cut_date->Show();
    }
    ?>
</table>