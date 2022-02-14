<tr>
    <?php if($this->shift == 'day'): ?>
    <td rowspan="<?=$total_shifts_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td rowspan="<?=$total_shifts_count ?>"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
</tr>