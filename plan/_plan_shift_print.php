<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top.' '.$this->shift ?> border-right" rowspan="<?=$this->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold; margin-top: 10px;"><?= ltrim($this->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <td class="<?=$top.' '.$this->shift ?> border-right">
        <?php
        $key = $this->timetable->work_id.'_'.$this->timetable->machine_id.'_'.$this->date->format('Y-m-d').'_'.$this->shift;
        if(array_key_exists($key, $this->timetable->workshifts1)) {
            $employee = $this->timetable->employees[$this->timetable->workshifts1[$key]];
            echo "<span class='text-nowrap'>".$employee['last_name'].' '.$employee['first_name']."</span>";
        }
        
        if($this->timetable->work_id == WORK_PRINTING && $this->timetable->machine_id == PRINTER_COMIFLEX) {
            if(array_key_exists($key, $this->timetable->workshifts2)) {
                echo '<br />';
                $employee = $this->timetable->employees[$this->timetable->workshifts2[$key]];
                echo "<span class='text-nowrap'>".$employee['last_name'].' '.$employee['first_name']."</span>";
            }
        }
        ?>
    </td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?> storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> cutting_hidden lamination_hidden storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> cutting_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> cutting_hidden lamination_hidden storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> not_storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> not_storekeeper_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?> not_storekeeper_hidden cutting_hidden"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?>"></td>
    <td class="<?=$top.' '.$this->shift ?> colorist_hidden"></td>
</tr>