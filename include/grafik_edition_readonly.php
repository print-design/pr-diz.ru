<tr>
    <?php if($this->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <?php if($this->grafik->user1Name): ?><td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?= array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : '' ?></td><?php endif; ?>
    <?php if($this->grafik->user2Name): ?><td class="<?=$top.' '.$this->shift ?>" rowspan="<?=$this->shift_editions_count ?>"><?= array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : '' ?></td><?php endif; ?>
    <?php endif; ?>
    <?php if($this->grafik->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['organization'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['edition'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"><?= empty($this->edition['status']) ? $this->edition['length'] : $this->edition['status'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['roller'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['lamination'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['coloring'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['manager'] ?></td><?php endif; ?>
    <?php if($this->grafik->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"><?=$this->edition['comment'] ?></td><?php endif; ?>
</tr>