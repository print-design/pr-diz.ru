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
        <button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm mb-1 clipboard_paste' data-toggle='tooltip' title='Вставить тираж выше'$disabled><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-up'></i></button>
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
        <button id='paste_edition_submit' name='paste_edition_submit' class='btn btn-outline-dark btn-sm clipboard_paste' data-toggle='tooltip' title='Вставить тираж ниже'$disabled><i class='fas fa-paste'></i><i class='fas fa-long-arrow-alt-down'></i></button>
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
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="number" min="0" pattern="\d*" id="length" name="length" value="'.(isset($edition['length']) ? $edition['length'] : '').'" class="editable" style="width:65px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                if(isset($edition['status']) && $edition['status'] != null) {
                    echo $edition['status'];
                }
                else if (isset ($edition['length'])) {
                    echo $edition['length'];
                }
            }
            echo "</td>";
        };
        
        // Статус
        if($is_admin) {
            if($hasStatus) {
                echo "<td class='$top $shift'>";
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='status_id' name='status_id' style='width:85px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($statuses as $value) {
                    $selected = '';
                    if(isset($edition['status_id']) && $edition['status_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
                echo "</td>";
            }
        };
        
        // Вал
        if($hasRoller) {
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='roller_id' name='roller_id'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($rollers as $value) {
                    $selected = '';
                    if(isset($edition['roller_id']) && $edition['roller_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['roller']) ? $edition['roller'] : '');
            }
            echo "</td>";
        };
        
        // Ламинация
        if($hasLamination) {
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='lamination_id' name='lamination_id' style='width:55px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($laminations as $value) {
                    $selected = '';
                    if(isset($edition['lamination_id']) && $edition['lamination_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['name']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['lamination']) ? $edition['lamination'] : '');
            }
            echo "</td>";
        }
        
        // Красочность
        if($hasColoring) {
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<input type="number" min="0" max="'.$coloring.'" pattern="\d*" id="coloring" name="coloring" value="'.(isset($edition['coloring']) ? $edition['coloring'] : '').'" class="editable" style="width:35px;" />';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['coloring']) ? $edition['coloring'] : '');
            }
            echo "</td>";
        }
        
        // Менеджер
        if($hasManager) {
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo "<form method='post'>";
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo "<select id='manager_id' name='manager_id' style='width:120px;'>";
                echo '<optgroup>';
                echo '<option value="">...</option>';
                foreach ($managers as $value) {
                    $selected = '';
                    if(isset($edition['manager_id']) && $edition['manager_id'] == $value['id']) $selected = " selected = 'selected'";
                    echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                }
                echo '</optgroup>';
                echo '</select>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['manager']) ? $edition['manager'] : '');
            }
            echo "</td>";
        }
        
        // Комментарий
        if($hasComment) {
            echo "<td class='$top $shift'>";
            if($is_admin) {
                echo '<form method="post">';
                echo '<input type="hidden" id="scroll" name="scroll" />';
                echo "<input type='hidden' id='id' name='id' value='".$edition['id']."' />";
                echo '<div class="input-group">';
                echo '<textarea rows="5" cols="30" wrap="hard" id="comment" name="comment" class="editable">'.(isset($edition['comment']) ? htmlentities($edition['comment']) : '').'</textarea>';
                echo '<div class="input-group-append d-none"><button type="submit" class="btn btn-outline-dark"><span class="font-awesome">&#xf0c7;</span></button></div>';
                echo '</div>';
                echo '</form>';
            }
            else {
                echo (isset($edition['comment']) ? $edition['comment'] : '');
            }
            echo "</td>";
        }
        
        // Копирование тиража
        if($is_admin):
        ?>
        <td class='<?=$top ?> <?=$shift ?>'>
        <button class='btn btn-outline-dark btn-sm clipboard_copy' data='<?=$edition['id'] ?>' title='Копировать тираж' data-toggle='tooltip'><i class='fas fa-copy'></i><div class='alert alert-info clipboard_alert'>Скопировано</div></button>
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