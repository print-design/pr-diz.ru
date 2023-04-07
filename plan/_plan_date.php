<?php
require_once './_plan_timetable.php';
require_once './_plan_shift.php';

class PlanDate {
    private $date;
    private $timetable;
    
    public function __construct(DateTime $date, PlanTimetable $timetable) {
        $this->date = $date;
        $this->timetable = $timetable;
    }
    
    function Show() {
        $day_editions_count = 0;
        $night_editions_count = 0;
        $date_editions_count = ($day_editions_count == 0 ? 1 : $day_editions_count) + ($night_editions_count == 0 ? 1 : $night_editions_count);
        
        $day_shift = new PlanShift($this->date, 'day', $this->timetable, $date_editions_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new PlanShift($this->date, 'night', $this->timetable, $date_editions_count, $night_editions_count);
        $night_shift->Show();
    }
}
?>