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
        <select class="form-control small select_employee1">
            <option value="">...</option>
            <?php
            $sql = "select id, first_name, last_name from plan_employee where active = 1 and role_id = ".ROLE_PRINT;
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            ?>
            <option id="<?=$row['id'] ?>"><?=$row['last_name'].' '.$row['first_name'] ?></option>
            <?php endwhile; ?>
        </select>
    </td>
    <?php if($this->timetable->machine == CalculationBase::COMIFLEX): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <select class="form-control small select_employee2">
            <option value="">...</option>
            <?php
            $sql = "select id, first_name, last_name from plan_employee where active = 1 and role_id = ".ROLE_ASSISTANT;
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()):
            ?>
            <option id="<?=$row['id'] ?>"><?=$row['last_name'].' '.$row['first_name'] ?></option>
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