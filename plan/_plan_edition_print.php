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
    <td class="<?=$top.' '.$this->plan_shift->shift ?> border-right" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
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
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT): ?>
        <?=$this->edition['calculation'] ?>
        <?php else: ?>
        <div style="font-weight: bold; display: inline;"><?=$this->edition['calculation'] ?></div><br /><?=$this->edition['customer'] ?>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> text-nowrap storekeeper_hidden">
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
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden storekeeper_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : rtrim(rtrim(CalculationBase::Display(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden storekeeper_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden storekeeper_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> storekeeper_hidden">
        <?= CalculationBase::Display(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_storekeeper_hidden">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)): ?>
        <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_1']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
        <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_2']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
        <div class="text-nowrap"><?= CalculationBase::Display(floatval($this->edition['length_dirty_3']), 0) ?></div>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_storekeeper_hidden">
        <div class="text-nowrap">
        <?php
        if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)) {
            $film_name = $this->edition['film_name'];
            $thickness = $this->edition['thickness'];
        
            if(empty($film_name)) {
                $film_name = $this->edition['individual_film_name'];
                $thickness = $this->edition['individual_thickness'];
            }
            
            echo $film_name."&nbsp;&nbsp;&nbsp;".$thickness;
        }
        elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1) {
            $lamination1_film_name = $this->edition['lamination1_film_name'];
            $lamination1_thickness = $this->edition['lamination1_thickness'];
        
            if(empty($lamination1_film_name)) {
                $lamination1_film_name = $this->edition['lamination1_individual_film_name'];
                $lamination1_thickness = $this->edition['lamination1_individual_thickness'];
            }
            
            echo $lamination1_film_name."&nbsp;&nbsp;&nbsp;".$lamination1_thickness;
        }
        elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2) {
            $lamination2_film_name = $this->edition['lamination2_film_name'];
            $lamination2_thickness = $this->edition['lamination2_thickness'];
        
            if(empty($lamination2_film_name)) {
                $lamination2_film_name = $this->edition['lamination2_individual_film_name'];
                $lamination2_thickness = $this->edition['lamination2_individual_thickness'];
            }
            
            echo $lamination2_film_name."&nbsp;&nbsp;&nbsp;".$lamination2_thickness;
        }
        ?>
        </div>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?=$this->edition['comment'] ?>
    </td>
</tr>