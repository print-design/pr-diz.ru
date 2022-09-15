<?php
class GrafikEdition {
    public function __construct(DateTime $date, $shift, $shift_data, GrafikTimetable $timetable, $edition_key, $edition, $date_editions_count, $shift_editions_count, $allow_edit_disabled) {
        $this->date = $date;
        $this->shift = $shift;
        $this->shift_data = $shift_data;
        $this->timetable = $timetable;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
        $this->allow_edit_disabled = $allow_edit_disabled;
    }
    
    private $date;
    private $shift;
    private $shift_data;
    private $timetable;
    private $edition_key;
    private $edition;
    private $date_editions_count;
    private $shift_editions_count;
    private $allow_edit_disabled;
            
    function Show() {
        $top = 'nottop';
        if($this->shift == 'day' && $this->edition_key == 0) {
            $top = 'top';
        }
        
        $from = $this->timetable->dateFrom->format("Y-m-d");
        $to = $this->timetable->dateTo->format("Y-m-d");
        
        $disabled = " disabled='disabled'";
        if($this->timetable->clipboard_db) {
            $disabled = '';
        }
        
        $is_admin = IsInRole('technologist', 'manager-senior');
        
        include 'grafik_edition.php';
    }
    
    function Print() {
        $top = 'nottop';
        if($this->shift == 'day' && $this->edition_key == 0) {
            $top = 'top';
        }
        
        include 'grafik_print_edition.php';
    }
}
?>