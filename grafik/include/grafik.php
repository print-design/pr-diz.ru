<?php
class Grafik {
    public function __construct(DateTime $from, DateTime $to, $machine_id) {
        $this->dateFrom = $from;
        $this->dateTo = $to;
        $this->machineId = $machine_id;
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
            
    function ProcessForms() {
        // Выбор работника 1
        $user1_id = filter_input(INPUT_POST, 'user1_id');
        if($user1_id !== null) {
            if($user1_id == '') $user1_id = "NULL";
            $sql = '';
            $id = filter_input(INPUT_POST, 'id');
            
            if($id !== null) {
                $this->error_message = (new Executer("update workshift set user1_id=$user1_id where id=$id"))->error;
            }
            else {
                $date = filter_input(INPUT_POST, 'date');
                $shift = filter_input(INPUT_POST, 'shift');
                $sql = "insert into workshift (date, machine_id, shift, user1_id) values ('$date', $this->machineId, '$shift', $user1_id)";
                $ws_executer = new Executer($sql);
                $this->error_message = $ws_executer->error;
                $workshift_id = $ws_executer->insert_id;
                
                if($workshift_id > 0) {
                    $this->error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, 1)"))->error;
                }
            }
        }
        
        // Создание нового работника 1
        $user1 = filter_input(INPUT_POST, 'user1');
        if($user1 !== null) {
            $user1 = addslashes($user1);
            $u_executer = new Executer("insert into user (fio, username) values ('$user1', CURRENT_TIMESTAMP())");
            $this->error_message = $u_executer->error;
            $user1_id = $u_executer->insert_id;
            
            if($user1_id > 0) {
                $role_id = $this->userRole;
                $r_executer = new Executer("insert into user_role (user_id, role_id) values ($user1_id, $role_id)");
                $this->error_message = $r_executer->error;
                
                if($r_executer->error == '') {
                    $sql = '';
                    $id = filter_input(INPUT_POST, 'id');
                    
                    if($id !== null) {
                        $this->error_message = (new Executer("update workshift set user1_id=$user1_id where id=$id"))->error;
                    }
                    else {
                        $date = filter_input(INPUT_POST, 'date');
                        $shift = filter_input(INPUT_POST, 'shift');
                        $sql = "insert into workshift (date, machine_id, shift, user1_id) values ('$date', $this->machineId, '$shift', $user1_id)";
                        $ws_executer = new Executer($sql);
                        $this->error_message = $ws_executer->error;
                        $workshift_id = $ws_executer->insert_id;
                        
                        if($workshift_id > 0) {
                            $this->error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, 1)"))->error;
                        }
                    }
                }
            }
        }
        
        // Выбор работника 2
        $user2_id = filter_input(INPUT_POST, 'user2_id');
        if($user2_id !== null) {
            if($user2_id == '') $user2_id = "NULL";
            $sql = '';
            $id = filter_input(INPUT_POST, 'id');
            
            if($id != null) {
                $this->error_message = (new Executer("update workshift set user2_id=$user2_id where id=$id"))->error;
            }
            else {
                $date = filter_input(INPUT_POST, 'date');
                $shift = filter_input(INPUT_POST, 'shift');
                $sql = "insert into workshift (date, machine_id, shift, user2_id) values ('$date', $this->machineId, '$shift', $user2_id)";
                $ws_executer = new Executer($sql);
                $this->error_message = $ws_executer->error;
                $workshift_id = $ws_executer->insert_id;
                
                if($workshift_id > 0) {
                    $this->error_message = (new Executer("insert into edition (workshift_id) values ($workshift_id)"))->error;
                }
            }
        }
        
