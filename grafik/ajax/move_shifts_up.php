<?php
include '../include/topscripts.php';

$direction = filter_input(INPUT_GET, 'direction');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$shift_from = filter_input(INPUT_GET, 'shift_from');
$to = filter_input(INPUT_GET, 'to');
$shift_to = filter_input(INPUT_GET, 'shift_to');
$days = filter_input(INPUT_GET, 'days');
$half = filter_input(INPUT_GET, 'half');

$days_1 = intval($days) + 1;

$where_to = '';
if(!empty($to)) {
    if($shift_to == 'day') {
        $where_to = " and (date < '$to' or (date = '$to' and shift = 'day'))";
        }
        else if($shift_to == 'night') {
            $where_to = " and date <= '$to'";
        }
}

if($shift_from == 'day') {
    if($half == true) {
        $sql = "update workshift set date = if(shift = 'day', date_add(date, interval -$days_1 day), date_add(date, interval -$days day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $machine_id and date >= '$from'$where_to";
        $error_message = (new Executer($sql))->error;
    }
    else {
        $sql = "update workshift set date = date_add(date, interval -$days day) where machine_id = $machine_id and date >= '$from'$where_to";
        $error_message = (new Executer($sql))->error;
    }
}
else if($shift_from == 'night') {
    if($half == true) {
        $sql = "update workshift set date = if(shift = 'day', date_add(date, interval -$days_1 day), date_add(date, interval -$days day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $machine_id and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
        $error_message = (new Executer($sql))->error;
    }
    else {
        $sql = "update workshift set date = date_add(date, interval -$days day) where machine_id = $machine_id and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
        $error_message = (new Executer($sql))->error;
    }
}
?>