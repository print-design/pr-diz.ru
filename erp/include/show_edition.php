<?php
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
        if(isset($edition['status']) && $edition['status'] != null) {
            echo $edition['status'];
        }
        else if (isset ($edition['length'])) {
            echo $edition['length'];
        }
    ?>
</td>
    <?php
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
        echo (isset($edition['lamination']) ? $edition['lamination'] : '');
    ?>
</td>
    <?php
    }
    
    // Красочность
    if($hasColoring) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
        <?php
            echo (isset($edition['coloring']) ? $edition['coloring'] : '');
        ?>
</td>
    <?php
    }
    
    // Менеджер
    if($hasManager) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
            echo (isset($edition['manager']) ? $edition['manager'] : '');
    ?>
</td>
    <?php
    }
    
    // Комментарий
    if($hasComment) {
        ?>
<td class='<?=$top ?> <?=$shift ?>'>
    <?php
        echo (isset($edition['comment']) ? $edition['comment'] : '');
    ?>
</td>
    <?php
    }
?>