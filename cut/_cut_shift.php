<?php
require_once './_cut_timetable.php';
require_once './_cut_edition.php';

class CutShift {
    public $date;
    public $shift;
    public $timetable;
    public $editions;
    public $date_editions_count;
    public $shift_editions_count;
    
    // Общее рабочее время
    public $shift_worktime = 0;
    
    // Последний ли это тираж в смене
    public $is_last = false;
    
    // Присутствует ли в этой смене допечатка
    public $includes_continuation = false;
    
    public function __construct(DateTime $date, $shift, CutTimetable $timetable, $editions, $date_editions_count, $shift_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->timetable = $timetable;
        $this->editions = $editions;
        $this->date_editions_count = $date_editions_count;
        $this->shift_editions_count = $shift_editions_count;
    }
    
    function Show() {
        if(count($this->editions) == 0) {
            include './_cut_shift_view.php';
        }
        else {
            foreach ($this->editions as $edition) {
                $this->shift_worktime += $edition['worktime'];
                
                if($edition['type'] == PLAN_TYPE_CONTINUATION || $edition['type'] == PLAN_TYPE_PART_CONTINUATION) {
                    $this->includes_continuation = true;
                }
            }
            
            // Отображаем тиражи
            $counter = 0;
            
            foreach($this->editions as $key => $value) {
                $this->is_last = false;
                $counter++;
                if($counter == count($this->editions)) {
                    $this->is_last = true;
                }
                
                $edition = new CutEdition($this, $key, $value);
                $edition->Show();
            }
        }
    }
}
?>