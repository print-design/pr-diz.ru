<?php
include '../include/topscripts.php';

$user2 = filter_input(INPUT_GET, 'user2');
$machine_id = filter_input(INPUT_GET, 'machine_id');

if(!empty($user2)) {
    $user2 = addslashes($user2);
    $u_executer = new Executer("insert into user (fio, username) values ('$user2', CURRENT_TIMESTAMP())");
    $error_message = $u_executer->error;
    $user2_id = $u_executer->insert_id;

    if($user2_id > 0) {
        $role_id = filter_input(INPUT_GET, 'role_id');
        $r_executer = new Executer("insert into user_role (user_id, role_id) values ($user2_id, $role_id)");
        $error_message = $r_executer->error;
        
        if($r_executer->error == '') {
            $sql = '';
            $id = filter_input(INPUT_GET, 'id');
            
            if(!empty($id)) {
                $error_message = (new Executer("update workshift set user2_id=$user2_id where id=$id"))->error;
            }
            else {
                $date = filter_input(INPUT_GET, 'date');
                $shift = filter_input(INPUT_GET, 'shift');
                $sql = "insert into workshift (date, machine_id, shift, user2_id) values ('$date', $machine_id, '$shift', $user2_id)";
                $ws_executer = new Executer($sql);
                $error_message = $ws_executer->error;
                $workshift_id = $ws_executer->insert_id;
                
                if($workshift_id > 0) {
                    $error_message = (new Executer("insert into edition (workshift_id) values ($workshift_id)"))->error;
                }
            }
        }
    }
}
?>