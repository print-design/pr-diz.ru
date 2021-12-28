<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)) {
    $sql = "select pallet_id, ordinal from pallet_roll where id=$id";
    $fetcher = new Fetcher($sql);
    
    if($row = $fetcher->Fetch()) {
        echo "П".$row['pallet_id']."Р".$row['ordinal'];
        exit();
    }
    else {
        echo "Объект не найден";
        exit();
    }
}

echo "Ошибка";
?>