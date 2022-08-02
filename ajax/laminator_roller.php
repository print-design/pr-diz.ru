<?php
include '../include/topscripts.php';

$laminator_id = filter_input(INPUT_GET, 'laminator_id');

echo "<option value='' hidden='hidden'>Ширина ламинирующего вала...</option>";

$sql = "select value from norm_laminator_roller where laminator_id = $laminator_id and active = 1";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    echo "<option>".$row[0]."</option>";
}
?>