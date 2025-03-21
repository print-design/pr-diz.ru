<div class='queue_item'>
    <div class="d-flex justify-content-between" style="border-bottom: solid 1px #E7E6ED; margin-bottom: 5px; padding-bottom: 5px;">
        <div class="d-flex justify-content-start">
            <?php if($row['type'] == PLAN_TYPE_EDITION): ?>
            <div style="padding-top: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>" draggable="true" ondragstart="DragEdition(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <?php elseif ($row['type'] == PLAN_TYPE_PART): ?>
            <div style="padding-top: 10px; padding-right: 10px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>" draggable="true" ondragstart="DragPart(event);"><img src="../images/icons/double-vertical-dots.svg" draggable="false" /></div>
            <?php endif; ?>
            <div>
                <div style="font-weight: bold; font-size: large; line-height: 1.4rem; margin-bottom: 0.5rem;"><a href='../calculation/techmap.php?id=<?=$row['id'] ?>'><?=$row['calculation'] ?></a></div>
                <?=$row['customer'] ?>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-end">
                <?php if($row['queue_top'] == 1): ?>
                <div class="mr-1">
                    <form method="post">
                        <input type="hidden" name="calculation_id" value="<?=$row['calculation_id'] ?>" />
                        <input type="hidden" name="scroll" />
                        <button type="submit" class="btn btn-link ui_tooltip right" title="Открепить заказ" name="unpin_submit"><img src="../images/icons/unpin.svg" /></button>
                    </form>
                </div>
                <?php endif; ?>
                <div style="position: relative; padding-top: 10px;">
                    <a class="black queue_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg" /></a>
                    <div class="queue_menu text-left">
                        <div>
                            <a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/<?= IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD])) || /*ВРЕМЕННО*/ GetUserId() == CUTTER_SOMA ? "print_tm" : "techmap" ?>.php?id=<?=$row['calculation_id'] ?>"<?= IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD])) || /*ВРЕМЕННО*/ GetUserId() == CUTTER_SOMA ? " target='_blank'" : "" ?>>
                                <div class="command">
                                    <div style="display: inline; padding-right: 10px;"><img src="../images/icons/details.svg" /></div>Подробнее
                                </div>
                            </a>
                        </div>
                        <div class="command d-none">
                            <?php if($row['type'] == PLAN_TYPE_EDITION): ?>
                            <button type="button" class="btn btn-link h-25 btn_divide" style="font-size: 14px;" data-id="<?=$row['id'] ?>" data-lamination="<?=$row['lamination'] ?>"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/divide.svg" /></div>Разделить</button>
                            <?php elseif($row['type'] == PLAN_TYPE_PART): ?>
                            <form method="post">
                                <input type="hidden" name="calculation_id" value="<?=$row['calculation_id'] ?>" />
                                <input type="hidden" name="work_id" value="<?=$this->work_id ?>" />
                                <input type="hidden" name="lamination" value="<?=$row['lamination'] ?>" />
                                <input type="hidden" name="scroll" />
                                <button type="submit" class="btn btn-link h-25" name="undivide_submit" style="font-size: 14px;"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/divide.svg" /></div>Отменить разделение</button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php if($row['queue_top'] == 0): ?>
                        <form method="post">
                            <input type="hidden" name="calculation_id" value="<?=$row['calculation_id'] ?>" />
                            <input type="hidden" name="scroll" />
                            <button type="submit" class="btn btn-link h-25" name="pin_submit" style="font-size: 14px;">
                                <div class="command">
                                    <div style="display: inline; padding-right: 10px;"><img src="../images/icons/pin.svg" /></div>Закрепить наверху
                                </div>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="padding-left: 10px; padding-top: 10px;"><img src="../images/icons/right-arrow.svg" /></div>
            </div>
        </div>
    </div>
    <?php if($this->work_id == WORK_LAMINATION): ?>
    <div class="mb-2">
        <?php
        $films_strings = GetFilmsString($row["lamination"], $row["film_name"], $row["thickness"], $row["individual_film_name"], $row["individual_thickness"], $row['width_1'], 
                $row["lamination1_film_name"], $row["lamination1_thickness"], $row["lamination1_individual_film_name"], $row["lamination1_individual_thickness"], $row['width_2'], 
                $row["lamination2_film_name"], $row["lamination2_thickness"], $row["lamination2_individual_film_name"], $row["lamination2_individual_thickness"], $row['width_3']);
        ?>
        <span class="text-nowrap"><?=$films_strings[0] ?></span> <span class="text-nowrap"><?=$films_strings[1] ?></span> <span class="text-nowrap"><?=$films_strings[2] ?></span>
    </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-6"><strong>Метраж:</strong> <?= DisplayNumber(intval($row['length']), 0) ?></div>
        <div class="col-6"><strong>Красочность:</strong> <?=$row['ink_number'] ?></div>
    </div>
    <div class="row">
        <div class="col-6">
            <strong>Ламинации:</strong> <?= ($this->work_id == WORK_LAMINATION ? ($laminations_number == 2 ? $row['lamination']." прогон" : $laminations_number) : $laminations_number) ?>
            <?php if($this->work_id == WORK_LAMINATION): ?>
            <br />
            <strong>Лам вал:</strong> <?=$row['lamination_roller_width'] ?>
            <?php endif; ?>
        </div>
        <div class="col-6">
            <?php if($this->work_id == WORK_LAMINATION): ?>
            <strong>Тип работы:</strong> <span class="text-nowrap"><?= WORK_TYPE_NAMES[$row['work_type_id']] ?></span>
            <?php else: ?>
            <strong>Вал:</strong> <?= DisplayNumber(floatval($row['raport']), 3) ?>
            <?php endif; ?>
        </div>
    </div>
    <div><strong>Статус:</strong> <span style="color: <?=ORDER_STATUS_COLORS[$row['status_id']] ?>; font-weight: bold;"><?=ORDER_STATUS_NAMES[$row['status_id']] ?></span></div>
    <div><strong>Менеджер:</strong> <?=$row['last_name'] ?> <?= mb_substr($row['first_name'], 0, 1)  ?>.</div>
</div>