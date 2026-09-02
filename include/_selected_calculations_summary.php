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
);

if(!empty($ids)) {
    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    
    // Суммарный вес нетто (из данных резки), вес брутто и количество паллетов
    // (для заказов, где упаковщица их ещё не ввела, считаем как 0)
    $sql = "select sum(duplicate_weight_cut) net_weight, sum(ifnull(gross_weight, 0)) gross_weight, sum(ifnull(pallet_count, 0)) pallet_count "
            . "from calculation where id in ($placeholders)";
    $fetcher = new Fetcher($sql, $ids);
    if($row = $fetcher->Fetch()) {
        $result['net_weight'] = floatval($row['net_weight'] ?? 0);
        $result['gross_weight'] = floatval($row['gross_weight'] ?? 0);
        $result['pallet_count'] = intval($row['pallet_count'] ?? 0);
    }
    
    // Суммарный объём готовых роликов по всей группе заказов -- та же формула,
    // что и в конструкторе CalculationRolls, только не для одного id, а сразу для всех выбранных
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
