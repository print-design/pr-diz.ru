<?php
require_once './types.php';
?>
<div class='queue_item'>
    <div class="d-flex justify-content-between" style="border-bottom: solid 1px #E7E6ED; margin-bottom: 5px; padding-bottom: 5px;">
        <div class="d-flex justify-content-start">
            <?php if($row['type'] == TYPE_EDITION): ?>
            <div style="padding-top: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>" draggable="true" ondragstart="DragEdition(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <?php elseif ($row['type'] == TYPE_PART): ?>
            <div style="padding-top: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>" draggable="true" ondragstart="DragPart(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <?php endif; ?>
            <div>
                <div style="font-weight: bold; font-size: large; line-height: 1.4rem; margin-bottom: 0.5rem;"><a href='../calculation/techmap.php?id=<?=$row['id'] ?>'><?=$row['calculation'] ?></a></div>
                <?=$row['customer'] ?>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-end" style="padding-top: 10px;">
                <div style="position: relative;">
                    <a class="black queue_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg" /></a>
                    <div class="queue_menu text-left">
                        <div class="command">
                            <a class="btn btn-link h-25" style="font-size: 14px;" href="../calculation/techmap.php?id=<?=$row['calculation_id'] ?>"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/details.svg" /></div>Подробнее</a>
                        </div>
                        <div class="command">
                            <?php if($row['type'] == TYPE_EDITION): ?>
                            <button type="button" class="btn btn-link h-25 btn_divide" style="font-size: 14px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/divide.svg" /></div>Разделить</button>
                            <?php elseif($row['type'] == TYPE_PART): ?>
                            <form method="post">
                                <input type="hidden" name="calculation_id" value="<?=$row['calculation_id'] ?>" />
                                <input type="hidden" name="work_id" value="<?=$this->work_id ?>" />
                                <input type="hidden" name="lamination" value="<?=$row['lamination'] ?>" />
                                <button type="submit" class="btn btn-link h-25" name="undivide_submit" style="font-size: 14px;"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/divide.svg" /></div>Отменить разделение</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div style="padding-left: 10px;"><img src="../images/icons/right-arrow.svg" /></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6"><strong>Метраж:</strong> <?= CalculationBase::Display(intval($row['length']), 0) ?></div>
        <div class="col-6"><strong>Красочность:</strong> <?=$row['ink_number'] ?></div>
    </div>
    <div class="row">
        <div class="col-6"><strong>Ламинации:</strong> <?= ($this->work_id == WORK_LAMINATION ? ($laminations_number == 2 ? $row['lamination']." прогон" : $laminations_number) : $laminations_number) ?></div>
        <div class="col-6">
            <?php if($this->work_id == WORK_LAMINATION): ?>
            <strong>Тип работы:</strong> <span class="text-nowrap"><?= $row['work_type'] ?></span>
            <?php else: ?>
            <strong>Вал:</strong> <?= CalculationBase::Display(floatval($row['raport']), 3) ?>
            <?php endif; ?>
        </div>
    </div>
    <div style="margin-top: 10px;"><strong>Менеджер:</strong> <?=$row['last_name'] ?> <?= mb_substr($row['first_name'], 0, 1)  ?>.</div>
</div>