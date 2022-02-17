<?php
include 'GrafikEdition.php';

class GrafikShift {
    public function __construct(DateTime $date, $shift, $shift_data, GrafikMachine $machine, $editions, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->shift_data = $shift_data;
        $this->machine = $machine;
        $this->editions = $editions;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    private DateTime $date;
    private $shift;
    private $shift_data;
    private GrafikMachine $machine;
    private $editions;
    private $date_editions_count;
    private $shift_editions_count;
    
    function Show() {
        if(count($this->editions) == 0) {
            $top = 'nottop';
            if($this->shift == 'day') {
                $top = 'top';
            }
            include 'grafik_shift.php';
        }
        else {
            foreach($this->editions as $key => $value) {
                $edition = new GrafikEdition($this->date, $this->shift, $this->shift_data, $this->machine, $key, $value, $this->date_editions_count, $this->shift_editions_count);
                $edition->Show();
            }
        }
    }
}
?>