<?php
class PlanEdition {
    private $date;
    private $shift;
    private $timetable;
    private $edition_key;
    private $edition;
    private $date_editions_count;
    private $shift_editions_count;
    
    public function __construct(DateTime $date, $shift, PlanTimetable $timetable, $edition_key, $edition, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->timetable = $timetable;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    function Show() {
        $from = $this->timetable->dateFrom->format('Y-m-d');
        $to = $this->timetable->dateTo->format('Y-m-d');
        
        include './_plan_edition_view.php';
    }
}
?>