<div class='queue_item'>
    <div class="d-flex justify-content-between" style="border-bottom: solid 1px #E7E6ED; margin-bottom: 5px; padding-bottom: 5px;">
        <div class="d-flex justify-content-start">
            <div style="padding-top: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" draggable="true" ondragstart="DragQueue(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <div>
                <div style="font-weight: bold; font-size: large; line-height: 20px; margin-bottom: 8px;"><a href='../calculation/techmap.php?id=<?=$row['id'] ?>'><?=$row['calculation'] ?></a></div>
                <?=$row['customer'] ?>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-end" style="padding-top: 10px;">
                <div><a href='../calculation/techmap.php?id=<?=$row['id'] ?>'><img src="../images/icons/vertical-dots1.svg" /></a></div>
                <div style="padding-left: 10px;"><img src="../images/icons/right-arrow.svg" /></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6"><strong>Метраж:</strong> <?= CalculationBase::Display(intval($row['length_dirty_1']), 0) ?></div>
        <div class="col-6"><strong>Красочность:</strong> <?=$row['ink_number'] ?></div>
    </div>
    <div class="row">
        <div class="col-6"><strong>Ламинации:</strong> <?=$laminations_number ?></div>
        <div class="col-6"><strong>Вал:</strong> <?= CalculationBase::Display(floatval($row['raport']), 3) ?></div>
    </div>
    <div style="margin-top: 10px;"><strong>Менеджер:</strong> <?=$row['last_name'] ?> <?= mb_substr($row['first_name'], 0, 1)  ?>.</div>
</div>