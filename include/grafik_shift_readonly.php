<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="2"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="2"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <?php if($this->grafik->user1Name): ?><td class="<?=$top.' '.$this->shift ?>"><?= array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : '' ?></td><?php endif; ?>
    <?php if($this->grafik->user2Name): ?><td class="<?=$top.' '.$this->shift ?>"><?= array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : '' ?></td><?php endif; ?>
    <?php if($this->grafik->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->grafik->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
</tr>