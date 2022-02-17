<table class="table table-bordered typography">
    <thead id="grafik1-thead1">
        <tr>
            <th></th>
            <th>Дата</th>
            <th>Смена</th>
            <?php if($this->user1Name != ''): ?> <th><?= $this->user1Name ?></th> <?php endif; ?>
            <?php if($this->user2Name != ''): ?> <th><?= $this->user2Name ?></th> <?php endif; ?>
            <?php if(IsInRole('admin')): ?> <th></th> <?php endif; ?>
            <?php if(IsInRole('admin')): ?> <th></th> <?php endif; ?>
            <?php if($this->hasOrganization): ?> <th>Заказчик</th> <?php endif; ?>
            <?php if($this->hasEdition): ?> <th>Наименование</th> <?php endif; ?>
            <?php if($this->hasLength): ?> <th>Метраж</th> <?php endif; ?>
            <?php if(IsInRole('admin')): if($this->hasStatus): ?> <th>Статус</th> <?php endif;    endif; ?>
            <?php if($this->hasRoller): ?> <th>Вал</th> <?php endif; ?>
            <?php if($this->hasLamination): ?> <th>Ламинация</th> <?php endif; ?>
            <?php if($this->hasColoring): ?> <th>Кр-ть</th> <?php endif; ?>
            <?php if($this->hasManager): ?> <th>Менеджер</th> <?php endif; ?>
            <?php if($this->hasComment): ?> <th>Комментарий</th> <?php endif; ?>
            <?php if(IsInRole('admin')): ?>
            <th></th>
            <th></th>
            <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="grafik1-tbody1">
        <?php
        foreach ($dateshifts as $dateshift):
        
        $date_diff_from_now = date_diff(new DateTime(), $dateshift['date']);
        
        $allow_edit_disabled = '';
        if(!$allow_edit && $date_diff_from_now->days < 1 && !$this->isCutter) {
            $allow_edit_disabled = " disabled='disabled'";
        }
        ?>
        <tr>
            <?php if($dateshift['shift'] == 'day'): ?>
            <td class='<?=$dateshift['top'] ?> <?= $dateshift['shift'] ?>' rowspan='<?= $dateshift['rowspan'] ?>'><?= $GLOBALS['weekdays'][$dateshift['date']->format('w')] ?></td>
            <td class='<?=$dateshift['top'] ?> <?= $dateshift['shift'] ?>' rowspan='<?= $dateshift['rowspan'] ?>'><?= $dateshift['date']->format('d.m').".".$dateshift['date']->format('Y') ?></td>
            <?php endif; ?>
            <td class='<?=$dateshift['top'] ?> <?= $dateshift['shift'] ?>' rowspan='<?= $dateshift['my_rowspan'] ?>'><?= ($dateshift['shift'] == 'day' ? 'День' : 'Ночь') ?></td>
        
            <!-- Работник №1 -->
            <?php if($this->user1Name != ''): ?>
            <td class='<?=$dateshift['top'] ?> <?=$dateshift['shift'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>' title='<?=$this->user1Name ?>'>
                <?php if(IsInRole('admin')): ?>
                <select id='user1_id' name='user1_id' style='width:100px;' onchange='javascript: EditUser1($(this))' data-id='<?=(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '') ?>' data-date='<?=$dateshift['date']->format('Y-m-d') ?>' data-shift='<?=$dateshift['shift'] ?>' data-machine='<?=$this->machineId ?>' data-from='<?=$this->dateFrom->format('Y-m-d') ?>' data-to='<?=$this->dateTo->format('Y-m-d') ?>'>
                    <optgroup>
                        <option value="">...</option>
                        <?php
                        foreach ($this->users1 as $value) {
                            $selected = '';
                            if(isset($dateshift['row']['u1_id']) && $dateshift['row']['u1_id'] == $value['id']) $selected = " selected = 'selected'";
                            echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                        }
                        ?>
                    </optgroup>
                    <optgroup label='______________'>
                        <option value='+'>(добавить)</option>
                    </optgroup>
                </select>
            
                <div class="input-group d-none">
                    <input type="text" id="user1" name="user1" value="" class="editable" />
                    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser1($(this));" data-id="<?=(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '') ?>" role_id="<?=$this->userRole ?>" data-date="<?=$dateshift['date']->format('Y-m-d') ?>" data-shift="<?=$dateshift['shift'] ?>" data-machine="<?=$this->machineId ?>" data-from="<?=$this->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->dateTo->format('Y-m-d') ?>"><i class="fas fa-save"></i></button></div>
                    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user1="<?=(isset($dateshift['row']['u1_id']) ? $dateshift['row']['u1_id'] : '') ?>" onclick="javascript: CancelCreateUser1($(this));"><i class="fas fa-window-close"></i></button></div>
                </div>
                <?php
                else:
                    echo (isset($dateshift['row']['u1_fio']) ? $dateshift['row']['u1_fio'] : '');
                endif;
                ?>
            </td>
            <?php endif; ?>
        
            <!-- Работник №2 -->
            <?php if($this->user2Name != ''): ?>
            <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>' title='<?=$this->user2Name ?>'>
                <?php if(IsInRole('admin')): ?>
                <select id='user2_id' name='user2_id' style='width:100px;' onchange='javascript: EditUser2($(this))' data-id='<?=(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '') ?>' data-date='<?=$dateshift['date']->format('Y-m-d') ?>' data-shift='<?=$dateshift['shift'] ?>' data-machine='<?=$this->machineId ?>' data-from='<?=$this->dateFrom->format('Y-m-d') ?>' data-to='<?=$this->dateTo->format('Y-m-d') ?>'>
                    <optgroup>
                        <option value="">...</option>
                        <?php
                        foreach ($this->users2 as $value) {
                            $selected = '';
                            if(isset($dateshift['row']['u2_id']) && $dateshift['row']['u2_id'] == $value['id']) $selected = " selected = 'selected'";
                            echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                        }
                        ?>
                    </optgroup>
                    <optgroup label='______________'>
                        <option value='+'>(добавить)</option>
                    </optgroup>
                </select>
                            
                <div class="input-group d-none">
                    <input type="text" id="user2" name="user2" value="" class="editable" />
                    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser2($(this));" data-id="<?=(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '') ?>" role_id="<?=$this->userRole ?>" data-date="<?=$dateshift['date']->format('Y-m-d') ?>" data-shift="<?=$dateshift['shift'] ?>" data-machine="<?=$this->machineId ?>" data-from="<?=$this->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->dateTo->format('Y-m-d') ?>"><i class="fas fa-save"></i></button></div>
                    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user2="<?=(isset($dateshift['row']['u2_id']) ? $dateshift['row']['u2_id'] : '') ?>" onclick="javascript: CancelCreateUser2($(this));"><i class="fas fa-window-close"></i></button></div>
                </div>
                <?php else: ?>
                <?php echo (isset($dateshift['row']['u2_fio']) ? $dateshift['row']['u2_fio'] : ''); ?>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        
            <!-- Создание тиража -->
            <?php if(IsInRole('admin')): ?>
                <?php if(count($dateshift['editions']) == 0): ?>
                    <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?> align-bottom' rowspan='<?=$dateshift['my_rowspan'] ?>'>
                        <button type='button' class='btn btn-outline-dark btn-sm'<?=$allow_edit_disabled ?> style='display: block;' data-toggle='tooltip' data-machine='<?=$this->machineId ?>' data-from='<?=$this->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->dateTo->format("Y-m-d") ?>' data-date='<?=$dateshift['date']->format('Y-m-d') ?>' data-shift='<?=$dateshift['shift'] ?>' data-workshift='<?=(empty($dateshift['row']['id']) ? '' : $dateshift['row']['id']) ?>' onclick='javascript: CreateEdition($(this))' title='Добавить тираж'><i class='fas fa-plus'></i></button>
                    </td>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Смены -->
            <?php $edition = null; ?>
            
            <?php if(count($dateshift['editions']) == 0): ?>
                <?php if(IsInRole('admin')): ?>
                    <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'>
                        <!-- Вставка тиража -->
                        <?php
                        $disabled = " disabled='disabled'";
                        if($clipboard_db) {
                            $disabled = '';
                        }
                        ?>
                        <?php if(!empty($allow_edit_disabled)): ?>
                        <button type="button" class="btn btn-outline-dark btn-sm" style="display: block;"<?=$allow_edit_disabled ?>><i class="fas fa-paste"></i></button>
                        <?php else: ?>
                        <button type='button' class='btn btn-outline-dark btn-sm btn_clipboard_paste' style='display: block;' data-toggle='tooltip' data-machine='<?=$this->machineId ?>' data-from='<?=$this->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->dateTo->format("Y-m-d") ?>' data-date='<?=$dateshift['date']->format('Y-m-d') ?>' data-shift='<?=$dateshift['shift'] ?>' data-workshift='<?=(empty($dateshift['row']['id']) ? '' : $dateshift['row']['id']) ?>' onclick='javascript: PasteEditionDb($(this))' title='Вставить тираж'<?=$disabled ?>><i class='fas fa-paste'></i></button>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
                <?php if($this->hasOrganization): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasEdition): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasLength): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if(IsInRole('admin')): if($this->hasStatus): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; endif; ?>
                <?php if($this->hasRoller): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasLamination): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasColoring): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasManager): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if($this->hasComment): ?> <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td> <?php endif; ?>
                <?php if(IsInRole('admin')): ?>
                    <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                    <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                    <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'>
                    <?php if(isset($dateshift['row']['id'])): ?>
                        <button type='button' class='btn btn-outline-dark btn-sm'<?=$allow_edit_disabled ?> data-id='<?=$dateshift['row']['id'] ?>' data-machine='<?=$this->machineId ?>' data-from='<?=$this->dateFrom->format("Y-m-d") ?>' data-to='<?=$this->dateTo->format("Y-m-d") ?>' onclick='javascript: if(confirm("Действительно удалить?")){ DeleteShift($(this)); }' data-toggle='tooltip' title='Удалить смену'><i class='fas fa-trash-alt'></i></button>
                    <?php endif; ?>
                    </td>
                <?php endif; ?>
            <?php else: ?>
                <?php
                $edition = array_shift($dateshift['editions']);
                $this->ShowEdition($edition, $dateshift['top'], $clipboard_db, $allow_edit_disabled);
                ?>
            <?php endif; ?>
        </tr>
    
        <!-- Дополнительные смены -->
        <?php
        $edition = array_shift($dateshift['editions']);
            
        while ($edition != null) {
            echo '<tr>';
            $this->ShowEdition($edition, 'nottop', $clipboard_db, $allow_edit_disabled);
            echo '</tr>';
            $edition = array_shift($dateshift['editions']);
        }
        ?>
        <?php endforeach; ?>
    </tbody>
</table>