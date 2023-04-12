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
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            
            include './_plan_shift_view.php';
        }
        else {
            foreach($this->editions as $key => $value) {
                $edition = new PlanEdition($this->date, $this->shift, $this->timetable, $key, $value, $this->date_editions_count, $this->shift_editions_count);
                $edition->Show();
            }
        }
    }
}
?>