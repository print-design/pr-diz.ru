<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Виды красок
const CMYK = 'cmyk';
const PANTON = 'panton';
const WHITE = 'white';
const LACQUER = 'lacquer';

// Виды форм
const OLD = 'old';
const FLINT = 'flint';
const KODAK = 'kodak';
const TVER = 'tver';

// Текущее время
$current_date_time = date("dmYHis");

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.name, c.quantity, c.unit, c.stream_width, c.streams_number, c.length, c.raport, "
        . "c.brand_name, c.thickness, c.individual_brand_name, c.individual_thickness, "
        . "c.lamination1_brand_name, c.lamination1_thickness, c.lamination1_individual_brand_name, c.lamination1_individual_thickness, "
        . "c.lamination2_brand_name, c.lamination2_thickness, c.lamination2_individual_brand_name, c.lamination2_individual_thickness, "
        . "c.ink_1, c.ink_2, c.ink_3, c.ink_4, c.ink_5, c.ink_6, c.ink_7, c.ink_8, "
        . "c.color_1, c.color_2, c.color_3, c.color_4, c.color_5, c.color_6, c.color_7, c.color_8, "
        . "c.cmyk_1, c.cmyk_2, c.cmyk_3, c.cmyk_4, c.cmyk_5, c.cmyk_6, c.cmyk_7, c.cmyk_8, "
        . "c.cliche_1, c.cliche_2, c.cliche_3, c.cliche_4, c.cliche_5, c.cliche_6, c.cliche_7, c.cliche_8, "
        . "t.request_calc_id, t.date, t.reverse_print, t.shipment, t.spool, t.winding, t.sign, t.label, t.package, t.roll_type, t.comment, "
        . "cus.name customer, wt.name work_type, u.first_name, u.last_name "
        . "from request_calc c "
        . "inner join techmap t on t.request_calc_id = c.id "
        . "inner join customer cus on c.customer_id=cus.id "
        . "inner join work_type wt on c.work_type_id = wt.id "
        . "inner join user u on c.manager_id = u.id "
        . "where t.id=$id";
$row = (new Fetcher($sql))->Fetch();

$name = $row['name'];
$quantity = $row['quantity'];
$unit = $row['unit'];
$stream_width = $row['stream_width'];
$streams_number = $row['streams_number'];
$length = $row['length'];
$raport = $row['raport'];

$brand_name = $row['brand_name'];
$thickness = $row['thickness'];
$individual_brand_name = $row['individual_brand_name'];
$individual_thickness = $row['individual_thickness'];
$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination1_thickness = $row['lamination1_thickness'];
$lamination1_individual_brand_name = $row['lamination1_individual_brand_name'];
$lamination1_individual_thickness = $row['lamination1_individual_thickness'];
$lamination2_brand_name = $row['lamination2_brand_name'];
$lamination2_thickness = $row['lamination2_thickness'];
$lamination2_individual_brand_name = $row['lamination2_individual_brand_name'];
$lamination2_individual_thickness = $row['lamination2_individual_thickness'];

$lamination1_brand_name = $row['lamination1_brand_name'];
$lamination2_brand_name = $row['lamination2_brand_name'];

for($i=1; $i<=8; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $row[$ink_var];
    
    $color_var = "color_$i";
    $$color_var = $row[$color_var];
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $row[$cmyk_var];
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $row[$cliche_var];
}

$request_calc_id = $row['request_calc_id'];
$date = $row['date'];
$reverse_print = $row['reverse_print'];
$shipment = $row['shipment'];
$spool = $row['spool'];
$winding = $row['winding'];
$sign = $row['sign'];
$label = $row['label'];
$package = $row['package'];
$roll_type = $row['roll_type'];
$comment = $row['comment'];

$customer = $row['customer'];
$work_type = $row['work_type'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
?>