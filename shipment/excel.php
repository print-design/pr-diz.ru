<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    include '../include/_unauthorized.php';
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$sql = "select id, document_number, vehicle_number, cargo_type, places_count, gross_weight, net_weight, volume, created_at, is_draft "
        . "from shipment where id = ?";
$fetcher = new Fetcher($sql, [$id]);
$shipment = $fetcher->Fetch();

// Выгрузка черновиков запрещена -- документ уже недействителен
if(!$shipment || $shipment['is_draft']) {
    header("Location: index.php");
    exit();
}

// Ответственные (упаковщицы, установившие последний статус) и заказчики по всем заказам
// отгрузки -- каждое имя перечисляется только один раз
$sql = "select distinct u.last_name, u.first_name "
        . "from shipment_calculation sc "
        . "inner join calculation c on sc.calculation_id = c.id "
        . "left join calculation_status_history csh on csh.id = (select id from calculation_status_history where calculation_id = c.id order by date desc limit 1) "
        . "left join user u on csh.user_id = u.id "
        . "where sc.shipment_id = ? and u.id is not null";
$grabber = new Grabber($sql, [$id]);
$responsible_names = array();
foreach($grabber->result as $row) {
    $responsible_names[] = trim($row['last_name'].' '.$row['first_name']);
}

$sql = "select distinct cus.name "
        . "from shipment_calculation sc "
        . "inner join calculation c on sc.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "where sc.shipment_id = ?";
$grabber = new Grabber($sql, [$id]);
$customer_names = array();
foreach($grabber->result as $row) {
    $customer_names[] = $row['name'];
}

$created_at = DateTime::createFromFormat('Y-m-d H:i:s', $shipment['created_at']);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Акт-отчёт');

$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(50);

$sheet->mergeCells('A1:B1');
$sheet->setCellValue('A1', 'Акт-отчёт №'.$shipment['id'].' о передаче данных по отгрузке');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rows = array(
    array('Дата создания отгрузки', $created_at->format('d.m.Y H:i')),
    array('Ответственный', implode(', ', $responsible_names)),
    array('Документ', $shipment['document_number']),
    array('Контрагент', implode(', ', $customer_names)),
    array('Транспортное средство', $shipment['vehicle_number']),
    array('Наименование груза', CARGO_TYPE_NAMES[$shipment['cargo_type']] ?? ''),
    array('Количество мест', DisplayNumber(intval($shipment['places_count']), 0)),
    array('Масса брутто', DisplayNumber(floatval($shipment['gross_weight']), 0).' кг'),
    array('Масса нетто', DisplayNumber(floatval($shipment['net_weight']), 0).' кг'),
    array('Объём', DisplayNumber(floatval($shipment['volume']), 2).' м3'),
);

$row_number = 3;

foreach($rows as $row_data) {
    $sheet->setCellValue('A'.$row_number, $row_data[0]);
    $sheet->getStyle('A'.$row_number)->getFont()->setBold(true);
    $sheet->setCellValue('B'.$row_number, $row_data[1]);
    $sheet->getStyle('A'.$row_number.':B'.$row_number)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
    $row_number++;
}

$row_number++;
$sheet->mergeCells('A'.$row_number.':B'.$row_number);
$sheet->setCellValue('A'.$row_number, 'Способ определения массы: Данные складского учёта (расчётный)');
$row_number += 2;
$sheet->mergeCells('A'.$row_number.':B'.$row_number);
$sheet->setCellValue('A'.$row_number, 'Подпись кладовщика: ______________________');

// Сохранение
$filename = 'Акт-отчёт №'.$shipment['id'].'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
