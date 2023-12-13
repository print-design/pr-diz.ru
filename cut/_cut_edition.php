<?php
class CutEdition {
    private $cut_shift;
    private $edition_key;
    private $edition;
    
    public function __construct(CutShift $cut_shift, $edition_key, $edition) {
        $this->cut_shift = $cut_shift;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
    }
    
    function Show() {
        $from = $this->cut_shift->timetable->dateFrom->format('Y-m-d');
        $to = $this->cut_shift->timetable->dateTo->format('Y-m-d');
        
        include './_cut_edition_view.php';
    }
}
?>