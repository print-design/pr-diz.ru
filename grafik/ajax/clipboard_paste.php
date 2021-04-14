<?php
include '../include/topscripts.php';

$error_message = '';
$clipboard = filter_input(INPUT_GET, 'clipboard');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$machineId = filter_input(INPUT_GET, 'machine_id');

$row = json_decode($clipboard, true);

$name = $row['name'] == null ? 'NULL' : "'".addslashes($row['name'])."'";
$organization = $row['organization'] == null ? 'NULL' : "'".addslashes($row['organization'])."'";
$length = $row['length'] == null ? 'NULL' : "'".$row['length']."'";
$status_id = $row['status_id'] == null ? 'NULL' : "'".$row['status_id']."'";
$lamination_id = $row['lamination_id'] == null ? 'NULL' : "'".$row['lamination_id']."'";
$coloring = $row['coloring'] == null ? 'NULL' : "'".$row['coloring']."'";
$roller_id = $row['roller_id'] == null ? 'NULL' : "'".$row['roller_id']."'";
$manager_id = $row['manager_id'] == null ? 'NULL' : "'".$row['manager_id']."'";
$comment = $row['comment'] == null ? 'NULL' : "'".addslashes($row['comment'])."'";
$user1_id = $row['user1_id'] == null ? 'NULL' : "'".$row['user1_id']."'";
$user2_id = $row['user2_id'] == null ? 'NULL' : "'".$row['user2_id']."'";
$position = 1;

$workshift_id = filter_input(INPUT_POST, 'workshift_id');
if($workshift_id == null) {
    $sql = "insert into workshift (date, machine_id, shift, user1_id, user2_id) values ('$date', $machineId, '$shift', $user1_id, $user2_id)";
    $ws_executer = new Executer($sql);
    $error_message = $ws_executer->error;
    $workshift_id = $ws_executer->insert_id;
    
    if($workshift_id > 0) {
        $error_message = (new Executer($sql))->error;
    }
}

$direction_post = filter_input(INPUT_POST, 'direction');
$position_post = filter_input(INPUT_POST, 'position');
if($direction_post !== null && $position_post !== null) {
    if($direction_post == 'up') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position - 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position < $position_post"))->error;
        $position = intval($position_post) - 1;
    }
    
    if($direction_post == 'down') {
        $error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position + 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$machineId' and position > $position_post"))->error;
        $position = intval($position_post) + 1;
    }
}

$sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
        . "values ($name, $organization, $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, $comment, $workshift_id, $position)";
$executer = new Executer($sql);
$error_message = $executer->error;
$insert_id = $executer->insert_id;

// Информация о машине
$user1Name = '';
if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $user1Name = "Печатник";
}
if(in_array($machineId, [6, 13])) {
    $user1Name = "Ламинаторщик";
}
if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $user1Name = "Резчик";
}

$user2Name = '';
if(in_array($machineId, [1])) {
    $user2Name = "Помощник";
}

$userRole = 0;
if(in_array($machineId, [1])) {
    $userRole = 3;
}
if(in_array($machineId, [2])) {
    $userRole = 6;
}
if(in_array($machineId, [3])) {
    $userRole = 7;
}
if(in_array($machineId, [4])) {
    $userRole = 8;
}
if(in_array($machineId, [5])) {
    $userRole = 9;
}
if(in_array($machineId, [6, 13])) {
    $userRole = 4;
}
if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $userRole = 5;
}
   
$hasEdition = false;
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $hasEdition = true;
}

$hasOrganization = false;
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $hasOrganization = true;
}

$hasLength = false;
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $hasLength = true;
}

$hasStatus = false;
if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $hasStatus = true;
}

$hasRoller = false;
if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $hasRoller = true;
}

$hasLamination = false;
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 13])) {
    $hasLamination = true;
}

$hasColoring = false;
if(in_array($machineId, [1, 2, 3, 4, 5])) {
    $hasColoring = true;
}

$coloring = 0;
if(in_array($machineId, [2, 3, 5])) {
    $coloring = 6;
}
if(in_array($machineId, [1, 4])) {
    $coloring = 8;
}

$hasManager = false;
if(in_array($machineId, [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14])) {
    $hasManager = true;
}

$hasComment = false;
if(in_array($machineId, [6, 7, 9, 10, 11, 12, 13, 14])) {
    $hasComment = true;
}

$isCutter = false;
if(in_array($machineId, [7, 9, 10, 11, 12, 14])) {
    $isCutter = true;
}

// Получаем данные об этой смене и её тиражах
$date = "";
$shift = "";

$sql = "select ws.date, ws.shift, ws.user1_id, ws.user2_id, e.id edition_id, "
        . "(select count(e1.id) from edition e1 inner join workshift ws1 on e1.workshift_id=ws1.id where ws1.date = ws.date and ws1.shift = 'day') day_rowspan, "
        . "(select count(e1.id) from edition e1 inner join workshift ws1 on e1.workshift_id=ws1.id where ws1.date = ws.date and ws1.shift = 'night') night_rowspan "
        . "from workshift ws "
        . "inner join edition e on e.workshift_id=ws.id where ws.id=$workshift_id order by e.position";
$fetcher = new Fetcher($sql);
$position = 0;
$index = 1;
$top = 'nottop';
$rowspan = 0;
$my_rowspan = 0;

while ($row = $fetcher->Fetch()) {
    if($row['edition_id'] == $insert_id) {
        $date = $row['date'];
        $shift = $row['shift'];
        $day_rowspan = $row['day_rowspan'];
        $night_rowspan = $row['night_rowspan'];
        if($day_rowspan == 0) {
            $day_rowspan = 1;
        }
        if($night_rowspan == 0) {
            $night_rowspan = 1;
        }
        $rowspan = intval($day_rowspan) + intval($night_rowspan);
        $my_rowspan = $shift == 'day' ? $day_rowspan : $night_rowspan;

        $position = $index;
    }
    
    $index++;
}

if($position == 1 && $shift = 'day') {
    $top = 'top';
}

if(empty($error_message)) {
    include '../include/show_row.php';
}
else {
    echo $error_message;
}
?>