<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="<?=$total_shifts_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$total_shifts_count ?>"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <?php if($this->grafik->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>"><?php print_r($this->editions); ?></td>
    <?php endif; ?>
</tr>