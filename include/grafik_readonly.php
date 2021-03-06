<?php
include 'database_grafik.php';

class GrafikReadonly {
    public function __construct(DateTime $from, DateTime $to, $machine_id) {
        $this->dateFrom = $from;
        $this->dateTo = $to;
        $this->machineId = $machine_id;
        
        $sql = "select name, user1_name, user2_name, role_id, has_edition, has_organization, has_length, has_status, has_roller, has_lamination, has_coloring, coloring, has_manager, has_comment, is_cutter from machine where id = $machine_id";
        $fetcher = new FetcherGrafik($sql);
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
        ?>
<div class="d-flex justify-content-between mb-2">
    <h1><?= $this->name ?></h1>
</div>
<table class="table table-bordered typography">
    <thead id="grafik-thead">
        <tr>
            <th></th>
            <th>????????</th>
            <th>??????????</th>
            <?php
            if($this->user1Name != '') echo '<th>'.$this->user1Name.'</th>';
            if($this->user2Name != '') echo '<th>'.$this->user2Name.'</th>';
            if(IsInRole('admin')) echo '<th></th>';
            if(IsInRole('admin')) echo '<th></th>';
            if($this->hasOrganization) echo '<th>????????????????</th>';
            if($this->hasEdition) echo '<th>????????????????????????</th>';
            if($this->hasLength) echo '<th>????????????</th>';
            if(IsInRole('admin')) {
                if($this->hasStatus) echo '<th>????????????</th>';
            }
            if($this->hasRoller) echo '<th>??????</th>';
            if($this->hasLamination) echo '<th>??????????????????</th>';
            if($this->hasColoring) echo '<th>????-????</th>';
            if($this->hasManager) echo '<th>????????????????</th>'; 
            if($this->hasComment) echo '<th>??????????????????????</th>';
            if(IsInRole('admin')) {
                echo '<th></th>';
                echo '<th></th>';
                echo '<th></th>';
            }
            ?>
        </tr>
    </thead>
    <tbody id="grafik-tbody">
        <?php
        // ???????????? ?????????????? ????????
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
        
        // ???????????? ??????????????
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
        
        $fetcher = new FetcherGrafik($sql);
        
        while ($item = $fetcher->Fetch()) {
            if(!array_key_exists($item['date'], $all_editions) || !array_key_exists($item['shift'], $all_editions[$item['date']])) $all_editions[$item['date']][$item['shift']] = [];
            array_push($all_editions[$item['date']][$item['shift']], $item);
        }
        
        // ???????????? ?????? ?? ????????
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
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            array_push($dateshifts, $dateshift);
        }
        
        foreach ($dateshifts as $dateshift) {
            $formatted_date = $dateshift['date']->format('Y-m-d');
            $key = $formatted_date.$dateshift['shift'];
            $row = array();
            if(isset($all[$key])) $row = $all[$key];
            
            $str_date = $dateshift['date']->format('Y-m-d');
            
            $editions = array();
            if(array_key_exists($str_date, $all_editions) && array_key_exists($dateshift['shift'], $all_editions[$str_date])) {
                $editions = $all_editions[$str_date][$dateshift['shift']];
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
            $rowspan = $day_rowspan + $night_rowspan;
            $my_rowspan = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
            
            $top = "nottop";
            if($dateshift['shift'] == 'day') {
                $top = "top";
            }
            
            $date = $dateshift['date'];
            $shift = $dateshift['shift'];
            
            echo '<tr>';
            if($dateshift['shift'] == 'day') {
                echo "<td class='$top $shift' rowspan='$rowspan'>".$GLOBALS['weekdays'][$dateshift['date']->format('w')].'</td>';
                echo "<td class='$top $shift' rowspan='$rowspan'>".$dateshift['date']->format('d.m').".".$dateshift['date']->format('Y')."</td>";
            }
            echo "<td class='$top $shift' rowspan='$my_rowspan'>".($dateshift['shift'] == 'day' ? '????????' : '????????')."</td>";
            
            // ???????????????? ???1
            if($this->user1Name != '') {
                echo "<td class='$top $shift' rowspan='$my_rowspan' title='".$this->user1Name."'>";
                echo (isset($row['u1_fio']) ? $row['u1_fio'] : '');
                echo '</td>';
            }
            
            // ???????????????? ???2
            if($this->user2Name != '') {
                echo "<td class='$top $shift' rowspan='$my_rowspan' title='".$this->user2Name."'>";
                echo (isset($row['u2_fio']) ? $row['u2_fio'] : '');
                echo '</td>';
            }
            
            // ??????????
            $edition = null;
            
            if(count($editions) == 0) {
                if($this->hasOrganization) echo "<td class='$top $shift'></td>";
                if($this->hasEdition) echo "<td class='$top $shift'></td>";
                if($this->hasLength) echo "<td class='$top $shift'></td>";
                if($this->hasRoller) echo "<td class='$top $shift'></td>";
                if($this->hasLamination) echo "<td class='$top $shift'></td>";
                if($this->hasColoring) echo "<td class='$top $shift'></td>";
                if($this->hasManager) echo "<td class='$top $shift'></td>";
                if($this->hasComment) echo "<td class='$top $shift'></td>";
            }
            else {
                $edition = array_shift($editions);
                $this->ShowEdition($edition, $top);
            }
            
            echo '</tr>';
            
            // ???????????????????????????? ??????????
            $edition = array_shift($editions);
            
            while ($edition != null) {
                echo '<tr>';
                $this->ShowEdition($edition, 'nottop');
                echo '</tr>';
                $edition = array_shift($editions);
            }
        }
        ?>
    </tbody>
</table>
<?php
    }

