<?php
include 'GrafikShift.php';

class GrafikDate {
    public function __construct(DateTime $date, GrafikMachine $machine, $day_data, $night_data, $day_editions, $night_editions) {
        $this->date = $date;
        $this->machine = $machine;
        $this->day_data = $day_data;
        $this->night_data = $night_data;
        $this->day_editions = $day_editions;
        $this->night_editions = $night_editions;
    }
    
    private $date;
    private GrafikMachine $machine;
    private $day_data;
    private $night_data;
    private $day_editions;
    private $night_editions;
    
    function Show() {
        // Количество тиражей в день, в ночь и в целые сутки
        $day_editions_count = count($this->day_editions);
        $night_editions_count = count($this->night_editions);
        $date_editions_count = $day_editions_count + $night_editions_count;
        
        // Можно ли редактировать сегодня и завтра
        $date_diff_from_now = date_diff(new DateTime(), $this->date);
        $allow_edit_disabled = '';
        if(!$this->machine->allow_edit && $date_diff_from_now->days < 2 && !$this->machine->isCutter) {
            $allow_edit_disabled = " disabled='disabled'";
        }
        
        $day_shift = new GrafikShift($this->date, 'day', $this->day_data, $this->machine, $this->day_editions, $date_editions_count, $day_editions_count, $allow_edit_disabled);
        $day_shift->Show();
        
        $night_shift = new GrafikShift($this->date, 'night', $this->night_data, $this->machine, $this->night_editions, $date_editions_count, $night_editions_count, $allow_edit_disabled);
        $night_shift->Show();
    }
    
    function Print() {
        // Количество тиражей в день, в ночь и в целые сутки
        $day_editions_count = count($this->day_editions);
        $night_editions_count = count($this->night_editions);
        $date_editions_count = $day_editions_count + $night_editions_count;
        
        $day_shift = new GrafikShift($this->date, 'day', $this->day_data, $this->machine, $this->day_editions, $date_editions_count, $day_editions_count, 0);
        $day_shift->Print();
        
        $night_shift = new GrafikShift($this->date, 'night', $this->night_data, $this->machine, $this->night_editions, $date_editions_count, $night_editions_count, 0);
        $night_shift->Print();
    }
}
?>