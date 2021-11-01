<?php
include 'include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

function CreateDateShift(&$dateshift, $techmaps) {
    $formatted_date = $dateshift['date']->format('Y-m-d');
    $key = $formatted_date.$dateshift['shift'];
    $dateshift['row'] = array();
    if(isset($techmaps[$key])) $dateshift['row'] = $techmaps[$key];
            
    $str_date = $dateshift['date']->format('Y-m-d');
            
    $dateshift['techmaps'] = array();
    if(array_key_exists($str_date, $techmaps) && array_key_exists($dateshift['shift'], $techmaps[$str_date])) {
        $dateshift['editions'] = $techmaps[$str_date][$dateshift['shift']];
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

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
            
// Список технологических карт
$techmaps = [];
            
$sql = "select t.id, t.work_date, t.work_shift, c.name "
        . "from techmap t "
        . "inner join calculation c on t.calculation_id = c.id "
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
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include 'include/head.php';
        ?>
    </head>
    <body>
        <?php
        include 'include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1 text-nowrap">
                    <h1 style="font-size: 32px; font-weight: 600;" class="d-inline">Расписание</h1>
                </div>
                <div class="p-1 text-nowrap">
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label for="from" class="mr-2" style="font-size: large;">От</label>
                            <input type="date" name="from" value="<?= filter_input(INPUT_GET, 'from') ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="to" class="ml-2 mr-2" style="font-size: large;">до</label>
                            <input type="date" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark ml-2">OK</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-bordered">
                <?php foreach ($dateshifts as $dateshift): ?>
                <tr>
                    <td style="width: 5%;"><?= $GLOBALS['weekdays'][$dateshift['date']->format('w')] ?></td>
                    <td style="width: 10%;"><?=$dateshift['date']->format('d.m.Y') ?></td>
                    <td>
                        <?php
                        /*$date_formatted = $dateshift['date']->format('Y-m-d');
                        
                        if(key_exists($date_formatted, $techmaps)):
                        foreach ($techmaps[$date_formatted] as $techmap):
                        ?>
                        <p><a href="<?=APPLICATION ?>/techmap/details.php?id=<?=$techmap['id'] ?>"><?=$techmap['name'] ?></a></p>
                        <?php
                        endforeach;
                        endif;*/
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>