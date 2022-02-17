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
    <?php if($this->machine->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->machine->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
</tr>