<tr>
    <?php if($this->cut_shift->shift == 'day' && $this->edition_key == 0): ?>
    <td class="border-right" rowspan="<?=$this->cut_shift->date_editions_count ?>">
        <?=$GLOBALS['weekday_names'][$this->cut_shift->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?=ltrim($this->cut_shift->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$this->cut_shift->shift ?>" rowspan="<?=$this->cut_shift->shift_editions_count ?>">
        <?=($this->cut_shift->shift == 'day' ? 'День' : 'Ночь') ?>
    </td>
    <td class="<?=$this->cut_shift->shift ?> border-right" rowspan="<?=$this->cut_shift->shift_editions_count ?>">
        <?php
        $key = $this->cut_shift->date->format('Y-m-d').'_'.$this->cut_shift->shift;
        if(array_key_exists($key, $this->cut_shift->timetable->workshifts)) {
            $employee = $this->cut_shift->timetable->employees[$this->cut_shift->timetable->workshifts[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        }
        ?>
    </td>
    <?php endif; ?>
    <td class="<?=$this->cut_shift->shift ?> text-nowrap">
        <?php
        if(!empty($this->edition['customer_id']) && !empty($this->edition['num_for_customer'])) {
            echo $this->edition['customer_id'].'-'.$this->edition['num_for_customer'];
        }
        ?>
    </td>
    <td class="<?=$this->cut_shift->shift ?>">
        <div style="font-weight: bold; display: inline;"><?=$this->edition['calculation'] ?></div><br /><?=$this->edition['customer'] ?>
    </td>
    <td class="<?=$this->cut_shift->shift ?> text-nowrap">
        <div class="d-flex justify-content-between">
            <div>
                <div class="text-nowrap">
                    <?= DisplayNumber(floatval($this->edition['length_pure_1']), 0) ?>
                </div>
                <?= $this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION ? ' '.WORK_CONTINUATIONS[WORK_CUTTING] : '' ?>
            </div>
            <div>
                <?php if($this->edition['has_continuation']): ?>
                <i class="fas fa-chevron-down"></i>
                <?php endif; ?>
            </div>
        </div>
    </td>
    <td class="<?=$this->cut_shift->shift ?>">
        <?= DisplayNumber(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$this->cut_shift->shift ?>">
        <?= $this->edition['manager'] ?>
    </td>
    <td class="<?=$this->cut_shift->shift ?> text-nowrap"><i class="fas fa-circle" style="color: <?=ORDER_STATUS_COLORS[$this->edition['status_id']] ?>;"></i>&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$this->edition['status_id']] ?></td>
    <td class="<?=$this->cut_shift->shift ?>">
        <button type="button" class="btn btn-outline-dark">Приступить</button>
    </td>
</tr>