<?php
if($is_admin) {
    // Кнопки добавления тиража
    ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='workshift_id' name='workshift_id' value='<?=$workshift_id ?>' />
        <input type='hidden' id='date' name='date' value='<?=$date ?>' />
        <input type='hidden' id='shift' name='shift' value='<?=$shift ?>' />
        <input type='hidden' id='position' name='position' value='<?=$position ?>' />
        <input type='hidden' id='direction' name='direction' value='up' />
        <button type='submit' id='create_edition_submit' name='create_edition_submit' class='btn btn-outline-dark btn-sm mb-1' data-toggle='tooltip' title='Добавить тираж выше'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-up'></i></button>
    </form>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='workshift_id' name='workshift_id' value='<?=$workshift_id ?>' />
        <input type='hidden' id='date' name='date' value='<?=$date ?>' />
        <input type='hidden' id='shift' name='shift' value='<?=$shift ?>' />
        <input type='hidden' id='position' name='position' value='<?=$position ?>' />
        <input type='hidden' id='direction' name='direction' value='down' />
        <button type='submit' id='create_edition_submit' name='create_edition_submit' class='btn btn-outline-dark btn-sm' data-toggle='tooltip' title='Добавить тираж ниже'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-down'></i></button>
    </form>
</td>
    <?php
    // Кнопки вставки тиража
    $clipboard = '';
    $disabled = " disabled='disabled'";
    
    $paste_edition_submit = filter_input(INPUT_POST, 'paste_edition_submit');
    if($paste_edition_submit !== null) {
        $clipboard = filter_input(INPUT_POST, 'clipboard');
        if($clipboard != '') {
            $disabled = '';
        }
    }
    ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' class='clipboard' id='clipboard' name='clipboard' value='<?=$clipboard ?>'>
        <input type='hidden' id='workshift_id' name='workshift_id' value='<?=$workshift_id ?>' />
        <input type='hidden' id='date' name='date' value='<?=$date ?>' />
        <input type='hidden' id='shift' name='shift' value='<?=$shift ?>' />
        <input type='hidden' id='machine_id' name='machine_id' value='<?=$machine_id ?>' />
        <input type='hidden' id='position' name='position' value='<?=$position ?>' />
        <input type='hidden' id='direction' name='direction' value='up' />
        <button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm mb-1 clipboard_paste' data-toggle='tooltip' title='Вставить тираж выше'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>
        <button type="button" class='btn btn-outline-dark btn-sm mb-1 btn_clipboard_paste' data-toggle='tooltip' title='Вставить тираж выше'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>
    </form>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' class='clipboard' id='clipboard' name='clipboard' value='<?=$clipboard ?>'>
        <input type='hidden' id='workshift_id' name='workshift_id' value='<?=$workshift_id ?>' />
        <input type='hidden' id='date' name='date' value='<?=$date ?>' />
        <input type='hidden' id='shift' name='shift' value='<?=$shift ?>' />
        <input type='hidden' id='machine_id' name='machine_id' value='<?=$machine_id ?>' />
        <input type='hidden' id='position' name='position' value='<?=$position ?>' />
        <input type='hidden' id='direction' name='direction' value='down' />
        <button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm clipboard_paste' data-toggle='tooltip' title='Вставить тираж ниже'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>
        <button type="button" class='btn btn-outline-dark btn-sm btn_clipboard_paste' data-toggle='tooltip' title='Вставить тираж ниже'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>
    </form>
</td>
    <?php
    }
    
    // Заказчик
    if($hasOrganization) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
        <?php if($is_admin): ?>
    <form method="post">
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <div class="input-group">
            <input type="text" id="organization" name="organization" value="<?=(isset($edition['organization']) ? htmlentities($edition['organization']) : '') ?>" class="editable organizations" style="width:140px;" />
            <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
        </div>
    </form>
        <?php
        else:
            echo (isset($edition['organization']) ? htmlentities($edition['organization']) : '');
        endif;
        ?>
</td>
    <?php
    }
    
    // Наименование заказа
    if($hasEdition){
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
        <?php
        if($is_admin) {
            ?>
    <form method="post">
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <div class="input-group">
            <input type="text" id="edition" name="edition" value="<?=(isset($edition['edition']) ? htmlentities($edition['edition']) : '') ?>" class="editable editions" style="width:140px;" />
            <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
        </div>
    </form>
        <?php
        }
        else {
                echo (isset($edition['edition']) ? htmlentities($edition['edition']) : '');
        }
        ?>
</td>
    <?php
    }
    
    // Метраж
    if($hasLength) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
    if($is_admin) {
        ?>
    <form method="post">
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <div class="input-group">
            <input type="number" min="0" pattern="\d*" id="length" name="length" value="<?=(isset($edition['length']) ? $edition['length'] : '') ?>" class="editable" style="width:65px;" />
            <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
        </div>
    </form>
        <?php
    }
    else {
        if(isset($edition['status']) && $edition['status'] != null) {
            echo $edition['status'];
        }
        else if (isset ($edition['length'])) {
            echo $edition['length'];
        }
    }
    ?>
