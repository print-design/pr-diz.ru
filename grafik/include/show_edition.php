<?php
if($is_admin) {
    // Кнопки добавления тиража
    ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <button type='button' class='btn btn-outline-dark btn-sm' style='display: block;' data-workshift='<?=$workshift_id ?>' data-date='<?=$date ?>' data-shift='<?=$shift ?>' data-machine='<?=$machine_id ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$position ?>' data-direction='up' onclick='javascript: CreateEdition($(this));' data-toggle='tooltip' title='Добавить тираж выше'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-up'></i></button>
    <button type='button' class='btn btn-outline-dark btn-sm' style='display: block;' data-workshift='<?=$workshift_id ?>' data-date='<?=$date ?>' data-shift='<?=$shift ?>' data-machine='<?=$machine_id ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-position='<?=$position ?>' data-direction='down' onclick="javascript: CreateEdition($(this));" data-toggle='tooltip' title='Добавить тираж ниже'><i class='fas fa-plus'></i><i class='fas fa-long-arrow-alt-down'></i></button>
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
    <button type="button" class='btn btn-outline-dark btn-sm btn_clipboard_paste' style='display: block;' data-toggle='tooltip' data-machine='<?=$machine_id ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-date='<?=$date ?>' data-shift='<?=$shift ?>' data-workshift='<?=$workshift_id ?>' data-direction='up' data-position='<?=$position ?>' onclick="javascript: PasteEdition($(this))" title='Вставить тираж выше'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>
    <button type="button" class='btn btn-outline-dark btn-sm btn_clipboard_paste' style="display: block;" data-toggle='tooltip' data-machine='<?=$machine_id ?>' data-from='<?=$from ?>' data-to='<?=$to ?>' data-date='<?=$date ?>' data-shift='<?=$shift ?>' data-workshift='<?=$workshift_id ?>' data-direction='down' data-position='<?=$position ?>' onclick="javascript: PasteEdition($(this))" title='Вставить тираж ниже'<?=$disabled ?>><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>
</td>
    <?php
    }
    
    // Заказчик
    if($hasOrganization) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
        <?php if($is_admin): ?>
    <input type="text" value="<?=(isset($edition['organization']) ? htmlentities($edition['organization']) : '') ?>" onfocusout='javascript: EditOrganization($(this))' class="editable organizations" data-id='<?=$edition['id'] ?>' style="width:140px;" />
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
    <input type="text" value="<?=(isset($edition['edition']) ? htmlentities($edition['edition']) : '') ?>" onfocusout="javascript: EditEdition($(this))" class="editable editions" data-id='<?=$edition['id'] ?>' style="width:140px;" />
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
    <input type="number" min="0" pattern="\d*" value="<?=(isset($edition['length']) ? $edition['length'] : '') ?>" onfocusout="javascript: EditLength($(this))" data-id='<?=$edition['id'] ?>' class="editable" style="width:65px;" />
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
    <select data-id='<?=$edition['id'] ?>' onfocusout="javascript: EditStatus($(this))" style='width:85px;'>
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
    <select data-id='<?=$edition['id'] ?>' onfocusout="javascript: EditRoller($(this))">
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
    <select data-id='<?=$edition['id'] ?>' onfocusout="javascript: EditLamination($(this))" style='width:55px;'>
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
    <input type="number" min="0" max="<?=$coloring ?>" pattern="\d*" value="<?=(isset($edition['coloring']) ? $edition['coloring'] : '') ?>" data-id='<?=$edition['id'] ?>' onfocusout="EditColoring($(this))" class="editable" style="width:35px;" />
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
    <select data-id='<?=$edition['id'] ?>' onfocusout="javascript: EditManager($(this))" style='width:120px;'>
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
        <?php
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
    <textarea rows="5" cols="30" wrap="hard" data-id='<?=$edition['id'] ?>' onfocusout="EditComment($(this))" class="editable"><?=(isset($edition['comment']) ? htmlentities($edition['comment']) : '') ?></textarea>
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
    <button type='button' class='btn btn-outline-dark btn-sm' data-id="<?=$edition['id'] ?>" data-machine="<?=$machine_id ?>" data-from="<?=$from ?>" data-to="<?=$to ?>" onclick="javascript: if(confirm('Действительно удалить?')) { DeleteEdition($(this)) };" title='Удалить тираж' data-toggle="tooltip"><i class='fas fa-trash-alt'></i></button>
</td>
    <?php
    endif;
?>