<?php
include '../include/topscripts.php';

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    include '../include/_unauthorized.php';
}

// Превращает строку "1,2,3" в массив уникальных положительных чисел
function ParseIdList($raw) {
    return array_values(array_unique(array_filter(array_map('intval', explode(',', $raw ?? '')))));
}

// Считает места/брутто/нетто/объём заново по спискам id -- никогда не доверяем этим числам,
// если они вдруг придут из формы: единственный источник истины -- сами заказы в базе.
// $ids -- полный список заказов (для веса нетто и объёма, которые привязаны к заказу).
// $pallet_ids -- список после снятия флажков-дублей (для веса брутто и количества мест,
// которые привязаны к физическому паллету, а не к заказу)
function GetShipmentTotals($ids, $pallet_ids) {
    $totals = array('places_count' => 0, 'gross_weight' => 0, 'net_weight' => 0, 'volume' => 0,
            'max_pallet_length' => null, 'max_pallet_width' => null, 'max_pallet_height' => null);
    
    if(!empty($ids)) {
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        
        $sql = "select sum(duplicate_weight_cut) net_weight from calculation where id in ($placeholders)";
        $fetcher = new Fetcher($sql, $ids);
        if($row = $fetcher->Fetch()) {
            $totals['net_weight'] = floatval($row['net_weight'] ?? 0);
        }
        
        // Суммарный объём -- та же формула, что и в CalculationRolls/_selected_calculations_summary.php
        $sql = "select sum(power(ifnull(cts.radius, 0) * 2 + ifnull(tm.spool, 0), 2) * ifnull(cs.width, 0) / 1000000000) as volume "
                . "from calculation c "
                . "inner join techmap tm on tm.calculation_id = c.id "
                . "inner join calculation_stream cs on cs.calculation_id = c.id "
                . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
                . "where c.id in ($placeholders)";
        $fetcher = new Fetcher($sql, $ids);
        if($row = $fetcher->Fetch()) {
            $volume_max = floatval($row[0] ?? 0);
            $volume_min = $volume_max * (sqrt(3) / 2);
            $totals['volume'] = ($volume_min + $volume_max) / 2;
        }
    }
    
    if(!empty($pallet_ids)) {
        $placeholders = implode(', ', array_fill(0, count($pallet_ids), '?'));
        $sql = "select sum(ifnull(pallet_count, 0)) places_count, sum(ifnull(gross_weight, 0)) gross_weight from calculation where id in ($placeholders)";
        $fetcher = new Fetcher($sql, $pallet_ids);
        if($row = $fetcher->Fetch()) {
            $totals['places_count'] = intval($row['places_count'] ?? 0);
            $totals['gross_weight'] = floatval($row['gross_weight'] ?? 0);
        }
        
        // Габариты паллета с наибольшим объёмом (длина * ширина * высота) -- та же логика,
        // что и в строке "Максимальный (отмеченные)" на панели selected_orders_panel
        $sql = "select pallet_length, pallet_width, pallet_height from calculation "
                . "where id in ($placeholders) and pallet_length is not null and pallet_width is not null and pallet_height is not null "
                . "order by (pallet_length * pallet_width * pallet_height) desc limit 1";
        $fetcher = new Fetcher($sql, $pallet_ids);
        if($row = $fetcher->Fetch()) {
            $totals['max_pallet_length'] = floatval($row['pallet_length']);
            $totals['max_pallet_width'] = floatval($row['pallet_width']);
            $totals['max_pallet_height'] = floatval($row['pallet_height']);
        }
    }
    
    return $totals;
}

$form_valid = true;
$error_message = '';

$document_number_valid = '';
$vehicle_number_valid = '';
$driver_name_valid = '';
$cargo_type_valid = '';

$document_number = '';
$vehicle_number = '';
$driver_name = '';
$cargo_type = null;

