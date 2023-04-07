<?php
class PlanTimetable {
    private $machineId;
    private $dateFrom;
    private $dateTo;


    public function __construct($machineId, $dateFrom, $dateTo) {
        $this->machineId = $machineId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Список дат и смен
        if($this->dateFrom < $this->dateTo) {
            $date_diff = $this->dateFrom->diff($this->dateTo);
            $interval = DateInterval::createFromDateString("1 day");
            $period = new DatePeriod($this->dateFrom, $interval, $date_diff->days);
        }
        else {
            $period = array();
            array_push($period, $this->dateFrom);
        }
        
        $plan_dates = array();
        
        foreach ($period as $date) {
            $str_date = $date->format('Y-m-d');
        }
    }
    
    public function Show() {
        include './_plan_timetable_view.php';
    }
}
?>