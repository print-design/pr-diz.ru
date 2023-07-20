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
    $titles = array();
    array_push($titles, "Дата");
    array_push($titles, "День/Ночь");
    array_push($titles, "Менеджер");
    array_push($titles, "ID заказа");
    array_push($titles, "Наименование");
    array_push($titles, "Объём заказа, кг");
    array_push($titles, "Метраж");
    if($work_id == WORK_PRINTING) { array_push($titles, "Красочность"); }
    if($work_id == WORK_PRINTING) { array_push($titles, "Расходы на краску"); }
    if($work_id == WORK_LAMINATION) { array_push($titles, "Расходы на клей"); }
    if($work_id == WORK_PRINTING) { array_push($titles, "Себестоимость ПФ"); }
    array_push($titles, "Себестоимость");
    array_push($titles, "Отгрузочаня стоимость");
    array_push($titles, "Итоговая прибыль");
    
    // Данные CSV-файла
    $file_data = array();
    
    $sql = "select pe.date, pe.shift, pe.lamination, c.name, c.customer_id, c.ink_number, u.first_name, u.last_name, "
            . "if(isnull(pe.worktime_continued), round(cr.length_pure_1), round(cr.length_pure_1) / pe.worktime * pe.worktime_continued) as length_pure_1, "
            . "if(isnull(pe.worktime_continued), round(cr.length_pure_2), round(cr.length_pure_2) / pe.worktime * pe.worktime_continued) as length_pure_2, "
            . "if(isnull(pe.worktime_continued), round(cr.length_pure_3), round(cr.length_pure_3) / pe.worktime * pe.worktime_continued) as length_pure_3, "
            . "if(isnull(pe.worktime_continued), round(cr.weight_pure_1), round(cr.weight_pure_1) / pe.worktime * pe.worktime_continued) as weight_pure_1, "
            . "if(isnull(pe.worktime_continued), round(cr.weight_pure_2), round(cr.weight_pure_2) / pe.worktime * pe.worktime_continued) as weight_pure_2, "
            . "if(isnull(pe.worktime_continued), round(cr.weight_pure_3), round(cr.weight_pure_3) / pe.worktime * pe.worktime_continued) as weight_pure_3, "
            . "cr.ink_cost, cr.glue_cost_2, cr.glue_cost_3, cr.cliche_cost, cr.cost, cr.shipping_cost, cr.income + cr.income_cliche + cr.income_knife as total_income, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
            . "from plan_edition pe "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "inner join user u on c.manager_id = u.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where pe.work_id = $work_id and pe.machine_id = $machine_id and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."' "
            . "union "
            . "select pc.date, pc.shift, pe.lamination, c.name, c.customer_id, c.ink_number, u.first_name, u.last_name, "
            . "round(cr.length_pure_1) / pe.worktime * pc.worktime as length_pure_1, "
            . "round(cr.length_pure_2) / pe.worktime * pc.worktime as length_pure_2, "
            . "round(cr.length_pure_3) / pe.worktime * pc.worktime as length_pure_3, "
            . "round(cr.weight_pure_1) / pe.worktime * pc.worktime as weight_pure_1, "
            . "round(cr.weight_pure_2) / pe.worktime * pc.worktime as weight_pure_2, "
            . "round(cr.weight_pure_3) / pe.worktime * pc.worktime as weight_pure_3, "
            . "cr.ink_cost, cr.glue_cost_2, cr.glue_cost_3, cr.cliche_cost, cr.cost, cr.shipping_cost, cr.income + cr.income_cliche + cr.income_knife as total_income, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
            . "from plan_continuation pc "
            . "inner join plan_edition pe on pc.plan_edition_id = pe.id "
            . "inner join calculation c on pe.calculation_id = c.id "
            . "inner join user u on c.manager_id = u.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where pe.work_id = $work_id and pe.machine_id = $machine_id and pc.date >= '".$date_from->format('Y/m/d')."' and pc.date <= '".$date_to->format('Y/m/d')."' "
            . "union "
            . "select pp.date, pp.shift, pp.lamination, c.name, c.customer_id, c.ink_number, u.first_name, u.last_name, "
            . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_1, "
            . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_2, "
            . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_3, "
            . "if(isnull(pp.worktime_continued), round(cr.weight_pure_1 / cr.length_pure_1 * pp.length), round(cr.weight_pure_1 / cr.length_pure_1 * pp.length) / pp.worktime * pp.worktime_continued) as weight_pure_1, "
            . "if(isnull(pp.worktime_continued), round(cr.weight_pure_2 / cr.length_pure_2 * pp.length), round(cr.weight_pure_2 / cr.length_pure_2 * pp.length) / pp.worktime * pp.worktime_continued) as weight_pure_2, "
            . "if(isnull(pp.worktime_continued), round(cr.weight_pure_3 / cr.length_pure_3 * pp.length), round(cr.weight_pure_3 / cr.length_pure_3 * pp.length) / pp.worktime * pp.worktime_continued) as weight_pure_3, "
            . "cr.ink_cost, cr.glue_cost_2, cr.glue_cost_3, cr.cliche_cost, cr.cost, cr.shipping_cost, cr.income + cr.income_cliche + cr.income_knife as total_income, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
            . "from plan_part pp "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "inner join user u on c.manager_id = u.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where pp.work_id = $work_id and pp.machine_id = $machine_id and pp.date >= '".$date_from->format('Y/m/d')."' and pp.date <= '".$date_to->format('Y/m/d')."' "
            . "union "
            . "select ppc.date, ppc.shift, pp.lamination, c.name, c.customer_id, c.ink_number, u.first_name, u.last_name, "
            . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_1, "
            . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_2, "
            . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_3, "
            . "round(cr.weight_pure_1 / cr.length_pure_1 * pp.length) / pp.worktime * ppc.worktime as weight_pure_1, "
            . "round(cr.weight_pure_2 / cr.length_pure_2 * pp.length) / pp.worktime * ppc.worktime as weight_pure_2, "
            . "round(cr.weight_pure_3 / cr.length_pure_3 * pp.length) / pp.worktime * ppc.worktime as weight_pure_3, "
            . "cr.ink_cost, cr.glue_cost_2, cr.glue_cost_3, cr.cliche_cost, cr.cost, cr.shipping_cost, cr.income + cr.income_cliche + cr.income_knife as total_income, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "inner join user u on c.manager_id = u.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where pp.work_id = $work_id and pp.machine_id = $machine_id and ppc.date >= '".$date_from->format('Y/m/d')."' and ppc.date <= '".$date_to->format('Y/m/d')."' "
            . "order by date, shift";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $weight_pure = $row['weight_pure_1'];
        $length_pure = $row['length_pure_1'];
        $glue_cost = 0;
        
        if($work_id == WORK_LAMINATION && $row['lamination'] == 1) {
            $weight_pure = $row['weight_pure_2'];
            $length_pure = $row['length_pure_2'];
            $glue_cost = $row['glue_cost_2'];
        }
        elseif($work_id == WORK_LAMINATION && $row['lamination'] == 2) {
            $weight_pure = $row['weight_pure_3'];
            $length_pure = $row['length_pure_3'];
            $glue_cost = $row['glue_cost_3'];
        }
        
        $data_array = array();
        array_push($data_array, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
        array_push($data_array, $row['shift'] == "day" ? "День": "Ночь");
        array_push($data_array, $row['last_name'].' '.$row['first_name']);
        array_push($data_array, $row['customer_id']."-".$row["num_for_customer"]);
        array_push($data_array, $row['name']);
        array_push($data_array, DisplayNumber(floatval($weight_pure), 0));
        array_push($data_array, DisplayNumber(floatval($length_pure), 0));
        if($work_id == WORK_PRINTING) { array_push($data_array, $row["ink_number"]); }
        if($work_id == WORK_PRINTING) { array_push($data_array, DisplayNumber(floatval($row["ink_cost"]), 0)); }
        if($work_id == WORK_LAMINATION) { array_push($data_array, DisplayNumber(floatval($glue_cost), 0)); }
        if($work_id == WORK_PRINTING) { array_push($data_array, DisplayNumber(floatval($row['cliche_cost']), 0)); }
        array_push($data_array, DisplayNumber(floatval($row['cost']), 0));
        array_push($data_array, DisplayNumber(floatval($row['shipping_cost']), 0));
        array_push($data_array, DisplayNumber(floatval($row['total_income']), 0));
        
        array_push($file_data, $data_array);
    }

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