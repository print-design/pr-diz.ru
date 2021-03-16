<?php
include '../include/topscripts.php';
include '../fpdf182/fpdf.php';

// Если не задано значение id, сообщаем об этом
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    $pdf = new FPDF('L', 'in', [4, 6]);
    $pdf->AddPage();
    $pdf->AddFont('TimesBI','','timesbi.php');
    $pdf->SetFont('TimesBI');
    $pdf->Write(0,iconv('utf-8', 'windows-1251',"Ошибка"));
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
$pdf->AddFont('Arial', '', 'arial.php');
$pdf->AddFont('ArialBI', '', 'arialbi.php');

// Заголовок
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(34, 138, 214);
$pdf->Write(0, iconv('utf-8', 'windows-1251', "< Назад"), "pallet.php?id=".filter_input(INPUT_GET, 'id'));
$pdf->SetFont('ArialBI', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Write(0, iconv('utf-8', 'windows-1251', "                    ООО «Принт-Дизайн»"));
$pdf->Ln();

// Таблица
$pdf->SetDrawColor(222, 226, 230);
$pdf->SetFont('Arial', '', 12);

$pdf->SetX(0);
$pdf->SetY(0.6);
$pdf->Cell(2.8, 0.4, '', 1);
$pdf->Cell(0.1, 0.4, '', 0);
$pdf->Cell(2.6, 0.4, iconv('utf-8', 'windows-1251', "Паллет № П$id от ". DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y')), 0);

$pdf->SetX(0);
$pdf->SetY(1);
$pdf->Cell(2.8, 0.4, '', 1);
$pdf->Cell(0.1, 0.4, '', 0);

// Формируем QR-код
include '../qr/qrlib.php';
$errorCorrectionLevel = 'L'; // 'L','M','Q','H'
$data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
$current_date_time = date("dmYHis");
$filename = "../temp/$current_date_time.png";
QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);

// Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
$files = scandir("../temp/");
foreach ($files as $file) {
    if($file != "$current_date_time.png" && !is_dir($file)) {
        unlink("../temp/$file");
    }
}
$pdf->Image($filename, null, null, 2.2);

$pdf->Output();
?>