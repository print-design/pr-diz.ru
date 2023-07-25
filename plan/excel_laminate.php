<?php
include '../include/topscripts.php';
require_once '../include/PHPExcel.php';
require_once '../PHPExcel/Writer/Excel5.php';

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

if(!empty($work_id) && !empty($machine_id)) {
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo($from, $to, $date_from, $date_to);
    
    $xls = new PHPExcel();
    $activeSheetIndex = 0;
    
    foreach(LAMINATORS as $laminator) {
        if($activeSheetIndex > 0) {
            $xls->createSheet();
        }
        
        $xls->setActiveSheetIndex($activeSheetIndex);
        $sheet = $xls->getActiveSheet();
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
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "0 as second_part "
                . "from plan_edition pe "
                . "inner join calculation c on pe.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "where pe.work_id = ".WORK_LAMINATION." and pe.machine_id = ".$laminator
                ." and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."' "
                . "union "
                . "select pp.date, pp.shift, pp.lamination, c.name, c.customer_id, c.lamination_roller_width, u.first_name, u.last_name, "
                . "cr.length_pure_2, cr.length_pure_3, cr.weight_pure_2, cr.weight_pure_3, cr.glue_cost_2, cr.glue_cost_3, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer, "
                . "(select count(id) from plan_part where calculation_id = pp.calculation_id and ((work_id = pp.work_id and machine_id = pp.machine_id and date < pp.date) || (work_id = pp.work_id and machine_id <> pp.machine_id and id < pp.id))) second_part "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "where pp.work_id = ".WORK_LAMINATION." and pp.machine_id = ".$laminator
                ." and pp.date >= '".$date_from->format('Y/m/d')."' and pp.date <= '".$date_to->format('Y/m/d')."' "
                . "order by date, shift";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $rowindex++;
            
            $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
            $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
            $sheet->setCellValue('C'.$rowindex, $row['last_name'].' '.$row['first_name']);
            $sheet->setCellValue('D'.$rowindex, $row['customer_id']."-".$row['num_for_customer']);
            $sheet->setCellValue('E'.$rowindex, $row['name']);
            
            if($row['second_part'] > 0) {
                $sheet->getCell('F'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('F'.$rowindex, 'Разделен');
                
                $sheet->getCell('G'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('G'.$rowindex, 'Разделен');
                
                $sheet->getCell('H'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('H'.$rowindex, 'Разделен');
                
                $sheet->getCell('I'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('I'.$rowindex, 'Разделен');
                
                $sheet->getCell('J'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('J'.$rowindex, 'Разделен');
            }
            else {
                $sheet->getStyle('F'.$rowindex.':M'.$rowindex)->getNumberFormat()->setFormatCode('#,##0');
                
                $sheet->getCell('F'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue('F'.$rowindex, $row['lamination'] == 1 ? $row['weight_pure_2'] : $row['weight_pure_3']);
                
                $sheet->getCell('G'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue('G'.$rowindex, $row['lamination'] == 1 ? $row['length_pure_2'] : $row['length_pure_3']);
                
                $sheet->getCell('H'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue('H'.$rowindex, $row['lamination_roller_width']);
                
                $sheet->getCell('I'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue('I'.$rowindex, $row['lamination']);
                
                $sheet->getCell('J'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $sheet->setCellValue('J'.$rowindex, $row['lamination'] == 1 ? $row['glue_cost_2'] : $row['glue_cost_3']);
            }
        }
        
        $activeSheetIndex++;
    }
    
    $filename = "Ламинация_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xls";
    
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