</td>
    <?php
    }
    
    // Статус
    if($is_admin) {
        if($hasStatus) {
            ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <select id='status_id' name='status_id' style='width:85px;'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($statuses as $value) {
                    $selected = '';
                    if(isset($edition['status_id']) && $edition['status_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
    </form>
</td>
        <?php
        }       
    }
    
    // Вал
    if($hasRoller) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
    if($is_admin) {
        ?>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <select id='roller_id' name='roller_id'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($rollers as $value) {
                    $selected = '';
                    if(isset($edition['roller_id']) && $edition['roller_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
    </form>
        <?php
    }
    else {
        echo (isset($edition['roller']) ? $edition['roller'] : '');
    }
    ?>
</td>
    <?php
    }
    
    // Ламинация
    if($hasLamination) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
    if($is_admin) {
        ?>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <select id='lamination_id' name='lamination_id' style='width:55px;'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($laminations as $value) {
                    $selected = '';
                    if(isset($edition['lamination_id']) && $edition['lamination_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
    </form>
        <?php
    }
    else {
        echo (isset($edition['lamination']) ? $edition['lamination'] : '');
    }
    ?>
</td>
    <?php
    }
    
    // Красочность
    if($hasColoring) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
        <?php
        if($is_admin) {
            ?>
    <form method="post">
        <input type="hidden" id="scroll" name="scroll" /><input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <div class="input-group">
            <input type="number" min="0" max="<?=$coloring ?>" pattern="\d*" id="coloring" name="coloring" value="<?=(isset($edition['coloring']) ? $edition['coloring'] : '') ?>" class="editable" style="width:35px;" />
            <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
        </div>
    </form>
        <?php
        }
        else {
            echo (isset($edition['coloring']) ? $edition['coloring'] : '');
        }
    ?>
</td>
    <?php
    }
    
    // Менеджер
    if($hasManager) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
        if($is_admin) {
        ?>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <select id='manager_id' name='manager_id' style='width:120px;'>
            <optgroup>
                <option value="">...</option>
                <?php
                foreach ($managers as $value) {
                    $selected = '';
                    if(isset($edition['manager_id']) && $edition['manager_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                }
                ?>
            </optgroup>
        </select>
        <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>
    </form><?php
        }
        else {
            echo (isset($edition['manager']) ? $edition['manager'] : '');
    }
    ?>
</td>
    <?php
    }
    
    // Комментарий
    if($hasComment) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
    if($is_admin) {
        ?>
    <form method="post">
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <div class="input-group">
            <textarea rows="5" cols="30" wrap="hard" id="comment" name="comment" class="editable"><?=(isset($edition['comment']) ? htmlentities($edition['comment']) : '') ?></textarea>
            <div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><span class="font-awesome">&#xf0c7;</span></button></div>
        </div>
    </form>
        <?php
    }
    else {
        echo (isset($edition['comment']) ? $edition['comment'] : '');
    }
    ?>
</td>
    <?php
    }
    
    // Копирование тиража
    if($is_admin):
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <button class='btn btn-outline-dark btn-sm clipboard_copy' data='<?=$edition['id'] ?>' title='Копировать тираж' data-toggle='tooltip' onclick="javascript: CopyEdition(<?=$edition['id'] ?>, $(this));"><i class='fas fa-copy'></i><div class='alert alert-info clipboard_alert'>Скопировано</div></button>
</td>
    <?php
    endif;
    
    // Сдвиг нескольких тиражей
    if($is_admin):
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <button class="btn btn-outline-dark btn-sm show_move_form" title="Сдвинуть несколько тиражей" data-toggle='tooltip' data-date='<?=$date ?>' data-shift='<?=$shift ?>' data-position='<?=$position ?>' data-machine_id='<?=$machine_id ?>' data-workshift_id='<?=$workshift_id ?>'><i class="fas fa-table"></i></button>
</td>
    <?php
    endif;
    
    // Удаление тиража
    if($is_admin):
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <form method='post'>
        <input type="hidden" id="scroll" name="scroll" />
        <input type='hidden' id='id' name='id' value='<?=$edition['id'] ?>' />
        <button type='submit' id='delete_edition_submit' name='delete_edition_submit' class='btn btn-outline-dark btn-sm confirmable' title='Удалить тираж' data-toggle="tooltip"><i class='fas fa-trash-alt'></i></button>
    </form>
</td>
    <?php
    endif;
?>