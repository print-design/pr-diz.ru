<?php
include '../include/topscripts.php';

$machine_type = filter_input(INPUT_GET, 'machine_type');

echo "<option value='' hidden='hidden'>Рапорт...</option>";

if($machine_type == "comiflex") {
    $sql = "select r.value "
            . "from raport r "
            . "inner join machine m on r.machine_id = m.id "
            . "where m.shortname = 'comiflex' "
            . "order by r.value";
    $grabber = new Grabber($sql);
    $result = $grabber->result;
    
    foreach($result as $row) {
        $value = $row['value'];
        $raport = floatval($value);
        echo "<option value='$value'>$raport</option>";
    }
}
elseif($machine_type == "zbs") {
    $sql = "select distinct r.value "
            . "from raport r "
            . "inner join machine m on r.machine_id = m.id "
            . "where m.shortname in ('zbs1', 'zbs2', 'zbs3') "
            . "order by r.value";
    $grabber = new Grabber($sql);
    $result = $grabber->result;
    
    foreach ($result as $row) {
        $value = $row['value'];
        $raport = floatval($value);
        echo "<option value='$value'>$raport</option>";
    }
}
?>