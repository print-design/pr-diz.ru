<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

if(!empty($work_id) && !empty($machine_id)) {
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo($from, $to, $date_from, $date_to);
    
    $spreadsheet = new Spreadsheet();
    $activeSheetIndex = 0;
    
    foreach(LAMINATORS as $laminator) {
        if($activeSheetIndex > 0) {
            $spreadsheet->createSheet();
        }
        
        $spreadsheet->setActiveSheetIndex($activeSheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(LAMINATOR_NAMES[$laminator]);
        
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
        $sheet->setCellValue('B'.$rowindex, "День/ночь");
        $sheet->setCellValue('C'.$rowindex, "Менеджер");
        $sheet->setCellValue('D'.$rowindex, "ID заказа");
        $sheet->setCellValue('E'.$rowindex, "Наименование");
        $sheet->setCellValue('F'.$rowindex, "Объем заказа");
        $sheet->setCellValue('G'.$rowindex, "Метраж");
        $sheet->setCellValue('H'.$rowindex, "Вал");
        $sheet->setCellValue('I'.$rowindex, "Номер ламинации");
        $sheet->setCellValue('J'.$rowindex, "Расходы на клей");
        
        $sql = "select pe.date, pe.shift, pe.lamination, c.name, c.customer_id, c.lamination_roller_width, u.first_name, u.last_name, "
                . "cr.length_pure_2, cr.length_pure_3, cr.weight_pure_2, cr.weight_pure_3, cr.glue_cost_2, cr.glue_cost_3, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition pe "
                . "inner join calculation c on pe.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "where pe.work_id = ".WORK_LAMINATION." and pe.machine_id = ".$laminator
                ." and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."' "
                . "order by date, shift";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $rowindex++;
            
            $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
            $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
            $sheet->setCellValue('C'.$rowindex, $row['last_name'].' '.$row['first_name']);
            $sheet->setCellValue('D'.$rowindex, $row['customer_id']."-".$row['num_for_customer']);
            $sheet->setCellValue('E'.$rowindex, $row['name']);
            
            $sheet->getCell('F'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('F'.$rowindex, $row['lamination'] == 1 ? $row['weight_pure_2'] : $row['weight_pure_3']);
                
            $sheet->getCell('G'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('G'.$rowindex, $row['lamination'] == 1 ? $row['length_pure_2'] : $row['length_pure_3']);
                
            $sheet->getCell('H'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('H'.$rowindex, $row['lamination_roller_width']);
                
            $sheet->getCell('I'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('I'.$rowindex, $row['lamination']);
                
            $sheet->getCell('J'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('J'.$rowindex, $row['lamination'] == 1 ? $row['glue_cost_2'] : $row['glue_cost_3']);
                
            $sheet->getStyle('F'.$rowindex.':I'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('J'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
        
        $activeSheetIndex++;
    }
    
    $filename = "Ламинация_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>
<html>
    <body>
        <h1 style="text-decoration: underline;">Чтобы выгрузить в Excel, надо нажать на кнопку "Выгрузка" в верхней правой части страницы.</h1>
    </body>
</html>