<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="2"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="2"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top ?>"><?=($dateshift['shift'] == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->machine->user1Name != ''): ?>
    <td class='<?=$top ?>' title='<?=$this->machine->user1Name ?>'>
        <?php echo (array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->machine->user2Name != ''): ?>
    <td class='<?=$top ?>' title='<?=$this->machine->user2Name ?>'>
        <?php echo (array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : ''); ?>
    </td>
    <?php endif; ?>
</tr>