<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?><td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td><?php endif; ?>
    <?php if($this->grafik->user1Name): ?><td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"></td><?php endif; ?>
    <?php if($this->grafik->user2Name): ?><td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"></td><?php endif; ?>
    <?php if($this->grafik->hasOrganization): ?><td></td><?php endif; ?>
    <?php if($this->grafik->hasEdition): ?><td><?php echo $this->edition_key.'<br />'; print_r($this->edition); ?></td><?php endif; ?>
</tr>