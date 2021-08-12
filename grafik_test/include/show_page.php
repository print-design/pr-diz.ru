<?php
include 'show_top.php';
?>
<table class="table table-bordered typography">
    <thead id="grafik-thead">
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
    <tbody id="grafik-tbody">
        <?php
        foreach ($dateshifts as $dateshift) {
            $formatted_date = $dateshift['date']->format('Y-m-d');
            $key = $formatted_date.$dateshift['shift'];
            $dateshift['row'] = array();
            if(isset($all[$key])) $dateshift['row'] = $all[$key];
            
            $str_date = $dateshift['date']->format('Y-m-d');
            
            $editions = array();
            if(array_key_exists($str_date, $all_editions) && array_key_exists($dateshift['shift'], $all_editions[$str_date])) {
                $editions = $all_editions[$str_date][$dateshift['shift']];
            }
            
            $day_editions = array();
            if(array_key_exists($str_date, $all_editions) && array_key_exists('day', $all_editions[$str_date])) {
                $day_editions = $all_editions[$str_date]['day'];
            }
            
            $night_editions = array();
            if(array_key_exists($str_date, $all_editions) && array_key_exists('night', $all_editions[$str_date])) {
                $night_editions = $all_editions[$str_date]['night'];
            }
            
            $day_rowspan = count($day_editions);
            if($day_rowspan == 0) $day_rowspan = 1;
            $night_rowspan = count($night_editions);
            if($night_rowspan == 0) $night_rowspan = 1;
            $dateshift['rowspan'] = $day_rowspan + $night_rowspan;
            $dateshift['my_rowspan'] = $dateshift['shift'] == 'day' ? $day_rowspan : $night_rowspan;
            
            echo '<tr>';
            if($dateshift['shift'] == 'day') {
                echo "<td class='".$dateshift['top']." ".$dateshift['shift']."' rowspan='".$dateshift['rowspan']."'>".$GLOBALS['weekdays'][$dateshift['date']->format('w')].'</td>';
                echo "<td class='".$dateshift['top']." ".$dateshift['shift']."' rowspan='".$dateshift['rowspan']."'>".$dateshift['date']->format('d.m').".".$dateshift['date']->format('Y')."</td>";
            }
            echo "<td class='".$dateshift['top']." ".$dateshift['shift']."' rowspan='".$dateshift['my_rowspan']."'>".($dateshift['shift'] == 'day' ? 'День' : 'Ночь')."</td>";
            
            // Работник №1
            if($this->user1Name != '') {
                echo "<td class='".$dateshift['top']." ".$dateshift['shift']."' rowspan='".$dateshift['my_rowspan']."' title='".$this->user1Name."'>";
                if(IsInRole('admin')) {
                    echo "<select id='user1_id' name='user1_id' style='width:100px;' onchange='javascript: EditUser1($(this))' data-id='".(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '')."' data-date='".$dateshift['date']->format('Y-m-d')."' data-shift='".$dateshift['shift']."' data-machine='".$this->machineId."' data-from='".$this->dateFrom->format('Y-m-d')."' data-to='".$this->dateTo->format('Y-m-d')."'>";
                    echo '<optgroup>';
                    echo '<option value="">...</option>';
                    foreach ($this->users1 as $value) {
                        $selected = '';
                        if(isset($dateshift['row']['u1_id']) && $dateshift['row']['u1_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                    }
                    echo '</optgroup>';
                    echo "<optgroup label='______________'>";
                    echo "<option value='+'>(добавить)</option>";
                    echo '</optgroup>';
                    echo '</select>';
                    
                    echo '<div class="input-group d-none">';
                    echo '<input type="text" id="user1" name="user1" value="" class="editable" />';
                    echo '<div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser1($(this));" data-id="'.(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '').'" role_id="'.$this->userRole.'" data-date="'.$dateshift['date']->format('Y-m-d').'" data-shift="'.$dateshift['shift'].'" data-machine="'.$this->machineId.'" data-from="'.$this->dateFrom->format('Y-m-d').'" data-to="'.$this->dateTo->format('Y-m-d').'"><i class="fas fa-save"></i></button></div>';
                    echo '<div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user1="'.(isset($dateshift['row']['u1_id']) ? $dateshift['row']['u1_id'] : '').'" onclick="javascript: CancelCreateUser1($(this));"><i class="fas fa-window-close"></i></button></div>';
                    echo '</div>';
                }
                else {
                    echo (isset($dateshift['row']['u1_fio']) ? $dateshift['row']['u1_fio'] : '');
                }
                echo '</td>';
            }
            
            // Работник №2
            if($this->user2Name != '') {
                echo "<td class='".$dateshift['top']." ".$dateshift['shift']."' rowspan='".$dateshift['my_rowspan']."' title='".$this->user2Name."'>";
                if(IsInRole('admin')) {
                    echo "<select id='user2_id' name='user2_id' style='width:100px;' onchange='javascript: EditUser2($(this))' data-id='".(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '')."' data-date='".$dateshift['date']->format('Y-m-d')."' data-shift='".$dateshift['shift']."' data-machine='".$this->machineId."' data-from='".$this->dateFrom->format('Y-m-d')."' data-to='".$this->dateTo->format('Y-m-d')."'>";
                    echo '<optgroup>';
                    echo '<option value="">...</option>';
                    foreach ($this->users2 as $value) {
                        $selected = '';
                        if(isset($dateshift['row']['u2_id']) && $dateshift['row']['u2_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                    }
                    echo '</optgroup>';
                    echo "<optgroup label='______________'>";
                    echo "<option value='+'>(добавить)</option>";
                    echo '</optgroup>';
                    echo '</select>';
                            
                    echo '<div class="input-group d-none">';
                    echo '<input type="text" id="user2" name="user2" value="" class="editable" />';
                    echo '<div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser2($(this));" data-id="'.(isset($dateshift['row']['id']) ? $dateshift['row']['id'] : '').'" role_id="'.$this->userRole.'" data-date="'.$dateshift['date']->format('Y-m-d').'" data-shift="'.$dateshift['shift'].'" data-machine="'.$this->machineId.'" data-from="'.$this->dateFrom->format('Y-m-d').'" data-to="'.$this->dateTo->format('Y-m-d').'"><i class="fas fa-save"></i></button></div>';
                    echo '<div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user2="'.(isset($dateshift['row']['u2_id']) ? $dateshift['row']['u2_id'] : '').'" onclick="javascript: CancelCreateUser2($(this));"><i class="fas fa-window-close"></i></button></div>';
                    echo '</div>';
                }
                else {
                    echo (isset($dateshift['row']['u2_fio']) ? $dateshift['row']['u2_fio'] : '');
                }
                echo '</td>';
            }
            
            // Создание тиража
            if(IsInRole('admin')) {
                if(count($editions) == 0) {
                    echo "<td class='".$dateshift['top']." ".$dateshift['shift']." align-bottom' rowspan='".$dateshift['my_rowspan']."'>";
                    // Создание тиража
                    echo "<button type='button' class='btn btn-outline-dark btn-sm' style='display: block;' data-toggle='tooltip' data-machine='$this->machineId' data-from='".$this->dateFrom->format("Y-m-d")."' data-to='".$this->dateTo->format("Y-m-d")."' data-date='$formatted_date' data-shift='".$dateshift['shift']."' data-workshift='".(empty($dateshift['row']['id']) ? '' : $dateshift['row']['id'])."' onclick='javascript: CreateEdition($(this))' title='Добавить тираж'><i class='fas fa-plus'></i></button>";
                    echo '</td>'; // Также кнопки "Создать выше" и "Создать ниже" доступны внутри тиража
                }
            }
            
            // Смены
            $edition = null;
            
            if(count($editions) == 0) {
                if(IsInRole('admin')) {
                    echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'>";
                    // Вставка тиража
                    $disabled = " disabled='disabled'";
                    if($clipboard_db) {
                        $disabled = '';
                    }
                    echo "<button type='button' class='btn btn-outline-dark btn-sm btn_clipboard_paste' style='display: block;' data-toggle='tooltip' data-machine='$this->machineId' data-from='".$this->dateFrom->format("Y-m-d")."' data-to='".$this->dateTo->format("Y-m-d")."' data-date='$formatted_date' data-shift='".$dateshift['shift']."' data-workshift='".(empty($dateshift['row']['id']) ? '' : $dateshift['row']['id'])."' onclick='javascript: PasteEditionDb($(this))' title='Вставить тираж'$disabled><i class='fas fa-paste'></i></button>";
                    echo "</td>"; // Также кнопки "Вставка выше" и "Вставка ниже" доступны внутри тиража
                }
                if($this->hasOrganization) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasEdition) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasLength) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if(IsInRole('admin')) {
                    if($this->hasStatus) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                }
                if($this->hasRoller) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasLamination) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasColoring) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasManager) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if($this->hasComment) echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                if(IsInRole('admin')) {
                    echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                    echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'></td>";
                    echo "<td class='".$dateshift['top']." ".$dateshift['shift']."'>";
                    if(isset($dateshift['row']['id'])) {
                        echo "<button type='button' class='btn btn-outline-dark btn-sm' data-id='".$dateshift['row']['id']."' data-machine='$this->machineId' data-from='".$this->dateFrom->format("Y-m-d")."' data-to='".$this->dateTo->format("Y-m-d")."' onclick='javascript: if(confirm(\"Действительно удалить?\")){ DeleteShift($(this)); }' data-toggle='tooltip' title='Удалить смену'><i class='fas fa-trash-alt'></i></button>";
                    }
                    echo "</td>";
                }
            }
            else {
                $edition = array_shift($editions);
                $this->ShowEdition($edition, $dateshift['top'], $clipboard_db);
            }
            
            echo '</tr>';
            
            // Дополнительные смены
            $edition = array_shift($editions);
            
            while ($edition != null) {
                echo '<tr>';
                $this->ShowEdition($edition, 'nottop', $clipboard_db);
                echo '</tr>';
                $edition = array_shift($editions);
            }
        }
        
        foreach ($dateshifts as $dateshift) {
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
    </tr>
        <?php
        }
        ?>
    </tbody>
</table>