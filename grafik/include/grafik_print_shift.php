<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="2"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="2"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->machine->user1Name != ''): ?>
    <td class='<?=$top.' '.$this->shift ?>' title='<?=$this->machine->user1Name ?>'>
        <?php echo (array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->machine->user2Name != ''): ?>
    <td class='<?=$top.' '.$this->shift ?>' title='<?=$this->machine->user2Name ?>'>
        <?php echo (array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->machine->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->machine->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->machine->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Вал -->
    <?php if($this->machine->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->machine->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->machine->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->machine->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->machine->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
</tr>