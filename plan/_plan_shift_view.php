<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="" data-position="">
    <?php if($this->shift == 'day'): ?>
    <td class="border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$this->shift ?> border-right">
        <?php
        $key = $this->timetable->work_id.'_'.$this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
        
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER])) || /*ВРЕМЕННО*/ GetUserId() == CUTTER_SOMA):    
        ?>
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-work-id="<?=$this->timetable->work_id ?>" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->timetable->dateTo->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
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
        <?php
        elseif(array_key_exists($key, $this->timetable->workshifts1)):
            $employee = $this->timetable->employees[$this->timetable->workshifts1[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        endif;
        
        if($this->timetable->work_id == WORK_PRINTING && ($this->timetable->machine_id == PRINTER_COMIFLEX || $this->timetable->machine_id == PRINTER_SOMA_OPTIMA)):
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER])) || /*ВРЕМЕННО*/ GetUserId() == CUTTER_SOMA):
        ?>
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small mt-2" data-work-id="<?=$this->timetable->work_id ?>" data-machine-id="<?=$this->timetable->machine_id ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->timetable->dateTo->format('Y-m-d') ?>">
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
        <?php
        elseif(array_key_exists($key, $this->timetable->workshifts2)):
            echo '<br />';
            $employee = $this->timetable->employees[$this->timetable->workshifts2[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        endif;
        endif;
        ?>
    </td>
    <td class="<?=$this->shift ?> showdropline fordrag" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline storekeeper_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline not_colorist_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline storekeeper_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> not_storekeeper_hidden"></td>
    <td class="<?=$this->shift ?> not_storekeeper_hidden"></td>
    <td class="<?=$this->shift ?> not_storekeeper_hidden cutting_hidden"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline comment_cell comment_invisible colorist_hidden" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
</tr>