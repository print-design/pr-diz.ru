<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

echo "<option value='' hidden='hidden'>Рапорт...</option>";

if(!empty($machine_id)) {
    $sql = "select value from raport where machine_id = $machine_id order by value"; echo $sql;
    $grabber = new Grabber($sql);
    $result = $grabber->result;
    
    foreach ($result as $row) {
        $raport = $row['value'];
        echo "<option value='$raport'>".floatval($raport)."</option>";
    }
}
?>