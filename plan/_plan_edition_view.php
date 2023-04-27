<?php
require_once './_roles.php';
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
        <?php if(!$this->plan_shift->has_continuation): ?>
        <div style="display: block;">
            <a href="javascript: void(0);" onclick="javascript: MoveUp(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/up_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
        <div style="display: block; white-space: nowrap;">
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><br /><span class="font-italic"><?= CalculationBase::Display($this->plan_shift->shift_worktime, 2) ?> ч.</span>
        </div>
        <?php if(!$this->plan_shift->has_continuation): ?>
        <div style="display: block; margin-top: 6px;">
            <a href="javascript: void(0);" onclick="javascript: MoveDown(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/down_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
            foreach($this->plan_shift->timetable->employees as $employee):
                $selected = '';
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts1) && $employee['id'] == $this->plan_shift->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == ROLE_PRINT && ($employee['active'] == 1 || $employee['id'] == $this->plan_shift->timetable->workshifts1[$key])):
            ?>
            <option value="<?=$employee['id'] ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
    </td>
    <?php if($this->plan_shift->timetable->machine == CalculationBase::COMIFLEX): ?>
    <td class="<?=$this->plan_shift->shift ?> assistant" style="display: none;" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
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
    </td>
    <?php endif; ?>
    <?php endif; ?>
    <?php
    $ondragstart = "DragTimetable(event);";
    
    if($this->edition['is_event']) {
        $ondragstart = "DragTimetableEvent(event);";
    }
    
    $drop = " ondrop='DropTimetable(event);' ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'";
    
    if($this->edition['is_continuation']) {
        $drop = "";
    }
    ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline border-left fordrag"<?=$drop ?>>
        <?php if(!$this->edition['is_continuation'] && !$this->edition['has_continuation']): ?>
        <div draggable="true" ondragstart="<?=$ondragstart ?>" data-id="<?=$this->edition['calculation_id'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
    </td>
    <?php if($this->edition['is_event']): ?>
    <td colspan="5" class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?= $this->edition['calculation'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-right" style="position: relative;"<?=$drop ?>>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg"<?=$drop ?> /></a>
        <div class="timetable_menu text-left">
            <div class="command">
                <button type="button" class="btn btn-link p-0 m-0 h-25 confirmable" style="font-size: 14px;" onclick="javascript: DeleteEvent(<?=$this->edition['calculation_id'] ?>);"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/trash2.svg" /></div>Удалить</button>
            </div>
        </div>
    </td>
    <?php else: ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <div style="font-weight: bold; display: inline;"<?=$drop ?>><?= $this->edition['calculation'] ?></div><br /><?= $this->edition['customer'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?= $this->edition['is_continuation'] ? 'Допечатка' : CalculationBase::Display(floatval($this->edition['length_dirty_1']), 0) ?>
        <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && !$this->edition['is_continuation'] && !$this->edition['has_continuation']): ?>
        <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
            <label class="btn btn-light btn-edition-continue">
                <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off"><i class="fas fa-chevron-down"></i>
            </label>
        </div>
        <?php endif; ?>
        <?php if($this->edition['has_continuation']): ?>
        <div class="btn-group-toggle ml-1" style="display: inline;" data-toggle="buttons">
            <label class="btn btn-light btn-edition-continue active">
                <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off"><i class="fas fa-chevron-down"></i>
            </label>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?= rtrim(rtrim(CalculationBase::Display(floatval($this->edition['raport']), 3), "0"), ",") ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?=$this->edition['laminations'] ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?=$this->edition['ink_number'] ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?=$this->edition['manager'] ?></td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-right"<?=$drop ?>>
        <a href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>"<?=$drop ?>>
            <img src="../images/icons/vertical-dots1.svg"<?=$drop ?> />
        </a><?=$this->edition['position'] ?>
    </td>
    <?php endif; ?>
</tr>