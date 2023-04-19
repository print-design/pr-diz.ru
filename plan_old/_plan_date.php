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
        $day_editions_count = count($this->day_editions);
        $day_timespan = 0;
        foreach($this->day_editions as $day_edition) {
            $day_timespan += $day_edition['timespan'];
        }
        if($day_editions_count > 0 && round($day_timespan, 2) < 12) {
            $day_editions_count += 1;
        }
        
        $night_editions_count = count($this->night_editions);
        $night_timespan = 0;
        foreach($this->night_editions as $night_edition) {
            $night_timespan += $night_edition['timespan'];
        }
        if($night_editions_count > 0 && round($night_timespan, 2) < 12) {
            $night_editions_count += 1;
        }
        
        $date_editions_count = ($day_editions_count == 0 ? 1 : $day_editions_count) + ($night_editions_count == 0 ? 1 : $night_editions_count);
        
        $day_shift = new PlanShift($this->date, 'day', $this->timetable, $this->day_editions, $date_editions_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new PlanShift($this->date, 'night', $this->timetable, $this->night_editions, $date_editions_count, $night_editions_count);
        $night_shift->Show();
    }
}
?>