<?php
include '../include/topscripts.php';

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT]))) {
    include '../include/_unauthorized.php';
}

$is_draft = filter_input(INPUT_GET, 'draft', FILTER_VALIDATE_INT) == 1 ? 1 : 0;

$sql = "select id, document_number, vehicle_number, cargo_type, places_count, gross_weight, net_weight, volume, created_at, draft_reason "
        . "from shipment where is_draft = ? order by id desc";
$grabber = new Grabber($sql, [$is_draft]);
$shipments = $grabber->result;
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php include '../include/head.php'; ?>
    </head>
    <body>
        <?php include './header.php'; ?>
        <div class="container-fluid">
            <h3 class="mt-3 mb-3"><?= $is_draft ? 'Черновики отгрузок' : 'Отгрузки' ?></h3>
            <table class="table table-hover">
                <tr>
                    <th>№</th>
                    <th>Дата создания</th>
                    <th>Документ</th>
                    <th>Груз</th>
                    <th>Мест</th>
                    <th>Брутто</th>
                    <?php if($is_draft): ?>
                    <th>Причина</th>
                    <?php endif; ?>
                </tr>
                <?php foreach($shipments as $shipment): ?>
                <?php $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $shipment['created_at']); ?>
                <tr class="clickable-row" onclick="document.location = '<?=APPLICATION ?>/shipment/details.php?id=<?=$shipment['id'] ?>';" style="cursor: pointer;">
                    <td><?=$shipment['id'] ?></td>
                    <td><?=$created_at->format('d.m.Y H:i') ?></td>
                    <td><?=htmlspecialchars($shipment['document_number']) ?></td>
                    <td><?=CARGO_TYPE_NAMES[$shipment['cargo_type']] ?? '' ?></td>
                    <td><?= DisplayNumber(intval($shipment['places_count']), 0) ?></td>
                    <td><?= DisplayNumber(floatval($shipment['gross_weight']), 0) ?> кг</td>
                    <?php if($is_draft): ?>
                    <td><?=htmlspecialchars($shipment['draft_reason'] ?? '') ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($shipments)): ?>
                <tr>
                    <td colspan="<?=$is_draft ? 7 : 6 ?>" class="text-center text-muted">Список пуст</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php include '../include/footer.php'; ?>
    </body>
</html>
