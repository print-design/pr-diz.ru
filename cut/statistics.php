<?php
include '../include/topscripts.php';

const WEEKEND = 0;
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
                
            $date_from = null;
            $date_to = null;
            GetDateFromDateTo($from, $to, $date_from, $date_to);
                
            // Смены и резчики
            $workers = array();
            $worker_names = array(WEEKEND => "ВЫХОДНОЙ ДЕНЬ");
            $workers_sorted = array();
    
            $sql = "select date_format(pw.date, '%d-%m-%Y') date, pw.shift, pw.machine_id, pe.id employee_id, pe.last_name, pe.first_name "
                    . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
                    . "where pw.date >= '".(clone $date_from)->modify("-1 day")->format('Y-m-d')."' and pw.date <= '".$date_to->format('Y-m-d')."' "
                    . "and pw.work_id = ".WORK_CUTTING." "
                    . "order by last_name, first_name, date, shift";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                if(empty($row['employee_id'])) {
                    $workers[$row['date'].$row['shift'].$row['machine_id']] = WEEKEND;
                }
                else {
                    $workers[$row['date'].$row['shift'].$row['machine_id']] = $row['employee_id'];
                    $worker_names[$row['employee_id']] = $row['last_name'].' '. mb_substr($row['first_name'], 0, 1).'.';
                    
                    if(!in_array($row['employee_id'], $workers_sorted)) {
                        array_push($workers_sorted, $row['employee_id']);
                    }
                }
            }
            
            if(in_array(WEEKEND, $workers)) {
                array_push($workers_sorted, WEEKEND);
            }
            
            $sql = "select cts.id, cts.printed, c.unit, cs.name, e.machine_id "
                    . "from calculation_take_stream cts "
                    . "inner join calculation_take ct on cts.calculation_take_id = ct.id "
                    . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
                    . "inner join calculation c on ct.calculation_id = c.id "
                    . "inner join plan_edition e on e.calculation_id = c.id "
                    . "where cts.printed >= '".$date_from->format('Y-m-d')."' and cts.printed <= '".$date_to->format('Y-m-d')."' and e.work_id = ". WORK_CUTTING." "
                    . "order by printed";
            $grabber = new Grabber($sql);
            $error_message = $grabber->error;
            $take_streams = $grabber->result;
            
            foreach ($take_streams as $take_stream) {
                $printed = DateTime::createFromFormat('Y-m-d H:i:s', $take_stream['printed']);
                // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
                // Ночная смена: 20:00 текущего дна - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
                // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
                $stream_hour = $printed->format('G');
                $stream_shift = 'day';
                $working_printed = clone $printed; // Дата с точки зрения рабочего графика (напр. ночь 7 марта считается ночной сменой 6 марта)
            
                if($stream_hour > 19 && $stream_hour < 24) {
                    $stream_shift = 'night';
                }
                elseif($stream_hour >= 0 && $stream_hour < 8) {
                    $stream_shift = 'night';
                    $working_printed->modify("-1 day");
                }
            
                $stream_worker = WEEKEND;
            
                if(array_key_exists($working_printed->format('d-m-Y').$stream_shift.$take_stream['machine_id'], $workers)) {
                    $stream_worker = $workers[$working_printed->format('d-m-Y').$stream_shift.$take_stream['machine_id']];
                }
                
                echo "<p>".$take_stream['id'].' '.$printed->format('d.m.Y H:i').' '.$take_stream['unit'].' '.$take_stream['name'].' '.CUTTER_NAMES[$take_stream['machine_id']].' '.$worker_names[$stream_worker]."</p>";
            }
            
            foreach($workers_sorted as $worker) {
                echo $worker_names[$worker]."<br />";
            }
            ?>
            
            <?php endif; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>