if(null !== filter_input(INPUT_POST, 'create_shipment_submit')) {
    $ids = ParseIdList(filter_input(INPUT_POST, 'ids'));
    $pallet_ids = ParseIdList(filter_input(INPUT_POST, 'pallet_ids'));
    
    $document_number = filter_input(INPUT_POST, 'document_number') ?? '';
    if(empty($document_number)) {
        $document_number_valid = ISINVALID;
        $form_valid = false;
    }
    
    $vehicle_number = filter_input(INPUT_POST, 'vehicle_number') ?? '';
    if(empty($vehicle_number)) {
        $vehicle_number_valid = ISINVALID;
        $form_valid = false;
    }
    
    $driver_name = filter_input(INPUT_POST, 'driver_name') ?? '';
    if(empty($driver_name)) {
        $driver_name_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cargo_type = filter_input(INPUT_POST, 'cargo_type', FILTER_VALIDATE_INT);
    if(empty($cargo_type) || !in_array($cargo_type, CARGO_TYPES)) {
        $cargo_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(empty($ids)) {
        $error_message = "Не выбрано ни одного заказа";
        $form_valid = false;
    }
    
    if($form_valid) {
        // Числа считаем заново на сервере -- не берём из формы
        $totals = GetShipmentTotals($ids, $pallet_ids);
        
        // Создание отгрузки и привязка к ней всех заказов -- одна связанная цепочка,
        // выполняется в рамках одной транзакции
        $transaction = new Transaction();
        
        $shipment_id = $transaction->Execute(
                "insert into shipment (document_number, vehicle_number, driver_name, cargo_type, places_count, gross_weight, net_weight, volume, "
                . "max_pallet_length, max_pallet_width, max_pallet_height) "
                . "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$document_number, $vehicle_number, $driver_name, $cargo_type, $totals['places_count'], $totals['gross_weight'], $totals['net_weight'], $totals['volume'],
                        $totals['max_pallet_length'], $totals['max_pallet_width'], $totals['max_pallet_height']]);
        
        foreach($ids as $calculation_id) {
            $transaction->Execute("insert into shipment_calculation (shipment_id, calculation_id) values (?, ?)", [$shipment_id, $calculation_id]);
        }
        
        $error_message = $transaction->error;
        
        if(empty($error_message)) {
            $transaction->Commit();
            header("Location: details.php?id=$shipment_id");
        }
        else {
            $transaction->Rollback();
        }
    }
}
else {
    $ids = ParseIdList(filter_input(INPUT_GET, 'ids'));
    $pallet_ids = ParseIdList(filter_input(INPUT_GET, 'pallet_ids'));
}

// Список заказов, которые войдут в отгрузку (для отображения на форме)
$orders = array();

if(!empty($ids)) {
    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    $sql = "select c.id, c.customer_id, c.duplicate_num_for_customer, c.name from calculation c where c.id in ($placeholders) order by c.id";
    $grabber = new Grabber($sql, $ids);
    $orders = $grabber->result;
}

// Значения, которые будут записаны -- пересчитанные тем же способом, что и при отправке формы
$totals = GetShipmentTotals($ids, $pallet_ids);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php include '../include/head.php'; ?>
    </head>
    <body>
        <?php include './header.php'; ?>
        <div class="container-fluid">
            <h1 class="mt-3 mb-3">Создание отгрузки</h1>
            
            <?php if(!empty($error_message)): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if(empty($orders)): ?>
            <div class="alert alert-warning">Не выбрано ни одного заказа. Вернитесь на список заказов и отметьте флажками заказы, которые нужно отгрузить.</div>
            <?php else: ?>
            
            <h5>Заказы, входящие в отгрузку</h5>
            <table class="table table-sm" style="max-width: 500px;">
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><?=$order['customer_id'].'-'.$order['duplicate_num_for_customer'] ?></td>
                    <td><?=htmlspecialchars($order['name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <form method="post" style="max-width: 500px;">
                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                <input type="hidden" name="ids" value="<?=implode(',', $ids) ?>" />
                <input type="hidden" name="pallet_ids" value="<?=implode(',', $pallet_ids) ?>" />
                
                <div class="form-group">
                    <label for="document_number">Документ</label>
                    <input type="text" class="form-control<?=$document_number_valid ?>" id="document_number" name="document_number" value="<?=htmlspecialchars($document_number) ?>" placeholder="Номер товарной накладной" required="required" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="vehicle_number">Транспортное средство</label>
                    <input type="text" class="form-control<?=$vehicle_number_valid ?>" id="vehicle_number" name="vehicle_number" value="<?=htmlspecialchars($vehicle_number) ?>" placeholder="Гос. номер" required="required" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="driver_name">ФИО водителя</label>
                    <input type="text" class="form-control<?=$driver_name_valid ?>" id="driver_name" name="driver_name" value="<?=htmlspecialchars($driver_name) ?>" required="required" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="cargo_type">Наименование груза</label>
                    <select class="form-control<?=$cargo_type_valid ?>" id="cargo_type" name="cargo_type" required="required">
                        <option value="" hidden="hidden">...</option>
                        <?php foreach(CARGO_TYPES as $type): ?>
                        <option value="<?=$type ?>" <?=$cargo_type == $type ? 'selected="selected"' : '' ?>><?=CARGO_TYPE_NAMES[$type] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <table class="table">
                    <tr>
                        <td>Количество паллетов</td>
                        <td class="text-right"><?= DisplayNumber($totals['places_count'], 0) ?></td>
                    </tr>
                    <tr>
                        <td>Масса брутто</td>
                        <td class="text-right"><?= DisplayNumber($totals['gross_weight'], 0) ?> кг</td>
                    </tr>
                    <tr>
                        <td>Масса нетто</td>
                        <td class="text-right"><?= DisplayNumber($totals['net_weight'], 0) ?> кг</td>
                    </tr>
                    <tr>
                        <td>Объём</td>
                        <td class="text-right"><?= DisplayNumber($totals['volume'], 2) ?> м<sup>3</sup></td>
                    </tr>
                    <tr>
                        <td>Максимальный</td>
                        <td class="text-right"><?php if($totals['max_pallet_length'] !== null): ?><?= DisplayNumber($totals['max_pallet_length'], 2) ?>&times;<?= DisplayNumber($totals['max_pallet_width'], 2) ?>&times;<?= DisplayNumber($totals['max_pallet_height'], 2) ?> м<?php else: ?>&mdash;<?php endif; ?></td>
                    </tr>
                </table>
                
                <button type="submit" class="btn btn-dark" name="create_shipment_submit">Создать отгрузку</button>
            </form>
            <?php endif; ?>
        </div>
        <?php include '../include/footer.php'; ?>
    </body>
</html>
