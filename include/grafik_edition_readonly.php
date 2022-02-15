<?php
class GrafikEditionReadonly {
    public function __construct(DateTime $date, $shift, GrafikReadonly $grafik, $day_shifts_count, $night_shifts_count, $edition_key, $edition) {
        $this->date = $date;
        $this->shift = $shift;
        $this->grafik = $grafik;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
        $this->day_shifts_count = $day_shifts_count;
        $this->night_shifts_count = $night_shifts_count;
    }
    
    private DateTime $date;
    private $shift;
    private GrafikReadonly $grafik;
    private $day_shifts_count;
    private $night_shifts_count;
    private $edition_key;
    private $edition;
    
    function Show() {
        $top = 'nottop';
        if($this->shift == 'day') {
            $top = 'top';
        }
        
        $total_shifts_count = $this->day_shifts_count + $this->night_shifts_count;
        include 'show_grafik_edition_readonly.php';
    }
}
?>