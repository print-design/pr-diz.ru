<?php
include 'GrafikEditionReadonly.php';

class GrafikShiftReadonly {
    public function __construct(DateTime $date, $shift, GrafikReadonly $grafik, $editions, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->grafik = $grafik;
        $this->editions = $editions;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    private DateTime $date;
    private $shift;
    private GrafikReadonly $grafik;
    private $editions;
    private $date_editions_count;
    private $shift_editions_count;

    function Show() {
        $formatted_date = $this->date->format('Y-m-d');
        $key = $formatted_date. $this->shift;
        
        if(count($this->editions) == 0) {
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            include 'grafik_shift_readonly.php';
        }
        else {
            foreach($this->editions as $key => $value) {
                $edition = new GrafikEditionReadonly($this->date, $this->shift, $this->grafik, $key, $value, $this->date_editions_count, $this->shift_editions_count);
                $edition->Show();
            }
        }
    }
}
?>