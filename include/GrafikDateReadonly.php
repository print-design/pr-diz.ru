<?php
include 'GrafikDateshiftReadonly.php';

class GrafikDateReadonly {
    public function __construct(DateTime $date, GrafikReadonly $grafik, $day_editions, $night_editions) {
        $this->date = $date;
        $this->grafik = $grafik;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    private $date;
    private GrafikReadonly $grafik;
    private $day_editions;
    private $night_editions;
    
    function Show() {
        $day_shift = new GrafikDateshiftReadonly($this->date, 'day', $this->grafik, $this->day_editions);
        $day_shift->Show();
        $night_shift = new GrafikDateshiftReadonly($this->date, 'night', $this->grafik, $this->night_editions);
        $night_shift->Show();
    }
}
?>