<?php
require_once './_roles.php';
?>
<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top ?> border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control small" data-machine-id="<?=$this->timetable->machineId ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machineId.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
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
    <td class="<?=$top.' '.$this->shift ?> assistant" rowspan="<?=$this->shift_editions_count ?>">
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control small" data-machine-id="<?=$this->timetable->machineId ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machineId.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
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
    <td class="<?=$top.' '.$this->shift ?> border-left" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><strong><?=$this->edition['calculation'] ?></strong><br /><?=$this->edition['customer'] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><?=$this->edition['length_dirty_1'] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><?=$this->edition['raport'] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><?=$this->edition['laminations'] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><?=$this->edition['ink_number'] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" ondrop="Drop(event);" ondragover="DragOver(event);" ondragleave="DragLeave(event);"><?=$this->edition['manager'] ?></td>
    <td class="<?=$top.' '.$this->shift ?> text-right"><a href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>"><img src="../images/icons/vertical-dots1.svg" /></a></td>
</tr>