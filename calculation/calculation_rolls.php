<?php
require_once '../include/constants.php';

// Готовые ролики
class CalculationRolls {
    // "Примерный" объём для отображения -- среднее между минимумом и максимумом
    public $volume = 0;
    
    // Минимум -- если ролики уложены плотно, "в шахматном порядке" (каждый следующий ряд вложен в промежутки предыдущего)
    public $volume_min = 0;
    
    // Максимум -- если ролики уложены просто рядами, каждый в своей отдельной "ячейке" (безопасная верхняя граница)
    public $volume_max = 0;
    
    // Общий итог по заказу: катушки/вес/метраж из съёмов + рулоны постороннего происхождения.
    // Заполняется только после вызова LoadDetails().
    public $totals = array('bobbins' => 0, 'weight' => 0, 'length' => 0);
    
    // Сводка по ручьям: [{id, name, width, image1, image2, bobbins, weight, length}, ...]
    public $streams = array();
    
    // Съёмы: [{id, timestamp, weight, length, worker, rolls: [{id, name, width, printed, weight, length, last_name, first_name}, ...]}, ...]
    public $takes = array();
    
    // Рулоны постороннего происхождения -- добавленные к отгрузке, но не являющиеся
    // результатом резки данного заказа (в БД -- calculation_not_take_stream)
    public $externalRolls = array();
    public $externalTotals = array('weight' => 0, 'length' => 0);
    
    private $calculationId;
    private $detailsLoaded = false;
    
    public function __construct($id) {
        $this->calculationId = $id;
        
        $sql = "select sum(power(ifnull(cts.radius, 0) * 2 + ifnull(tm.spool, 0), 2) * ifnull(cs.width, 0) / 1000000000) as volume "
                . "from calculation c "
                . "inner join techmap tm on tm.calculation_id = c.id "
                . "inner join calculation_stream cs on cs.calculation_id = c.id "
                . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
                . "where c.id = ?";
        $fetcher = new Fetcher($sql, [$id]);
        if($row = $fetcher->Fetch()) {
            $this->volume_max = $row[0] ?? 0;
        }
        
        // Плотная ("шахматная") укладка теоретически компактнее укладки рядами в pi / (2 * sqrt(3)) раз (~1,1547),
        // то есть требует примерно в sqrt(3) / 2 (~0,866) раз меньше места при том же количестве роликов.
        // Точная экономия зависит от конкретной раскладки (сколько рядов, в какую сторону идёт вложение),
        // поэтому здесь используется теоретический предел как ориентировочная нижняя граница.
        $this->volume_min = $this->volume_max * (sqrt(3) / 2);
        
        $this->volume = ($this->volume_min + $this->volume_max) / 2;
    }

    public static function Create($id) {
        return new CalculationRolls($id);
    }
    
