<?php
class GrafikDateshiftReadonly {
    public function __construct(DateTime $date, $shift) {
        $this->date = $date;
        $this->shift = $shift;
    }
    
    private $date;
    private $shift;
    
    function Show() {
        $formatted_date = $this->date->format('Y-m-d');
        $key = $formatted_date. $this->shift;
        
        $day_shifts_count = 1;
        $night_shifts_count = 1;
        $total_shifts_count = $day_shifts_count + $night_shifts_count;
        include 'show_grafik_dateshift_readonly.php';
    }
}
?>