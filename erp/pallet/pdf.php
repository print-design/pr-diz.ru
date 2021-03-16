<?php
include '../include/topscripts.php';
define(FPDF_FONTPATH, APPLICATION."/font");
include '../fpdf182/fpdf.php';

// Если не задано значение id, сообщаем об этом
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    $pdf = new FPDF('L', 'in', [4, 6]);
    $pdf->AddPage();
    //$pdf->AddFont('Arial','','arial.php');
    //$pdf->SetFont('Arial');
    //$pdf->Write(0,iconv('utf-8', 'windows-1251',"Коммерческое предложение"));
    $txt = 'Не задан параметр id';
    $txt = iconv('utf-8', 'windows-1251', $txt);
    $pdf->Cell(1, 2, $txt);
    $pdf->Output();
}

// Получение данных
$sql = "select p.date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, s.name supplier, p.id_from_supplier, "
        . "p.film_brand_id, fb.name film_brand, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, "
        . "(select ps.name from pallet_status_history psh left join pallet_status ps on psh.status_id = ps.id where psh.pallet_id = p.id order by psh.id desc limit 0, 1) status, "
        . "p.comment "
        . "from pallet p "
        . "left join user u on p.storekeeper_id = u.id "
        . "left join supplier s on p.supplier_id = s.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "where p.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];
$supplier_id = $row['supplier_id'];
$supplier = $row['supplier'];
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$film_brand = $row['film_brand'];
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$rolls_number = $row['rolls_number'];
$cell = $row['cell'];
$status = $row['status'];
$comment = $row['comment'];

// Определяем удельный вес
$ud_ves = null;
$sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ud_ves = $row[0];
}

// Генерация PDF
$pdf = new FPDF('L', 'in', [4, 6]);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(1, 2, 'Hello world');
$pdf->Output();
?>