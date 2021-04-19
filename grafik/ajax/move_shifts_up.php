<?php
$from = filter_input(INPUT_POST, 'move_shifts_from');
            $to = filter_input(INPUT_POST, 'move_shifts_to');
            $shift = filter_input(INPUT_POST, 'move_shifts_shift');
            $shift_to = filter_input(INPUT_POST, 'move_shifts_shift_to');
            $days = filter_input(INPUT_POST, 'days');
            $days_1 = intval($days) + 1;
            $half = filter_input(INPUT_POST, 'half');
            
            $where_to = '';
            if(!empty($to)) {
                if($shift_to == 'day') {
                    $where_to = " and (date < '$to' or (date = '$to' and shift = 'day'))";
                }
                else if($shift_to == 'night') {
                    $where_to = " and date <= '$to'";
                }
            }
            
            if($shift == 'day') {
                if($half == 'on') {
                    $sql = "update workshift set date = if(shift = 'day', date_add(date, interval -$days_1 day), date_add(date, interval -$days day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $this->machineId and date >= '$from'$where_to";
                    $this->error_message = (new Executer($sql))->error;
                    if(!empty($this->error_message)) {
                        echo $sql;
                        exit($this->error_message);
                    }
                }
                else {
                    $sql = "update workshift set date = date_add(date, interval -$days day) where machine_id = $this->machineId and date >= '$from'$where_to";
                    $this->error_message = (new Executer($sql))->error;
                    if(!empty($this->error_message)) {
                        echo $sql;
                        exit($this->error_message);
                    }
                }
            }
            else if($shift == 'night') {
                if($half == 'on') {
                    $sql = "update workshift set date = if(shift = 'day', date_add(date, interval -$days_1 day), date_add(date, interval -$days day)), shift = if(shift = 'day', 'night', 'day') where machine_id = $this->machineId and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
                    $this->error_message = (new Executer($sql))->error;
                    if(!empty($this->error_message)) {
                        echo $sql;
                        exit($this->error_message);
                    }
                }
                else {
                    $sql = "update workshift set date = date_add(date, interval -$days day) where machine_id = $this->machineId and (date > '$from' or (date = '$from' and shift = 'night'))$where_to";
                    $this->error_message = (new Executer($sql))->error;
                    if(!empty($this->error_message)) {
                        echo $sql;
                        exit($this->error_message);
                    }
                }
            }
?>