<?php
include '../include/topscripts.php';

// Авторизация -- те же роли, что у pack/ и buh/
if(!IsInRole(array(ROLE_NAMES[ROLE_PACKER], ROLE_NAMES[ROLE_ACCOUNTANT], ROLE_NAMES[ROLE_TECHNOLOGIST]))) {
    include '../include/_unauthorized.php';
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$form_valid = true;
$error_message = '';
$draft_reason_valid = '';

// Перевод в черновики -- необратимое действие, поэтому причина обязательна,
// а отменить пометку "черновик" впоследствии нельзя
if(null !== filter_input(INPUT_POST, 'draft_submit')) {
    $draft_reason = filter_input(INPUT_POST, 'draft_reason') ?? '';
    
    if(empty($draft_reason)) {
        $draft_reason_valid = ISINVALID;
        $form_valid = false;
        $error_message = "Укажите причину";
    }
    
    if($form_valid) {
        $sql = "update shipment set is_draft = 1, draft_reason = ?, draft_at = now(), draft_by = ? where id = ? and is_draft = 0";
        $executer = new Executer($sql, [$draft_reason, GetUserId(), $id]);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            header("Location: details.php?id=$id");
        }
    }
}

// Данные самой отгрузки
$sql = "select id, document_number, vehicle_number, driver_name, cargo_type, places_count, gross_weight, net_weight, volume, created_at, "
        . "max_pallet_length, max_pallet_width, max_pallet_height, "
        . "is_draft, draft_reason, draft_at, draft_by "
        . "from shipment where id = ?";
$fetcher = new Fetcher($sql, [$id]);
$shipment = $fetcher->Fetch();

if(!$shipment) {
    header("Location: index.php");
    exit();
}

// Заказы, входящие в отгрузку -- вместе с именем заказчика и тем, кто установил
// заказу последний статус (это и есть "ответственный" за отгрузку этого заказа)
$sql = "select c.id, c.customer_id, c.duplicate_num_for_customer, c.name, cus.name as customer_name, u.last_name, u.first_name "
        . "from shipment_calculation sc "
        . "inner join calculation c on sc.calculation_id = c.id "
        . "inner join customer cus on c.customer_id = cus.id "
        . "left join calculation_status_history csh on csh.id = (select id from calculation_status_history where calculation_id = c.id order by date desc limit 1) "
        . "left join user u on csh.user_id = u.id "
        . "where sc.shipment_id = ? "
        . "order by c.id";
$grabber = new Grabber($sql, [$id]);
$orders = $grabber->result;

// Кто перевёл в черновики (для отображения)
$draft_by_name = '';
if(!empty($shipment['draft_by'])) {
    $sql = "select last_name, first_name from user where id = ?";
    $fetcher = new Fetcher($sql, [$shipment['draft_by']]);
    if($row = $fetcher->Fetch()) {
        $draft_by_name = $row['last_name'].' '.$row['first_name'];
    }
}