        // Создание нового работника 2
        $user2 = filter_input(INPUT_POST, 'user2');
        if($user2 !== null) {
            $user2 = addslashes($user2);
            $u_executer = new Executer("insert into user (fio, username) values ('$user2', CURRENT_TIMESTAMP())");
            $this->error_message = $u_executer->error;
            $user2_id = $u_executer->insert_id;
            
            if($user2_id > 0) {
                $role_id = $this->userRole;
                $r_executer = new Executer("insert into user_role (user_id, role_id) values ($user2_id, $role_id)");
                $this->error_message = $r_executer->error;
                
                if($r_executer->error == '') {
                    $sql = '';
                    $id = filter_input(INPUT_POST, 'id');
                    
                    if($id !== null) {
                        $this->error_message = (new Executer("update workshift set user2_id=$user2_id where id=$id"))->error;
                    }
                    else {
                        $date = filter_input(INPUT_POST, 'date');
                        $shift = filter_input(INPUT_POST, 'shift');
                        $sql = "insert into workshift (date, machine_id, shift, user2_id) values ('$date', $this->machineId, '$shift', $user2_id)";
                        $ws_executer = new Executer($sql);
                        $this->error_message = $ws_executer->error;
                        $workshift_id = $ws_executer->insert_id;
                        
                        if($workshift_id > 0) {
                            $this->error_message = (new Executer("insert into edition (workshift_id) values ($workshift_id)"))->error;
                        }
                    }
                }
            }
        }
        
        // Создание тиража
        $create_shift_submit = filter_input(INPUT_POST, 'create_edition_submit');
        if($create_shift_submit !== null) {
            $workshift_id = filter_input(INPUT_POST, 'workshift_id');
            $date = filter_input(INPUT_POST, 'date');
            $shift = filter_input(INPUT_POST, 'shift');
            $position = 1;
            
            $direction_post = filter_input(INPUT_POST, 'direction');
            $position_post = filter_input(INPUT_POST, 'position');
            if($direction_post !== null && $position_post !== null) {
                if($direction_post == 'up') {
                    $this->error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position - 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$this->machineId' and position < $position_post"))->error;
                    $position = intval($position_post) - 1;
                }
                
                if($direction_post == 'down') {
                    $this->error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position + 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$this->machineId' and position > $position_post"))->error;
                    $position = intval($position_post) + 1;
                }
            }
            
            $this->error_message = (new Executer("insert into edition (workshift_id, position) values ($workshift_id, $position)"))->error;
        }
        
        // Вставка тиража
        $clipboard = '';
        $disabled = " disabled='disabled'";
                
        $paste_edition_submit = filter_input(INPUT_POST, 'paste_edition_submit');
        if($paste_edition_submit !== null) {
            $clipboard = filter_input(INPUT_POST, 'clipboard');
            $row = json_decode($clipboard, true);
            
            $name = $row['name'] == null ? 'NULL' : "'".addslashes($row['name'])."'";
            $organization = $row['organization'] == null ? 'NULL' : "'".addslashes($row['organization'])."'";
            $length = $row['length'] == null ? 'NULL' : "'".$row['length']."'";
            $status_id = $row['status_id'] == null ? 'NULL' : "'".$row['status_id']."'";
            $lamination_id = $row['lamination_id'] == null ? 'NULL' : "'".$row['lamination_id']."'";
            $coloring = $row['coloring'] == null ? 'NULL' : "'".$row['coloring']."'";
            $roller_id = $row['roller_id'] == null ? 'NULL' : "'".$row['roller_id']."'";
            $manager_id = $row['manager_id'] == null ? 'NULL' : "'".$row['manager_id']."'";
            $comment = $row['comment'] == null ? 'NULL' : "'".addslashes($row['comment'])."'";
            $user1_id = $row['user1_id'] == null ? 'NULL' : "'".$row['user1_id']."'";
            $user2_id = $row['user2_id'] == null ? 'NULL' : "'".$row['user2_id']."'";
            $date = filter_input(INPUT_POST, 'date');
            $shift = filter_input(INPUT_POST, 'shift');
            $position = 1;
            
            $workshift_id = filter_input(INPUT_POST, 'workshift_id');
            if($workshift_id == null) {
                $sql = "insert into workshift (date, machine_id, shift, user1_id, user2_id) values ('$date', $this->machineId, '$shift', $user1_id, $user2_id)";
                $ws_executer = new Executer($sql);
                $this->error_message = $ws_executer->error;
                $workshift_id = $ws_executer->insert_id;
                
                if($workshift_id > 0) {
                    $this->error_message = (new Executer($sql))->error;
                }
            }
            
            $direction_post = filter_input(INPUT_POST, 'direction');
            $position_post = filter_input(INPUT_POST, 'position');
            if($direction_post !== null && $position_post !== null) {
                if($direction_post == 'up') {
                    $this->error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position - 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$this->machineId' and position < $position_post"))->error;
                    $position = intval($position_post) - 1;
                }
                
                if($direction_post == 'down') {
                    $this->error_message = (new Executer("update edition e inner join workshift ws on e.workshift_id = ws.id set e.position = e.position + 1 where ws.date = '$date' and ws.shift = '$shift' and ws.machine_id = '$this->machineId' and position > $position_post"))->error;
                    $position = intval($position_post) + 1;
                }
            }
            
            $sql = "insert into edition (name, organization, length, status_id, lamination_id, coloring, roller_id, manager_id, comment, workshift_id, position) "
                    . "values ($name, $organization, $length, $status_id, $lamination_id, $coloring, $roller_id, $manager_id, $comment, $workshift_id, $position)";
            
            $this->error_message = (new Executer($sql))->error;
        }
        
