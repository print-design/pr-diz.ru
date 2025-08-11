<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

const FIRST_COLUMN_ID = 7;

const COLUMNS = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", 
    "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", 
    "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", 
    "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ", 
    "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI");

if(!empty($work_id) && !empty($machine_id)) {
    $date_from = null;
    $date_to = null;
    GetDateFromDateTo($from, $to, $date_from, $date_to);
    
    $spreadsheet = new Spreadsheet();
    $activeSheetIndex = 0;
    
    foreach(PRINTERS as $printer) {
        if($printer == PRINTER_ATLAS) {
            break;
        }
        
        if($activeSheetIndex > 0) {
            $spreadsheet->createSheet();
        }
        
        $spreadsheet->setActiveSheetIndex($activeSheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
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
        
        $sql = "select pe.date, pe.shift, pe.lamination, c.name, c.customer_id, c.individual_film_name, c.individual_thickness, c.ink_number, c.raport, c.streams_number, c.stream_width, c.streams_number * c.stream_width width, c.ski, c.width_ski, "
                . "f.name film, fv.thickness, "
                . "u.first_name, u.last_name, "
                . "cr.length_pure_1, cr.weight_pure_1, cr.ink_cost, cr.cliche_cost, cr.cost, cr.shipping_cost, "
                . "cr.income + cr.income_cliche + cr.income_knife as total_income, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition pe "
                . "inner join calculation c on pe.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
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
            
            $sheet->getCell('F'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('F'.$rowindex, $row['weight_pure_1']);
            
            $sheet->getCell('G'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('G'.$rowindex, $row['length_pure_1']);
            
            $sheet->getCell('H'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('H'.$rowindex, $row["ink_number"]);
                
            $sheet->getCell('I'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('I'.$rowindex, $row['raport']);
                
            $sheet->getCell('J'.$rowindex)->setDataType(DataType::TYPE_STRING);
            $sheet->setCellValue('J'.$rowindex, empty($row['film']) ? $row['individual_film_name'] : $row['film']);
                
            $sheet->getCell('K'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('K'.$rowindex, empty($row['thickness']) ? $row['individual_thickness'] : $row['thickness']);
                
            $sheet->getCell('L'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            if($row['ski'] == SKI_NONSTANDARD) {
                $sheet->setCellValue('L'.$rowindex, $row['width_ski']);
            }
            elseif ($row['ski'] == SKI_NO) {
                $sheet->setCellValue('L'.$rowindex, $row['width']);
            }
            else {
                $sheet->setCellValue('L'.$rowindex, strval($row['width'] + 20));
            }
                
            $sheet->getCell('M'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('M'.$rowindex, $row['streams_number']);
                
            $sheet->getCell('N'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('N'.$rowindex, $row['stream_width']);
            
            $sheet->getCell('O'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('O'.$rowindex, $row['ink_cost']);
            
            $sheet->getCell('P'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('P'.$rowindex, $row['cliche_cost']);
            
            $sheet->getCell('Q'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('Q'.$rowindex, $row['cost']);
            
            $sheet->getCell('R'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('R'.$rowindex, $row['shipping_cost']);
            
            $sheet->getCell('S'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('S'.$rowindex, $row['total_income']);
                
            $sheet->getStyle('F'.$rowindex.':H'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('I'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('K'.$rowindex.':N'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('O'.$rowindex.':S'.$rowindex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
        
        $activeSheetIndex++;
    }
    
    // Расчёт выработки
    foreach (PRINTERS as $printer) {
        if($printer == PRINTER_ATLAS) {
            break;
        }
        
        if($activeSheetIndex > 0) {
            $spreadsheet->createSheet();
        }
        
        $spreadsheet->setActiveSheetIndex($activeSheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('₽ '.PRINTER_NAMES[$printer]);
        
        // Основная часть
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        
        $rowindex = 1;
        
        $sheet->setCellValue('A'.$rowindex, "Дата");
        $sheet->setCellValue('B'.$rowindex, "День/Ночь");
        $sheet->setCellValue('C'.$rowindex, "ID заказа");
        $sheet->setCellValue('D'.$rowindex, "Наименование заказа");
        $sheet->setCellValue('E'.$rowindex, "Красочность");
        $sheet->setCellValue('F'.$rowindex, "Приладил");
        $sheet->setCellValue('G'.$rowindex, "Метраж заказа");
        $sheet->setCellValue('H'.$rowindex, "Всего отпечатано");
        
        $editions_count = 0;
        
        $sql = "select count(id) from plan_edition pe "
                . "where pe.work_id = ". WORK_PRINTING." and pe.machine_id = ".$printer
                . " and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."'";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $editions_count = $row[0];
        }
        
        $sql = "select pe.date, pe.shift, c.name, c.customer_id, c.ink_number, cr.length_pure_1, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition pe "
                . "inner join calculation c on pe.calculation_id = c.id "
                . "inner join user u on c.manager_id = u.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "left join film_variation fv on c.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "where pe.work_id = ". WORK_PRINTING." and pe.machine_id = ".$printer
                . " and pe.date >= '".$date_from->format('Y/m/d')."' and pe.date <= '".$date_to->format('Y/m/d')."' "
                . "order by date, shift";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $rowindex++;
            
            $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
            $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
            $sheet->setCellValue('C'.$rowindex, $row['customer_id']."-".$row["num_for_customer"]);
            $sheet->setCellValue('D'.$rowindex, $row['name']);
            
            $sheet->getCell('E'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('E'.$rowindex, $row['ink_number']);
            
            $sheet->setCellValue('F'.$rowindex, '');
            
            $sheet->getCell('G'.$rowindex)->setDataType(DataType::TYPE_NUMERIC);
            $sheet->setCellValue('G'.$rowindex, $row['length_pure_1']);
            
            $sheet->setCellValue('H'.$rowindex, '');
        }
        
        $sql = "select distinct pe.last_name "
                . "from plan_workshift1 pw "
                . "inner join plan_employee pe on pw.employee1_id = pe.id "
                . "where pw.work_id = ". WORK_PRINTING." and pw.machine_id = ".$printer
                . " and pw.date >= '".$date_from->format('Y/m/d')."' and pw.date <= '".$date_to->format('Y/m/d')."' "
                . "order by pe.last_name";
        $grabber = new Grabber($sql);
        $workers = $grabber->result;
        $workers_string = implode(",", array_column($workers, 'last_name'));
        
        $column_id = FIRST_COLUMN_ID;
        $first_worker_id = $column_id;
        $first_worker_id++;
        
        foreach($workers as $worker) {
            $sheet->getColumnDimension(COLUMNS[++$column_id])->setAutoSize(true);
            $sheet->setCellValue(COLUMNS[$column_id].'1', $worker['last_name']);
        }
        
        $last_worker_id = $column_id;
        
        // Приладил
        $editions_count++;
        for($i = 2; $i <= $editions_count; $i++) {
            $validation = $sheet->getCell('F'.$i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST );
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Ошибка ввода');
            $validation->setError('Значение не из списка.');
            $validation->setPromptTitle('Выберите из списка');
            $validation->setPrompt('Выберите значение из раскрывающегося списка.');
            $validation->setFormula1('"'.$workers_string.'"');
        }
        
        // Сумма "Метраж заказа"
        $sheet->setCellValue('G'.$i, "=SUM(G2:G".$editions_count.")");
        
        // Всего отпечатано
        for($i = 2; $i <= $editions_count; $i++) {
            $sheet->setCellValue('H'.$i, "=SUM(". COLUMNS[$first_worker_id].$i.':'. COLUMNS[$last_worker_id].$i.')');
        }
        
        // Сумма "Всего отпечатано"
        $sheet->setCellValue('H'.$i, "=SUM(H2:H".$editions_count.")");
        
        // ₽ за приладку 1 кр ₽↓
        $sheet->getStyle('F'.($editions_count + 4))->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)));
        $sheet->setCellValue('F'.($editions_count + 4), '₽ за приладку 1 кр ₽ ↓');
        
        // ₽ за печать 1 км ↓
        $sheet->getStyle('G'.($editions_count + 4))->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)));
        $sheet->setCellValue('G'.($editions_count + 4), '₽ за печать 1 км ↓');
        
        // Прилажено красок →
        $sheet->setCellValue('H'.($editions_count + 2), 'Прилажено красок →');
        
        $column_id = FIRST_COLUMN_ID;
        
        foreach($workers as $worker) {
            $sheet->setCellValue(COLUMNS[++$column_id].($editions_count + 2), '=SUMIF(F2:F'.$editions_count.','. COLUMNS[$column_id].'1,E2:E'.$editions_count.')');
        }
        
        // Отпечатано КМ →
        $sheet->setCellValue('H'.($editions_count + 3), 'Отпечатано КМ →');
        
        $column_id = FIRST_COLUMN_ID;
        
        foreach ($workers as $worker) {
            $sheet->setCellValue(COLUMNS[++$column_id].($editions_count + 3), '=SUM('. COLUMNS[$column_id].'2:'. COLUMNS[$column_id].$editions_count.')/1000');
        }
        
        // ₽ за КМ →
        $sheet->getStyle('H'.($editions_count + 4))->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)));
        $sheet->setCellValue('H'.($editions_count + 4), '₽ за КМ →');
        
        $column_id = FIRST_COLUMN_ID;
        
        foreach ($workers as $worker) {
            $sheet->setCellValue(COLUMNS[++$column_id].($editions_count + 4), "=". COLUMNS[$column_id].($editions_count + 3)."*G". ($editions_count + 5));
        }
        
        // ₽ за приладку →
        $sheet->getStyle('H'.($editions_count + 5))->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)));
        $sheet->setCellValue('H'.($editions_count + 5), '₽ за приладку →');
        
        $column_id = FIRST_COLUMN_ID;
        
        foreach ($workers as $worker) {
            $sheet->setCellValue(COLUMNS[++$column_id].($editions_count + 5), "=". COLUMNS[$column_id].($editions_count + 2)."*F".($editions_count + 5));
        }
        
        // Итого
        $sheet->getStyle('H'.($editions_count + 6))->applyFromArray(array('alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)));
        $sheet->setCellValue('H'.($editions_count + 6), 'Итого');
        
        $column_id = FIRST_COLUMN_ID;
        
        foreach ($workers as $worker) {
            $sheet->setCellValue(COLUMNS[++$column_id].($editions_count + 6), "=". COLUMNS[$column_id].($editions_count + 4)."+". COLUMNS[$column_id].($editions_count + 5));
        }
        
        $activeSheetIndex++;
    }
    
    // Все ₽
    if($activeSheetIndex > 0) {
        $spreadsheet->createSheet();
    }
    
    $spreadsheet->setActiveSheetIndex($activeSheetIndex);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Все ₽');
    
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    
    $sheet->setCellValue('B1', "За наклейку 1 ПФ ₽");
    $sheet->setCellValue('C1', "За КМ ₽");
    $sheet->setCellValue('A2', "Тариф →");
    $sheet->setCellValue('A3', "Печатники ↓");
    $sheet->getStyle('B2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $sheet->setCellValue('B2', '0');
    $sheet->getStyle('C2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $sheet->setCellValue('C2', '0');
    $sheet->setCellValue('D3', "Итого ₽");
    
    $row_number = 4;
    
    $sql = "select distinct pe.last_name, pe.first_name "
            . "from plan_workshift1 pw "
            . "inner join plan_employee pe on pw.employee1_id = pe.id "
            . "where pw.work_id = ". WORK_PRINTING." "
            . "and pw.date >= '".$date_from->format('Y/m/d')."' and pw.date <= '".$date_to->format('Y/m/d')."' "
            . "order by pe.last_name, pe.first_name";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $sheet->setCellValue('A'.$row_number, $row['last_name'].(empty($row['first_name']) ? '' : ' '. mb_substr($row['first_name'], 0, 1, 'UTF-8').'.'));
        $sheet->getStyle('B'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('C'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('D'.$row_number)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->setCellValue('D'.$row_number, '=PRODUCT(B2,B'.$row_number.')+PRODUCT(C2,C'.$row_number.')');
        $row_number++;
    }
    
    // Сохранение
    $filename = "Печать_".$date_from->format('Y-m-d')."_".$date_to->format('Y-m-d').".xlsx";
    
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