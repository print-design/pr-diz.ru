<?php
include 'GrafikEdition.php';

class GrafikShift {
    public function __construct(DateTime $date, $shift, $shift_data, GrafikTimetable $timetable, $editions, $date_editions_count, $shift_editions_count, $allow_edit_disabled) {
        $this->date = $date;
        $this->shift = $shift;
        $this->shift_data = $shift_data;
        $this->timetable = $timetable;
        $this->editions = $editions;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
        $this->allow_edit_disabled = $allow_edit_disabled;
    }
    private DateTime $date;
    private $shift;
    private $shift_data;
    private GrafikTimetable $timetable;
    private $editions;
    private $date_editions_count;
    private $shift_editions_count;
    private $allow_edit_disabled;
            
    function Show() {
        if(count($this->editions) == 0) {
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            
            $is_admin = IsInRole('admin');
            
            include 'grafik_shift.php';
        }
        else {
            foreach($this->editions as $key => $value) {
                $edition = new GrafikEdition($this->date, $this->shift, $this->shift_data, $this->timetable, $key, $value, $this->date_editions_count, $this->shift_editions_count, $this->allow_edit_disabled);
                $edition->Show();
            }
        }
    }
    
    function Print() {
        if(count($this->editions) == 0) {
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            
            include 'grafik_print_shift.php';
        }
        else {
            foreach($this->editions as $key => $value) {
                $edition = new GrafikEdition($this->date, $this->shift, $this->shift_data, $this->timetable, $key, $value, $this->date_editions_count, $this->shift_editions_count, $this->allow_edit_disabled);
                $edition->Print();
            }
        }
    }
}
?>