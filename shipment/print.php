<?php
include '../include/topscripts.php';

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    include '../include/_unauthorized.php';
}

// Список id отгрузок для печати
$ids_param = filter_input(INPUT_GET, 'ids');
$ids = array_filter(array_map('intval', explode(',', $ids_param ?? '')));

if(empty($ids)) {
    header("Location: index.php");
    exit();
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));

// Черновики распечатать нельзя -- документ уже недействителен (как и при выгрузке в excel)
$sql = "select id, document_number, vehicle_number, driver_name, cargo_type, places_count, gross_weight, net_weight, volume, created_at, "
        . "max_pallet_length, max_pallet_width, max_pallet_height "
        . "from shipment where is_draft = 0 and id in ($placeholders) order by id";
$grabber = new Grabber($sql, $ids);
$shipments = $grabber->result;

if(empty($shipments)) {
    header("Location: index.php");
    exit();
}

// Ответственные и заказчики по каждой отгрузке -- та же логика, что и в excel.php
foreach($shipments as &$shipment) {
    $sql = "select distinct u.last_name, u.first_name "
            . "from shipment_calculation sc "
            . "inner join calculation c on sc.calculation_id = c.id "
            . "left join calculation_status_history csh on csh.id = (select id from calculation_status_history where calculation_id = c.id order by date desc limit 1) "
            . "left join user u on csh.user_id = u.id "
            . "where sc.shipment_id = ? and u.id is not null";
    $responsible_grabber = new Grabber($sql, [$shipment['id']]);
    $responsible_names = array();
    foreach($responsible_grabber->result as $row) {
        $responsible_names[] = trim($row['last_name'].' '.$row['first_name']);
    }
    $shipment['responsible'] = implode(', ', $responsible_names);

    $sql = "select distinct cus.name "
            . "from shipment_calculation sc "
            . "inner join calculation c on sc.calculation_id = c.id "
            . "inner join customer cus on c.customer_id = cus.id "
            . "where sc.shipment_id = ?";
    $customer_grabber = new Grabber($sql, [$shipment['id']]);
    $customer_names = array();
    foreach($customer_grabber->result as $row) {
        $customer_names[] = $row['name'];
    }
    $shipment['customer'] = implode(', ', $customer_names);

    $shipment['max_pallet_dimensions'] = '—';
    if($shipment['max_pallet_length'] !== null) {
        $shipment['max_pallet_dimensions'] = DisplayNumber(floatval($shipment['max_pallet_length']), 2).'×'.DisplayNumber(floatval($shipment['max_pallet_width']), 2).'×'.DisplayNumber(floatval($shipment['max_pallet_height']), 2).' м';
    }
}
unset($shipment);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <link href="<?=APPLICATION ?>/fontawesome-free-5.15.1-web/css/all.min.css" rel="stylesheet" />
        <link href="<?=APPLICATION ?>/css/main.css?version=73" rel="stylesheet">
        <link rel="shortcut icon" type="image/x-icon" href="<?=APPLICATION ?>/favicon.ico" />
        <style>
            body {
                margin: 0;
                padding: 10mm;
                font-family: 'SF Pro Display';
                font-size: 13px;
            }

            table {
                border-collapse: collapse;
            }

            .shipments-row {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .shipment-card {
                width: 48%;
                margin-bottom: 20px;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .shipment-card-title {
                font-weight: bold;
                font-size: 15px;
                text-align: center;
                border-bottom: solid 2px #333;
                padding-bottom: 4px;
                margin-bottom: 4px;
            }

            .shipment-card table {
                width: 100%;
            }

            .shipment-card table td {
                padding: 3px 4px;
                vertical-align: top;
                border-bottom: solid 1px #ddd;
                font-size: 12px;
            }

            .shipment-card table td:first-child {
                font-weight: bold;
                white-space: nowrap;
                width: 40%;
            }

            .shipment-card-footer {
                margin-top: 4px;
                font-size: 11px;
            }

            .shipment-card-footer div {
                margin-top: 10px;
            }

            @media print {
                body {
                    padding: 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="shipments-row">
            <?php foreach($shipments as $shipment): ?>
            <?php $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $shipment['created_at']); ?>
            <div class="shipment-card">
                <div class="shipment-card-title">Акт-отчёт №<?=$shipment['id'] ?> о передаче данных по отгрузке</div>
                <table>
                    <tr><td>Дата создания отгрузки</td><td><?=$created_at->format('d.m.Y H:i') ?></td></tr>
                    <tr><td>Ответственный</td><td><?=htmlspecialchars($shipment['responsible']) ?></td></tr>
                    <tr><td>Документ</td><td><?=htmlspecialchars($shipment['document_number']) ?></td></tr>
                    <tr><td>Контрагент</td><td><?=htmlspecialchars($shipment['customer']) ?></td></tr>
                    <tr><td>Транспортное средство</td><td><?=htmlspecialchars($shipment['vehicle_number']) ?></td></tr>
                    <tr><td>ФИО водителя</td><td><?=htmlspecialchars($shipment['driver_name'] ?? '') ?></td></tr>
                    <tr><td>Наименование груза</td><td><?=CARGO_TYPE_NAMES[$shipment['cargo_type']] ?? '' ?></td></tr>
                    <tr><td>Количество паллетов</td><td><?= DisplayNumber(intval($shipment['places_count']), 0) ?></td></tr>
                    <tr><td>Максимальный</td><td><?=$shipment['max_pallet_dimensions'] ?></td></tr>
                    <tr><td>Масса брутто</td><td><?= DisplayNumber(floatval($shipment['gross_weight']), 0) ?> кг</td></tr>
                    <tr><td>Масса нетто</td><td><?= DisplayNumber(floatval($shipment['net_weight']), 0) ?> кг</td></tr>
                    <tr><td>Объём</td><td><?= DisplayNumber(floatval($shipment['volume']), 2) ?> м3</td></tr>
                </table>
                <div class="shipment-card-footer">
                    <div>Способ определения массы: Данные складского учёта (взвешивание)</div>
                    <div>Подпись кладовщика: ______________________</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <script>
            var css = '@page { size: A4 portrait; margin: 10mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            window.print();
        </script>
    </body>
</html>