$created_at = DateTime::createFromFormat('Y-m-d H:i:s', $shipment['created_at']);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php include '../include/head.php'; ?>
    </head>
    <body>
        <?php include './header.php'; ?>
        <div class="container-fluid">
            <?php
            $backlink_url = "";
            if($shipment['is_draft']) {
                $backlink_url = BuildQueryAddRemoveArray('is_draft', 1, ['id']);
            }
            else {
                $backlink_url = BuildQueryRemove("id");
            }
            ?>
            <a class="btn btn-light backlink" href="<?= APPLICATION ?>/shipment/<?= $backlink_url ?>" title="К списку">К списку</a>
            
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">    
                <h1>Отгрузка №<?=$shipment['id'] ?><?php if($shipment['is_draft']): ?> <span class="badge badge-secondary">Черновик</span><?php endif; ?></h1>
                <?php if(!$shipment['is_draft']): ?>
                <a class="btn btn-outline-dark" href="excel.php?id=<?=$shipment['id'] ?>"><i class="fas fa-file-excel mr-2"></i>Акт-отчёт (Excel)</a>
                <?php endif; ?>
            </div>
            
            <?php if($shipment['is_draft']): ?>
            <div class="alert alert-secondary">
                <strong>Причина перевода в черновики:</strong> <?=htmlspecialchars($shipment['draft_reason']) ?><br />
                <?=htmlspecialchars($draft_by_name) ?>, <?= DateTime::createFromFormat('Y-m-d H:i:s', $shipment['draft_at'])->format('d.m.Y H:i') ?>
            </div>
            <?php endif; ?>
            
            <table class="table" style="max-width: 600px;">
                <tr>
                    <td>Дата создания</td>
                    <td class="text-right"><?=$created_at->format('d.m.Y H:i') ?></td>
                </tr>
                <tr>
                    <td>Документ</td>
                    <td class="text-right"><?=htmlspecialchars($shipment['document_number']) ?></td>
                </tr>
                <tr>
                    <td>Транспортное средство</td>
                    <td class="text-right"><?=htmlspecialchars($shipment['vehicle_number']) ?></td>
                </tr>
                <tr>
                    <td>ФИО водителя</td>
                    <td class="text-right"><?=htmlspecialchars($shipment['driver_name'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Наименование груза</td>
                    <td class="text-right"><?=CARGO_TYPE_NAMES[$shipment['cargo_type']] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Количество паллетов</td>
                    <td class="text-right"><?= DisplayNumber(intval($shipment['places_count']), 0) ?></td>
                </tr>
                <tr>
                    <td>Масса брутто</td>
                    <td class="text-right"><?= DisplayNumber(floatval($shipment['gross_weight']), 0) ?> кг</td>
                </tr>
                <tr>
                    <td>Масса нетто</td>
                    <td class="text-right"><?= DisplayNumber(floatval($shipment['net_weight']), 0) ?> кг</td>
                </tr>
                <tr>
                    <td>Объём</td>
                    <td class="text-right"><?= DisplayNumber(floatval($shipment['volume']), 2) ?> м<sup>3</sup></td>
                </tr>
                <tr>
                    <td>Максимальный</td>
                    <td class="text-right"><?php if($shipment['max_pallet_length'] !== null): ?><?= DisplayNumber(floatval($shipment['max_pallet_length']), 2) ?>&times;<?= DisplayNumber(floatval($shipment['max_pallet_width']), 2) ?>&times;<?= DisplayNumber(floatval($shipment['max_pallet_height']), 2) ?> м<?php else: ?>&mdash;<?php endif; ?></td>
                </tr>
            </table>
            
            <h5>Ответственный / Контрагент</h5>
            <table class="table table-sm" style="max-width: 700px;">
                <tr>
                    <th>Заказ</th>
                    <th>Контрагент</th>
                    <th>Ответственный</th>
                </tr>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><?=$order['customer_id'].'-'.$order['duplicate_num_for_customer'].' '.htmlspecialchars($order['name']) ?></td>
                    <td><?=htmlspecialchars($order['customer_name']) ?></td>
                    <td><?=htmlspecialchars(trim($order['last_name'].' '.$order['first_name'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <?php if(!$shipment['is_draft']): ?>
            <h5 class="mt-4">Перевести в черновики</h5>
            <p class="text-muted" style="font-size: 13px;">Это действие необратимо. Если в документе допущена ошибка, создайте новый документ с исправленными данными.</p>
            <form method="post" style="max-width: 500px;" onsubmit="return confirm('Вы уверены, что хотите перевести этот документ в черновики? Это необратимо.');">
                <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
                <div class="form-group">
                    <label for="draft_reason">Причина</label>
                    <textarea class="form-control<?=$draft_reason_valid ?>" id="draft_reason" name="draft_reason" required="required"><?php if(!empty($error_message)): ?><?=htmlspecialchars(filter_input(INPUT_POST, 'draft_reason') ?? '') ?><?php endif; ?></textarea>
                </div>
                <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-danger" name="draft_submit">В черновики</button>
            </form>
            <?php endif; ?>
        </div>
        <?php include '../include/footer.php'; ?>
    </body>
</html>
