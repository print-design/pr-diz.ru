<!-- Заказчик -->
<?php if($this->hasOrganization): ?>
<td class='<?=$top ?>'><?=(isset($edition['organization']) ? htmlentities($edition['organization']) : '') ?></td>
<?php endif; ?>
        
<!-- Наименование заказа -->
<?php if($this->hasEdition): ?>
<td class='<?=$top ?>'><?=(isset($edition['name']) ? htmlentities($edition['name']) : '') ?></td>
<?php endif; ?>
        
<!-- Метраж -->
<?php if($this->hasLength): ?>
<td class='<?=$top ?>'>
    <?php
    if(isset($edition['status']) && $edition['status'] != null) {
        echo $edition['status'];
    }
    else if (isset ($edition['length'])) {
        echo $edition['length'];
    }
    ?>
</td>
<?php endif; ?>
        
<!-- Вал -->
<?php if($this->hasRoller): ?>
<td class='<?=$top ?>'><?=(isset($edition['roller']) ? $edition['roller'] : '') ?></td>
<?php endif; ?>
        
<!-- Ламинация -->
<?php if($this->hasLamination): ?>
<td class='<?=$top ?>'><?=(isset($edition['lamination']) ? $edition['lamination'] : '') ?></td>
<?php endif; ?>
        
<!-- Красочность -->
<?php if($this->hasColoring): ?>
<td class='<?=$top ?>'><?=(isset($edition['coloring']) ? $edition['coloring'] : '') ?></td>
<?php endif; ?>
        
<!-- Менеджер -->
<?php if($this->hasManager): ?>
<td class='<?=$top ?>'><?=(isset($edition['manager']) ? $edition['manager'] : '') ?></td>
<?php endif; ?>
        
<!-- Комментарий -->
<?php if($this->hasComment): ?>
<td class='<?=$top ?>'><?=(isset($edition['comment']) ? $edition['comment'] : '') ?></td>
<?php endif; ?>