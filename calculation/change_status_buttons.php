<?php if ($status_id == DRAFT): ?>
<form method="post">
    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
    <input type="hidden" name="status_id" value="<?=CALCULATION ?>" />
    <button type="submit" name="change-status-submit" class="btn btn-outline-dark mt-3" style="width: 200px;">Сохранить</button>
</form>
<?php elseif($status_id == CALCULATION): ?>
<div class="d-flex justify-content-between">
    <div>
        <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
        <a href="techmap.php?id=<?=$id ?>" class="btn btn-outline-dark mt-3" style="width: 200px;">Составить тех. карту</a>
        <?php endif; ?>
    </div>
    <div>
        <form method="post">
            <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
            <input type="hidden" name="status_id" value="<?=DRAFT ?>" />
            <button type="submit" name="change-status-submit" class="btn btn-outline-dark draft mt-3">Отправить в черновики</button>
        </form>
    </div>
</div>
<?php elseif($status_id == TECHMAP): ?>
<a href="techmap.php?id=<?=$id ?>" class="btn btn-outline-dark mt-3 mr-2" style="width: 200px;">Посмотреть тех. карту</a>
<?php endif; ?>