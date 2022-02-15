<?php
include 'grafik_edition_readonly.php';

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
        
        if(count($this->editions) == 0) {
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            include 'grafik_dateshift_readonly.php';
        }
        else {
            $day_shifts_count = count(array_filter($this->editions, function($edition){ return $edition['shift'] == 'day'; })); echo $day_shifts_count."<br />";
            $night_shifts_count = count(array_filter($this->editions, function($edition) { return $edition['shift'] == 'night'; })); echo $night_shifts_count."<br /><br />";
            
            foreach($this->editions as $key => $value) {
                $edition = new GrafikEditionReadonly($this->date, $this->shift, $this->grafik, $day_shifts_count, $night_shifts_count, $key, $value);
                $edition->Show();
            }
        }
    }
}
?>