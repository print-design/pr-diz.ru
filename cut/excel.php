<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

$date_from = null;
$date_to = null;
GetDateFromDateTo($from, $to, $date_from, $date_to);

$diff1Day = new DateInterval('P1D');
    
$spreadsheet = new Spreadsheet();
$activeSheetIndex = 0;

const CUTTERS_ALL = 1000;
$cutters = CUTTERS;
array_push($cutters, CUTTERS_ALL);

foreach($cutters as $cutter) {
    if($activeSheetIndex > 0) {
        $spreadsheet->createSheet();
    }
    
    $spreadsheet->setActiveSheetIndex($activeSheetIndex);
    $sheet = $spreadsheet->getActiveSheet();
    
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
    
    // Тиражи
    $sql = "select distinct date(cts.printed) printed, ped.date, ped.shift, ped.position, ". PLAN_TYPE_EDITION." as type, pem.last_name, pem.first_name, c.customer_id, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) as num_for_customer, "
            . "cus.name customer, c.name calculation, c.unit, "
            . "(select sum(length) from calculation_take_stream where printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and plan_employee_id = cts.plan_employee_id and date(printed) = date(cts.printed) and calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)) length_cut, "
            . "(select sum(weight) from calculation_take_stream where printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and plan_employee_id = cts.plan_employee_id and date(printed) = date(cts.printed) and calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)) weight_cut "
            . "from plan_edition ped "
            . "inner join calculation c on ped.calculation_id = c.id "
            . "inner join calculation_stream cs on cs.calculation_id = c.id "
            . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "left join plan_employee pem on cts.plan_employee_id = pem.id "
            . "where cts.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and ped.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and ped.machine_id = ".$cutter;
    }
    $sql .= " and ped.id not in (select plan_edition_id from plan_continuation) "
            . "union "
            . "select distinct date(cts.printed) printed, pc.date, pc.shift, 1 as position, ". PLAN_TYPE_CONTINUATION." as type, pem.last_name, pem.first_name, c.customer_id, "
            . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) as num_for_customer, "
            . "cus.name customer, c.name calculation, c.unit, "
            . "(select sum(length) from calculation_take_stream where printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and plan_employee_id = cts.plan_employee_id and date(printed) = date(cts.printed) and calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)) length_cut, "
            . "(select sum(weight) from calculation_take_stream where printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and plan_employee_id = cts.plan_employee_id and date(printed) = date(cts.printed) and calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)) weight_cut "
            . "from plan_continuation pc "
            . "inner join plan_edition ped on pc.plan_edition_id = ped.id "
            . "inner join calculation c on ped.calculation_id = c.id "
            . "inner join calculation_stream cs on cs.calculation_id = c.id "
            . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "left join plan_employee pem on cts.plan_employee_id = pem.id "
            . "where cts.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and ped.work_id = ". WORK_CUTTING;
    if($cutter != CUTTERS_ALL) {
        $sql .= " and ped.machine_id = ".$cutter;
    }
    $sql .= " and pc.has_continuation = false "
            . "order by printed";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $rowindex++;
        
        $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
        $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
        $sheet->setCellValue('C'.$rowindex, $row['last_name'].' '.(empty($row['first_name']) ? '' : mb_substr($row['first_name'], 0, 1).'.'));
        $sheet->setCellValue('D'.$rowindex, $row['customer_id'].'-'.$row['num_for_customer']);
        $sheet->setCellValue('E'.$rowindex, $row['customer']);
        $sheet->setCellValue('F'.$rowindex, $row['calculation'].($row['type'] == PLAN_TYPE_CONTINUATION ? ' (дорезка)' : ''));
        $sheet->setCellValue('G'.$rowindex, $row['unit'] == 'kg' ? "Кг" : "Шт");
        $sheet->setCellValue('H'.$rowindex, $row['length_cut']);
        $sheet->setCellValue('I'.$rowindex, $row['weight_cut']);
        
        // Подсчёт суммы
        /*if($cutter == CUTTERS_ALL) {
            if($row['unit'] == KG) {
                if(key_exists($key, $workshifts) && key_exists($workshifts[$key], $employees)) {
                    if(!key_exists(KG, $employees[$workshifts[$key]])) {
                        $employees[$workshifts[$key]][KG] = 0.0;
                    }
                    
                    $employees[$workshifts[$key]][KG] = floatval($employees[$workshifts[$key]][KG]) + (strval(floatval($row['weight_cut']) * floatval($row['worktime']) / floatval($row['worktime_cut'])) / 1000);
                }
            }
            
            if($row['unit'] == PIECES) {
                if(key_exists($key, $workshifts) && key_exists($workshifts[$key], $employees)) {
                    if(!key_exists(PIECES, $employees[$workshifts[$key]])) {
                        $employees[$workshifts[$key]][PIECES] = 0.0;
                    }
                    
                    $employees[$workshifts[$key]][PIECES] = floatval($employees[$workshifts[$key]][PIECES]) + (strval($length_cut * floatval($row['worktime']) / floatval($row['worktime_cut'])) / 1000);
                }
            }
        }*/
    }
    
    $activeSheetIndex++;
}

// Подсчёт всего
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex($activeSheetIndex);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("₽");

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);

$sheet->setCellValue('B1', "Тонна ₽");
$sheet->setCellValue('C1', "Км ₽");
$sheet->setCellValue('A2', "Тариф");
$sheet->getStyle('B2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$sheet->setCellValue('B2', '0');
$sheet->getStyle('C2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$sheet->setCellValue('C2', '0');
$sheet->setCellValue('D3', "Итого ₽");

$row_number = 4;

/*foreach($employees_sorted as $employee_id) {
    if(key_exists($employee_id, $employees) && in_array($employee_id, $workshifts)) {
        $sheet->setCellValue('A'.$row_number, $employees[$employee_id]['last_name'].' '.$employees[$employee_id]['first_name']);
        
        if(key_exists(KG, $employees[$employee_id]) && !empty($employees[$employee_id][KG])) {
            $sheet->getStyle('B'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->setCellValue('B'.$row_number, strval($employees[$employee_id][KG]));
        }
        
        if(key_exists(PIECES, $employees[$employee_id]) && !empty($employees[$employee_id][PIECES])) {
            $sheet->getStyle('C'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->setCellValue('C'.$row_number, strval($employees[$employee_id][PIECES]));
        }
        
        $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('D'.$row_number, '=PRODUCT(B2,B'.$row_number.')+PRODUCT(C2,C'.$row_number.')');
        
        $row_number++;
    }
}*/

// Сохранение
$filename = "Резчики_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xlsx";
    
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>