<?php
include '../include/topscripts.php';
require_once '../include/PHPExcel.php';
require_once '../PHPExcel/Writer/Excel5.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

$date_from = null;
$date_to = null;
GetDateFromDateTo($from, $to, $date_from, $date_to);
    
$xls = new PHPExcel();
$activeSheetIndex = 0;

const CUTTERS_ALL = 1000;
$cutters = CUTTERS;
array_push($cutters, CUTTERS_ALL);

foreach($cutters as $cutter) {
    if($activeSheetIndex > 0) {
        $xls->createSheet();
    }
    
    $xls->setActiveSheetIndex($activeSheetIndex);
    $sheet = $xls->getActiveSheet();
    
    if($cutter == CUTTERS_ALL) {
        $sheet->setTitle("Все");
    }
    else {
        $sheet->setTitle(html_entity_decode(CUTTER_NAMES[$cutter]));
    }
        
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);
        
    $rowindex = 1;
        
    $sheet->setCellValue('A'.$rowindex, "Дата");
    $sheet->setCellValue('B'.$rowindex, "День/Ночь");
    $sheet->setCellValue('C'.$rowindex, "ФИО резчика");
    $sheet->setCellValue('D'.$rowindex, "ID заказа");
    $sheet->setCellValue('E'.$rowindex, "Заказчик");
    $sheet->setCellValue('F'.$rowindex, "Заказ");
    $sheet->setCellValue('G'.$rowindex, "Кг/Шт");
    $sheet->setCellValue('H'.$rowindex, "Выполненный метраж");
    $sheet->setCellValue('I'.$rowindex, "Выполненная масса");
    
    // Работники
    $employees = array();
    $employees_sorted = array();
    
    $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $employees[$row['id']] = array("first_name" => mb_substr($row['first_name'], 0, 1).'.', "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']);
        array_push($employees_sorted, $row['id']);
    }
    
    // Работники1
    $workshifts = array();
    
    $sql = "select ws.date, ws.shift, ws.machine_id, e.id, e.first_name, e.last_name "
            . "from plan_workshift1 ws "
            . "left join plan_employee e on ws.employee1_id = e.id "
            . "where ws.work_id = ".WORK_CUTTING." and ws.date >= '".$date_from->format('Y-m-d')."' and ws.date <= '".$date_to->format('Y-m-d')."'";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $workshifts[$row['date'].'_'.$row['shift'].'_'.$row['machine_id']] = $row['id'];
    }
    
    // Начало и окончание смены
    // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
    // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 следующего дня - 7:59 следующего дня
    // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
    
    // Тиражи
    $sql = "select e.id, e.date, e.shift, e.machine_id, ". PLAN_TYPE_EDITION." as type, if(isnull(e.worktime_continued), 0, 1) as has_continuation, ifnull(e.worktime_continued, e.worktime) worktime, e.position, c.customer_id, c.id calculation_id, c.name calculation, c.unit, c.streams_number, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, cus.name customer, "
            . "if(e.shift = 'day'"
            . ", (select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id) and printed >= addtime(e.date, '08:00:0') and printed < addtime(e.date, '20:00:0'))"
            . ", (select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id) and printed >= addtime(e.date, '20:00:0') and printed > addtime(e.date, '20:00:0'))) length_cut, "
            . "if(e.shift = 'day'"
            . ", (select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id))"
            . ", (select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id))) weight_cut "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where e.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and e.machine_id = ".$cutter;
    }
    $sql .= " and e.date >= '".$date_from->format('Y-m-d')."' and e.date < '".$date_to->format('Y-m-d')."' "
            . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
            . "union "
            . "select pc.id, pc.date, pc.shift, e.machine_id, ". PLAN_TYPE_CONTINUATION." as type, pc.has_continuation, pc.worktime, 1 as position, c.customer_id, c.id calculation_id, c.name calculation, c.unit, c.streams_number, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, cus.name customer, "
            . "if(pc.shift = 'day'"
            . ", (select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id) and printed >= addtime(pc.date, '08:00:0') and printed < addtime(pc.date, '20:00:0'))"
            . ", (select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id))) length_cut, "
            . "if(e.shift = 'day'"
            . ", (select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id))"
            . ", (select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id))) weight_cut "
            . "from plan_continuation pc "
            . "inner join plan_edition e on pc.plan_edition_id = e.id "
            . "inner join calculation c on e.calculation_id = c.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where e.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and e.machine_id = ".$cutter;
    }
    $sql .= " and pc.date >= '".$date_from->format('Y-m-d')."' and e.date < '".$date_to->format('Y-m-d')."' "
            . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
            . "union "
            . "select pp.id, pp.date, pp.shift, pp.machine_id, ". PLAN_TYPE_PART." as type, if(isnull(pp.worktime_continued), 0, 1) as has_continuation, ifnull(pp.worktime_continued, pp.worktime) worktime, pp.position, c.customer_id, c.id calculation_id, c.name calculation, c.unit, c.streams_number, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, cus.name customer, "
            . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
            . "(select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) weight_cut "
            . "from plan_part pp "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where pp.in_plan = 1 and pp.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and pp.machine_id = ".$cutter;
    }
    $sql .= " and pp.date >= '".$date_from->format('Y-d-m')."' and pp.date < '".$date_to->format('Y-m-d')."' "
            . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
            . "union "
            . "select ppc.id, ppc.date, ppc.shift, pp.machine_id, ". PLAN_TYPE_PART_CONTINUATION." as type, ppc.has_continuation, ppc.worktime, 1 as position, c.customer_id, c.id calculation_id, c.name calcualtion, c.unit, c.streams_number, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, cus.name customer, "
            . "(select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) length_cut, "
            . "(select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)) weight_cut "
            . "from plan_part_continuation ppc "
            . "inner join plan_part pp on ppc.plan_part_id = pp.id "
            . "inner join calculation c on pp.calculation_id = c.id "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where pp.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and pp.machine_id = ".$cutter;
    }
    $sql .= " and ppc.date >= '".$date_from->format('Y-m-d')."' and ppc.date < '".$date_to->format('Y-m-d')."' "
            . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
            . "order by date, shift, position";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $rowindex++;
        
        $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
        $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
        
        $key = $row['date'].'_'.$row['shift'].'_'.$row['machine_id'];
        if(array_key_exists($key, $workshifts)) {
            $employee = $employees[$workshifts[$key]];
            $sheet->setCellValue('C'.$rowindex, $employee['last_name'].' '.$employee['first_name']);
        }
        
        $sheet->setCellValue('D'.$rowindex, $row['customer_id'].'-'.$row['num_for_customer']);
        $sheet->setCellValue('E'.$rowindex, $row['customer']);
        $sheet->setCellValue('F'.$rowindex, $row['calculation'].($row['type'] == PLAN_TYPE_CONTINUATION || $row['type'] == PLAN_TYPE_PART_CONTINUATION ? ' (дорезка)' : ''));
        $sheet->setCellValue('G'.$rowindex, $row['unit'] == 'kg' ? "Кг" : "Шт");
        $sheet->getStyle('H'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        $length_cut = $row['length_cut'];
        $streams_number = $row['streams_number'];
        if($length_cut > 0 && $streams_number > 0) {
            $length_cut = $length_cut / $streams_number;
        }
        $sheet->setCellValue('H'.$rowindex, strval($length_cut));
        $sheet->getStyle('I'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('I'.$rowindex, $row['weight_cut']);
        
        // Подсчёт суммы
        if($cutter == CUTTERS_ALL) {
            if($row['unit'] == KG) {
                if(key_exists($key, $workshifts) && key_exists($workshifts[$key], $employees)) {
                    if(!key_exists(KG, $employees[$workshifts[$key]])) {
                        $employees[$workshifts[$key]][KG] = 0.0;
                    }
                    
                    $employees[$workshifts[$key]][KG] = floatval($employees[$workshifts[$key]][KG]) + floatval($row['weight_cut']);
                }
            }
        }
    }
    
    $activeSheetIndex++;
}

// Подсчёт всего
$xls->createSheet();
$xls->setActiveSheetIndex($activeSheetIndex);
$sheet = $xls->getActiveSheet();
$sheet->setTitle("Р");

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);

$sheet->setCellValue('A2', "Тариф");
$sheet->setCellValue('B1', "Тонна");
$sheet->setCellValue('C1', "Км");
$sheet->setCellValue('D3', "Итого");

$row_number = 4;

foreach($employees_sorted as $employee_id) {
    if(key_exists($employee_id, $employees) && in_array($employee_id, $workshifts)) {
        $sheet->setCellValue('A'.$row_number, $employees[$employee_id]['last_name'].' '.$employees[$employee_id]['first_name']);
        
        if(key_exists(KG, $employees[$employee_id])) {
            $sheet->getStyle('B'.$row_number)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->setCellValue('B'.$row_number, strval($employees[$employee_id][KG]));
        }
        
        $row_number++;
    }
}

// Сохранение
$filename = "Резчики_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xls";
    
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
$objWriter->save('php://output');
exit();
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>