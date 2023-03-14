<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->timetable->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php echo(array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->timetable->user2Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php echo (array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : ''); ?>
    </td>
    <?php endif; ?>
    
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->timetable->hasOrganization): ?>
    <td class='<?=$top.' '.$this->shift ?>'>
        <?=$this->edition['organization'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->timetable->hasEdition): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['edition'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Марка пленки -->
    <?php if($this->timetable->hasMaterial): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['material'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Толщина -->
    <?php if($this->timetable->hasThickness): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=(empty($this->edition['thickness']) ? '' : $this->edition['thickness'].' мкм') ?>
    </td>
    <?php endif; ?>
    
    <!-- Ширина -->
    <?php if($this->timetable->hasWidth): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['width'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->timetable->hasLength): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=(empty($this->edition['status']) ? $this->edition['length'] : $this->edition['status']) ?>
    </td>
    <?php endif; ?>
    
    <!-- Вал -->
    <?php if($this->timetable->hasRoller && false): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['roller'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->timetable->hasLamination && false): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['lamination'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->timetable->hasColoring): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['coloring'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->timetable->hasManager): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['manager'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->timetable->hasComment): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?=$this->edition['comment'] ?>
    </td>
    <?php endif; ?>
    
    <!-- Дата продолжения работы над этим тиражом -->
    <td class="<?=$top.' '.$this->shift ?>"><?= $this->edition['continuation'] ?></td>
</tr>