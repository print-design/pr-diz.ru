<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->machine->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php
        if(IsInRole('admin')) {
            include 'grafik_select_user1.php';
        }
        elseif(array_key_exists('u1_fio', $this->shift_data)) {
            echo $this->shift_data['u1_fio'];
        }
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->machine->user2Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php
        if(IsInRole('admin')) {
            include 'grafik_select_user2.php';
        }
        elseif(array_key_exists('u2_fio', $this->shift_data)) {
            echo $this->shift_data['u2_fio'];
        }
        ?>
    </td>
    <?php endif; ?>
    
    <?php endif; ?>
    
    <!-- Создание тиража -->
    <?php if(IsInRole('admin')): ?>
    <td class='<?=$top.' '.$this->shift ?>' style="position: relative;">
        <button type='button' class='btn btn-outline-dark btn-sm open_add_edition_buttons'<?=$this->allow_edit_disabled ?> style='display: block;' data-toggle='tooltip' title='Добавить тираж' onclick="javascript: $(this).next('.add_edition_buttons').removeClass('d-none');"><i class='fas fa-plus'></i></button>
        <div class="add_edition_buttons d-none">
            <button type='button' class='btn btn-outline-dark btn-sm' style='display: inline;' data-workshift='<?=$this->shift_data['id'] ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$this->edition['position'] ?>' data-direction='up' onclick='javascript: CreateEdition($(this));' data-toggle='tooltip' title='Добавить тираж выше'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-up'></i></button>
            <button type='button' class='btn btn-outline-dark btn-sm' style='display: inline;' data-workshift='<?=$this->shift_data['id'] ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$this->edition['position'] ?>' data-direction='down' onclick="javascript: CreateEdition($(this));" data-toggle='tooltip' title='Добавить тираж ниже'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-down'></i></button>
            <a href="javascript: void(0);" class="add_edition_buttons_close" onclick="javascript: $(this).parent().addClass('d-none');"><i class="fa fa-window-close"></i></a>
        </div>
    </td>
    <?php endif; ?>
    
    <!-- Вставка тиража -->
    <?php if(IsInRole('admin')): ?>
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->machine->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['organization'] ?></td><?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->machine->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['edition'] ?></td><?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->machine->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"><?= empty($this->edition['status']) ? $this->edition['length'] : $this->edition['status'] ?></td><?php endif; ?>
    
    <!-- Статус -->
    <?php if(IsInRole('admin')): if($this->machine->hasStatus): ?>
    <?php endif; endif; ?>
    
    <!-- Вал -->
    <?php if($this->machine->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['roller'] ?></td><?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->machine->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['lamination'] ?></td><?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->machine->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['coloring'] ?></td><?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->machine->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['manager'] ?></td><?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->machine->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['comment'] ?></td><?php endif; ?>
    
    <?php if(IsInRole('admin')): ?>
    
    <!-- Копирование тиража -->
    
    <!-- Сдвиг тиража -->
    
    <!-- Удаление смены -->
    
    <?php endif; ?>
</tr>