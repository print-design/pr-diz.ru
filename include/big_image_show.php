<?php
include '../include/topscripts.php';

const STREAM = "stream";
const PRINTING = "printing";

$object = filter_input(INPUT_GET, 'object');
$id = filter_input(INPUT_GET, 'id');
$image = filter_input(INPUT_GET, 'image');

$result = array( 'error' => '' );

if(!empty($object) && !empty($id) && !empty($image)) {
    $sql = "";
    
    if($object == STREAM) {
        $sql = "select name, image$image "
                . "from calculation_stream "
                . "where id = $id";
    }
    elseif($object == PRINTING) {
        $sql = "select concat(c.name, cq.id) name, cq.image$image "
                . "from calculation_quantity cq "
                . "inner join calculation c on cq.calculation_id = c.id "
                . "where cq.id = $id";
    }
    
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result["name"] = htmlentities($row["name"]);
        $result["filename"] = $row["image$image"];
        $result["delete_file_name"] = htmlentities($row['name']).", ".($image == 1 ? "с подписью заказчика" : "без подписи заказчика");
    }
}

echo json_encode($result);
?>