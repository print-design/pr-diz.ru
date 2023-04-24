<?php
require_once './_roles.php';
?>
<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="" data-position="">
    <?php if($this->shift == 'day'): ?>
    <td class="border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$this->shift ?>">
        <div style="display: block;">
            <img src="../images/icons/up_arrow.png" />
        </div>
        <?=($this->shift == 'day' ? 'День' : 'Ночь') ?>
        <div style="display: block; margin-top: 2px;">
            <img src="../images/icons/down_arrow.png" />
        </div>
    </td>
    <td class="<?=$this->shift ?>">
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
    <td class="<?=$this->shift ?> assistant" style="display: none;">
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
    <td class="<?=$this->shift ?> showdropline border-left fordrag" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
</tr>