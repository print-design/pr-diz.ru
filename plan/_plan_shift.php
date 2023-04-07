<?php
require_once './_plan_timetable.php';

class PlanShift {
    private $date;
    private $shift;
    private $timetable;
    private $date_editions_count;
    private $shift_editions_count;

    public function __construct(DateTime $date, $shift, PlanTimetable $timetable, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->timetable = $timetable;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    function Show() {
        $top = 'nottop';
        if($this->shift == 'day') {
            $top = 'top';
        }
        include './_plan_shift_view.php';
    }
}
?>