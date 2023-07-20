<?php
include '../include/topscripts.php';

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

if(!empty($work_id) && !empty($machine_id)) {
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo($from, $to, $date_from, $date_to);

    // Заголовки CSV-файла
    $titles = array("Дата", "День/Ночь", "Менеджер", "ID заказа", "Наименование", "Обьем заказ, кг", "Метраж", "Красочность", "Расходы на краску", "Расходы на клей", "Себестоимость ПФ", "Себестоимость", "Отгрузочаня стоимость", "Итоговая прибыль");

    // Данные CSV-файла
    $file_data = array();

    //***************************************************************************
    // Сохранение в файл
    $work_name = WORK_NAMES[$work_id];
    $machine_name = '';

    switch ($work_id) {
        case WORK_PRINTING:
            $machine_name = PRINTER_SHORTNAMES[$machine_id];
            break;
        case WORK_LAMINATION:
            $machine_name = LAMINATOR_NAMES[$machine_id];
            break;
        case WORK_CUTTING:
            $machine_name = CUTTER_NAMES[$machine_id];
            break;
    }

    $file_name = $work_name."_".$machine_name."_".$date_from->format('Y-m-d') ."_".$date_to->format('Y-m-d').".csv";
    
    DownloadSendHeaders($file_name);
    echo Array2Csv($file_data, $titles);
    die();
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в CSV, надо нажать на кнопку "Выгрузка" в верхнеё правой части страницы.</h1>
    </body>
</html>