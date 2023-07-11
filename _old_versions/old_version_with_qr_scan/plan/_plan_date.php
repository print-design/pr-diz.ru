<?php
require_once './_plan_timetable.php';
require_once './_plan_shift.php';

class PlanDate {
    private $date;
    private $timetable;
    private $day_editions;
    private $night_editions;

    public function __construct(DateTime $date, PlanTimetable $timetable, $day_editions, $night_editions) {
        $this->date = $date;
        $this->timetable = $timetable;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    function Show() {
        $day_editions_count = (count($this->day_editions) > 0 ? count($this->day_editions) : 1);
        $night_editions_count = (count($this->night_editions) > 0 ? count($this->night_editions) : 1);
        
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
            $day_editions_count = (count($this->day_editions) > 0 && end($this->day_editions)['has_continuation']) ? count($this->day_editions) : count($this->day_editions) + 1;
            $night_editions_count = (count($this->night_editions) > 0 && end($this->night_editions)['has_continuation']) ? count($this->night_editions) : count($this->night_editions) + 1;            
        }
        
        $date_editions_count = ($day_editions_count == 0 ? 1 : $day_editions_count) + ($night_editions_count == 0 ? 1 : $night_editions_count);
        
        $day_shift = new PlanShift($this->date, 'day', $this->timetable, $this->day_editions, $date_editions_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new PlanShift($this->date, 'night', $this->timetable, $this->night_editions, $date_editions_count, $night_editions_count);
        $night_shift->Show();
    }
    
    function Print() {
        $day_editions_count = (count($this->day_editions) > 0 ? count($this->day_editions) : 1);
        $night_editions_count = (count($this->night_editions) > 0 ? count($this->night_editions) : 1);
        $date_editions_count = ($day_editions_count == 0 ? 1 : $day_editions_count) + ($night_editions_count == 0 ? 1 : $night_editions_count);
        
        $day_shift = new PlanShift($this->date, 'day', $this->timetable, $this->day_editions, $date_editions_count, $day_editions_count);
        $day_shift->Print();
        
        $night_shift = new PlanShift($this->date, 'night', $this->timetable, $this->night_editions, $date_editions_count, $night_editions_count);
        $night_shift->Print();
    }
}
?>