        // Заказчик
        $organization = filter_input(INPUT_POST, 'organization');
        if($organization !== null) {
            $organization = addslashes($organization);
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set organization='$organization' where id=$id"))->error;
        }
        
        // Наименование заказа
        $edition = filter_input(INPUT_POST, 'edition');
        if($edition !== null) {
            $edition = addslashes($edition);
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set name='$edition' where id=$id"))->error;
        }
        
        // Метраж
        $length = filter_input(INPUT_POST, 'length');
        if($length !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set length=$length where id=$id"))->error;
        }
        
        // Статус
        $status_id = filter_input(INPUT_POST, 'status_id');
        if($status_id !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set status_id=$status_id where id=$id"))->error;
        }
        
        // Вал
        $roller_id = filter_input(INPUT_POST, 'roller_id');
        if($roller_id !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set roller_id=$roller_id where id=$id"))->error;
        }
        
        // Ламинация
        $lamination_id = filter_input(INPUT_POST, 'lamination_id');
        if($lamination_id !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set lamination_id=$lamination_id where id=$id"))->error;
        }
        
        // Красочность
        $coloring = filter_input(INPUT_POST, 'coloring');
        if($coloring !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set coloring=$coloring where id=$id"))->error;
        }
        
        // Менеджер
        $manager_id = filter_input(INPUT_POST, 'manager_id');
        if($manager_id !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("update edition set manager_id=$manager_id where id=$id"))->error;
        }
        
        // Комментарий
        $comment = filter_input(INPUT_POST, 'comment');
        if($comment !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $comment = addslashes($comment);
            $this->error_message = (new Executer("update edition set comment='$comment' where id=$id"))->error;
        }
        
        // Удаление тиража
        $delete_edition_submit = filter_input(INPUT_POST, 'delete_edition_submit');
        if($delete_edition_submit !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("delete from edition where id=$id"))->error;
        }
        
        // Удаление смены
        $delete_shift_submit = filter_input(INPUT_POST, 'delete_shift_submit');
        if($delete_shift_submit !== null) {
            $id = filter_input(INPUT_POST, 'id');
            $this->error_message = (new Executer("delete from workshift where id=$id"))->error;
        }
    }

    function ShowPage() {
        ?>
<div class="d-flex justify-content-between mb-2">
    <div class="p-1">
        <h1><?= $this->name ?></h1>
    </div>
    <div class="p-1">
        <?php if(IsInRole('admin')): ?>
        <div class="d-flex justify-content-end mb-auto">
            <div class="p-1">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="from">от&nbsp;</label>
                        <input type="date" id="from" name="from" class="form-control" value="<?= filter_input(INPUT_GET, 'from') ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="to">&nbsp;до&nbsp;</label>
                        <input type="date" id="to" name="to" class="form-control" value="<?= filter_input(INPUT_GET, 'to') ?>"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="form-control btn btn-light">Показать&nbsp;<i class="fas fa-desktop"></i></button>
                    </div>
                </form>
            </div>
            <div class="p-1 ml-1">
                <form class="form-inline" action="<?=APPLICATION ?>/print.php" target="_blank" method="post">
                    <input type="hidden" id="from" name="from" value="<?= $this->dateFrom->format('Y-m-d') ?>" class="print_from" />
                    <input type="hidden" id="machine" name="machine" value="<?= $this->machineId ?>"/>
                    <input type="hidden" id="name" name="name" value="<?= $this->name ?>"/>
                    <input type="hidden" id="user1Name" name="user1Name" value="<?= $this->user1Name ?>"/>
                    <input type="hidden" id="user2Name" name="user2Name" value="<?= $this->user2Name ?>"/>
                    <input type="hidden" id="userRole" name="userRole" value="<?= $this->userRole ?>"/>
                    <input type="hidden" id="hasEdition" name="hasEdition" value="<?= $this->hasEdition ?>"/>
                    <input type="hidden" id="hasOrganization" name="hasOrganization" value="<?= $this->hasOrganization ?>"/>
                    <input type="hidden" id="hasLength" name="hasLength" value="<?= $this->hasLength ?>"/>
                    <input type="hidden" id="hasStatus" name="hasStatus" value="<?= $this->hasStatus ?>"/>
                    <input type="hidden" id="hasRoller" name="hasRoller" value="<?= $this->hasRoller ?>"/>
                    <input type="hidden" id="hasLamination" name="hasLamination" value="<?= $this->hasLamination ?>"/>
                    <input type="hidden" id="hasColoring" name="hasColoring" value="<?= $this->hasColoring ?>"/>
                    <input type="hidden" id="hasManager" name="hasManager" value="<?= $this->hasManager ?>"/>
                    <input type="hidden" id="hasComment" name="hasComment" value="<?= $this->hasComment ?>"/>
                    <button type="submit" class="form-control btn btn-light" id="print_submit" name="print_submit">Печать&nbsp;<i class="fas fa-print"></i></button>
                </form>
            </div>
            <div class="p-1 ml-1">
                <form action="<?=APPLICATION ?>/csv.php" method="post">
                    <input type="hidden" id="from" name="from" value="<?= $this->dateFrom->format('Y-m-d') ?>"/>
                    <input type="hidden" id="to" name="to" value="<?= $this->dateTo->format('Y-m-d') ?>"/>
                    <input type="hidden" id="machine" name="machine" value="<?= $this->machineId ?>"/>
                    <button type="submit" class="form-control btn btn-light" id="export_submit" name="export_submit">Экспорт&nbsp;<i class="fas fa-file-csv"></i></button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<table class="table table-bordered typography">
    <thead id="grafik-thead">
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Смена</th>
            <?php
            if($this->user1Name != '') echo '<th>'.$this->user1Name.'</th>';
            if($this->user2Name != '') echo '<th>'.$this->user2Name.'</th>';
            if(IsInRole('admin')) echo '<th></th>';
            if(IsInRole('admin')) echo '<th></th>';
            if($this->hasOrganization) echo '<th>Заказчик</th>';
            if($this->hasEdition) echo '<th>Наименование</th>';
            if($this->hasLength) echo '<th>Метраж</th>';
            if(IsInRole('admin')) {
                if($this->hasStatus) echo '<th>Статус</th>';
            }
            if($this->hasRoller) echo '<th>Вал</th>';
            if($this->hasLamination) echo '<th>Ламинация</th>';
            if($this->hasColoring) echo '<th>Кр-ть</th>';
            if($this->hasManager) echo '<th>Менеджер</th>'; 
            if($this->hasComment) echo '<th>Комментарий</th>';
            if(IsInRole('admin')) {
                echo '<th></th>';
                echo '<th></th>';
            }
            ?>
        </tr>
    </thead>
    <tbody id="grafik-tbody">
        <?php
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
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            array_push($dateshifts, $dateshift);
        }
        
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
            
            $date = $dateshift['date'];
            $shift = $dateshift['shift'];
            
            echo '<tr>';
            if($dateshift['shift'] == 'day') {
                echo "<td class='$top $shift' rowspan='$rowspan'>".$GLOBALS['weekdays'][$dateshift['date']->format('w')].'</td>';
                echo "<td class='$top $shift' rowspan='$rowspan'>".$dateshift['date']->format('d.m').".".$dateshift['date']->format('Y')."</td>";
            }
            echo "<td class='$top $shift' rowspan='$my_rowspan'>".($dateshift['shift'] == 'day' ? 'День' : 'Ночь')."</td>";
            
            // Работник №1
            if($this->user1Name != '') {
                echo "<td class='$top $shift' rowspan='$my_rowspan' title='".$this->user1Name."'>";
                if(IsInRole('admin')) {
                    echo "<form method='post'>";
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo "<select id='user1_id' name='user1_id' style='width:100px;'>";
                    echo '<optgroup>';
                    echo '<option value="">...</option>';
                    foreach ($this->users1 as $value) {
                        $selected = '';
                        if(isset($row['u1_id']) && $row['u1_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                    }
                    echo '</optgroup>';
                    echo "<optgroup label='______________'>";
                    echo "<option value='+'>(добавить)</option>";
                    echo '</optgroup>';
                    echo '</select>';
                    echo '</form>';
                            
                    echo '<form method="post" class="d-none">';
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo '<div class="input-group">';
                    echo '<input type="text" id="user1" name="user1" value="" class="editable" />';
                    echo '<div class="input-group-append"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                    echo '</div>';
                    echo '</form>';
                }
                else {
                    echo (isset($row['u1_fio']) ? $row['u1_fio'] : '');
                }
                echo '</td>';
            }
            
            // Работник №2
            if($this->user2Name != '') {
                echo "<td class='$top $shift' rowspan='$my_rowspan' title='".$this->user2Name."'>";
                if(IsInRole('admin')) {
                    echo "<form method='post'>";
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo "<select id='user2_id' name='user2_id' style='width:100px;'>";
                    echo '<optgroup>';
                    echo '<option value="">...</option>';
                    foreach ($this->users2 as $value) {
                        $selected = '';
                        if(isset($row['u2_id']) && $row['u2_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                    }
                    echo '</optgroup>';
                    echo "<optgroup label='______________'>";
                    echo "<option value='+'>(добавить)</option>";
                    echo '</optgroup>';
                    echo '</select>';
                    echo '</form>';
                            
                    echo '<form method="post" class="d-none">';
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo '<div class="input-group">';
                    echo '<input type="text" id="user2" name="user2" value="" class="editable" />';
                    echo '<div class="input-group-append"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                    echo '</div>';
                    echo '</form>';
                }
                else {
                    echo (isset($row['u2_fio']) ? $row['u2_fio'] : '');
                }
                echo '</td>';
            }
            
            // Создание и вставка тиража
            if(IsInRole('admin')) {
                if(count($editions) == 0) {
                    echo "<td class='$top $shift align-bottom' rowspan='$my_rowspan'>";
                    
                    if(isset($row['id'])) {
                        // Создание тиража
                        echo "<form method='post'>";
                        echo "<input type='hidden' id='scroll' name='scroll' />";
                        echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                        echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                        echo "<input type='hidden' id='workshift_id' name='workshift_id' value='".$row['id']."' />";
                        echo "<button id='create_edition_submit' name='create_edition_submit' class='btn btn-outline-dark btn-sm mb-1' title='Добавить тираж'><i class='fas fa-plus'></i></button>";
                        echo '</form>';
                    }
                    
                    // Вставка тиража
                    $clipboard = '';
                    $disabled = " disabled='disabled'";
                    
                    $paste_edition_submit = filter_input(INPUT_POST, 'paste_edition_submit');
                    if($paste_edition_submit !== null) {
                        $clipboard = filter_input(INPUT_POST, 'clipboard');
                        if($clipboard != '') {
                            $disabled = '';
                        }
                    }
                    
                    echo "<form method='post'>";
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    echo "<input type='hidden' class='clipboard' id='clipboard' name='clipboard' value='$clipboard'>";
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    if(isset($row['id'])) {
                        echo "<input type='hidden' id='workshift_id' name='workshift_id' value='".$row['id']."' />";
                    }
                    echo "<button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm clipboard_paste' title='Вставить тираж'$disabled><i class='fas fa-paste'></i></button>";
                    echo "</form>";
                    
                    echo '</td>';
                }
            }
            
            // Смены
            $edition = null;
            
            if(count($editions) == 0) {
                if(IsInRole('admin')) {
                    echo "<td class='$top $shift'></td>"; // Кнопки вставки тиража, доступны внутри тиража
                }
                if($this->hasOrganization) echo "<td class='$top $shift'></td>";
                if($this->hasEdition) echo "<td class='$top $shift'></td>";
                if($this->hasLength) echo "<td class='$top $shift'></td>";
                if(IsInRole('admin')) {
                    if($this->hasStatus) echo "<td class='$top $shift'></td>";
                }
                if($this->hasRoller) echo "<td class='$top $shift'></td>";
                if($this->hasLamination) echo "<td class='$top $shift'></td>";
                if($this->hasColoring) echo "<td class='$top $shift'></td>";
                if($this->hasManager) echo "<td class='$top $shift'></td>";
                if($this->hasComment) echo "<td class='$top $shift'></td>";
                if(IsInRole('admin')) {
                    echo "<td class='$top $shift'></td>";
                    echo "<td class='$top $shift'>";
                    if(isset($row['id'])) {
                        echo "<form method='post'>";
                        echo '<input type="hidden" id="scroll" name="scroll" />';
                        echo "<input type='hidden' id='id' name='id' value='".$row['id']."' />";
                        echo "<button type='submit' id='delete_shift_submit' name='delete_shift_submit' class='btn btn-outline-dark btn-sm confirmable' title='Удалить смену'><i class='fas fa-trash-alt'></i></button>";
                        echo "</form>";
                    }
                    echo "</td>";
                }
            }
            else {
                $edition = array_shift($editions);
                $this->ShowEdition($edition, $top);
            }
            
            echo '</tr>';
            
            // Дополнительные смены
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
        
        if(IsInRole('admin')) {
            // Кнопки добавления тиража
            echo "<td class='$top $shift'>";
            
            echo "<form method='post'>";
            echo '<input type="hidden" id="scroll" name="scroll" />';
            echo "<input type='hidden' id='workshift_id' name='workshift_id' value='$workshift_id' />";
            echo "<input type='hidden' id='date' name='date' value='$date' />";
            echo "<input type='hidden' id='shift' name='shift' value='$shift' />";
            echo "<input type='hidden' id='position' name='position' value='$position' />";
            echo "<input type='hidden' id='direction' name='direction' value='up' />";
            echo "<button type='submit' id='create_edition_submit' name='create_edition_submit' class='btn btn-outline-dark btn-sm mb-1' title='Добавить тираж выше'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-up'></i></button>";
            echo '</form>';
            
            echo "<form method='post'>";
            echo '<input type="hidden" id="scroll" name="scroll" />';
            echo "<input type='hidden' id='workshift_id' name='workshift_id' value='$workshift_id' />";
            echo "<input type='hidden' id='date' name='date' value='$date' />";
            echo "<input type='hidden' id='shift' name='shift' value='$shift' />";
            echo "<input type='hidden' id='position' name='position' value='$position' />";
            echo "<input type='hidden' id='direction' name='direction' value='down' />";
            echo "<button type='submit' id='create_edition_submit' name='create_edition_submit' class='btn btn-outline-dark btn-sm' title='Добавить тираж ниже'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-down'></i></button>";
            echo '</form>';
            
            echo "</td>";
            
            // Кнопки вставки тиража
            $clipboard = '';
            $disabled = " disabled='disabled'";
            
            $paste_edition_submit = filter_input(INPUT_POST, 'paste_edition_submit');
            if($paste_edition_submit !== null) {
                $clipboard = filter_input(INPUT_POST, 'clipboard');
                if($clipboard != '') {
                    $disabled = '';
                }
            }
            
            echo "<td class='$top $shift'>";
            
            echo "<form method='post'>";
            echo '<input type="hidden" id="scroll" name="scroll" />';
            echo "<input type='hidden' class='clipboard' id='clipboard' name='clipboard' value='$clipboard'>";
            echo "<input type='hidden' id='workshift_id' name='workshift_id' value='$workshift_id' />";
            echo "<input type='hidden' id='date' name='date' value='$date' />";
            echo "<input type='hidden' id='shift' name='shift' value='$shift' />";
            echo "<input type='hidden' id='machine_id' name='machine_id' value='$machine_id' />";
            echo "<input type='hidden' id='position' name='position' value='$position' />";
            echo "<input type='hidden' id='direction' name='direction' value='up' />";
            echo "<button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm mb-1 clipboard_paste' title='Вставить тираж выше'$disabled><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>";
            echo "</form>";
            
            echo "<form method='post'>";
            echo '<input type="hidden" id="scroll" name="scroll" />';
            echo "<input type='hidden' class='clipboard' id='clipboard' name='clipboard' value='$clipboard'>";
            echo "<input type='hidden' id='workshift_id' name='workshift_id' value='$workshift_id' />";
            echo "<input type='hidden' id='date' name='date' value='$date' />";
            echo "<input type='hidden' id='shift' name='shift' value='$shift' />";
            echo "<input type='hidden' id='machine_id' name='machine_id' value='$machine_id' />";
            echo "<input type='hidden' id='position' name='position' value='$position' />";
            echo "<input type='hidden' id='direction' name='direction' value='down' />";
            echo "<button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm clipboard_paste' title='Вставить тираж ниже'$disabled><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>";
            echo "</form>";
            
            echo "</td>";
        }
        
        // Заказчик
        if($this->hasOrganization) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="text" id="organization" name="organization" value="'.(isset($edition['organization']) ? htmlentities($edition['organization']) : '').'" class="editable organizations" style="width:140px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['organization']) ? htmlentities($edition['organization']) : '');
            }
            echo "</td>";
        }
        
        // Наименование заказа
        if($this->hasEdition){
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="text" id="edition" name="edition" value="'.(isset($edition['edition']) ? htmlentities($edition['edition']) : '').'" class="editable editions" style="width:140px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['name']) ? htmlentities($edition['name']) : '');
            }
            echo "</td>";
        }
        
        // Метраж
        if($this->hasLength) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="number" min="0" pattern="\d*" id="length" name="length" value="'.(isset($edition['length']) ? $edition['length'] : '').'" class="editable" style="width:65px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                if(isset($edition['status']) && $edition['status'] != null) {
                    echo $edition['status'];
                }
                else if (isset ($edition['length'])) {
                    echo $edition['length'];
                }
            }
            echo "</td>";
        };
        
        // Статус
        if(IsInRole('admin')) {
            if($this->hasStatus) {
                echo "<td class='$top $shift'>";
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='status_id' name='status_id' style='width:85px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($this->statuses as $value) {
                    $selected = '';
                    if(isset($edition['status_id']) && $edition['status_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
                echo "</td>";
            }
        };
        
        // Вал
        if($this->hasRoller) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='roller_id' name='roller_id'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($this->rollers as $value) {
                    $selected = '';
                    if(isset($edition['roller_id']) && $edition['roller_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['roller']) ? $edition['roller'] : '');
            }
            echo "</td>";
        };
        
        // Ламинация
        if($this->hasLamination) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='lamination_id' name='lamination_id' style='width:55px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($this->laminations as $value) {
                    $selected = '';
                    if(isset($edition['lamination_id']) && $edition['lamination_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['lamination']) ? $edition['lamination'] : '');
            }
            echo "</td>";
        }
        
        // Красочность
        if($this->hasColoring) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="number" min="0" max="'.$this->coloring.'" pattern="\d*" id="coloring" name="coloring" value="'.(isset($edition['coloring']) ? $edition['coloring'] : '').'" class="editable" style="width:35px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['coloring']) ? $edition['coloring'] : '');
            }
            echo "</td>";
        }
        
        // Менеджер
        if($this->hasManager) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='manager_id' name='manager_id' style='width:120px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($this->managers as $value) {
                    $selected = '';
                    if(isset($edition['manager_id']) && $edition['manager_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['manager']) ? $edition['manager'] : '');
            }
            echo "</td>";
        }
        
        // Комментарий
        if($this->hasComment) {
            echo "<td class='$top $shift'>";
            if(IsInRole('admin')) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<textarea rows="5" cols="30" wrap="hard" id="comment" name="comment" class="editable">'.(isset($edition['comment']) ? htmlentities($edition['comment']) : '').'</textarea>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><span class="font-awesome">&#xf0c7;</span></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['comment']) ? $edition['comment'] : '');
            }
            echo "</td>";
        }
        
        // Копирование
        if(IsInRole('admin')){
            echo "<td class='$top $shift'>";
            echo "<button class='btn btn-outline-dark btn-sm clipboard_copy' data='".$edition['id']."' title='Копировать тираж'><i class='fas fa-copy'></i><div class='alert alert-info clipboard_alert'>Скопировано</div></button>";
            echo "</td>";
        }
        
        // Удаление
        if(IsInRole('admin')) {
            echo "<td class='$top $shift'>";
            echo "<form method='post'>";
            echo '<input type="hidden" id="scroll" name="scroll" />';
            echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
            echo "<button type='submit' id='delete_edition_submit' name='delete_edition_submit' class='btn btn-outline-dark btn-sm confirmable' title='Удалить тираж'><i class='fas fa-trash-alt'></i></button>";
            echo "</form>";
            echo "</td>";
        };
    }
    
    function Print() {
        echo '<h1>'. $this->name.'</h1>';
        
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
            array_push($dateshifts, $dateshift);
            
            $dateshift['date'] = $date;
            $dateshift['shift'] = 'night';
            array_push($dateshifts, $dateshift);
        }
        
        echo '<table class="table table-bordered print">';
        echo '<th></th>';
        echo '<th>Дата</th>';
        echo '<th>Смена</th>';
        if($this->user1Name != '') echo '<th>'.$this->user1Name.'</th>';
        if($this->user2Name != '') echo '<th>'.$this->user2Name.'</th>';
        if($this->hasOrganization) echo '<th>Заказчик</th>';
        if($this->hasEdition) echo '<th>Наименование</th>';
        if($this->hasLength) echo '<th>Метраж</th>';
        if($this->hasRoller) echo '<th>Вал</th>';
        if($this->hasLamination) echo '<th>Ламинация</th>';
        if($this->hasColoring) echo '<th>Кр-ть</th>';
        if($this->hasManager) echo '<th>Менеджер</th>'; 
        if($this->hasComment) echo '<th>Комментарий</th>';
        
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
            echo "<td class='$top' rowspan='$my_rowspan'>".($dateshift['shift'] == 'day' ? 'День' : 'Ночь')."</td>";
            
            // Работник №1
            if($this->user1Name != '') {
                echo "<td class='$top' rowspan='$my_rowspan' title='".$this->user1Name."'>";
                echo (isset($row['u1_fio']) ? $row['u1_fio'] : '');
                echo '</td>';
            }
            
            // Работник №2
            if($this->user2Name != '') {
                echo "<td class='$top' rowspan='$my_rowspan' title='".$this->user2Name."'>";
                echo (isset($row['u2_fio']) ? $row['u2_fio'] : '');
                echo '</td>';
            }
            
            // Смены
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
            
            // Дополнительные смены
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
        // Заказчик
        if($this->hasOrganization) {
            echo "<td class='$top'>";
            echo (isset($edition['organization']) ? htmlentities($edition['organization']) : '');
            echo "</td>";
        }
        
        // Наименование заказа
        if($this->hasEdition){
            echo "<td class='$top'>";
            echo (isset($edition['name']) ? htmlentities($edition['name']) : '');
            echo "</td>";
        }
        
        // Метраж
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
        
        // Вал
        if($this->hasRoller) {
            echo "<td class='$top'>";
            echo (isset($edition['roller']) ? $edition['roller'] : '');
            echo "</td>";
        };
        
        // Ламинация
        if($this->hasLamination) {
            echo "<td class='$top'>";
            echo (isset($edition['lamination']) ? $edition['lamination'] : '');
            echo "</td>";
        }
        
        // Красочность
        if($this->hasColoring) {
            echo "<td class='$top'>";
            echo (isset($edition['coloring']) ? $edition['coloring'] : '');
            echo "</td>";
        }
        
        // Менеджер
        if($this->hasManager) {
            echo "<td class='$top'>";
            echo (isset($edition['manager']) ? $edition['manager'] : '');
            echo "</td>";
        }
        
        // Комментарий
        if($this->hasComment) {
            echo "<td class='$top'>";
            echo (isset($edition['comment']) ? $edition['comment'] : '');
            echo "</td>";
        }
    }
}
?>