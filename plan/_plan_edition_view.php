<?php
require_once '../calculation/calculation.php';
require_once './roles.php';
require_once './types.php';
?>
<tr data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-id="<?=$this->edition['calculation_id'] ?>" data-position="<?=$this->edition['position'] ?>">
    <?php if($this->plan_shift->shift == 'day' && $this->edition_key == 0): ?>
    <td class="border-right" rowspan="<?=$this->plan_shift->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->plan_shift->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->plan_shift->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php if(!$this->plan_shift->includes_continuation): ?>
        <div style="display: block;">
            <a href="javascript: void(0);" onclick="javascript: MoveUp(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/up_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
        <div style="display: block; white-space: nowrap;">
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><div class="font-italic" style="display: <?=$this->plan_shift->timetable->work_id == WORK_CUTTING ? 'none' : 'block' ?>;"><?= CalculationBase::Display($this->plan_shift->shift_worktime, 2) ?> ч.</div>
        </div>
        <?php if(!$this->plan_shift->includes_continuation): ?>
        <div style="display: block; margin-top: 6px;">
            <a href="javascript: void(0);" onclick="javascript: MoveDown(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/down_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-work-id="<?=$this->plan_shift->timetable->work_id ?>" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
            foreach($this->plan_shift->timetable->employees as $employee):
                $selected = '';
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts1) && $employee['id'] == $this->plan_shift->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == WORK_ROLES[$this->plan_shift->timetable->work_id] && ($employee['active'] == 1 || $employee['id'] == $this->plan_shift->timetable->workshifts1[$key])):
            ?>
            <option value="<?=$employee['id'] ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php if($this->plan_shift->timetable->work_id == WORK_PRINTING && $this->plan_shift->timetable->machine_id == PRINTER_COMIFLEX): ?>
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small mt-2" data-work-id="<?=$this->plan_shift->timetable->work_id ?>" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
            foreach($this->plan_shift->timetable->employees as $employee):
                $selected = '';
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts2) && $employee['id'] == $this->plan_shift->timetable->workshifts2[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == ROLE_ASSISTANT && ($employee['active'] == 1 || $employee['id'] == $this->plan_shift->timetable->workshifts2[$key])):
            ?>
            <option value="<?=$employee['id'] ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    <?php
    $drop = " ondrop='DropTimetable(event);' ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'";
    
    if($this->edition['type'] == TYPE_CONTINUATION || $this->edition['type'] == TYPE_PART_CONTINUATION) {
        $drop = "";
    }
    ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline border-left fordrag"<?=$drop ?>>
        <?php if($this->edition['type'] == TYPE_EDITION && !$this->edition['has_continuation']): ?>
        <div draggable="true" ondragstart="DragTimetableEdition(event);" data-id="<?=$this->edition['calculation_id'] ?>" data-lamination="<?=$this->edition['lamination'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
        <?php if($this->edition['type'] == TYPE_PART && !$this->edition['has_continuation']): ?>
        <div draggable="true" ondragstart="DragTimetablePart(event);" data-id="<?=$this->edition['id'] ?>" data-lamination="<?=$this->edition['lamination'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
        <?php if($this->edition['type'] == TYPE_EVENT): ?>
        <div draggable="true" ondragstart="DragTimetableEvent(event);" data-id="<?=$this->edition['id'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?php if($this->edition['type'] == TYPE_EVENT): ?>
        <?= $this->edition['calculation'] ?>
        <?php else: ?>
        <div style="font-weight: bold; display: inline;"<?=$drop ?>><?= $this->edition['calculation'] ?></div><br /><?= $this->edition['customer'] ?>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?php if($this->edition['type'] != TYPE_EVENT): ?>
        <div class="d-flex justify-content-between">
            <div>
                <?php if($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING): ?>
                <div class='text-nowrap'><?= CalculationBase::Display(floatval($this->edition['length_dirty_1']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
                <div class='text-nowrap'><?= CalculationBase::Display(floatval($this->edition['length_dirty_2']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
                <div class='text-nowrap'><?= CalculationBase::Display(floatval($this->edition['length_dirty_3']), 0) ?></div>
                <?php endif; ?>
                <?= $this->edition['type'] == TYPE_CONTINUATION || $this->edition['type'] == TYPE_PART_CONTINUATION ? ' Допечатка' : '' ?>
            </div>
            <div>
                <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && $this->edition['type'] == TYPE_EDITION && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && $this->edition['type'] == TYPE_PART && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_EDITION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_PART && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemovePartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_CONTINUATION && !$this->edition['has_continuation'] && $this->edition['worktime'] > 12): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_PART_CONTINUATION && !$this->edition['has_continuation'] && $this->edition['worktime'] > 12): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == TYPE_PART_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == TYPE_EVENT ? "" : rtrim(rtrim(CalculationBase::Display(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden"<?=$drop ?>>
        <?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?= $this->edition['type'] == TYPE_EVENT ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-right"<?=$drop ?>>
        <?php if($this->edition['type'] == TYPE_EVENT): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg"<?=$drop ?> /></a>
        <div class="timetable_menu text-left">
            <div class="command">
                <button type="button" class="btn btn-link h-25" style="font-size: 14px;" onclick="javascript: DeleteEvent(<?=$this->edition['calculation_id'] ?>);"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/trash2.svg" /></div>Удалить</button>
            </div>
        </div>
        <?php else: ?>
        <a href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>"<?=$drop ?>>
            <img src="../images/icons/vertical-dots1.svg"<?=$drop ?> />
        </a>
        <?php endif; ?>
    </td>
</tr>