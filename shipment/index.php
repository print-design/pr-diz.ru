<?php
include '../include/topscripts.php';
include '../include/pager_top.php';
$rowcounter = 0;

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    include '../include/_unauthorized.php';
}

$is_draft = filter_input(INPUT_GET, 'draft', FILTER_VALIDATE_INT) == 1 ? 1 : 0;

// Общее количество отгрузок для определения количества страниц в постраничном выводе
$sql = "select count(id) from shipment where is_draft = ?";
$fetcher = new Fetcher($sql, [$is_draft]);
if($row = $fetcher->Fetch()) {
    $pager_total_count = $row[0];
}

$sql = "select id, document_number, vehicle_number, cargo_type, places_count, gross_weight, net_weight, volume, created_at, draft_reason "
        . "from shipment where is_draft = ? order by id desc limit $pager_skip, $pager_take";
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
            <h1 class="mt-3 mb-3"><?= $is_draft ? 'Черновики отгрузок' : 'Отгрузки' ?></h1>
            <table class="table table-hover">
                <tr>
                    <th>№</th>
                    <th>Дата создания</th>
                    <th>Документ</th>
                    <th>Груз</th>
                    <th>Паллетов</th>
                    <th>Брутто</th>
                    <?php if($is_draft): ?>
                    <th>Причина</th>
                    <?php endif; ?>
                    <th></th>
                </tr>
                <?php foreach($shipments as $shipment): ?>
                <?php $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $shipment['created_at']); ?>
                <tr>
                    <td><?=$shipment['id'] ?></td>
                    <td><?=$created_at->format('d.m.Y H:i') ?></td>
                    <td><?=htmlspecialchars($shipment['document_number']) ?></td>
                    <td><?=CARGO_TYPE_NAMES[$shipment['cargo_type']] ?? '' ?></td>
                    <td><?= DisplayNumber(intval($shipment['places_count']), 0) ?></td>
                    <td><?= DisplayNumber(floatval($shipment['gross_weight']), 0) ?> кг</td>
                    <?php if($is_draft): ?>
                    <td><?=htmlspecialchars($shipment['draft_reason'] ?? '') ?></td>
                    <?php endif; ?>
                    <td>
                        <a href='<?=APPLICATION ?>/shipment/details.php?id=<?=$shipment['id'] ?>'>
                            <svg viewBox="0 0 24 24" width="24" height="24" data-flexim-name="arrow-right" style="color: currentcolor; display: inline-flex; flex-shrink: 0;">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.5859 12L8.29297 17.2929L9.70718 18.7071L16.4143 12L9.70718 5.29291L8.29297 6.70712L13.5859 12Z" fill="currentColor"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($shipments)): ?>
                <tr>
                    <td colspan="<?=$is_draft ? 7 : 6 ?>" class="text-center text-muted">Список пуст</td>
                </tr>
                <?php endif; ?>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php include '../include/footer.php'; ?>
    </body>
</html>
