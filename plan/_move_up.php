<?php
require_once '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');
$date = filter_input(INPUT_GET, 'date');
$shift = filter_input(INPUT_GET, 'shift');
$error = '';

$sql = "";

if($shift == 'day') {
    $sql = "update plan_edition pe set pe.position = pe.position + max("
            . "(select max(position) from plan_edition where shift = 'night' and date = date_add(pe.date, interval -1 day)), "
            . "(select max(position) from plan_event where shift = 'night' and date = date_add(pe.date, interval - day))) where pe.date >= '$date'";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plen_event pe set pe.position = pe.position = max("
                . "(select max(position) from plan_edition where shift = 'night' and date = date_add(pe.date, interval -1 day)), "
                . "(select max(position) from plan_event where shift = 'night' and date = date_add(pe.date, interval -1 day))) where pe.date >= '$date'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_edition set shift = 'night', date = date_add(date, interval -1 day) where date >= '$date'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_event set shift = 'night', date = date_add(date, interval -1 day) where date >= '$date'";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}
elseif($shift == 'night') {
    $sql = "update plan_edition pe set pe.position = pe.position + max("
            . "(select max(position) from plan_edition where shift = 'day' and date = '$date'), "
            . "(select max(position) from plan_event where shift = 'day' and date = '$date')) "
            . "where (pe.date = '$date' and pe.shift = 'night') or (pe.date > '$date')";
    $executer = new Executer($sql);
    $error = $executer->error;
    
    if(empty($error)) {
        $sql = "update plan_event pe set pe.position = pe.position + max("
                . "(select max(position) from plan_edition where shift = 'day' and date = '$date'), "
                . "(select max(position) from plan_event where shift = 'day' and date = '$date')) "
                . "where (pe.date = '$date' and pe.shift = 'night') or (pe.date > '$date')";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_edition set shift = 'day' "
                . "where (date = '$date' and shift = 'night') or (date > '$date')";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
    
    if(empty($error)) {
        $sql = "update plan_event set shift = 'day' "
                . "where (date = '$date' and shift = 'night') or (date > '$date')";
        $executer = new Executer($sql);
        $error = $executer->error;
    }
}

echo json_encode(array("error" => $error));
?>