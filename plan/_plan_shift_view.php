<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="" data-position="">
    <?php if($this->shift == 'day'): ?>
    <td class="border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$this->shift ?>">
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-work-id="<?=$this->timetable->work_id ?>" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->work_id.'_'.$this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            foreach($this->timetable->employees as $emp_key => $employee):
                $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts1) && $emp_key == $this->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == WORK_PLAN_ROLES[$this->timetable->work_id] && ($employee['active'] == 1 || $emp_key == $this->timetable->workshifts1[$key])):
            ?>
            <option value="<?=$emp_key ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php if($this->timetable->work_id == WORK_PRINTING && $this->timetable->machine_id == PRINTER_COMIFLEX): ?>
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small mt-2" data-work-id="<?=$this->timetable->work_id ?>" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->work_id.'_'.$this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            foreach($this->timetable->employees as $emp_key => $employee):
                $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts2) && $emp_key == $this->timetable->workshifts2[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == PLAN_ROLE_ASSISTANT && ($employee['active'] == 1 || $emp_key == $this->timetable->workshifts2[$key])):
            ?>
            <option value="<?=$emp_key ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php endif; ?>
    </td>
    <td class="<?=$this->shift ?> showdropline border-left fordrag" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td style="display: none;" class="<?=$this->shift ?> showdropline comment_cell" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
</tr>