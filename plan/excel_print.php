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
    
    foreach(PRINTERS as $printer) {
        if($printer == PRINTER_ATLAS) {
            break;
        }
        
        if($activeSheetIndex > 0) {
            $xls->createSheet();
        }
        
        $xls->setActiveSheetIndex($activeSheetIndex);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle(PRINTER_NAMES[$printer]);
        
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
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        
        $rowindex = 1;
        
        $sheet->setCellValue('A'.$rowindex, "Дата");
        $sheet->setCellValue('B'.$rowindex, "День/Ночь");
        $sheet->setCellValue('C'.$rowindex, "Менеджер");
        $sheet->setCellValue('D'.$rowindex, "ID заказа");
        $sheet->setCellValue('E'.$rowindex, "Наименование");
        $sheet->setCellValue('F'.$rowindex, "Объём заказа, кг");
        $sheet->setCellValue('G'.$rowindex, "Метраж");
        $sheet->setCellValue('H'.$rowindex, "Красочность");
        $sheet->setCellValue('I'.$rowindex, "Рапорт");
        $sheet->setCellValue('J'.$rowindex, "Марка");
        $sheet->setCellValue('K'.$rowindex, "Толщина");
        $sheet->setCellValue('L'.$rowindex, "Ширина");
        $sheet->setCellValue('M'.$rowindex, "Кол-во ручьёв");
        $sheet->setCellValue('N'.$rowindex, "Ширина ручья");
        $sheet->setCellValue('O'.$rowindex, "Расходы на краску");
        $sheet->setCellValue('P'.$rowindex, "Себестоимость ПФ");
        $sheet->setCellValue('Q'.$rowindex, "Себестоимость");
        $sheet->setCellValue('R'.$rowindex, "Отгрузочная стоимость");
        $sheet->setCellValue('S'.$rowindex, "Итоговая прибыль");
        
        $sql = "select pe.date, pe.shift, pe.lamination, c.name, c.customer_id, c.ink_number, c.raport, c.streams_number, c.stream_width, c.streams_number * c.stream_width width, c.ski, c.width_ski, "
                . "f.name film, fv.thickness, "
                . "u.first_name, u.last_name, "
                . "cr.length_pure_1, cr.weight_pure_1, cr.ink_cost, cr.cliche_cost, cr.cost, cr.shipping_cost, "
                . "cr.income + cr.income_cliche + cr.income_knife as total_income, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition pe "
                . "inner join calculation c on pe.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join film_variation fv on c.film_variation_id = fv.id "
                . "inner join film f on fv.film_id = f.id "
                . "where pe.work_id = ".WORK_PRINTING." and pe.machine_id = ".$printer
                . " and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."' "
                . "order by date, shift";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $rowindex++;
            
            $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
            $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День": "Ночь");
            $sheet->setCellValue('C'.$rowindex, $row['last_name'].' '.$row['first_name']);
            $sheet->setCellValue('D'.$rowindex, $row['customer_id']."-".$row["num_for_customer"]);
            $sheet->setCellValue('E'.$rowindex, $row['name']);
            
            $sheet->getCell('F'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('F'.$rowindex, $row['weight_pure_1']);
            
            $sheet->getCell('G'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('G'.$rowindex, $row['length_pure_1']);
            
            $sheet->getCell('H'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('H'.$rowindex, $row["ink_number"]);
                
            $sheet->getCell('I'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('I'.$rowindex, $row['raport']);
                
            $sheet->getCell('J'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('J'.$rowindex, $row['film']);
                
            $sheet->getCell('K'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('K'.$rowindex, $row['thickness']);
                
            $sheet->getCell('L'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            if($row['ski'] == SKI_NONSTANDARD) {
                $sheet->setCellValue('L'.$rowindex, $row['width_ski']);
            }
            elseif ($row['ski'] == SKI_NO) {
                $sheet->setCellValue('L'.$rowindex, $row['width']);
            }
            else {
                $sheet->setCellValue('L'.$rowindex, strval($row['width'] + 20));
            }
                
            $sheet->getCell('M'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('M'.$rowindex, $row['streams_number']);
                
            $sheet->getCell('N'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('N'.$rowindex, $row['stream_width']);
            
            $sheet->getCell('O'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('O'.$rowindex, $row['ink_cost']);
            
            $sheet->getCell('P'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('P'.$rowindex, $row['cliche_cost']);
            
            $sheet->getCell('Q'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('Q'.$rowindex, $row['cost']);
            
            $sheet->getCell('R'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('R'.$rowindex, $row['shipping_cost']);
            
            $sheet->getCell('S'.$rowindex)->setDataType(PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValue('S'.$rowindex, $row['total_income']);
                
            $sheet->getStyle('F'.$rowindex.':H'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('I'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('K'.$rowindex.':N'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('O'.$rowindex.':S'.$rowindex)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
        
        $activeSheetIndex++;
    }
    
    $filename = "Печать_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xls";
    
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