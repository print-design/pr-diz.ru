<tr>
    <?php if($this->shift == 'day'): ?>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>" style="padding-left: 7px; padding-right: 7px;"><?=$GLOBALS['weekdays'][$this->date->format('w')] ?></td>
    <td class="<?=$top ?>" rowspan="<?=$this->date_editions_count ?>" style="padding-left: 7px; padding-right: 7px;"><?=$this->date->format('d.m').".".$this->date->format('Y') ?></td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->shift ?>"><?=($this->shift == 'day' ? 'День' : 'Ночь') ?></td>
    <?php if($this->timetable->user1Name): ?><td class="<?=$top.' '.$this->shift ?>"><?= array_key_exists('u1_fio', $this->shift_data) ? $this->shift_data['u1_fio'] : '' ?></td><?php endif; ?>
    <?php if($this->timetable->user2Name): ?><td class="<?=$top.' '.$this->shift ?>"><?= array_key_exists('u2_fio', $this->shift_data) ? $this->shift_data['u2_fio'] : '' ?></td><?php endif; ?>
    <?php if($this->timetable->hasOrganization): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasEdition): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasMaterial): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasThickness): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasWidth): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasLength): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasRoller): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasLamination): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasColoring): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasManager): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
    <?php if($this->timetable->hasComment): ?><td class="<?=$top.' '.$this->shift ?>"></td><?php endif; ?>
</tr>