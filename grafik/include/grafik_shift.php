<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="2"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="2"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->machine->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>">
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
    <td class="<?=$top.' '.$this->shift ?>">
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
    
    <!-- Создание тиража -->
    <?php if(IsInRole('admin')): ?>
    <td class='<?=$top." ".$this->shift ?> align-bottom'>
        <button type='button' class='btn btn-outline-dark btn-sm'<?=$this->allow_edit_disabled ?> style='display: block;' data-toggle='tooltip' data-machine='<?=$this->machine->machineId ?>' data-from='<?=$this->machine->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->machine->dateTo->format("Y-m-d") ?>' data-date='<?=$this->date->format('Y-m-d') ?>' data-shift='<?=$this->shift ?>' data-workshift='<?=(empty($this->shift_data['id']) ? '' : $this->shift_data['id']) ?>' onclick='javascript: CreateEdition($(this))' title='Добавить тираж'><i class='fas fa-plus'></i></button>
    </td>
    <?php endif; ?>
    
    <!-- Вставка тиража -->
    <?php if(IsInRole('admin')): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php
        $disabled = " disabled='disabled'";
        if($this->machine->clipboard_db) {
            $disabled = '';
        }
        ?>
        <?php if(!empty($this->allow_edit_disabled)): ?>
        <button type="button" class="btn btn-outline-dark btn-sm" style="display: block;"<?=$this->allow_edit_disabled ?>><i class="fas fa-paste"></i></button>
        <?php else: ?>
        <button type='button' class='btn btn-outline-dark btn-sm btn_clipboard_paste' style='display: block;' data-toggle='tooltip' data-machine='<?=$this->machine->machineId ?>' data-from='<?=$this->machine->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->machine->dateTo->format("Y-m-d") ?>' data-date='<?=$this->date->format('Y-m-d') ?>' data-shift='<?=$this->shift ?>' data-workshift='<?=(empty($this->shift_data['id']) ? '' : $this->shift_data['id']) ?>' onclick='javascript: PasteEditionDb($(this))' title='Вставить тираж'<?=$disabled ?>><i class='fas fa-paste'></i></button>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->machine->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->machine->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->machine->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    
    <!-- Статус -->
    <?php if(IsInRole('admin')): if($this->machine->hasStatus): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; endif; ?>
    
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
    
    <?php if(IsInRole('admin')): ?>
    
    <!-- Копирование тиража -->
    <td class="<?=$top.' '.$this->shift ?>"></td>
    
    <!-- Сдвиг тиража -->
    <td class="<?=$top.' '.$this->shift ?>"></td>
    
    <!-- Удаление смены -->
    <td class="<?=$top.' '.$this->shift ?>">
    <?php if(isset($this->shift_data['id'])): ?>
    <button type='button' class='btn btn-outline-dark btn-sm'<?=$this->allow_edit_disabled ?> data-id='<?=$this->shift_data['id'] ?>' data-machine='<?=$this->machine->machineId ?>' data-from='<?=$this->machine->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->machine->dateTo->format("Y-m-d") ?>' onclick='javascript: if(confirm("Действительно удалить?")){ DeleteShift($(this)); }' data-toggle='tooltip' title='Удалить смену'><i class='fas fa-trash-alt'></i></button>
    <?php endif; ?>
    </td>
    
    <?php endif; ?>
</tr>