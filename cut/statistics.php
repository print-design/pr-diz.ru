<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <h1>Статистика по резчикам</h1>
                </div>
                <div>
                    <form class="form-inline mt-1" method="get">
                        <label for="from" style="font-size: larger;">от&nbsp;</label>
                        <input type="date" 
                               name="from" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'from') ?>" 
                               style="border: 0; width: 8.5rem;" />
                        <label for="to" style="font-size: larger;">до&nbsp;</label>
                        <input type="date" 
                               name="to" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'to') ?>" 
                               style="border: 0;" />
                        <button type="submit" class="btn btn-light ml-2""><i class="fas fa-list"></i>&nbsp;&nbsp;Сформировать</button>
                        <a href="statistics.php" class="btn btn-light ml-2"><i class="fas fa-times"></i>&nbsp;&nbsp;Очистить</a>
                    </form>
                </div>
            </div>
            <?php
            $from = filter_input(INPUT_GET, 'from');
            $to = filter_input(INPUT_GET, 'to');
                
            if(!empty($from) && !empty($to)):
            //************************************************
                
            // Смены и резчики
            $workers = array();
    
            $sql = "select date_format(pw.date, '%d-%m-%Y') date, pw.shift, pe.last_name, pe.first_name "
                    . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
                    . "where (pw.date in (select cast(timestamp as date) from calculation_take where calculation_id = $id) "
                    . "or pw.date = (select cast(min(timestamp) - interval 1 day as date) from calculation_take where calculation_id = $id) "
                    . "or pw.date in (select cast(printed as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)) "
                    . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)) "
                    . "or pw.date in (select cast(printed as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id)) "
                    . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id))) "
                    . "and pw.work_id = ".WORK_CUTTING." and pw.machine_id = $machine_id "
                    . "order by date, shift";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                if(empty($row['last_name']) && empty($row['first_name'])) {
                    $workers[$row['date'].$row['shift']] = "ВЫХОДНОЙ ДЕНЬ";
                }
                else {
                    $workers[$row['date'].$row['shift']] = $row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.';
                }
            }
                    
            $date_from = null;
            $date_to = null;
            GetDateFromDateTo($from, $to, $date_from, $date_to);
            
            $sql = "select cts.id, cts.printed, c.unit, cs.name "
                    . "from calculation_take_stream cts "
                    . "inner join calculation_take ct on cts.calculation_take_id = ct.id "
                    . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
                    . "inner join calculation c on ct.calculation_id = c.id "
                    . "where cts.printed >= '".$date_from->format('Y-m-d')."' and cts.printed <= '".$date_to->format('Y-m-d')."' "
                    . "order by printed";
            $grabber = new Grabber($sql);
            $error_message = $grabber->error;
            $take_streams = $grabber->result;
            
            foreach ($take_streams as $take_stream) {
                $printed = DateTime::createFromFormat('Y-m-d H:i:s', $take_stream['printed']);
                echo "<p>".$take_stream['id'].' '.$printed->format('d.m.Y H:i').' '.$take_stream['unit'].' '.$take_stream['name']."</p>";
            }
            ?>
            
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>