<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'add_not_take_stream_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $calculation_stream_id = filter_input(INPUT_POST, 'calculation_stream_id');
    $weight = filter_input(INPUT_POST, 'weight');
    $length = filter_input(INPUT_POST, 'length');
    $location = filter_input(INPUT_POST, 'php_self');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $location_get = array();
    
    foreach($_POST as $key=>$value) {
        if(mb_substr($key, 0, 4) == 'get_' && mb_strlen($key) > 4) {
            $location_get[mb_substr($key, 4)] = $value;
        }
    }
    
    unset($location_get['stream_id']);
    unset($location_get['take_stream_id']);
    unset($location_get['take_id']);
    unset($location_get['invalid_take']);
    unset($location_get['invalid_not_take']);
    unset($location_get['error_message']);
    
    if(empty($weight)) {
        unset($location_get['not_take_stream_id']);
        $location_get['invalid_not_take'] = 1;
        header('Location: '.$location."?".http_build_query($location_get)."&error_message=".urlencode('Невалидные данные'));
        exit();
    }
    
    // Если не было сделано ни одного съёма, то нет исходных данных, по которым рассчитывать длину нового ролика.
    // Значит выдаём ошибку: Сначала создайте хотя бы один ролик из съёма с таким названием.
    if($length == 0) {
        unset($location_get['not_take_stream_id']);
        header('Location: '.$location."?". http_build_query($location_get)."&error_message=". urlencode('Сначала создайте хотя бы один ролик из съёма с таким названием').'#');
        exit();
    }
    
    // Текущий резчик
    
    // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
    // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
    // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
    $working_time = new DateTime();
    $working_hour = date('G');
    $working_shift = 'day';
    
    if($working_hour > 19 && $working_hour < 24) {
        $working_shift = 'night';
    }
    elseif($working_hour >= 0 && $working_hour < 8) {
        $working_shift = 'night';
        $working_time->modify("-1 day");
    }
    
    $machine_id = null;
    $sql = "select machine_id from plan_edition where work_id = ". WORK_CUTTING." and calculation_id = ".$id;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $machine_id = $row['machine_id'];
        
        if(!empty($machine_id)) {
            $employee_id = null;
            $sql = "select employee1_id from plan_workshift1 where date_format(date, '%d-%m-%Y')='".$working_time->format('d-m-Y')."' and shift = '$working_shift' and work_id = ". WORK_CUTTING." and machine_id = $machine_id";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $employee_id = $row[0];
            }
        }
    }
    
    // Сохраняем рулон не из съёма
    if($employee_id == null) {
        $employee_id = "NULL";
    }
    
    $sql = "insert into calculation_not_take_stream (calculation_stream_id, weight, length, printed, plan_employee_id) values ($calculation_stream_id, $weight, $length, now(), $employee_id)";
    $executer = new Executer($sql);
    $not_take_stream_id = $executer->insert_id;
    
    $location_get['not_take_stream_id'] = $not_take_stream_id;
    
    header('Location: '.$location."?".http_build_query($location_get).'#not_take');
}
?>