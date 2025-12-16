<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Рулоны");

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

$sheet->setCellValue('A'.$rowindex, "Дата прихода");
$sheet->setCellValue('B'.$rowindex, "Марка пленки");
$sheet->setCellValue('C'.$rowindex, "Толщина");
$sheet->setCellValue('D'.$rowindex, "Ширина");
$sheet->setCellValue('E'.$rowindex, "Вес");
$sheet->setCellValue('F'.$rowindex, "Длина");
$sheet->setCellValue('G'.$rowindex, "Поставщик");
$sheet->setCellValue('H'.$rowindex, "ID рулона");
$sheet->setCellValue('I'.$rowindex, "№ ячейки");
$sheet->setCellValue('J'.$rowindex, "Комментарий");

$sql = "select r.id, DATE_FORMAT(r.date, '%d.%m.%Y') date, f.name film, fv.thickness, r.width, r.net_weight, r.length, "
        . "s.name supplier, (select cell from roll_cell_history where roll_id = r.id order by id desc limit 0, 1) cell, r.comment "
        . "from roll r "
        . "left join film_variation fv on r.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
        . "where (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.")"
        . "order by r.id desc";
$fetcher = new Fetcher($sql);
while ($row = $fetcher->Fetch()) {
    $rowindex++;
    
    $sheet->setCellValue('A'.$rowindex, $row['date']);
    $sheet->setCellValue('B'.$rowindex, $row['film']);
    $sheet->setCellValue('C'.$rowindex, $row['thickness']);
    $sheet->setCellValue('D'.$rowindex, $row['width']);
    $sheet->setCellValue('E'.$rowindex, $row['net_weight']);
    $sheet->setCellValue('F'.$rowindex, $row['length']);
    $sheet->setCellValue('G'.$rowindex, $row['supplier']);
    $sheet->setCellValue('H'.$rowindex, "Р".$row['id']);
    $sheet->setCellValue('I'.$rowindex, $row['cell']);
    $sheet->setCellValue('J'.$rowindex, $row['comment']);
}

$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1);
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Паллеты");

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
$sheet->getColumnDimension('K')->setAutoSize(true);
$sheet->getColumnDimension('L')->setAutoSize(true);

$rowindex = 1;

$sheet->setCellValue('A'.$rowindex, "Дата прихода");
$sheet->setCellValue('B'.$rowindex, "Марка пленки");
$sheet->setCellValue('C'.$rowindex, "Толщина");
$sheet->setCellValue('D'.$rowindex, "Ширина");
$sheet->setCellValue('E'.$rowindex, "Вес");
$sheet->setCellValue('F'.$rowindex, "Длина");
$sheet->setCellValue('G'.$rowindex, "Поставщик");
$sheet->setCellValue('H'.$rowindex, "ID паллета");
$sheet->setCellValue('I'.$rowindex, "Рулонов своб.");
$sheet->setCellValue('J'.$rowindex, "Рулонов исх.");
$sheet->setCellValue('K'.$rowindex, "№ ячейки");
$sheet->setCellValue('L'.$rowindex, "Комментарий");

$sql = "select p.id, DATE_FORMAT(p.date, '%d.%m.%Y') date, f.name film, fv.thickness, p.width, "
        . "(select sum(pr1.weight) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) net_weight, "
        . "(select sum(pr1.length) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) length, "
        . "s.name supplier, "
        . "(select count(pr1.id) from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) rolls_number_free, "
        . "(select count(pr1.id) from pallet_roll pr1 where pr1.pallet_id = p.id) rolls_number_total, "
        . "(select cell from pallet_cell_history where pallet_id = p.id order by id desc limit 0, 1) cell, p.comment "
        . "from pallet p "
        . "left join film_variation fv on p.film_variation_id = fv.id "
        . "left join film f on fv.film_id = f.id "
        . "left join supplier s on p.supplier_id = s.id "
        . "where p.id in (select pr1.pallet_id from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")) "
        . "order by p.id desc";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    $rowindex++;
    
    $sheet->setCellValue('A'.$rowindex, $row['date']);
    $sheet->setCellValue('B'.$rowindex, $row['film']);
    $sheet->setCellValue('C'.$rowindex, $row['thickness']);
    $sheet->setCellValue('D'.$rowindex, $row['width']);
    $sheet->setCellValue('E'.$rowindex, $row['net_weight']);
    $sheet->setCellValue('F'.$rowindex, $row['length']);
    $sheet->setCellValue('G'.$rowindex, $row['supplier']);
    $sheet->setCellValue('H'.$rowindex, "П".$row['id']);
    $sheet->setCellValue('I'.$rowindex, $row['rolls_number_free']);
    $sheet->setCellValue('J'.$rowindex, $row['rolls_number_total']);
    $sheet->setCellValue('K'.$rowindex, $row['cell']);
    $sheet->setCellValue('L'.$rowindex, $row['comment']);
}

$filename = "Склад_".(new DateTime())->format('Y-m-d').".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
<!DOCTYPE html>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>