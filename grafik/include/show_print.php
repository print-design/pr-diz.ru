<h1><?=$this->name ?></h1>
<table class="table table-bordered print">
    <tr>
        <th></th>
        <th>Дата</th>
        <th>Смена</th>
        <?php if($this->user1Name != ''): ?> <th><?=$this->user1Name ?></th> <?php endif; ?>
        <?php if($this->user2Name != ''): ?> <th><?=$this->user2Name ?></th> <?php endif; ?>
        <?php if($this->hasOrganization): ?> <th>Заказчик</th> <?php endif; ?>
        <?php if($this->hasEdition): ?> <th>Наименование</th> <?php endif; ?>
        <?php if($this->hasLength): ?> <th>Метраж</th> <?php endif; ?>
        <?php if($this->hasRoller): ?> <th>Вал</th> <?php endif; ?>
        <?php if($this->hasLamination): ?> <th>Ламинация</th> <?php endif; ?>
        <?php if($this->hasColoring): ?> <th>Кр-ть</th> <?php endif; ?>
        <?php if($this->hasManager): ?> <th>Менеджер</th> <?php endif; ?>
        <?php if($this->hasComment): ?> <th>Комментарий</th> <?php endif; ?>
    </tr>
    <?php foreach ($dateshifts as $dateshift): ?>
    <tr>
        <?php if($dateshift['shift'] == 'day'): ?>
        <td class='<?=$dateshift['top'] ?>' rowspan='<?=$dateshift['rowspan'] ?>'><?=$GLOBALS['weekdays'][$dateshift['date']->format('w')] ?></td>
        <td class='<?=$dateshift['top'] ?>' rowspan='<?=$dateshift['rowspan'] ?>'><?=$dateshift['date']->format("d.m.Y") ?></td>
        <?php endif; ?>
        <td class='<?=$dateshift['top'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>'><?=($dateshift['shift'] == 'day' ? 'День' : 'Ночь') ?></td>
        
        <!-- Работник №1 -->
        <?php if($this->user1Name != ''): ?>
        <td class='<?=$dateshift['top'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>' title='<?=$this->user1Name ?>'>
            <?php echo (isset($dateshift['row']['u1_fio']) ? $dateshift['row']['u1_fio'] : ''); ?>
        </td>
        <?php endif; ?>
            
        <!-- Работник №2 -->
        <?php if($this->user2Name != ''): ?>
        <td class='<?=$dateshift['top'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>' title='<?=$this->user2Name ?>'>
            <?php echo (isset($dateshift['row']['u2_fio']) ? $dateshift['row']['u2_fio'] : ''); ?>
        </td>
        <?php endif; ?>
        
        <!-- Смены -->
        <?php $edition = null; ?>
            
        <?php if(count($dateshift['editions']) == 0): ?>
        <?php if($this->hasOrganization): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasEdition): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasLength): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasRoller): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasLamination): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasColoring): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasManager): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php if($this->hasComment): ?> <td class='<?=$dateshift['top'] ?>'></td> <?php endif; ?>
        <?php
        else:
            $edition = array_shift($dateshift['editions']);
            $this->PrintEdition($edition, $dateshift['top']);
        endif;
        ?>
    </tr>
    
    <!-- Дополнительные смены -->
    <?php
    $edition = array_shift($dateshift['editions']);
            
    while ($edition != null) {
        echo '<tr>';
        $this->PrintEdition($edition, 'nottop');
        echo '</tr>';
        $edition = array_shift($dateshift['editions']);
    }
    ?>
    <?php endforeach; ?>
</table>