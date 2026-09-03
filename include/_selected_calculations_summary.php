<?php
include 'topscripts.php';

// Доступно тем же ролям, что могут видеть pack/index.php или buh/index.php
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '_unauthorized.php';
}

// Список id заказов, отмеченных флажками -- берём только то, что прошло через intval,
// поэтому дальнейшая проверка is_numeric/фильтрация уже гарантирует, что это просто числа
$ids_raw = filter_input(INPUT_GET, 'ids') ?? '';
$ids = array_values(array_unique(array_filter(array_map('intval', explode(',', $ids_raw)))));

$result = array(
    'count' => count($ids),
    'net_weight' => 0,
    'gross_weight' => 0,
    'pallet_count' => 0,
    'volume' => 0,
    'volume_min' => 0,
    'volume_max' => 0,
    'has_shared_pallet_orders' => false,
);

if(!empty($ids)) {
    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    
    // Вес нетто -- собственная величина производства каждого заказа, не привязана к паллету,
    // поэтому считаем по исходному списку отмеченных id, без дедупликации по владельцу паллета.
    // Но заказы, ссылающиеся на отмеченный заказ через pallet_shared_with_id, сами никогда не
    // могут быть отмечены (у них нет чекбокса на странице списка) -- поэтому их вес нетто
    // тоже нужно приплюсовать, иначе он вообще никогда не попадёт в сумму
    $sql = "select sum(duplicate_weight_cut) net_weight from calculation where id in ($placeholders) or pallet_shared_with_id in ($placeholders)";
    $fetcher = new Fetcher($sql, array_merge($ids, $ids));
    if($row = $fetcher->Fetch()) {
        $result['net_weight'] = floatval($row['net_weight'] ?? 0);
    }
    
    // Вес брутто и количество паллетов -- атрибуты самого физического паллета, а не заказа.
    // Если несколько отмеченных заказов физически лежат на одном паллете (через pallet_shared_with_id),
    // их нельзя суммировать по отдельности -- сначала находим "владельца" паллета для каждого
    // отмеченного заказа (сам заказ, если он ни с кем не связан, либо тот заказ, на который он ссылается),
    // затем берём каждого владельца только один раз
    $sql = "select id, coalesce(pallet_shared_with_id, id) owner_id, pallet_shared_with_id from calculation where id in ($placeholders)";
    $grabber = new Grabber($sql, $ids);
    $owner_ids = array();
    foreach($grabber->result as $row) {
        $owner_ids[$row['owner_id']] = true;
        if(!empty($row['pallet_shared_with_id'])) {
            $result['has_shared_pallet_orders'] = true;
        }
    }
    $owner_ids = array_keys($owner_ids);
    
    if(!empty($owner_ids)) {
        $owner_placeholders = implode(', ', array_fill(0, count($owner_ids), '?'));
        $sql = "select sum(ifnull(gross_weight, 0)) gross_weight, sum(ifnull(pallet_count, 0)) pallet_count "
                . "from calculation where id in ($owner_placeholders)";
        $fetcher = new Fetcher($sql, $owner_ids);
        if($row = $fetcher->Fetch()) {
            $result['gross_weight'] = floatval($row['gross_weight'] ?? 0);
            $result['pallet_count'] = intval($row['pallet_count'] ?? 0);
        }
    }
    
    // Суммарный объём готовых роликов -- собственная величина производства каждого заказа
    // (сколько всего напечатано), тоже не привязана к паллету -- считаем по исходному списку,
    // без дедупликации по владельцу паллета
    $sql = "select sum(power(ifnull(cts.radius, 0) * 2 + ifnull(tm.spool, 0), 2) * ifnull(cs.width, 0) / 1000000000) as volume "
            . "from calculation c "
            . "inner join techmap tm on tm.calculation_id = c.id "
            . "inner join calculation_stream cs on cs.calculation_id = c.id "
            . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
            . "where c.id in ($placeholders)";
    $fetcher = new Fetcher($sql, $ids);
    if($row = $fetcher->Fetch()) {
        $result['volume_max'] = floatval($row[0] ?? 0);
    }
    
    $result['volume_min'] = $result['volume_max'] * (sqrt(3) / 2);
    $result['volume'] = ($result['volume_min'] + $result['volume_max']) / 2;
}

echo json_encode($result);
?>
