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
$sql = "select r.date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
        . "r.film_brand_id, fb.name film_brand, r.width, r.thickness, r.length, "
        . "r.net_weight, r.cell, "
        . "(select rs.name from roll_status_history rsh left join roll_status rs on rsh.status_id = rs.id where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status, "
        . "r.comment "
        . "from roll r "
        . "left join user u on r.storekeeper_id = u.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join film_brand fb on r.film_brand_id = fb.id "
        . "where r.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = iconv('utf-8', 'windows-1251', $row['last_name'].' '.$row['first_name']);
$supplier_id = $row['supplier_id'];
$supplier = iconv('utf-8', 'windows-1251', $row['supplier']);
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$film_brand = iconv('utf-8', 'windows-1251', $row['film_brand']);
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$cell = iconv('utf-8', 'windows-1251', $row['cell']);
$status = iconv('utf-8', 'windows-1251', $row['status']);
$comment = iconv('utf-8', 'windows-1251', $row['comment']);

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
$arial = "Arial";
$pdf->AddFont('Arial', '', 'arial.php');
$arialBI = "ArialBI";
$pdf->AddFont('ArialBI', '', 'arialbi.php');
$arialBD = 'ArialBD';
$pdf->AddFont('ArialBD', '', 'arialbd.php');

// Заголовок
$pdf->SetFont($arial, '', 8);
$pdf->SetTextColor(34, 138, 214);
$pdf->Write(0, iconv('utf-8', 'windows-1251', "< Назад"), "roll.php?id=".filter_input(INPUT_GET, 'id'));
$pdf->SetFont($arialBI, '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Write(0, iconv('utf-8', 'windows-1251', "                    ООО «Принт-Дизайн»"));
$pdf->Ln();

// Формируем QR-код
include '../qr/qrlib.php';
$errorCorrectionLevel = 'L'; // 'L','M','Q','H'
$data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
$current_date_time = date("dmYHis");
$filename = "../temp/$current_date_time.png";
QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 5, 2, true);

// Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
$files = scandir("../temp/");
foreach ($files as $file) {
    if($file != "$current_date_time.png" && !is_dir($file)) {
        unlink("../temp/$file");
    }
}

// Таблица
$pdf->SetDrawColor(222, 226, 230);

$pdf->SetX(0);
$pdf->SetY(0.6);
$pdf->SetFont($arial, '', 8);
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Поставщик"), 'LRT');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Ширина"), 'LRT');
$pdf->SetX(3.8);
$pdf->Cell(1.9, 0.2, iconv('utf-8', 'windows-1251', "Рулон № П$id от ". DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y')), 0);

$pdf->SetX(0);
$pdf->SetY(0.8);
$pdf->SetFont($arialBD);
$pdf->Cell(1.6, 0.2, $supplier, 'LRB');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "$width мм"), 'LRB');

$pdf->SetX(3.8);
$pdf->Image($filename, null, null, 1.5);

$pdf->SetX(0);
$pdf->SetY(1);
$pdf->SetFont($arial);
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "ID от поставщика"), 'LRT');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Толщина, уд.вес"), 'LRT');

$pdf->SetX(0);
$pdf->SetY(1.2);
$pdf->SetFont($arialBD);
$pdf->Cell(1.6, 0.2, $id_from_supplier, 'LRB');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "$thickness мкм, $ud_ves г/м2"), 'LRB');

$pdf->SetX(0);
$pdf->SetY(1.4);
$pdf->SetFont($arial);
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Кладовщик"), 'LRT');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Длина"), 'LRT');

$pdf->SetX(0);
$pdf->SetY(1.6);
$pdf->SetFont($arialBD);
$pdf->Cell(1.6, 0.2, $storekeeper, 'LRB');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "$length м"), 'LRB');

$pdf->SetX(0);
$pdf->SetY(1.8);
$pdf->SetFont($arial);
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Марка пленки"), 'LRT');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Масса нетто"), 'LRT');

$pdf->SetX(0);
$pdf->SetY(2);
$pdf->SetFont($arialBD);
$pdf->Cell(1.6, 0.2, $film_brand, 'LRB');
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "$net_weight кг"), 'LRB');

$pdf->SetX(0);
$pdf->SetY(2.2);
$pdf->SetFont($arial);
$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Статус"), 'LRT');
/*$pdf->Cell(1.6, 0.2, iconv('utf-8', 'windows-1251', "Количество рулонов"), 'LRT');*/

$pdf->SetX(0);
$pdf->SetY(2.4);
$pdf->SetFont($arialBD);
$pdf->Cell(1.6, 0.2, $status, 'LRB');
/*$pdf->Cell(1.6, 0.2, $rolls_number, 'LRB');*/

$pdf->SetX(0);
$pdf->SetY(2.6);
$pdf->SetFont($arial);
$pdf->Cell(4.8, 0.2, iconv('utf-8', 'windows-1251', "Комментарий"), 'LRT');

$pdf->SetX(0);
$pdf->SetY(2.8);
$pdf->SetFont($arialBD);
$pdf->MultiCell(4.8, 0.4, $comment, 'LRB');

$pdf->Output();
?>