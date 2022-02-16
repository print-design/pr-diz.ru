<?php
include 'GrafikShiftReadonly.php';

class GrafikDateReadonly {
    public function __construct(DateTime $date, GrafikReadonly $grafik, $day_data, $night_data, $day_editions, $night_editions) {
        $this->date = $date;
        $this->grafik = $grafik;
        $this->day_data = $day_data;
        $this->night_data = $night_data;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    private $date;
    private GrafikReadonly $grafik;
    private $day_data;
    private $night_data;
    private $day_editions;
    private $night_editions;
    
    function Show() {
        $day_editions_count = count($this->day_editions);
        $night_editions_count = count($this->night_editions);
        $date_editionts_count = $day_editions_count + $night_editions_count;
        
        $day_shift = new GrafikShiftReadonly($this->date, 'day', $this->day_data, $this->grafik, $this->day_editions, $date_editionts_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new GrafikShiftReadonly($this->date, 'night', $this->night_data, $this->grafik, $this->night_editions, $date_editionts_count, $night_editions_count);
        $night_shift->Show();
    }
}
?>