<?php
class Grafik {
    public function __construct(DateTime $from, DateTime $to, $machine_id) {
        $this->dateFrom = $from;
        $this->dateTo = $to;
        $this->machineId = $machine_id;
        
        $sql = "select name, user1_name, user2_name, role_id, has_edition, has_organization, has_length, has_status, has_roller, has_lamination, has_coloring, coloring, has_manager, has_comment, is_cutter from machine where id = $machine_id";
        $fetcher = new Fetcher($sql);
        $this->error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $this->name = $row['name'];
            $this->user1Name = $row['user1_name'];
            $this->user2Name = $row['user2_name'];
            $this->userRole = $row['role_id'];
            $this->hasEdition = $row['has_edition'];
            $this->hasOrganization = $row['has_organization'];
            $this->hasLength = $row['has_length'];
            $this->hasStatus = $row['has_status'];
            $this->hasRoller = $row['has_roller'];
            $this->hasLamination = $row['has_lamination'];
            $this->hasColoring = $row['has_coloring'];
            $this->coloring = $row['coloring'];
            $this->hasManager = $row['has_manager'];
            $this->hasComment = $row['has_comment'];
            $this->isCutter = $row['is_cutter'];
        }
    }
    
    private $dateFrom;
    private $dateTo;
    private $machineId;
    
    public $name = '';
    public $user1Name = '';
    public $user2Name = '';
    public $userRole = 0;
    
    public $hasEdition = false;
    public $hasOrganization = false;
    public $hasLength = false;
    public $hasStatus = false;
    public $hasRoller = false;
    public $hasLamination = false;
    public $hasColoring = false;
    public $coloring = 0;
    public $hasManager = false;
    public $hasComment = false;
    public $isCutter = false;

    public $error_message = '';
    
    private $users1 = [];
    private $users2 = [];
    private $statuses = [];
    private $rollers = [];
    private $laminations = [];
    private $managers = [];

    function ShowPage() {
        // Проверяем, имеется ли что-нибудь в буфере обмена
        $clipboard_db = false;
        $sql = "select count(id) from clipboard";
        $row = (new Fetcher($sql))->Fetch();
        if($row[0] > 0) {
            $clipboard_db = true;
        }
        
        // Список работников №1
        if(IsInRole('admin') && $this->user1Name != '') {
            $this->users1 = (new Grabber('select u.id, u.fio from user u inner join user_role ur on ur.user_id = u.id where quit = 0 and ur.role_id = '. $this->userRole.' order by u.fio'))->result;
        }
        
        // Список работников №2
        if(IsInRole('admin') && $this->user2Name != '') {
            $this->users2 = (new Grabber('select u.id, u.fio from user u inner join user_role ur on ur.user_id = u.id where quit = 0 and ur.role_id = '. $this->userRole.' order by u.fio'))->result;
        }
        
        // Список статусов
        if(IsInRole('admin')) {
            $this->statuses = (new Grabber("select id, name from edition_status order by name"))->result;
        }
        
        // Список валов
        if(IsInRole('admin')) {
            $machine_id = $this->machineId;
            $this->rollers = (new Grabber("select id, name from roller where machine_id=$machine_id order by position, name"))->result;
        }
        
        // Список ламинаций
        if(IsInRole('admin')) {
            $sql = "select id, name from lamination where common = 1 order by sort";
            if($this->isCutter) {
                $sql = "select id, name from lamination where cutter = 1 order by sort";
            }
            $this->laminations = (new Grabber($sql))->result;
        }
                    
        // Список менеджеров
        if(IsInRole('admin')) {
            $this->managers = (new Grabber("select u.id, u.fio from user u inner join user_role ur on ur.user_id = u.id where ur.role_id = 2 order by u.fio"))->result;
        }
        
        // Список рабочих смен
        $all = array();
        $sql = "select ws.id, ws.date date, date_format(ws.date, '%d.%m.%Y') fdate, ws.shift, ws.machine_id, u1.id u1_id, u1.fio u1_fio, u2.id u2_id, u2.fio u2_fio, "
                . "(select count(id) from edition where workshift_id=ws.id) editions_count "
                . "from workshift ws "
                . "left join user u1 on ws.user1_id = u1.id "
                . "left join user u2 on ws.user2_id = u2.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId;
        $fetcher = new Fetcher($sql);
        
        while ($item = $fetcher->Fetch()) {
            $all[$item['date'].$item['shift']] = $item;
        }
        
        // Список тиражей
        $all_editions = [];
        $sql = "select ws.date, ws.shift, ws.machine_id, e.id, e.workshift_id, e.name edition, e.organization, e.length, e.coloring, e.comment, e.position, "
                . "e.status_id, s.name status, "
                . "e.roller_id, r.name roller, "
                . "e.lamination_id, lam.name lamination, "
                . "e.manager_id, m.fio manager "
                . "from edition e "
                . "left join edition_status s on e.status_id = s.id "
                . "left join roller r on e.roller_id = r.id "
                . "left join lamination lam on e.lamination_id = lam.id "
                . "left join user m on e.manager_id = m.id "
                . "inner join workshift ws on e.workshift_id = ws.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId." order by e.position";
        
        $fetcher = new Fetcher($sql);
        
        while ($item = $fetcher->Fetch()) {
            if(!array_key_exists($item['date'], $all_editions) || !array_key_exists($item['shift'], $all_editions[$item['date']])) $all_editions[$item['date']][$item['shift']] = [];
            array_push($all_editions[$item['date']][$item['shift']], $item);
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
        
        $dateshifts = array();
        
        foreach ($period as $date) {
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'day';
            $dateshift['top'] = 'top';
            $this->CreateDateShift($dateshift, $all, $all_editions);
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            $dateshift['top'] = 'nottop';
            $this->CreateDateShift($dateshift, $all, $all_editions);
            array_push($dateshifts, $dateshift);
        }
        
        include 'show_page.php';
    }
    
    private function CreateDateShift(&$dateshift, $all, $all_editions) {
        $formatted_date = $dateshift['date']->format('Y-m-d');
        $key = $formatted_date.$dateshift['shift'];
        $dateshift['row'] = array();
        if(isset($all[$key])) $dateshift['row'] = $all[$key];
            
        $str_date = $dateshift['date']->format('Y-m-d');
            
        $dateshift['editions'] = array();
        if(array_key_exists($str_date, $all_editions) && array_key_exists($dateshift['shift'], $all_editions[$str_date])) {
            $dateshift['editions'] = $all_editions[$str_date][$dateshift['shift']];
        }
            
        $day_editions = array();
        if(array_key_exists($str_date, $all_editions) && array_key_exists('day', $all_editions[$str_date])) {
            $day_editions = $all_editions[$str_date]['day'];
        }
            
        $night_editions = array();
        if(array_key_exists($str_date, $all_editions) && array_key_exists('night', $all_editions[$str_date])) {
            $night_editions = $all_editions[$str_date]['night'];
        }
            
        $day_rowspan = count($day_editions);
        if($day_rowspan == 0) $day_rowspan = 1;
        $night_rowspan = count($night_editions);
        if($night_rowspan == 0) $night_rowspan = 1;
        $dateshift['rowspan'] = $day_rowspan + $night_rowspan;
        $dateshift['my_rowspan'] = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
    }

    private function ShowEdition($edition, $top, $clipboard_db) {
        $date = $edition['date'];
        $shift = $edition['shift'];
        $position = $edition['position'];
        $machine_id = $edition['machine_id'];
        $workshift_id = $edition['workshift_id'];
        
        $is_admin = IsInRole('admin');
        
        $from = $this->dateFrom->format("Y-m-d");
        $to = $this->dateTo->format("Y-m-d");
        
        $hasOrganization = $this->hasOrganization;
        $hasEdition = $this->hasEdition;
        $hasLength = $this->hasLength;
        $hasStatus = $this->hasStatus;
        $statuses = $this->statuses;
        $hasRoller = $this->hasRoller;
        $rollers = $this->rollers;
        $hasLamination = $this->hasLamination;
        $laminations = $this->laminations;
        $hasColoring = $this->hasColoring;
        $coloring = $this->coloring;
        $hasManager = $this->hasManager;
        $managers = $this->managers;
        $hasComment = $this->hasComment;
        
        include 'show_edition.php';
    }
    
    function Print() {
        // Список рабочих смен
        $all = array();
        $sql = "select ws.id, ws.date date, date_format(ws.date, '%d.%m.%Y') fdate, ws.shift, u1.id u1_id, u1.fio u1_fio, u2.id u2_id, u2.fio u2_fio, "
                . "(select count(id) from edition where workshift_id=ws.id) editions_count "
                . "from workshift ws "
                . "left join user u1 on ws.user1_id = u1.id "
                . "left join user u2 on ws.user2_id = u2.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId;
        $fetcher = new Fetcher($sql);
        
        while ($item = $fetcher->Fetch()) {
            $all[$item['date'].$item['shift']] = $item;
        }
        
        // Список тиражей
        $all_editions = [];
        $sql = "select ws.date, ws.shift, e.id, e.workshift_id, e.name, e.organization, e.length, e.coloring, e.comment, "
                . "e.roller_id, r.name roller, "
                . "e.lamination_id, lam.name lamination, "
                . "e.manager_id, m.fio manager, "
                . "e.status_id, s.name status "
                . "from edition e "
                . "left join roller r on e.roller_id = r.id "
                . "left join lamination lam on e.lamination_id = lam.id "
                . "left join user m on e.manager_id = m.id "
                . "left join edition_status s on e.status_id = s.id "
                . "inner join workshift ws on e.workshift_id = ws.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId." order by e.position";
        
        $fetcher = new Fetcher($sql);
        
        while ($item = $fetcher->Fetch()) {
            if(!array_key_exists($item['date'], $all_editions) || !array_key_exists($item['shift'], $all_editions[$item['date']])) $all_editions[$item['date']][$item['shift']] = [];
            array_push($all_editions[$item['date']][$item['shift']], $item);
        }
        
        // Список дат и смен
        $date_diff = $this->dateFrom->diff($this->dateTo);
        $interval = DateInterval::createFromDateString("1 day");
        $period = new DatePeriod($this->dateFrom, $interval, $date_diff->days);
        $dateshifts = array();
        
        foreach ($period as $date) {
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'day';
            $dateshift['top'] = 'top';
            $this->CreateDateShift($dateshift, $all, $all_editions);
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            $dateshift['top'] = 'nottop';
            $this->CreateDateShift($dateshift, $all, $all_editions);
            array_push($dateshifts, $dateshift);
        }
        
        include 'show_print.php';
    }
    
    private function PrintEdition($edition, $top) {
        include 'show_print_edition.php';
    }
}
?>