<?php
include '../include/topscripts.php';

$id = 0;
$date = null;
$customer_id = 0;
$sql = "select id, date, customer_id from calculation where duplicate_status_id is null";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row['id'];
    $date = $row['date'];
    $customer_id = $row['customer_id'];
}

$result = 10000000;

if(!empty($id) && !empty($date) && !empty($customer_id)) {
    $sql = "update calculation set "
            . "duplicate_quantities = (select count(quantity) from calculation_quantity where calculation_id = $id), "
            . "duplicate_quantity_sum = (select sum(quantity) from calculation_quantity where calculation_id = $id), "
            . "duplicate_gap_raport = (select gap_raport from norm_gap where date <= '$date' order by id desc limit 1), "
            . "duplicate_length_cut = ifnull((select sum(length) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)), 0) "
            . "+ ifnull((select sum(length) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id)), 0), "
            . "duplicate_weight_cut = ifnull((select sum(weight) from calculation_take_stream where calculation_take_id in (select id from calculation_take where calculation_id = $id)), 0) "
            . "+ ifnull((select sum(weight) from calculation_not_take_stream where calculation_stream_id in (select id from calculation_stream where calculation_id = $id)), 0), "
            . "duplicate_status_id = (select status_id from calculation_status_history where calculation_id = $id order by date desc limit 1), "
            . "duplicate_status_comment = (select comment from calculation_status_history where calculation_id = $id order by date desc limit 1), "
            . "duplicate_status_date = (select date from calculation_status_history where calculation_id = $id order by date desc limit 1) "
            . "where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $num_for_customer = 0;
        
        $sql = "select count(id) from calculation where customer_id = $customer_id and id <= $id";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $num_for_customer = $row[0];
        }
        
        $sql = "update calculation set duplicate_num_for_customer = $num_for_customer where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $sql = "select count(id) from calculation where duplicate_status_id is not null";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            $result = $row[0];
        }
    }
}

echo $result;
?>