    private function ShowEdition($edition, $top) {
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
        echo '<h1>'. $this->name.'</h1>';
        
        // ???????????? ?????????????? ????????
        $all = array();
        $sql = "select ws.id, ws.date date, date_format(ws.date, '%d.%m.%Y') fdate, ws.shift, u1.id u1_id, u1.fio u1_fio, u2.id u2_id, u2.fio u2_fio, "
                . "(select count(id) from edition where workshift_id=ws.id) editions_count "
                . "from workshift ws "
                . "left join user u1 on ws.user1_id = u1.id "
                . "left join user u2 on ws.user2_id = u2.id "
                . "where ws.date >= '".$this->dateFrom->format('Y-m-d')."' and ws.date <= '".$this->dateTo->format('Y-m-d')."' and ws.machine_id = ". $this->machineId;
        $fetcher = new FetcherGrafik($sql);
        
        while ($item = $fetcher->Fetch()) {
            $all[$item['date'].$item['shift']] = $item;
        }
        
        // ???????????? ??????????????
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
        
        $fetcher = new FetcherGrafik($sql);
        
        while ($item = $fetcher->Fetch()) {
            if(!array_key_exists($item['date'], $all_editions) || !array_key_exists($item['shift'], $all_editions[$item['date']])) $all_editions[$item['date']][$item['shift']] = [];
            array_push($all_editions[$item['date']][$item['shift']], $item);
        }
        
        // ???????????? ?????? ?? ????????
        $date_diff = $this->dateFrom->diff($this->dateTo);
        $interval = DateInterval::createFromDateString("1 day");
        $period = new DatePeriod($this->dateFrom, $interval, $date_diff->days);
        $dateshifts = array();
        
        foreach ($period as $date) {
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'day';
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            array_push($dateshifts, $dateshift);
        }
        
        echo '<table class="table table-bordered print">';
        echo '<th></th>';
        echo '<th>????????</th>';
        echo '<th>??????????</th>';
        if($this->user1Name != '') echo '<th>'.$this->user1Name.'</th>';
        if($this->user2Name != '') echo '<th>'.$this->user2Name.'</th>';
        if($this->hasOrganization) echo '<th>????????????????</th>';
        if($this->hasEdition) echo '<th>????????????????????????</th>';
        if($this->hasLength) echo '<th>????????????</th>';
        if($this->hasRoller) echo '<th>??????</th>';
        if($this->hasLamination) echo '<th>??????????????????</th>';
        if($this->hasColoring) echo '<th>????-????</th>';
        if($this->hasManager) echo '<th>????????????????</th>'; 
        if($this->hasComment) echo '<th>??????????????????????</th>';
        
        foreach ($dateshifts as $dateshift) {
            $key = $dateshift['date']->format('Y-m-d').$dateshift['shift'];
            $row = array();
            if(isset($all[$key])) $row = $all[$key];
            
            $str_date = $dateshift['date']->format('Y-m-d');
            
            $editions = array();
            if(array_key_exists($str_date, $all_editions) && array_key_exists($dateshift['shift'], $all_editions[$str_date])) {
                $editions = $all_editions[$str_date][$dateshift['shift']];
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
            $rowspan = $day_rowspan + $night_rowspan;
            $my_rowspan = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
            
            $top = "nottop";
            if($dateshift['shift'] == 'day') {
                $top = "top";
            }
            
            echo '<tr>';
            if($dateshift['shift'] == 'day') {
                echo "<td class='$top' rowspan='$rowspan'>".$GLOBALS['weekdays'][$dateshift['date']->format('w')].'</td>';
                echo "<td class='$top' rowspan='$rowspan'>".$dateshift['date']->format("d.m.Y")."</td>";
            }
            echo "<td class='$top' rowspan='$my_rowspan'>".($dateshift['shift'] == 'day' ? '????????' : '????????')."</td>";
            
            // ???????????????? ???1
            if($this->user1Name != '') {
                echo "<td class='$top' rowspan='$my_rowspan' title='".$this->user1Name."'>";
                echo (isset($row['u1_fio']) ? $row['u1_fio'] : '');
                echo '</td>';
            }
            
            // ???????????????? ???2
            if($this->user2Name != '') {
                echo "<td class='$top' rowspan='$my_rowspan' title='".$this->user2Name."'>";
                echo (isset($row['u2_fio']) ? $row['u2_fio'] : '');
                echo '</td>';
            }
            
            // ??????????
            $edition = null;
            
            if(count($editions) == 0) {
                if($this->hasOrganization) echo "<td class='$top'></td>";
                if($this->hasEdition) echo "<td class='$top'></td>";
                if($this->hasLength) echo "<td class='$top'></td>";
                if($this->hasRoller) echo "<td class='$top'></td>";
                if($this->hasLamination) echo "<td class='$top'></td>";
                if($this->hasColoring) echo "<td class='$top'></td>";
                if($this->hasManager) echo "<td class='$top'></td>";
                if($this->hasComment) echo "<td class='$top'></td>";
            }
            else {
                $edition = array_shift($editions);
                $this->PrintEdition($edition, $top);
            }
            
            echo '</tr>';
            
            // ???????????????????????????? ??????????
            $edition = array_shift($editions);
            
            while ($edition != null) {
                echo '<tr>';
                $this->PrintEdition($edition, 'nottop');
                echo '</tr>';
                $edition = array_shift($editions);
            }
        }
        
        echo '</table>';
    }
    
    private function PrintEdition($edition, $top) {
        // ????????????????
        if($this->hasOrganization) {
            echo "<td class='$top'>";
            echo (isset($edition['organization']) ? htmlentities($edition['organization']) : '');
            echo "</td>";
        }
        
        // ???????????????????????? ????????????
        if($this->hasEdition){
            echo "<td class='$top'>";
            echo (isset($edition['name']) ? htmlentities($edition['name']) : '');
            echo "</td>";
        }
        
        // ????????????
        if($this->hasLength) {
            echo "<td class='$top'>";
            if(isset($edition['status']) && $edition['status'] != null) {
                echo $edition['status'];
            }
            else if (isset ($edition['length'])) {
                echo $edition['length'];
            }
            echo "</td>";
        };
        
        // ??????
        if($this->hasRoller) {
            echo "<td class='$top'>";
            echo (isset($edition['roller']) ? $edition['roller'] : '');
            echo "</td>";
        };
        
        // ??????????????????
        if($this->hasLamination) {
            echo "<td class='$top'>";
            echo (isset($edition['lamination']) ? $edition['lamination'] : '');
            echo "</td>";
        }
        
        // ??????????????????????
        if($this->hasColoring) {
            echo "<td class='$top'>";
            echo (isset($edition['coloring']) ? $edition['coloring'] : '');
            echo "</td>";
        }
        
        // ????????????????
        if($this->hasManager) {
            echo "<td class='$top'>";
            echo (isset($edition['manager']) ? $edition['manager'] : '');
            echo "</td>";
        }
        
        // ??????????????????????
        if($this->hasComment) {
            echo "<td class='$top'>";
            echo (isset($edition['comment']) ? $edition['comment'] : '');
            echo "</td>";
        }
    }
}
?>