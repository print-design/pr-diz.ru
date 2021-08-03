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

$sql = "select e.id, e.workshift_id, ws.date, ws.shift, ws.user1_id, ws.user2_id from edition e inner join workshift ws on e.workshift_id = ws.id where machine_id = $machine_id";

if($shift_from == 'day') {
    $sql .= " and date >= '$from'$where_to";
}
else if($shift_from == 'night') {
    $sql .= " and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
}

$grabber = new Grabber($sql);
$editions = $grabber->result;

foreach($editions as $edition) {
    $floor = floor($count / 2);
    $ceil = ceil($count / 2);
    $tail = $count - ($floor * 2);
    
    if($edition['shift'] == 'day') {
        $new_date = date_add(date_create($edition['date']), date_interval_create_from_date_string("$floor days"))->format('Y-m-d');
        
        if($tail == 0) {
            $new_shift = 'day';
        }
        else {
            $new_shift = 'night';
        }
    }
    elseif($edition['shift'] == 'night') {
        if($tail == 0) {
            $new_date = date_add(date_create($edition['date']), date_interval_create_from_date_string("$floor days"))->format('Y-m-d');
            $new_shift = 'night';
        }
        else {
            $new_date = date_add(date_create($edition['date']), date_interval_create_from_date_string("$ceil days"))->format('Y-m-d');
            $new_shift = 'day';
        }
    }
    
    // Получаем id смены в новой дате. Если смены нет, создаём её;
    $workshift_id = null;
    
    $sql = "select id from workshift where machine_id=$machine_id and date='$new_date' and shift='$new_shift'";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    
    if(!empty($error_message)) {
        exit($error_message);
    }
    
    if($row = $fetcher->Fetch()) {
        $workshift_id = $row[0];
    }
    
    if($workshift_id == null) {
        $sql = "insert into workshift (date, machine_id, shift) values ('$new_date', $machine_id, '$new_shift')";
        $executer = new Executer($sql);
        $workshift_id = $executer->insert_id;
        $error_message = $executer->error;
    }
    
    if(!empty($error_message)) {
        exit($error_message);
    }
    
    // Присваиваеваем тиражу id новой смены
    $sql = "update edition set workshift_id = $workshift_id where id = ".$edition['id'];
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(!empty($error_message)) {
        exit($error_message);
    }
    
    // Если в прежней смене не было работников, удаляем смену
    // Если были, создаём пустую смену
    if(empty($edition['user1_id']) || empty($edition['user2_id'])) {
        $sql = "delete workshift where id = ".$edition['workshift_id'];
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    else {
        $sql = "insert into edition (workshift_id, position) values (".$edition['workshift_id'].", 1)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(!empty($error_message)) {
        exit($error_message);
    }
}
?>