    // Загружает детальную разбивку по ручьям и съёмам -- отдельно от конструктора,
    // потому что нужна только на странице результатов резки (cut/_table.php),
    // а не всем, кто просто хочет знать объём готовых роликов (pack/details.php, buh/details.php).
    // $machine_id нужен только для определения резчика по смене -- можно не передавать,
    // тогда поле "worker" у каждого съёма будет пустым.
    public function LoadDetails($machine_id = null) {
        if($this->detailsLoaded) {
            return;
        }
        $this->detailsLoaded = true;
        
        $id = $this->calculationId;
        
        // Сводка по ручьям (катушки/вес/метраж из съёмов и не из съёма вместе)
        $sql = "select cs.id, cs.name, cs.width, cs.image1, cs.image2, "
                . "ifnull((select count(id) from calculation_take_stream where calculation_stream_id = cs.id and weight > 0 and length > 0), 0) "
                . "+ ifnull((select count(id) from calculation_not_take_stream where calculation_stream_id = cs.id and weight > 0 and length > 0), 0) "
                . "bobbins, "
                . "ifnull((select sum(weight) from calculation_take_stream where calculation_stream_id = cs.id), 0) "
                . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id = cs.id), 0) "
                . "weight, "
                . "ifnull((select sum(length) from calculation_take_stream where calculation_stream_id = cs.id), 0) "
                . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id = cs.id), 0) "
                . "length "
                . "from calculation_stream cs "
                . "where cs.calculation_id = ? "
                . "order by cs.position";
        $grabber = new Grabber($sql, [$id]);
        $this->streams = $grabber->result;
        
        // Итоги по каждому съёму -- один запрос
        $sql = "select ct.id, max(cts.printed) timestamp, sum(cts.weight) weight, sum(cts.length) length "
                . "from calculation_take_stream cts "
                . "left join calculation_take ct on cts.calculation_take_id = ct.id "
                . "where ct.calculation_id = ? "
                . "group by cts.calculation_take_id "
                . "order by ct.timestamp";
        $grabber = new Grabber($sql, [$id]);
        $takes = $grabber->result;
        
        // Катушки ВСЕХ съёмов -- тоже один запрос (вместо запроса на каждый съём в цикле),
        // затем раскладываем по съёмам в PHP
        $rollsByTake = array();
        
        if(!empty($takes)) {
            $sql = "select cts.id, cts.calculation_take_id, cs.name, cs.width, cts.printed, cts.weight, cts.length, pe.last_name, pe.first_name "
                    . "from calculation_take_stream cts "
                    . "inner join calculation_stream cs on cts.calculation_stream_id = cs.id "
                    . "left join plan_employee pe on cts.plan_employee_id = pe.id "
                    . "where cts.calculation_take_id in (select id from calculation_take where calculation_id = ?) "
                    . "order by cts.calculation_take_id, cs.position";
            $grabber = new Grabber($sql, [$id]);
            
            foreach($grabber->result as $roll) {
                $rollsByTake[$roll['calculation_take_id']][] = $roll;
            }
        }
        
        // Смены и резчики -- только если указана машина
        $workers = array();
        
        if(!empty($machine_id)) {
            $sql = "select date_format(pw.date, '%d-%m-%Y') date, pw.shift, pe.last_name, pe.first_name "
                    . "from plan_workshift1 pw inner join plan_employee pe on pw.employee1_id = pe.id "
                    . "where (pw.date in (select cast(timestamp as date) from calculation_take where calculation_id = ?) "
                    . "or pw.date = (select cast(min(timestamp) - interval 1 day as date) from calculation_take where calculation_id = ?) "
                    . "or pw.date in (select cast(printed as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = ?)) "
                    . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = ?)) "
                    . "or pw.date in (select cast(printed as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = ?)) "
                    . "or pw.date = (select cast(min(printed) - interval 1 day as date) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = ?))) "
                    . "and pw.work_id = ? and pw.machine_id = ? "
                    . "order by date, shift";
            $fetcher = new Fetcher($sql, [$id, $id, $id, $id, $id, $id, WORK_CUTTING, $machine_id]);
            while($row = $fetcher->Fetch()) {
                if(empty($row['last_name']) && empty($row['first_name'])) {
                    $workers[$row['date'].$row['shift']] = "ВЫХОДНОЙ ДЕНЬ";
                }
                else {
                    $workers[$row['date'].$row['shift']] = $row['last_name'].' '.mb_substr($row['first_name'], 0, 1).'.';
                }
            }
        }
        
        // Определение смены/резчика по времени съёма (та же логика, что была раньше в шаблоне)
        foreach($takes as &$take) {
            $take['rolls'] = $rollsByTake[$take['id']] ?? array();
            
            $take_date = DateTime::createFromFormat('Y-m-d H:i:s', $take['timestamp']);
            $take_hour = $take_date->format('G');
            $take_shift = 'day';
            $working_take_date = clone $take_date;
            
            // Дневная смена: 8:00 текущего дня - 19:59 текущего дня
            // Ночная смена: 20:00 текущего дня - 23:59 текущего дня, 0:00 предыдущего дня - 7:59 предыдущего дня
            // (например, когда наступает 0:00 7 марта, то это считается ночной сменой 6 марта)
            if($take_hour > 19 && $take_hour < 24) {
                $take_shift = 'night';
            }
            elseif($take_hour >= 0 && $take_hour < 8) {
                $take_shift = 'night';
                $working_take_date->modify("-1 day");
            }
            
            $take['worker'] = "ВЫХОДНОЙ ДЕНЬ";
            
            if(array_key_exists($working_take_date->format('d-m-Y').$take_shift, $workers)) {
                $take['worker'] = $workers[$working_take_date->format('d-m-Y').$take_shift];
            }
        }
        unset($take);
        
        $this->takes = $takes;
        
        // Рулоны постороннего происхождения (не из съёма)
        $sql = "select cnts.id, cs.name, cnts.printed, cnts.weight, cnts.length, pe.last_name, pe.first_name "
                . "from calculation_not_take_stream cnts "
                . "inner join calculation_stream cs on cnts.calculation_stream_id = cs.id "
                . "left join plan_employee pe on cnts.plan_employee_id = pe.id "
                . "where cs.calculation_id = ?";
        $grabber = new Grabber($sql, [$id]);
        $this->externalRolls = $grabber->result;
        
        $this->externalTotals = array('weight' => 0, 'length' => 0);
        
        foreach($this->externalRolls as $roll) {
            $this->externalTotals['weight'] += $roll['weight'];
            $this->externalTotals['length'] += $roll['length'];
        }
        
        // Общий итог: катушки/вес/метраж из съёмов + рулоны постороннего происхождения
        $this->totals = array('bobbins' => 0, 'weight' => 0, 'length' => 0);
        
        $sql = "select count(id) bobbins, sum(weight) weight, sum(length) length "
                . "from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = ?) and weight > 0 and length > 0";
        $fetcher = new Fetcher($sql, [$id]);
        if($row = $fetcher->Fetch()) {
            $this->totals['bobbins'] = intval($row['bobbins']);
            $this->totals['weight'] = floatval($row['weight']);
            $this->totals['length'] = floatval($row['length']);
        }
        
        $sql = "select count(id) bobbins, sum(weight) weight, sum(length) length "
                . "from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = ?) and weight > 0 and length > 0";
        $fetcher = new Fetcher($sql, [$id]);
        if($row = $fetcher->Fetch()) {
            $this->totals['bobbins'] += intval($row['bobbins']);
            $this->totals['weight'] += floatval($row['weight']);
            $this->totals['length'] += floatval($row['length']);
        }
    }
}
?>
