<?php if ($calculation->status_id == ORDER_STATUS_DRAFT): ?>
<div class="d-flex justify-content-between">
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=ORDER_STATUS_CALCULATION ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
        </form>
    </div>
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=ORDER_STATUS_TRASH ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Удалить</button>
        </form>
    </div>
</div>
<?php elseif($calculation->status_id == ORDER_STATUS_TRASH): ?>
<form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
    <input type="hidden" name="status_id" value="<?=ORDER_STATUS_DRAFT ?>" />
    <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Восстановить</button>
</form>
<?php elseif($calculation->status_id == ORDER_STATUS_CALCULATION): ?>
<div class="d-flex justify-content-between">
    <div>
        <a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
    </div>
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=ORDER_STATUS_DRAFT ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3">Отправить в черновики</button>
        </form>
    </div>
</div>
<?php elseif ($calculation->status_id == ORDER_STATUS_WAITING && IsInRole(array(ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_TECHNOLOGIST]))): ?>
<div class="d-flex justify-content-start">
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=ORDER_STATUS_CONFIRMED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Одобрить</button>
    </form>
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>" class="ml-4">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=ORDER_STATUS_REJECTED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Отклонить</button>
    </form>
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>" class="ml-4">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?= ORDER_STATUS_TECHMAP ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Отозвать заявку</button>
    </form>
</div>
<?php elseif($calculation->status_id == ORDER_STATUS_WAITING && IsInRole(ROLE_NAMES[ROLE_MANAGER])): ?>
<div class="d-flex justify-content-start">
    <a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>" class="ml-4">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?= ORDER_STATUS_TECHMAP ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Отозвать заявку</button>
    </form>
</div>
<?php elseif($calculation->status_id == ORDER_STATUS_CONFIRMED): ?>
<div class="d-flex justify-content-between">
    <div>
        <a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
    </div>
    <div>
        <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=ORDER_STATUS_TECHMAP ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Отменить запрос</button>
        </form>
    </div>
</div>
<?php else: ?>
<a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
<?php endif; ?>