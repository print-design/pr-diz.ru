<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekday_names'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$this->shift ?> border-right text-nowrap">
        <?php
        $key = $this->date->format('Y-m-d').'_'.$this->shift;
        if(array_key_exists($key, $this->timetable->workshifts)) {
            $employee = $this->timetable->employees[$this->timetable->workshifts[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        }
        ?>
    </td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
    <td class="<?=$this->shift ?>"></td>
</tr>