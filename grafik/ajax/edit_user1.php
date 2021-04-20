<?php print_r($_GET);
include '../include/topscripts.php';

$user1_id = filter_input(INPUT_GET, 'user1_id');
if($user1_id !== null) {
    if($user1_id == '') $user1_id = "NULL";
    $sql = '';
    $id = filter_input(INPUT_GET, 'id');

    if(!empty($id)) {
        $error_message = (new Executer("update workshift set user1_id=$user1_id where id=$id"))->error;
    }
    else {
        $date = filter_input(INPUT_GET, 'date');
        $shift = filter_input(INPUT_GET, 'shift');
        $machine_id = filter_input(INPUT_GET, 'machine_id');
        $sql = "insert into workshift (date, machine_id, shift, user1_id) values ('$date', $machine_id, '$shift', $user1_id)";
        $ws_executer = new Executer($sql);
        $error_message = $ws_executer->error;
        $workshift_id = $ws_executer->insert_id;
        
        if($workshift_id > 0) {
            $error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, 1)"))->error;
        }
    }
}
?>