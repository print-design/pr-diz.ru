<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m.Y') ?></td>
    <?php endif; ?>
    
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    
    <!-- Работник №1 -->
    <?php if($this->timetable->user1Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php
        if($is_admin) {
            include 'grafik_select_user1.php';
        }
        elseif(array_key_exists('u1_fio', $this->shift_data)) {
            echo $this->shift_data['u1_fio'];
        }
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Работник №2 -->
    <?php if($this->timetable->user2Name): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>">
        <?php
        if($is_admin) {
            include 'grafik_select_user2.php';
        }
        elseif(array_key_exists('u2_fio', $this->shift_data)) {
            echo $this->shift_data['u2_fio'];
        }
        ?>
    </td>
    <?php endif; ?>
    
    <?php endif; ?>
    
    <?php if($is_admin): ?>
    <!-- Создание тиража -->
    <td class='<?=$top.' '.$this->shift ?>' style="position: relative;">
        <button type='button' class='btn btn-outline-dark btn-sm open_add_edition_buttons'<?=$this->allow_edit_disabled ?> style='display: block;' data-toggle='tooltip' title='Добавить тираж' onclick="javascript: $(this).next('.add_edition_buttons').removeClass('d-none');"><i class='fas fa-plus'></i></button>
        <div class="add_edition_buttons d-none">
            <button type='button' class='btn btn-outline-dark btn-sm' style='display: inline;' data-workshift='<?=$this->shift_data['id'] ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$this->edition['position'] ?>' data-direction='up' onclick='javascript: CreateEdition($(this));' data-toggle='tooltip' title='Добавить тираж выше'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-up'></i></button>
            <button type='button' class='btn btn-outline-dark btn-sm' style='display: inline;' data-workshift='<?=$this->shift_data['id'] ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$this->edition['position'] ?>' data-direction='down' onclick="javascript: CreateEdition($(this));" data-toggle='tooltip' title='Добавить тираж ниже'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-down'></i></button>
            <a href="javascript: void(0);" class="add_edition_buttons_close" onclick="javascript: $(this).parent().addClass('d-none');"><i class="fa fa-window-close"></i></a>
        </div>
    </td>
    
    <!-- Вставка тиража -->
    <td class='<?=$top.' '.$this->shift ?>' style="position: relative;">
        <?php if(!empty($this->allow_edit_disabled)): ?>
        <button type="button" class="btn btn-outline-dark btn-sm" style="display: block;"<?=$this->allow_edit_disabled ?>><i class="fas fa-paste"></i></button>
        <?php else: ?>
        <button type="button" class="btn btn-outline-dark btn-sm open_add_edition_buttons btn_clipboard_paste" style="display: block;" data-toggle="tooltip" title="Вставить тираж"<?=$disabled ?> onclick="javascript: $(this).next('.add_edition_buttons').removeClass('d-none');"><i class="fas fa-paste"></i></button>
        <div class="add_edition_buttons d-none">
            <button type="button" class='btn btn-outline-dark btn-sm btn_clipboard_paste' style='display: inline;' data-toggle='tooltip' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-workshift='<?=$this->shift_data['id'] ?>' data-direction='up' data-position='<?=$this->edition['position'] ?>' onclick="javascript: PasteEditionDb($(this))" title='Вставить тираж выше'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>
            <button type="button" class='btn btn-outline-dark btn-sm btn_clipboard_paste' style="display: inline;" data-toggle='tooltip' data-machine='<?=$this->edition['machine_id'] ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-workshift='<?=$this->shift_data['id'] ?>' data-direction='down' data-position='<?=$this->edition['position'] ?>' onclick="javascript: PasteEditionDb($(this))" title='Вставить тираж ниже'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>
            <a href="javascript: void(0);" class="add_edition_buttons_close" onclick="javascript: $(this).parent().addClass('d-none');"><i class="fa fa-window-close"></i></a>
        </div>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    
    <!-- Заказчик -->
    <?php if($this->timetable->hasOrganization): ?>
    <td class='<?=$top.' '.$this->shift ?>'>
        <?php if($is_admin): ?>
        <input type="text"<?=$this->allow_edit_disabled ?> value="<?=htmlentities($this->edition['organization']) ?>" onfocusout='javascript: EditOrganization($(this))' class="editable organizations" data-id='<?=$this->edition['id'] ?>' style="width:140px;" />
        <?php
        else:
            echo $this->edition['organization'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Наименование -->
    <?php if($this->timetable->hasEdition): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <input type="text"<?=$this->allow_edit_disabled ?> value="<?=htmlentities($this->edition['edition']) ?>" onfocusout="javascript: EditEdition($(this))" class="editable editions" data-id='<?=$this->edition['id'] ?>' style="width:140px;" />
        <?php
        else:
            echo $this->edition['edition'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Марка пленки -->
    <?php if($this->timetable->hasMaterial): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <select data-id='<?=$this->edition['id'] ?>' onfocusout="javascript: EditMaterial($(this))">
            <optgroup>
                <option value="">...</option>
                <?php $selected = ''; if($this->edition['material'] == 'Прозрачная') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>Прозрачная</option>
                <?php $selected = ''; if($this->edition['material'] == 'Белая') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>Белая</option>
                <?php $selected = ''; if($this->edition['material'] == 'Металлическая') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>Металлическая</option>
            </optgroup>
        </select>
        <?php
        else:
            echo $this->edition['material'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Толщина -->
    <?php if($this->timetable->hasThickness): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <select data-id='<?=$this->edition['id'] ?>' onfocusout="javascript: EditThickness($(this))">
            <optgroup>
                <option value="">...</option>
                <?php $selected = ''; if($this->edition['thickness'] == '10 мкм') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>10 мкм</option>
                <?php $selected = ''; if($this->edition['thickness'] == '11 мкм') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>11 мкм</option>
                <?php $selected = ''; if($this->edition['thickness'] == '12 мкм') $selected = " selected = 'selected'"; ?>
                <option<?=$selected ?>>12 мкм</option>
            </optgroup>
        </select>
        <?php
        else:
            echo $this->edition['thickness'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Ширина -->
    <?php if($this->timetable->hasWidth): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <input type="text" value="<?=$this->edition['width'] ?>" onfocusout="javascript: EditWidth($(this))" data-id='<?=$this->edition['id'] ?>' class="editable" style="width:65px;" />
        <?php
        else:
            echo $this->edition['width'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Метраж -->
    <?php if($this->timetable->hasLength): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <input type="text" value="<?=$this->edition['length'] ?>" onfocusout="javascript: EditLength($(this))" data-id='<?=$this->edition['id'] ?>' class="editable int-only" style="width:65px;" />
        <?php
        elseif(!empty($this->edition['status'])):
            echo $this->edition['status'];
        else:
            echo $this->edition['length'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Статус -->
    <?php if($is_admin): if($this->timetable->hasStatus): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <select data-id='<?=$this->edition['id'] ?>' onfocusout="javascript: EditStatus($(this))" style='width:85px;'>
            <optgroup>
                <option value="">...</option>
                    <?php
                    foreach ($this->timetable->statuses as $value) {
                        $selected = '';
                        if($this->edition['status_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                    }
                    ?>
            </optgroup>
        </select>
    </td>
    <?php endif; endif; ?>
    
    <!-- Вал -->
    <?php if($this->timetable->hasRoller): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <select data-id='<?=$this->edition['id'] ?>' onfocusout="javascript: EditRoller($(this))">
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($this->timetable->rollers as $value) {
                    $selected = '';
                    if($this->edition['roller_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <?php
        else:
            echo $this->edition['roller'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Ламинация -->
    <?php if($this->timetable->hasLamination): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <select data-id='<?=$this->edition['id'] ?>' onfocusout="javascript: EditLamination($(this))" style='width:55px;'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($this->timetable->laminations as $value) {
                    $selected = '';
                    if($this->edition['lamination_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <?php
        else:
            echo $this->edition['lamination'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Красочность -->
    <?php if($this->timetable->hasColoring): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <input type="number" min="0" max="<?=$this->timetable->coloring ?>" pattern="\d*" value="<?=$this->edition['coloring'] ?>" data-id='<?=$this->edition['id'] ?>' onfocusout="EditColoring($(this))" class="editable" style="width:35px;" />
        <?php
        else:
            echo $this->edition['coloring'];
        endif;
    ?>
    </td>
    <?php endif; ?>
    
    <!-- Менеджер -->
    <?php if($this->timetable->hasManager): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <select data-id='<?=$this->edition['id'] ?>'<?=$this->allow_edit_disabled ?> onfocusout="javascript: EditManager($(this))" style='width:120px;'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($this->timetable->managers as $value) {
                    $selected = '';
                    if($this->edition['manager_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                }
                ?>
            </optgroup>
        </select>    
        <?php
        else:
            echo $this->edition['manager'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <!-- Комментарий -->
    <?php if($this->timetable->hasComment): ?>
    <td class="<?=$top.' '.$this->shift ?>">
        <?php if($is_admin): ?>
        <textarea rows="5" cols="30" wrap="hard" data-id='<?=$this->edition['id'] ?>' onfocusout="EditComment($(this))" class="editable"><?=htmlentities($this->edition['comment']) ?></textarea>
        <?php
        else:
            echo $this->edition['comment'];
        endif;
        ?>
    </td>
    <?php endif; ?>
    
    <?php if($is_admin): ?>
    <!-- Копирование тиража -->
    <td class='<?=$top.' '.$this->shift ?>'>
        <button type="button" class='btn btn-outline-dark btn-sm clipboard_copy' data='<?=$this->edition['id'] ?>' title='Копировать тираж' data-toggle='tooltip' onclick="javascript: CopyEditionDb(<?=$this->edition['id'] ?>, $(this));"><i class='fas fa-copy'></i><div class='alert alert-info clipboard_alert'>Скопировано</div></button>
    </td>
    
    <!-- Сдвиг тиража -->
    <td class='<?=$top.' '.$this->shift ?>'>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="javascript: ShowMoveForm($(this))" title="Сдвинуть несколько тиражей" data-toggle='tooltip' data-date='<?=$this->shift_data['date'] ?>' data-shift='<?=$this->shift ?>' data-position='<?=$this->edition['position'] ?>' data-machine='<?=$this->edition['machine_id'] ?>' data-workshift='<?=$this->shift_data['id'] ?>' data-from="<?=$from ?>" data-to="<?=$to ?>"><i class="fas fa-long-arrow-alt-up"></i><i class="fas fa-long-arrow-alt-down"></i></button>
    </td>
    
    <!-- Удаление смены -->
    <td class='<?=$top.' '.$this->shift ?>'>
        <button type='button'<?=$this->allow_edit_disabled ?> class='btn btn-outline-dark btn-sm' data-id="<?=$this->edition['id'] ?>" data-machine="<?=$this->edition['machine_id'] ?>" data-from="<?=$from ?>" data-to="<?=$to ?>" onclick="javascript: if(confirm('Действительно удалить?')) { DeleteEdition($(this)); }" title='Удалить тираж' data-toggle="tooltip"><i class='fas fa-trash-alt'></i></button>
    </td>
    <?php endif; ?>
    
    <!-- Дата продолжения работы над этим тиражом -->
    <td class="<?=$top.' '.$this->shift ?>"><?= $this->edition['continuation'] ?></td>
</tr>