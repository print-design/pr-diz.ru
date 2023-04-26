<?php
require_once './_roles.php';
?>
<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="<?=$this->edition['calculation_id'] ?>" data-position="<?=$this->edition['position'] ?>">
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <div style="display: block;">
            <a href="javascript: void(0);" onclick="javascript: MoveUp(event);" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>">
                <img src="../images/icons/up_arrow.png" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" />
            </a>
        </div>
        <div style="display: block;">
            <?=($this->shift == 'day' ? 'День' : 'Ночь') ?><br /><span class="font-italic"><?= CalculationBase::Display($this->shift_worktime, 2) ?> ч.</span>
        </div>
        <div style="display: block; margin-top: 6px;">
            <a href="javascript: void(0);" onclick="javascript: MoveDown(event);" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>">
                <img src="../images/icons/down_arrow.png" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" />
            </a>
        </div>
    </td>
    <td class="<?=$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            foreach($this->timetable->employees as $employee):
                $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts1) && $employee['id'] == $this->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == ROLE_PRINT && ($employee['active'] == 1 || $employee['id'] == $this->timetable->workshifts1[$key])):
            ?>
            <option value="<?=$employee['id'] ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
    </td>
    <?php if($this->timetable->machine == CalculationBase::COMIFLEX): ?>
    <td class="<?=$this->shift ?> assistant" style="display: none;" rowspan="<?=$this->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            foreach($this->timetable->employees as $employee):
                $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts2) && $employee['id'] == $this->timetable->workshifts2[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == ROLE_ASSISTANT && ($employee['active'] == 1 || $employee['id'] == $this->timetable->workshifts2[$key])):
            ?>
            <option value="<?=$employee['id'] ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
    </td>
    <?php endif; ?>
    <?php endif; ?>
    <?php
    $ondragstart = "DragTimetable(event);";
    
    if($this->edition['is_event']) {
        $ondragstart = "DragTimetableEvent(event);";
    }
    ?>
    <td class="<?=$this->shift ?> showdropline border-left fordrag" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
        <div draggable="true" ondragstart="<?=$ondragstart ?>" data-id="<?=$this->edition['calculation_id'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
    </td>
    <?php if($this->edition['is_event']): ?>
    <td colspan="5" class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
        <?= $this->edition['calculation'] ?>
    </td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' style="position: relative;">
        <a class="black timetable_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' /></a>
        <div class="timetable_menu text-left">
            <div class="command">
                <button type="button" class="btn btn-link p-0 m-0 h-25 confirmable" style="font-size: 14px;" onclick="javascript: DeleteEvent(<?=$this->edition['calculation_id'] ?>);"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/trash2.svg" /></div>Удалить</button>
            </div>
        </div>
    </td>
    <?php else: ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
        <div style="font-weight: bold; display: inline;" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?= $this->edition['calculation'] ?></div><br /><?= $this->edition['customer'] ?>
    </td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?= CalculationBase::Display(floatval($this->edition['length_dirty_1']), 0) ?>
        <?php if($this->shift_worktime > 12): ?>
        <button type="button" class="btn btn-light"><i class="fas fa-chevron-down"></i></button>
        <?php endif; ?>
    </td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?= rtrim(rtrim(CalculationBase::Display(floatval($this->edition['raport']), 3), "0"), ",") ?></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?=$this->edition['laminations'] ?></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?=$this->edition['ink_number'] ?></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'><?=$this->edition['manager'] ?></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
        <a href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/vertical-dots1.svg" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </a>
    </td>
    <?php endif; ?>
</tr>