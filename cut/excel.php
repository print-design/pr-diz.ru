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
    
foreach(CUTTERS as $cutter) {
    if($activeSheetIndex > 0) {
        $xls->createSheet();
    }
        
    $xls->setActiveSheetIndex($activeSheetIndex);
    $sheet = $xls->getActiveSheet();
    $sheet->setTitle(html_entity_decode(CUTTER_NAMES[$cutter]));
        
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
    
    $sql = "select id, first_name, last_name, role_id, active from plan_employee order by last_name, first_name";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()) {
        $employees[$row['id']] = array("first_name" => mb_substr($row['first_name'], 0, 1).'.', "last_name" => $row['last_name'], "role_id" => $row['role_id'], "active" => $row['active']);
    }
    
    // Тиражи
    $sql = "select e.id, e.date, e.shift, ". PLAN_TYPE_EDITION." as type, ifnull(e.worktime_continued, e.worktime) worktime, e.position, c.id calculation_id, c.name calculation, c.unit "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where e.work_id = ". WORK_CUTTING." and e.machine_id = ".$cutter." and e.date >= '".$date_from->format('Y-m-d')."' and e.date <= '".$date_to->format('Y-m-d')."' "
            . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0";
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        $rowindex++;
        
        $sheet->setCellValue('A'.$rowindex, DateTime::createFromFormat("Y-m-d", $row['date'])->format('d.m.Y'));
        $sheet->setCellValue('B'.$rowindex, $row['shift'] == "day" ? "День" : "Ночь");
    }
    
    /*
     * 
     * <?php
        $key = $this->date->format('Y-m-d').'_'.$this->shift;
        if(array_key_exists($key, $this->timetable->workshifts)) {
            $employee = $this->timetable->employees[$this->timetable->workshifts[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        }
        ?>
     * 
     $sql = "select e.id id, e.date, e.shift, ".PLAN_TYPE_EDITION." as type, if(isnull(e.worktime_continued), 0, 1) as has_continuation, ifnull(e.worktime_continued, e.worktime) worktime, e.position, e.comment, c.id calculation_id, c.name calculation, c.raport, c.length, c.status_id, c.cut_remove_cause, c.unit, c.quantity, "
                . "(select sum(quantity) from calculation_quantity where calculation_id = c.id) quantity_sum, "
                . "(select gap_raport from norm_gap where date <= c.date order by id desc limit 1) as gap_raport, "
                . "if(isnull(e.worktime_continued), round(cr.length_pure_1), round(cr.length_pure_1) / e.worktime * e.worktime_continued) as length_pure_1, "
                . "if(isnull(e.worktime_continued), round(cr.length_dirty_1), round(cr.length_dirty_1) / e.worktime * e.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_edition e "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ".$this->machine_id." and e.date >= '".$this->dateFrom->format('Y-m-d')."' and e.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select pc.id, pc.date, pc.shift, ".PLAN_TYPE_CONTINUATION." as type, pc.has_continuation, pc.worktime, 1 as position, pc.comment, c.id calculation_id, c.name calculation, c.raport, c.length, c.status_id, c.cut_remove_cause, c.unit, c.quantity, "
                . "0 as gap_raport, "
                . "0 as quantity_sum, "
                . "round(cr.length_pure_1) / e.worktime * pc.worktime as length_pure_1, "
                . "round(cr.length_dirty_1) / e.worktime * pc.worktime as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_continuation pc "
                . "inner join plan_edition e on pc.plan_edition_id = e.id "
                . "inner join calculation c on e.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where e.work_id = ".WORK_CUTTING." and e.machine_id = ".$this->machine_id." and pc.date >= '".$this->dateFrom->format('Y-m-d')."' and pc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select pp.id, pp.date, pp.shift, ".PLAN_TYPE_PART." as type, if(isnull(pp.worktime_continued), 0, 1) as has_continuation, ifnull(pp.worktime_continued, pp.worktime) worktime, pp.position, pp.comment, c.id calculation_id, c.name calculation, c.raport, c.length, c.status_id, c.cut_remove_cause, c.unit, 0 as quantity, "
                . "0 as gap_raport, "
                . "0 as quantity_sum, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_pure_1, "
                . "if(isnull(pp.worktime_continued), round(pp.length), round(pp.length) / pp.worktime * pp.worktime_continued) as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_part pp "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.in_plan = 1 and pp.work_id = ".WORK_CUTTING." and pp.machine_id = ".$this->machine_id." and pp.date >= '".$this->dateFrom->format('Y-m-d')."' and pp.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "union "
                . "select ppc.id, ppc.date, ppc.shift, ".PLAN_TYPE_PART_CONTINUATION." as type, ppc.has_continuation, ppc.worktime, 1 as position, ppc.comment, c.id calculation_id, c.name calculation, c.raport, c.length, c.status_id, '' as cut_remove_cause, '' as unit, 0 as quantity, "
                . "0 as gap_raport, "
                . "0 as quantity_sum, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_pure_1, "
                . "round(pp.length) / pp.worktime * ppc.worktime as length_dirty_1, "
                . "cr.width_1, c.work_type_id, c.customer_id, cus.name customer, c.manager_id, u.first_name, u.last_name, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) length_cut, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = c.id)), 0) "
                . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = c.id)), 0) weight_cut, "
                . "(select count(id) from calculation where customer_id = c.customer_id and id <= c.id) num_for_customer "
                . "from plan_part_continuation ppc "
                . "inner join plan_part pp on ppc.plan_part_id = pp.id "
                . "inner join calculation c on pp.calculation_id = c.id "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "inner join customer cus on c.customer_id = cus.id "
                . "inner join user u on c.manager_id = u.id "
                . "where pp.work_id = ".WORK_CUTTING." and pp.machine_id = ".$this->machine_id." and ppc.date >= '".$this->dateFrom->format('Y-m-d')."' and ppc.date <= '".$this->dateTo->format('Y-m-d')."' "
                . "and (select count(id) from calculation_stream where calculation_id = c.id) > 0 "
                . "order by date, shift, position";
     * 
     * 
     */
        
    $activeSheetIndex++;
}
    
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