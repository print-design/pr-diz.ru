<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

function CreateDateShift(&$dateshift, $techmaps) {
    $str_date = $dateshift['date']->format('Y-m-d');
            
    $dateshift['techmaps'] = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists($dateshift['shift'], $techmaps[$str_date])) {
        $dateshift['techmaps'] = $techmaps[$str_date][$dateshift['shift']];
    }
            
    $day_techmaps = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists('day', $techmaps[$str_date])) {
        $day_techmaps = $techmaps[$str_date]['day'];
    }
    
    $night_techmaps = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists('night', $techmaps[$str_date])) {
        $night_techmaps = $techmaps[$str_date]['night'];
    }
            
    $day_rowspan = count($day_techmaps);
    if($day_rowspan == 0) $day_rowspan = 1;
    $night_rowspan = count($night_techmaps);
    if($night_rowspan == 0) $night_rowspan = 1;
    $dateshift['rowspan'] = $day_rowspan + $night_rowspan;
    $dateshift['my_rowspan'] = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
}

function ShowTechmap($techmap, $top, $dateshift) {
    include 'show_techmap.php';
}

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
            
// Список технологических карт
$techmaps = [];
            
$sql = "select t.id, t.work_date, t.work_shift, t.grafik_id, "
        . "c.name, c.unit, c.quantity, "
        . "c.brand_name, c.thickness, c.other_brand_name, c.other_thickness, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_other_brand_name, c.lamination1_other_thickness, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_other_brand_name, c.lamination2_other_thickness, "
        . "c.machine_id, cus.name customer, m.name machine "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "left join machine m on c.machine_id = m.id "
        . "where t.work_date >='".$date_from->format('Y-m-d')."' and t.work_date <= '".$date_to->format('Y-m-d')."' "
        . "order by t.id";
$fetcher = new Fetcher($sql);
while($item = $fetcher->Fetch()) {
    if(!array_key_exists($item['work_date'], $techmaps) || !array_key_exists($item['work_shift'], $techmaps[$item['work_date']])) $techmaps[$item['work_date']][$item['work_shift']] = [];
    array_push($techmaps[$item['work_date']][$item['work_shift']], $item);
}

// Список дат и смен
if($date_from < $date_to) {
    $date_diff = $date_from->diff($date_to);
    $interval = DateInterval::createFromDateString("1 day");
    $period = new DatePeriod($date_from, $interval, $date_diff->days);
}
else {
    $period = array();
    array_push($period, $date_from);
}
            
$dateshifts = array();

foreach ($period as $date) {
    $dateshift['date'] = $date;
    $dateshift['shift'] = 'day';
    $dateshift['top'] = 'top';
    CreateDateShift($dateshift, $techmaps);
    array_push($dateshifts, $dateshift);
        
    $dateshift['date'] = $date;
    $dateshift['shift'] = 'night';
    $dateshift['top'] = 'nottop';
    CreateDateShift($dateshift, $techmaps);
    array_push($dateshifts, $dateshift);
}

include 'show_page.php';
?>