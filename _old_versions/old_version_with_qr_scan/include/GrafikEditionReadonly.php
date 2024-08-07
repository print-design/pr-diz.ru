<?php
class GrafikEditionReadonly {
    public function __construct(DateTime $date, $shift, $shift_data, GrafikTimetableReadonly $timetable, $edition_key, $edition, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->shift_data = $shift_data;
        $this->timetable = $timetable;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    private $date;
    private $shift;
    private $shift_data;
    private $timetable;
    private $edition_key;
    private $edition;
    private $date_editions_count;
    private $shift_editions_count;
            
    function Show() {
        $top = 'nottop';
        if($this->shift == 'day' && $this->edition_key == 0) {
            $top = 'top';
        }
        
        include 'grafik_edition_readonly.php';
    }
}
?>