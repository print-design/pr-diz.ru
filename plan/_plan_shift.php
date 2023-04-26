<?php
require_once './_plan_timetable.php';
require_once './_plan_edition.php';

class PlanShift {
    private $date;
    private $shift;
    private $timetable;
    private $editions;
    private $date_editions_count;
    private $shift_editions_count;

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
            // Определяем общее рабочее время
            $shift_worktime = 0;
            
            foreach ($this->editions as $edition) {
                $shift_worktime += $edition['worktime'];
            }
            
            // Проверяем, чтобы position увеличивались от одного тиража/события к другому (не было повторяющихся position)
            $previous_position = 0;
            
            foreach($this->editions as $key => $value)  {
                if($previous_position == $value['position']) {
                    if($value['is_event']) {
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
                    else {
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
                }
                
                $previous_position = $this->editions[$key]['position'];
            }
            
            // Отображаем тиражи и события
            foreach($this->editions as $key => $value) {
                $edition = new PlanEdition($this->date, $this->shift, $this->timetable, $key, $value, $this->date_editions_count, $this->shift_editions_count, $shift_worktime);
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