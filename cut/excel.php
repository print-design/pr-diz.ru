<?php
include '../include/topscripts.php';
require_once '../include/PHPExcel.php';
require_once '../PHPExcel/Writer/Excel5.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

/*if(empty($from)) {
    $diff5Days = new DateInterval('P5D');
    $now = new DateTime();
    $from = clone $now;
    $from->sub($diff5Days);
}*/

if(!empty($machine_id)) {
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo($from, $to, $date_from, $date_to);
    
    $xls = new PHPExcel();
    $activeSheetIndex = 0;
    
    $xls->setActiveSheetIndex($activeSheetIndex);
    $sheet = $xls->getActiveSheet();
    $sheet->setTitle(CUTTER_NAMES[$machine_id]);
    
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
    
    $filename = CUTTER_NAMES[$machine_id]."_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xls";
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
    $objWriter->save('php://output');
    exit();
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>