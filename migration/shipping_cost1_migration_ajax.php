<?php
include '../include/topscripts.php';
include '../order/calculation.php';
include '../order/calculation_result.php';

$id = 0;
$result = 0;
$error_message = '';

$sql = "select distinct c.id "
        . "from calculation c "
        . "inner join calculation_result cr on cr.calculation_id = c.id "
        . "where c.duplicate_shipping_cost = 0 and cr.shipping_cost > 0 and cr.shipping_cost > 0 and c.duplicate_status_id <> ". ORDER_STATUS_DRAFT." and c.duplicate_status_id <> ". ORDER_STATUS_TRASH." "
        . "order by id desc";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $id = $row[0];
}

if($id > 0) {
    $calculation = CalculationBase::Create($id);
    $calculation_result = CalculationResult::Create($calculation);
}

if(empty($error_message)) {
    $sql = "select count(distinct c.id) "
            . "from calculation c "
            . "inner join calculation_result cr on cr.calculation_id = c.id "
            . "where c.duplicate_shipping_cost = 0 and cr.shipping_cost > 0 and cr.shipping_cost > 0 and c.duplicate_status_id <> ". ORDER_STATUS_DRAFT." and c.duplicate_status_id <> ". ORDER_STATUS_TRASH;
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $result = $row[0];
    }
}
echo $result;
?>