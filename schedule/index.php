<?php
include '../include/topscripts.php';
include '../include/database_grafik.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Добавление тиража в график
if(null !== filter_input(INPUT_POST, 'grafik-submit')) {
    // Нахождение заказчика и названия заказа
    $customer = '';
    $name = '';
    
    $id = filter_input(INPUT_POST, 'id');
    
    $sql = "select cus.name customer, c.name request_calc "
            . "from techmap t "
            . "inner join request_calc c on t.request_calc_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where t.id=$id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        $customer = addslashes($row['customer']);
        $name = addslashes($row['request_calc']);
    }
    else {
        $error_message = "Ошибка при запросе заказчика и имени заказа";
    }
    
    // Нахождение рабочей смены в графике
    $date = filter_input(INPUT_POST, 'date');
    $shift = filter_input(INPUT_POST, 'shift');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $grafik_machines = array(1 => 2, 2 => 3, 3 => 4, 4 => 1);
    $grafik_machine_id = $grafik_machines[$machine_id];
    $workshift_id = null;
    
    $sql = "select id from workshift where date = '$date' and shift = '$shift' and machine_id = $grafik_machine_id";
    $fetcher = new FetcherGrafik($sql);
        
    if($row = $fetcher->Fetch()) {
        $workshift_id = $row['id'];
    }
    else {
        $sql = "insert into workshift (date, shift, machine_id) values ('$date', '$shift', $grafik_machine_id)";
        $executer = new ExecuterGrafik($sql);
        $workshift_id = $executer->insert_id;
        $error_message = $executer->error;
    }
    
    // Создание нового тиража
    $position = 1;
    $sql = "insert into edition (name, organization, workshift_id, position) values ('$name', '$customer', $workshift_id, $position)";
    $executer = new ExecuterGrafik($sql);
    $error_message = $executer->error;
    $insert_id = $executer->insert_id;
    
    // Удаление пустых тиражей в конечной смене
    $sql = "delete from edition where workshift_id = $workshift_id "
            . "and (name is null or name = '') "
            . "and (organization is null or organization = '') "
            . "and length is null "
            . "and status_id is null "
            . "and lamination_id is null "
            . "and coloring is null "
            . "and roller_id is null "
            . "and manager_id is null "
            . "and (comment is null or comment = '')";
    $executer = new ExecuterGrafik($sql);
    $error_message = $executer->error;
    
    $sql = "update techmap set grafik_id=$insert_id where id=$id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Удаление тиража из графика
if(null !== filter_input(INPUT_POST, 'remove-from-grafik-submit')) {
    $grafik_id = filter_input(INPUT_POST, 'grafik_id');
    
    $sql = "update techmap set grafik_id = null where grafik_id = $grafik_id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "select workshift_id from edition where id = $grafik_id";
        $fetcher = new FetcherGrafik($sql);
        $error_message = $fetcher->error;
        
        if($row = $fetcher->Fetch()) {
            $workshift_id = $row[0];
            
            $sql = "delete from edition where id = $grafik_id";
            $executer = new ExecuterGrafik($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                $count = (new FetcherGrafik("select count(id) from edition where workshift_id = $workshift_id"))->Fetch()[0];
                
                if($count == 0) {
                    $row = (new FetcherGrafik("select user1_id, user2_id from workshift where id = $workshift_id"))->Fetch();
                    
                    if(empty($row[0]) && empty($row[1])) {
                        $error_message = (new ExecuterGrafik("delete from workshift where id = $workshift_id"))->error;
                    }
                    else {
                        $position = 1;
                        $error_message = (new ExecuterGrafik("insert into edition (workshift_id, position) values ($workshift_id, $position)"))->error;
                    }
                }
            }
        }
    }
}

function CreateDateShift(&$dateshift, $techmaps) {
    $str_date = $dateshift['date']->format('Y-m-d');
            
    $dateshift['techmaps'] = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists($dateshift['shift'], $techmaps[$str_date])) {
        $dateshift['techmaps'] = $techmaps[$str_date][$dateshift['shift']];
    }
            
    $day_techmaps = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists('day', $techmaps[$str_date])) {
        $day_techmaps = $techmaps[$str_date]['day'];
    }
    
    $night_techmaps = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists('night', $techmaps[$str_date])) {
        $night_techmaps = $techmaps[$str_date]['night'];
    }
            
    $day_rowspan = count($day_techmaps);
    if($day_rowspan == 0) $day_rowspan = 1;
    $night_rowspan = count($night_techmaps);
    if($night_rowspan == 0) $night_rowspan = 1;
    $dateshift['rowspan'] = $day_rowspan + $night_rowspan;
    $dateshift['my_rowspan'] = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
}

function ShowTechmap($techmap, $top, $dateshift) {
    include 'show_techmap.php';
}

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);

// Проверка соответствия между расписанием и графиком, удаление неактуальных ссылок на график
$grafik_editions = array();
array_push($grafik_editions, 0);

$sql = "select e.id from edition e inner join "
        . "workshift ws on e.workshift_id = ws.id "
        . "where ws.date >= '".$date_from->format('Y-m-d')."' and ws.date <= '".$date_to->format('Y-m-d')."'";
$fetcher = new FetcherGrafik($sql);

while ($row = $fetcher->Fetch()) {
    array_push($grafik_editions, $row['id']);
}

$grafik_editions_implode = implode(', ', $grafik_editions);

$sql = "update techmap set grafik_id = null "
        . "where grafik_id not in ($grafik_editions_implode) "
        . "and work_date >= '".$date_from->format('Y-m-d')."' "
        . "and work_date <= '".$date_to->format('Y-m-d')."'";
$executer = new Executer($sql);
$error_message = $executer->error;
            
// Список технологических карт
$techmaps = [];
            
$sql = "select t.id, t.work_date, t.work_shift, t.grafik_id, "
        . "c.name, c.unit, c.quantity, "
        . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
        . "c.machine_id, cus.name customer, m.name machine "
        . "from techmap t "
        . "inner join request_calc c on t.request_calc_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "left join machine m on c.machine_id = m.id "
        . "where t.work_date >='".$date_from->format('Y-m-d')."' and t.work_date <= '".$date_to->format('Y-m-d')."' "
        . "order by t.id";
$fetcher = new Fetcher($sql);
while($item = $fetcher->Fetch()) {
    if(!array_key_exists($item['work_date'], $techmaps) || !array_key_exists($item['work_shift'], $techmaps[$item['work_date']])) $techmaps[$item['work_date']][$item['work_shift']] = [];
    array_push($techmaps[$item['work_date']][$item['work_shift']], $item);
}

// Список дат и смен
if($date_from < $date_to) {
    $date_diff = $date_from->diff($date_to);
    $interval = DateInterval::createFromDateString("1 day");
    $period = new DatePeriod($date_from, $interval, $date_diff->days);
}
else {
    $period = array();
    array_push($period, $date_from);
}
            
$dateshifts = array();

foreach ($period as $date) {
    $dateshift['date'] = $date;
    $dateshift['shift'] = 'day';
    $dateshift['top'] = 'top';
    CreateDateShift($dateshift, $techmaps);
    array_push($dateshifts, $dateshift);
        
    $dateshift['date'] = $date;
    $dateshift['shift'] = 'night';
    $dateshift['top'] = 'nottop';
    CreateDateShift($dateshift, $techmaps);
    array_push($dateshifts, $dateshift);
}

include 'show_page.php';
?>