<?php
require_once './_plan_timetable.php';
require_once './_plan_edition.php';
require_once './_types.php';

class PlanShift {
    public $date;
    public $shift;
    public $timetable;
    public $editions;
    public $date_editions_count;
    public $shift_editions_count;
    
    // Общее рабочее время
    public $shift_worktime = 0;
    
    // Последний ли это тираж в смене
    public $is_last = false;
    
    // Присутствует ли в этой смене допечатка
    public $includes_continuation = false;

    public function __construct(DateTime $date, $shift, PlanTimetable $timetable, $editions, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->timetable = $timetable;
        $this->editions = $editions;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    function Show() {
        if(count($this->editions) == 0) {
            include './_plan_shift_view.php';
        }
        else {
            foreach ($this->editions as $edition) {
                $this->shift_worktime += $edition['worktime'];
                
                if($edition['type'] == TYPE_CONTINUATION || $edition['type'] == TYPE_PART_CONTINUATION) {
                    $this->includes_continuation = true;
                }
            }
            
            // Проверяем, чтобы position увеличивались от одного тиража/события к другому (не было повторяющихся position)
            $previous_position = 0;
            
            foreach($this->editions as $key => $value)  {
                if($previous_position == $value['position']) {
                    if($value['type'] == TYPE_EVENT) {
                        $sql = "update plan_event set position = ifnull(position, 0) + 1 where id = ".$value['id'];
                        $executer = new Executer($sql);
                        $error = $executer->error;
                        
                        if(empty($error)) {
                            $sql = "select position from plan_event where id = ".$value['id'];
                            $fetcher = new Fetcher($sql);
                            if($row = $fetcher->Fetch()) {
                                $edition = $value;
                                $edition['position'] = $row['position'];
                                $this->editions[$key] = $edition;
                            }
                        }
                    }
                    elseif($value['type'] == TYPE_EDITION) {
                        $sql = "update plan_edition set position = ifnull(position, 0) + 1 where id = ".$value['id'];
                        $executer = new Executer($sql);
                        $error = $executer->error;
                        
                        if(empty($error)) {
                            $sql = "select position from plan_edition where id = ".$value['id'];
                            $fetcher = new Fetcher($sql);
                            if($row = $fetcher->Fetch()) {
                                $edition = $value;
                                $edition['position'] = $row['position'];
                                $this->editions[$key] = $edition;
                            }
                        }
                    }
                    elseif ($value['type'] == TYPE_PART) {
                        $sql = "update plan_part set position = ifnull(position, 0) + 1 where id = ".$value['id'];
                        $executer = new Executer($sql);
                        $error = $executer->error;
                        
                        if(empty($error)) {
                            $sql = "select position from plan_part where id = ".$value['id'];
                            $fetcher = new Fetcher($sql);
                            if($row = $fetcher->Fetch()) {
                                $edition = $value;
                                $edition['position'] = $row['position'];
                                $this->editions[$key] = $edition;
                            }
                        }
                    }
                }
                
                $previous_position = $this->editions[$key]['position'];
            }
            
            // Отображаем тиражи и события
            $counter = 0;
            
            foreach($this->editions as $key => $value) {
                $this->is_last = false;
                $counter++;
                if($counter == count($this->editions)) {
                    $this->is_last = true;
                }
                
                $edition = new PlanEdition($this, $key, $value);
                $edition->Show();
            }
            
            // Отображаем дополнительную строку (чтобы вставлять в неё новый тираж)
            $extra_count = $this->shift_editions_count - count($this->editions);
            
            for($i = 0; $i < $extra_count; $i++) {
                include './_plan_remainder_view.php';
            }
        }
    }
}
?>