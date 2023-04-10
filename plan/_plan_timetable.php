<?php
require_once '../include/topscripts.php';
require_once './_plan_date.php';

class PlanTimetable {
    private $machineId;
    private $dateFrom;
    private $dateTo;
    
    public $machine;
    public $plan_dates = array();

    public function __construct($machineId, $dateFrom, $dateTo) {
        $this->machineId = $machineId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Получение данных
        $sql = "select m.shortname machine from machine m where id = $machineId";
        $fetcher = new Fetcher($sql);
        while($row = $fetcher->Fetch()) {
            $this->machine = $row['machine'];
        }
        
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
        
        foreach ($period as $date) {
            $str_date = $date->format('Y-m-d');
            
            $plan_date = new PlanDate($date, $this);
            array_push($this->plan_dates, $plan_date);
        }
    }
    
    public function Show() {
        include './_plan_timetable_view.php';
    }
}
?>