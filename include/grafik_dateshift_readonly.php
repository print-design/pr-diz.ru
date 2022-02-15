<?php
class GrafikDateshiftReadonly {
    public function __construct(DateTime $date, $shift, GrafikReadonly $grafik, $editions) {
        $this->date = $date;
        $this->shift = $shift;
        $this->grafik = $grafik;
        $this->editions = $editions;
    }
    
    private DateTime $date;
    private $shift;
    private GrafikReadonly $grafik;
    private $editions;
            
    function Show() {
        $formatted_date = $this->date->format('Y-m-d');
        $key = $formatted_date. $this->shift;
        
        $day_shifts_count = 1;
        $night_shifts_count = 1;
        $total_shifts_count = $day_shifts_count + $night_shifts_count;
        
        $top = 'nottop';
        if($this->shift == 'day') {
            $top = 'top';
        }
        include 'show_grafik_dateshift_readonly.php';
    }
}
?>