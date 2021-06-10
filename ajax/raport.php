<?php
include '../include/topscripts.php';

$machine_id = filter_input(INPUT_GET, 'machine_id');

echo "<option value='' hidden='hidden'>Рапорт...</option>";

if(!empty($machine_id)) {
    $sql = "select name, value from raport where machine_id = $machine_id order by value";
    $grabber = new Grabber($sql);
    $result = $grabber->result;
    
    foreach ($result as $row) {
        $name = $row['name'];
        $value = $row['value'];
        $raport = empty($name) ? $value : $name;
        echo "<option value='$value'>$raport</option>";
    }
}
?>