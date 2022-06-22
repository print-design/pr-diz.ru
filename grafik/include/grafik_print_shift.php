<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->timetable->user1Name != ''): ?>
    <td class='<?=$top.' '.$this->shift ?>' title='<?=$this->timetable->user1Name ?>'>
        <?php echo (array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->timetable->user2Name != ''): ?>
    <td class='<?=$top.' '.$this->shift ?>' title='<?=$this->timetable->user2Name ?>'>
        <?php echo (array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->timetable->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->timetable->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->timetable->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Вал -->
    <?php if($this->timetable->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->timetable->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->timetable->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->timetable->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->timetable->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Дата продолжения работы над этим тиражом -->
    <td class="<?=$top.' '.$this->shift ?>"
</tr>