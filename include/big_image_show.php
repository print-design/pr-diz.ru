<?php
include '../include/topscripts.php';

$object = filter_input(INPUT_GET, 'object');
$id = filter_input(INPUT_GET, 'id');
$image = filter_input(INPUT_GET, 'image');

$stream_id = filter_input(INPUT_GET, 'stream_id');
$calculation_id = filter_input(INPUT_GET, 'calculation_id');

$result = array( 'error' => '' );

// Вариант 1. object + id + image
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

// Вариант 2. stream_id
if(!empty($stream_id)) {
    $sql = "select name, image1, image2 from calculation_stream where id = $stream_id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result["name"] = htmlentities($row["name"]);
        if(!empty($row['image1'])) {
            $result["filename"] = $row["image1"];
            $result["image"] = 1;
        }
        elseif(!empty ($row['image2'])) {
            $result["filename"] = $row["image2"];
            $result["image"] = 2;
        }
    }
}

// Вариант 3. calculation_id
if(!empty($calculation_id)) {
    $result['name'] = '';
    $result['filename'] = '';
    $result['object'] = '';
    $result['id'] = null;
    $result['image'] = null;
    
    $sql = "select id, name, image1, image2 from calculation_stream where calculation_id = $calculation_id and (image1 <> '' or image2 <> '')";
    $fetcher = new Fetcher($sql);
    while (($row = $fetcher->Fetch()) && empty($result['name'])) {
        $result['name'] = $row['name'];
        $result['id'] = $row['id'];
        $result['object'] = STREAM;
        if(!empty($row['image1'])) {
            $result['filename'] = $row['image1'];
            $result['image'] = 1;
        }
        elseif(!empty ($row['image2'])) {
            $result['filename'] = $row['image2'];
            $result['image'] = 2;
        }
    }
    
    if(empty($result['name']) || empty($result['filename']) || empty($result['object']) || empty($result['id']) || empty($result['image'])) {
        $sql = "select cq.id, concat(c.name, cq.id) name, cq.image1, cq.image2 "
                . "from calculation_quantity cq "
                . "inner join calculation c on cq.calculation_id = c.id "
                . "where c.id = $calculation_id and (image1 <> '' or image2 <> '')";
        $fetcher = new Fetcher($sql);
        while (($row = $fetcher->Fetch()) && empty($result['name'])) {
            $result['name'] = $row['name'];
            $result['id'] = $row['id'];
            $result['object'] = PRINTING;
            if(!empty($row['image1'])) {
                $result['filename'] = $row['image1'];
                $result['image'] = 1;
            }
            elseif(!empty ($row['image2'])) {
                $result['filename'] = $row['image2'];
                $result['image'] = 2;
            }
        }
    }
}

echo json_encode($result);
?>