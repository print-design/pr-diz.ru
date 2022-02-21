<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->machine->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php echo(array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->machine->user2Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php echo (array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->machine->hasOrganization): ?>
    <td class='<?=$top.' '.$this->shift ?>'>
        <?=$this->edition['organization'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->machine->hasEdition): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['edition'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->machine->hasLength): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=(empty($this->edition['status']) ? $this->edition['length'] : $this->edition['status']) ?>
    </td>
    <?php endif; ?>
    
    <!-- Вал -->
    <?php if($this->machine->hasRoller): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['roller'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->machine->hasLamination): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['lamination'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->machine->hasColoring): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['coloring'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->machine->hasManager): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['manager'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->machine->hasComment): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['comment'] ?>
    </td>
    <?php endif; ?>
</tr>