<?php if ($status_id == DRAFT): ?>
<div class="d-flex justify-content-between">
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=CALCULATION ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
        </form>
    </div>
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=TRASH ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Удалить</button>
        </form>
    </div>
</div>
<?php elseif($status_id == TRASH): ?>
<form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
    <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
    <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Восстановить</button>
</form>
<?php elseif($status_id == CALCULATION): ?>
<div class="d-flex justify-content-between">
    <div>
        <a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
    </div>
    <div>
        <form method="post" action="details.php?id=<?= filter_input(INPUT_GET, 'id') ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3">Отправить в черновики</button>
        </form>
    </div>
</div>
<?php elseif ($status_id == WAITING && IsInRole(array(ROLE_NAMES[ROLE_MANAGER_SENIOR], ROLE_NAMES[ROLE_TECHNOLOGIST]))): ?>
<div class="d-flex justify-content-start">
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=CONFIRMED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Одобрить</button>
    </form>
    <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>" class="ml-4">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=REJECTED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Отклонить</button>
    </form>
</div>
<?php elseif($status_id == CONFIRMED): ?>
<div class="d-flex justify-content-between">
    <div>
        <a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
    </div>
    <div>
        <form method="post" action="details.php<?= BuildQuery('id', filter_input(INPUT_GET, 'id')) ?>">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=TECHMAP ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Отменить запрос</button>
        </form>
    </div>
</div>
<?php else: ?>
<a href="techmap.php<?= BuildQuery('id', $id) ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
<?php endif; ?>