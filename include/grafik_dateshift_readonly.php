<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="2"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="2"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <?php if($this->grafik->user1Name): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->grafik->user2Name): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->grafik->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->grafik->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"><?php print_r($this->editions); ?></td><?php endif; ?>
</tr>