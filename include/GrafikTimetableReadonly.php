<?php
include 'GrafikDateReadonly.php';

const DATABASE_NAME_GRAFIK = "grafik";

class ExecuterGrafik {
    public $error = '';
    public $insert_id = 0;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_GRAFIK);

        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $conn->query($sql);
        $this->error = $conn->error;
        $this->insert_id = $conn->insert_id;
        
        $conn->close();
    }
}

class GrabberGrafik {
    public  $error = '';
    public $result = array();
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_GRAFIK);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        
        if(is_bool($result)) {
            $this->error = $conn->error;
        }
        else {
            $this->result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        $conn->close();
    }
}

class FetcherGrafik {
    public $error = '';
    private $result;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_GRAFIK);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $this->result = $conn->query($sql);
        
        if(is_bool($this->result)) {
            $this->error = $conn->error;
        }
        
        $conn->close();
    }
    
    function Fetch() {
        return mysqli_fetch_array($this->result);
    }
}

class GrafikTimetableReadonly {
    public function __construct(DateTime $from, DateTime $to, $machine_id) {
        $this->dateFrom = $from;
        $this->dateTo = $to;
        $this->machineId = $machine_id;
        
        $sql = "select name, user1_name, user2_name, role_id, has_organization, has_edition, has_material, has_thickness, has_width, has_length, has_status, has_roller, has_lamination, has_coloring, coloring, has_manager, has_comment, is_cutter from machine where id = $machine_id";
        $fetcher = new FetcherGrafik($sql);
        $this->error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $this->name = $row['name'];
            $this->user1Name = $row['user1_name'];
            $this->user2Name = $row['user2_name'];
            $this->userRole = $row['role_id'];
            $this->hasOrganization = $row['has_organization'];
            $this->hasEdition = $row['has_edition'];
            $this->hasMaterial = $row['has_material'];
            $this->hasThickness = $row['has_thickness'];
            $this->hasWidth = $row['has_width'];
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
        
        // Параметр "нужно подготовить" (только для роли "кладовщик")
        if(IsInRole("storekeeper") && ($this->machineId == self::COMIFLEX || $this->machineId == self::ZBS1 || $this->machineId == self::ZBS2 || $this->machineId == self::ZBS3 || $this->machineId == self::LAMINATOR_SOLVENT || $this->machineId == self::LAMINATOR_NOSOLVENT)) {
            $this->hasPrepare = true;
        }
        
        // Список рабочих смен
        $all = array();
        $sql = "select ws.id, ws.date date, date_format(ws.date, '%d.%m.%Y') fdate, ws.shift, ws.machine_id, u1.id u1_id, u1.fio u1_fio, u2.id u2_id, u2.fio u2_fio, "
                . "(select count(id) from edition where workshift_id=ws.id) editions_count "
                . "from workshift ws "
                . "left join user u1 on ws.user1_id = u1.id "
                . "left join user u2 on ws.user2_id = u2.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId;
        $fetcher = new FetcherGrafik($sql);
        
        while ($item = $fetcher->Fetch()) {
            $all[$item['date'].$item['shift']] = $item;
        }
        
        // Список тиражей
        $all_editions = [];
        $sql = "select ws.date, ws.shift, ws.machine_id, e.id, e.workshift_id, e.organization, e.name edition, e.material, e.thickness, e.width, e.length, e.coloring, e.comment, e.position, "
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
        
        $fetcher = new FetcherGrafik($sql);
        
        while ($item = $fetcher->Fetch()) {
            if(!array_key_exists($item['date'], $all_editions) || !array_key_exists($item['shift'], $all_editions[$item['date']])) {
                $all_editions[$item['date']][$item['shift']] = []; 
            }
            
            // Параметр "нужно подготовить" (только для роли "кладовщик")
            $item['prepare'] = "";
            
            if($this->hasPrepare) {
                $coeffLam = 0;
                if($item['lamination_id'] == self::ONE_LAMINATION) $coeffLam = 1;
                elseif($item['lamination_id'] == self::TWO_LAMINATIONS) $coeffLam = 2;
                
                if(($this->machineId == self::COMIFLEX || $this->machineId == self::ZBS1 || $this->machineId == self::ZBS2 || $this->machineId == self::ZBS3) && empty($item['status_id']) && !empty($item['length']) && !empty($item['coloring'])) {
                    $item['prepare'] = $item['length'] + ($item['coloring'] * 300) + ($item['length'] * 0.03) + ($coeffLam * 200);
                }
                elseif(($this->machineId == self::LAMINATOR_SOLVENT || $this->machineId == self::LAMINATOR_NOSOLVENT) && empty ($item['status_id']) && !empty ($item['length'])) {
                    $item['prepare'] = $item['length'] + ($item['length'] * 0.03) + 200;
                }
            }
            
            // Добавляем тираж в список тиражей
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
        
        $grafik_dates = array();
        
        foreach($period as $date) {
            $str_date = $date->format('Y-m-d');
            
            $day_data = array();
            if(isset($all[$str_date.'day'])) {
                $day_data = $all[$str_date.'day'];
            }
            
            $night_data = array();
            if(isset($all[$str_date.'night'])) {
                $night_data = $all[$str_date.'night'];
            }
            
            $day_editions = array();
            if(isset($all_editions[$str_date]['day'])) {
                $day_editions = $all_editions[$str_date]['day'];
            }
            
            $night_editions = array();
            if(isset($all_editions[$str_date]['night'])) {
                $night_editions = $all_editions[$str_date]['night'];
            }
            
            $grafik_date = new GrafikDateReadonly($date, $this, $day_data, $night_data, $day_editions, $night_editions);
            array_push($this->grafik_dates, $grafik_date);
        }
    }
    
    public const COMIFLEX = 1;
    public const ZBS1 = 2;
    public const ZBS2 = 3;
    public const ZBS3 = 4;
    public const ATLAS = 5;
    public const LAMINATOR_SOLVENT = 6;
    public const CUT1 = 7;
    public const CUT2 = 9;
    public const CUT3 = 10;
    public const CUT_ATLAS = 11;
    public const CUT_SOMA = 12;
    public const LAMINATOR_NOSOLVENT = 13;
    public const CUT4 = 14;
    
    public const ONE_LAMINATION = 4;
    public const TWO_LAMINATIONS = 5;
    
    private $dateFrom;
    private $dateTo;
    private $machineId;
    
    public $name = '';
    public $user1Name = '';
    public $user2Name = '';
    public $userRole = 0;
    
    public $hasOrganization = false;
    public $hasEdition = false;
    public $hasMaterial = false;
    public $hasThickness = false;
    public $hasWidth = false;
    public $hasLength = false;
    public $hasStatus = false;
    public $hasRoller = false;
    public $hasLamination = false;
    public $hasColoring = false;
    public $coloring = 0;
    public $hasManager = false;
    public $hasComment = false;
    public $isCutter = false;
    
    public $hasPrepare = false;

    public $error_message = '';
    private $grafik_dates = [];

    function Show() {
        include 'grafik_timetable_readonly.php';
    }
}
?>