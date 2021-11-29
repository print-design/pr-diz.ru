<?php
include '../include/topscripts.php';

$customer_id = filter_input(INPUT_GET, 'customer_id');

if(!empty($customer_id)):
$sql = "select distinct name from request_calc where customer_id=$customer_id order by name";
$fetcher = new Fetcher($sql);

while($row = $fetcher->Fetch()):
?>
<option value="<?= htmlentities($row['name']) ?>" />
<?php
endwhile;
endif;
?>