<?php
require_once './_cut_timetable.php';
require_once './_cut_shift.php';

class CutDate {
    private $date;
    private $timetable;
    private $day_editions;
    private $night_editions;
    
    public function __construct(DateTime $date, CutTimetable $timetable, $day_editions, $night_editions) {
        $this->date = $date;
        $this->timetable = $timetable;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    function Show() {
        $day_editions_count = (count($this->day_editions) > 0 ? count($this->day_editions) : 1);
        $night_editions_count = (count($this->night_editions) > 0 ? count($this->night_editions) : 1);
        $date_editions_count = $day_editions_count + $night_editions_count;
        
        $day_shift = new CutShift($this->date, 'day', $this->timetable, $this->day_editions, $date_editions_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new CutShift($this->date, 'night', $this->timetable, $this->night_editions, $date_editions_count, $night_editions_count);
        $night_shift->Show();
    }
}
?>