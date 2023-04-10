<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?> border" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$top.' '.$this->shift ?>">
        <select id="select_employee1" class="form-control small">
            <option value="">...</option>
        </select>
    </td>
    <?php if($this->timetable->machine == CalculationBase::COMIFLEX): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <select id="select_employee2" class="form-control small">
            <option value="">...</option>
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