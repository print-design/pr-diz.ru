<?php
require_once '../calculation/calculation.php';
?>
<tr>
    <?php if($this->plan_shift->shift == 'day' && $this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> border-right" rowspan="<?=$this->plan_shift->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->plan_shift->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->plan_shift->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <div style="display: block; white-space: nowrap;">
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><div class="font-italic" style="display: block;"><?= CalculationBase::Display($this->plan_shift->shift_worktime, 2) ?> ч.</div>
        </div>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php
        $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
        if(array_key_exists($key, $this->plan_shift->timetable->workshifts1)) {
            $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts1[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        }
        
        if($this->plan_shift->timetable->work_id == WORK_PRINTING && $this->plan_shift->timetable->machine_id == PRINTER_COMIFLEX) {
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts2)) {
                echo '<br />';
                $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts2[$key]];
                echo $employee['last_name'].' '.$employee['first_name'];
            }
        }
        ?>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> border-left">
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT): ?>
        <?=$this->edition['calculation'] ?>
        <?php else: ?>
        <div style="font-weight: bold; display: inline;"><?=$this->edition['calculation'] ?></div><br /><?=$this->edition['customer'] ?>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> text-nowrap">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT): ?>
        <div class="d-flex justify-content-between">
            <div>
                <?php if($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING): ?>
                <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_1']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
                <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_2']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
                <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_3']), 0) ?></div>
                <?php endif; ?>
                <?= $this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION ? ' Допечатка' : '' ?>
            </div>
            <div>
                <?php if($this->edition['has_continuation']): ?>
                <i class="fas fa-chevron-down"></i>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : rtrim(rtrim(CalculationBase::Display(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?=$this->edition['comment'] ?>
    </td>
</tr>