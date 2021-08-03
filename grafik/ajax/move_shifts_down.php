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

$sql = "select e.id, ws.date, ws.shift, ws.user1_id, ws.user2_id from edition e inner join workshift ws on e.workshift_id = ws.id where machine_id = $machine_id";

if($shift_from == 'day') {
    $sql .= " and date >= '$from'$where_to";
}
else if($shift_from == 'night') {
    $sql .= " and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
}

$grabber = new Grabber($sql);
$edition_ids = $grabber->result;

foreach($edition_ids as $edition_id) {
    $floor = floor($count / 2);
    $ceil = ceil($count / 2);
    $tail = $count - ($floor * 2);
    
    if($edition_id['shift'] == 'day') {
        $new_date = date_add(date_create($edition_id['date']), date_interval_create_from_date_string("$floor days"));
        
        if($tail == 0) {
            $new_shift = 'day';
        }
        else {
            $new_shift = 'night';
        }
    }
    elseif($edition_id['shift']) {
        if($tail == 0) {
            $new_date = date_add(date_create($edition_id['date']), date_interval_create_from_date_string("$floor days"));
            $new_shift = 'night';
        }
        else {
            $new_date = date_add(date_create($edition_id['date']), date_interval_create_from_date_string("$ceil days"));
            $new_shift = 'day';
        }
    }
    
    echo "<br />".$edition_id['id']." -- ".$edition_id['date']." -- ".$edition_id['shift']." -- ".$edition_id['user1_id']." --- ".$edition_id['user2_id']." --- ".date_format($new_date, 'Y-m-d')." --- ".$new_shift;
}
?>