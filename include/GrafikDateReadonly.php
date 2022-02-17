<?php
include 'GrafikShiftReadonly.php';

class GrafikDateReadonly {
    public function __construct(DateTime $date, GrafikMachineReadonly $machine, $day_data, $night_data, $day_editions, $night_editions) {
        $this->date = $date;
        $this->machine = $machine;
        $this->day_data = $day_data;
        $this->night_data = $night_data;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    private $date;
    private GrafikMachineReadonly $machine;
    private $day_data;
    private $night_data;
    private $day_editions;
    private $night_editions;
    
    function Show() {
        $day_editions_count = count($this->day_editions);
        $night_editions_count = count($this->night_editions);
        $date_editionts_count = $day_editions_count + $night_editions_count;
        
        $day_shift = new GrafikShiftReadonly($this->date, 'day', $this->day_data, $this->machine, $this->day_editions, $date_editionts_count, $day_editions_count);
        $day_shift->Show();
        
        $night_shift = new GrafikShiftReadonly($this->date, 'night', $this->night_data, $this->machine, $this->night_editions, $date_editionts_count, $night_editions_count);
        $night_shift->Show();
    }
}
?>