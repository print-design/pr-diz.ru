<?php
require_once './_roles.php';
?>
<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?> border" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$top.' '.$this->shift ?>">
        <select class="form-control small select_employee1" data-machine-id="<?=$this->timetable->machineId ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machineId.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            $sql = "select id, first_name, last_name from plan_employee where active = 1 and role_id = ".ROLE_PRINT;
            if(array_key_exists($key, $this->timetable->workshifts1)) {
                $sql .= " union "
                    . "select id, first_name, last_name from plan_employee where active = 0 and role_id = ".ROLE_PRINT
                    ." and id = ".$this->timetable->workshifts1[$key];
            }
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts1) && $row['id'] == $this->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            ?>
            <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['last_name'].' '.$row['first_name'] ?></option>
            <?php endwhile; ?>
        </select>
    </td>
    <?php if($this->timetable->machine == CalculationBase::COMIFLEX): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <select class="form-control small select_employee2" data-machine-id="<?=$this->timetable->machineId ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>">
            <option value="">...</option>
            <?php
            $key = $this->timetable->machineId.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
            $sql = "select id, first_name, last_name from plan_employee where active = 1 and role_id = ".ROLE_ASSISTANT;
            if(array_key_exists($key, $this->timetable->workshifts2)) {
                $sql .= " union "
                        . "select id, first_name, last_name from plan_employee where active = 0 and role_id = ".ROLE_ASSISTANT
                        ." and id = ".$this->timetable->workshifts2[$key];
            }
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            $selected = '';
            if(array_key_exists($key, $this->timetable->workshifts2) && $row['id'] == $this->timetable->workshifts2[$key]) {
                $selected = " selected='selected'";
            }
            ?>
            <option value="<?=$row['id'] ?>"<?=$selected ?>><?=$row['last_name'].' '.$row['first_name'] ?></option>
            <?php endwhile; ?>
        </select>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?> text-right"><img src="../images/icons/vertical-dots1.svg" /></td>
</tr>