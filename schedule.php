<?php
include 'include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
            <?php
            $date_from = null;
            $date_to = null;
            GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
            
            $date_diff = $date_from->diff($date_to);
            $interval = DateInterval::createFromDateString("1 day");
            $period = new DatePeriod($date_from, $interval, $date_diff->days);
            $techmaps = array();
            
            $sql = "select t.id, t.work_date, c.name "
                    . "from techmap t "
                    . "inner join calculation c on t.calculation_id = c.id "
                    . "where t.work_date >='".$date_from->format('Y-m-d')."' and t.work_date <= '".$date_to->format('Y-m-d')."' "
                    . "order by t.id";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                if(!key_exists($row['work_date'], $techmaps)) {
                    $techmaps[$row['work_date']] = array();
                }
                
                $techmap = array();
                $techmap['id'] = $row['id'];
                $techmap['name'] = $row['name'];
                array_push($techmaps[$row['work_date']], $techmap);
            }
            ?>
            <table class="table table-bordered">
            <?php
            foreach ($period as $date): 
            $weekday = $date->format('w');
            $rowstyle = '';
            
            if($weekday == 6 || $weekday == 0) {
                $rowstyle = " style='background-color: lightcyan;'";
            }
            ?>
                <tr<?=$rowstyle ?>>
                    <td style="width: 5%;"><?= $GLOBALS['weekdays'][$date->format('w')] ?></td>
                    <td style="width: 10%;"><?=$date->format('d.m.Y') ?></td>
                    <td>
                        <?php
                        $date_formatted = $date->format('Y-m-d');
                        
                        if(key_exists($date_formatted, $techmaps)):
                        foreach ($techmaps[$date_formatted] as $techmap):
                        ?>
                        <p><a href="<?=APPLICATION ?>/techmap/details.php?id=<?=$techmap['id'] ?>"><?=$techmap['name'] ?></a></p>
                        <?php
                        endforeach;
                        endif;
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