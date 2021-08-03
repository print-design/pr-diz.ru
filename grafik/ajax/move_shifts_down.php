<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$shift_from = filter_input(INPUT_GET, 'shift_from');
$to = filter_input(INPUT_GET, 'to');
$shift_to = filter_input(INPUT_GET, 'shift_to');
$count = filter_input(INPUT_GET, 'count');

$count_1 = intval($count) + 1;

$where_to = '';
if(!empty($to)) {
    if($shift_to == 'day') {
        $where_to = " and (date < '$to' or (date = '$to' and shift = 'day'))";
    }
    else if($shift_to == 'night') {
        $where_to = " and date <= '$to'";
    }
}

$sql = "select e.id from edition e inner join workshift ws on e.workshift_id = ws.id where machine_id = $machine_id";

if($shift_from == 'day') {
    $sql .= " and date >= '$from'$where_to";
    //if($half == 'true') {
    //    $sql = "update workshift set date = if(shift = 'day', date_add(date, interval $count day), date_add(date, interval $count_1 day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $machine_id and date >= '$from'$where_to";
    //    $error_message = (new Executer($sql))->error;
    //}
    //else {
    //    $sql = "update workshift set date = date_add(date, interval $count day) where machine_id = $machine_id and date >= '$from'$where_to";
    //    $error_message = (new Executer($sql))->error;
    //}
}
else if($shift_from == 'night') {
    $sql .= " and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
    //if($half == 'true') {
    //    $sql = "update workshift set date = if(shift = 'day', date_add(date, interval $count day), date_add(date, interval $count_1 day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $machine_id and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
    //    $error_message = (new Executer($sql))->error;
    //}
    //else {
    //    $sql = "update workshift set date = date_add(date, interval $count day) where machine_id = $machine_id and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
    //    $error_message = (new Executer($sql))->error;
    //}
}
$grabber = new Grabber($sql);
print_r($grabber->result);
?>
<br /><br />
OK