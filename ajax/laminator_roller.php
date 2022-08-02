<?php
include '../include/topscripts.php';

$solvent = filter_input(INPUT_GET, 'solvent');

echo "<option value='' hidden='hidden'>Ширина ламинирующего вала...</option>";

$laminator_id = 0;

if($solvent == 'yes') {
    $laminator_id = 1;
}
elseif($solvent == 'no') {
    $laminator_id = 2;
}

$sql = "select value from norm_laminator_roller where laminator_id = $laminator_id and active = 1";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    echo "<option>".$row[0]."</option>";
}
?>