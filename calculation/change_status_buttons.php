<?php if ($status_id == DRAFT): ?>
<form method="post">
    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
    <input type="hidden" name="status_id" value="<?=CALCULATION ?>" />
    <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
</form>
<?php elseif($status_id == CALCULATION): ?>
<div class="d-flex justify-content-between">
    <div>        
        <form method="post">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=WAITING ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Отправить в работу</button>
        </form>
    </div>
    <div>
        <form method="post">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3">Отправить в черновики</button>
        </form>
    </div>
</div>
<?php elseif ($status_id == WAITING && IsInRole(array("manager-senior", "technologist"))): ?>
<div class="d-flex justify-content-start">
    <form method="post">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=CONFIRMED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Одобрить</button>
    </form>
    <form method="post" class="ml-4">
        <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" name="status_id" value="<?=REJECTED ?>" />
        <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3" style="width: 200px;">Отклонить</button>
    </form>
</div>
<?php elseif ($status_id == CONFIRMED): ?>
<a href="<?=APPLICATION ?>/techmap/create.php?calculation_id=<?=$id ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
<?php elseif($status_id == TECHMAP): ?>
<a href="<?=APPLICATION ?>/techmap/details.php?id=<?=$techmap_id ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
<?php endif; ?>