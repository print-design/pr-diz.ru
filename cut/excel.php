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
    $sheet->getColumnDimension('J')->setAutoSize(true);
        
    $rowindex = 1;
    
    $sheet->setCellValue('A'.$rowindex, "Дата");
    $sheet->setCellValue('B'.$rowindex, "В плане");
    $sheet->setCellValue('C'.$rowindex, "День/Ночь");
    $sheet->setCellValue('D'.$rowindex, "ФИО резчика");
    $sheet->setCellValue('E'.$rowindex, "ID заказа");
    $sheet->setCellValue('F'.$rowindex, "Заказчик");
    $sheet->setCellValue('G'.$rowindex, "Заказ");
    $sheet->setCellValue('H'.$rowindex, "Кг/Шт");
    $sheet->setCellValue('I'.$rowindex, "Выполненный метраж");
    $sheet->setCellValue('J'.$rowindex, "Выполненная масса");
    
    // Тиражи
    $sql = "select distinct date(cts.printed) printed, ped.date, ped.shift, ped.position, pem.last_name, pem.first_name, c.customer_id, "
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
            . "select distinct date(cts.printed) printed, pc.date, pc.shift, 1 as position, pem.last_name, pem.first_name, c.customer_id, "
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
            . "order by printed, last_name, first_name";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $sheet->setCellValue('A'.(++$rowindex), DateTime::createFromFormat("Y-m-d", $row['printed'])->format('d.n.Y'));
        $sheet->setCellValue('B'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
        $sheet->setCellValue('C'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
        $sheet->setCellValue('D'.$rowindex, $row['last_name'].' '.(empty($row['first_name']) ? '' : mb_substr($row['first_name'], 0, 1).'.'));
        $sheet->setCellValue('E'.$rowindex, $row['customer_id'].'-'.$row['num_for_customer']);
        $sheet->setCellValue('F'.$rowindex, $row['customer']);
        $sheet->setCellValue('G'.$rowindex, $row['calculation']);
        $sheet->setCellValue('H'.$rowindex, $row['unit'] == 'kg' ? "Кг" : "Шт");
        $sheet->getStyle('I'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $sheet->setCellValue('I'.$rowindex, $row['length_cut']);
        $sheet->getStyle('J'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('J'.$rowindex, $row['weight_cut']);
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

$rowindex = 3;

/*$sql = "select distinct pem.id, pem.last_name, pem.first_name, "
        . "(select sum(cts1.weight) / 1000 "
        . "from calculation_take_stream cts1 "
        . "inner join calculation_stream cs1 on cts1.calculation_stream_id = cs1.id "
        . "inner join calculation c1 on cs1.calculation_id = c1.id "
        . "where cts1.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and cts1.plan_employee_id = pem.id and c1.unit = '".KG."') weight, "
        . "(select sum(cts1.length) / 1000 "
        . "from calculation_take_stream cts1 "
        . "inner join calculation_stream cs1 on cts1.calculation_stream_id = cs1.id "
        . "inner join calculation c1 on cs1.calculation_id = c1.id "
        . "where cts1.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' and cts1.plan_employee_id = pem.id and c1.unit = '".PIECES."') length "
        . "from calculation_take_stream cts "
        . "left join plan_employee pem on cts.plan_employee_id = pem.id "
        . "where cts.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' "
        . "order by pem.last_name, pem.first_name";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    $sheet->setCellValue('A'.(++$rowindex), $row['last_name'].' '.(empty($row['first_name']) ? '' : mb_substr($row['first_name'], 0, 1).'.'));
    $sheet->getStyle('B'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $sheet->setCellValue('B'.$rowindex, $row['weight']);
    $sheet->getStyle('C'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $sheet->setCellValue('C'.$rowindex, $row['length']);
    $sheet->getStyle('D'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $sheet->setCellValue('D'.$rowindex, '=(B2*B'.$rowindex.')+(C2*C'.$rowindex.')');
}*/

$sql = "select distinct pem.id, pem.last_name, pem.first_name "
        . "from calculation_take_stream cts "
        . "left join plan_employee pem on cts.plan_employee_id = pem.id "
        . "where cts.printed between '".$date_from->format('Y-m-d')."' and '".((clone $date_to)->add($diff1Day))->format('Y-m-d')."' "
        . "order by pem.last_name, pem.first_name";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    //
}

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