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
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><div class="font-italic" style="display: block;"><?= DisplayNumber($this->plan_shift->shift_worktime, 2) ?> ч.</div>
        </div>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> border-right text-nowrap" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php
        $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
        if(array_key_exists($key, $this->plan_shift->timetable->workshifts1)) {
            $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts1[$key]];
            echo "<span class='text-nowrap'>".$employee['last_name'].' '.$employee['first_name']."</span>";
        }
        
        if($this->plan_shift->timetable->work_id == WORK_PRINTING && ($this->plan_shift->timetable->machine_id == PRINTER_COMIFLEX || $this->plan_shift->timetable->machine_id == PRINTER_SOMA_OPTIMA)) {
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts2)) {
                echo '<br />';
                $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts2[$key]];
                echo "<span class='text-nowrap'>".$employee['last_name'].' '.$employee['first_name']."</span>";
            }
        }
        ?>
    </td>
    <?php endif; ?>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> text-nowrap">
        <?php
        if(!empty($this->edition['customer_id']) && !empty($this->edition['num_for_customer'])) {
            echo $this->edition['customer_id'].'-'.$this->edition['num_for_customer'];
        }
        ?>
    </td>
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
                <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_pure_1']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
                <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_pure_2']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
                <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_pure_3']), 0) ?></div>
                <?php endif; ?>
                <?= $this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION ? ' '.WORK_CONTINUATIONS[$this->plan_shift->timetable->work_id] : '' ?>
            </div>
            <div>
                <?php if($this->edition['has_continuation']): ?>
                <i class="fas fa-chevron-down"></i>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_lam_head_hidden">
        <?php
        if($this->edition['type'] != PLAN_TYPE_EVENT):
        $films_strings = GetFilmsString($this->edition['lamination'], $this->edition['film_name'], $this->edition['thickness'], $this->edition['individual_film_name'], $this->edition['individual_thickness'], $this->edition['width_1'], 
                $this->edition['lamination1_film_name'], $this->edition['lamination1_thickness'], $this->edition['lamination1_individual_film_name'], $this->edition['lamination1_individual_thickness'], $this->edition['width_2'], 
                $this->edition['lamination2_film_name'], $this->edition['lamination2_thickness'], $this->edition['lamination2_individual_film_name'], $this->edition['lamination2_individual_thickness'], $this->edition['width_3']);
        ?>
        <span class="text-nowrap"><?=$films_strings[0] ?></span> <span class="text-nowrap"><?=$films_strings[1] ?></span> <span class="text-nowrap"><?=$films_strings[2] ?></span>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_lam_head_hidden"><?=$this->edition['lamination_roller_width'] ?></td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> text-nowrap cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden">
        <?= $this->edition['samples_count'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden storekeeper_hidden colorist_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : rtrim(rtrim(DisplayNumber(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> cutting_hidden lamination_hidden storekeeper_hidden">
        <?=$this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_colorist_hidden">
        <?php
        $print_area = $this->edition['length_dirty_1_total'] * ($this->edition['stream_width'] * $this->edition['streams_number'] + 10) / 1000;
        $color_lines = array();
        
        if($this->edition['type'] != PLAN_TYPE_EVENT && !$this->edition['has_continuation']) {
            for($i = 1; $i <= 8; $i++) {
                switch($this->edition['ink_'.$i]) {
                    case INK_CMYK:
                        switch($this->edition['cmyk_'.$i]) {
                            case CMYK_CYAN:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_CYAN];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Cyan - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_MAGENDA:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_MAGENDA];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Magenda - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_YELLOW:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_YELLOW];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Yellow - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_KONTUR:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_KONTUR];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Kontur - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                        }
                        break;
                    case INK_PANTON:
                        $ink_expense = $this->plan_shift->timetable->ink_expenses[INK_PANTON];
                        $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                        array_push($color_lines, "<span class='text-nowrap'>P".$this->edition['color_'.$i]." - ".DisplayNumber($color_weight, 2)." кг</span>");
                        break;
                    case INK_WHITE:
                        $ink_expense = $this->plan_shift->timetable->ink_expenses[INK_WHITE];
                        $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                        array_push($color_lines, "<span class='text-nowrap'>Белая - ".DisplayNumber($color_weight, 2)." кг</span>");
                        break;
                    case INK_LACQUER:
                        switch($this->edition['lacquer_'.$i]) {
                            case LACQUER_GLOSSY:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[LACQUER_GLOSSY];
                                if($this->edition['work_type_id'] == WORK_TYPE_SELF_ADHESIVE) {
                                    $ink_expense = $this->plan_shift->timetable->ink_expenses[WORK_TYPE_SELF_ADHESIVE];
                                }
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Лак глянцевый - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case LACQUER_MATTE:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[LACQUER_MATTE];
                                if($this->edition['work_type_id'] == WORK_TYPE_SELF_ADHESIVE) {
                                    $ink_expense = $this->plan_shift->timetable->ink_expenses[WORK_TYPE_SELF_ADHESIVE];
                                }
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'>Лак матовый - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                        }
                        break;
                }
            }
        }
        
        echo implode('<br />', $color_lines);
        ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> storekeeper_hidden colorist_hidden">
        <?= DisplayNumber(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_storekeeper_hidden">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)): ?>
        <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_dirty_1']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
        <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_dirty_2']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
        <div class="text-nowrap"><?= DisplayNumber(floatval($this->edition['length_dirty_3']), 0) ?></div>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> not_storekeeper_hidden">
        <?php
        if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)) {
            echo empty($this->edition['requirement1']) ? '' : "<div class='font-italic' style='width: 300px;'>".$this->edition['requirement1']."</div>";
            
            $film_name = $this->edition['film_name'];
            $thickness = $this->edition['thickness'];
        
            if(empty($film_name)) {
                $film_name = $this->edition['individual_film_name'];
                $thickness = $this->edition['individual_thickness'];
            }
            
            echo $film_name."&nbsp;&nbsp;&nbsp;".$thickness;
        }
        elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1) {
            if($this->edition['work_type_id'] == WORK_TYPE_NOPRINT) {
                echo empty($this->edition['requirement1']) ? '' : "<div class='font-italic' style='width: 300px;'>".$this->edition['requirement1']."</div>";
            
                $film_name = $this->edition['film_name'];
                $thickness = $this->edition['thickness'];
        
                if(empty($film_name)) {
                    $film_name = $this->edition['individual_film_name'];
                    $thickness = $this->edition['individual_thickness'];
                }
            
                echo $film_name."&nbsp;&nbsp;&nbsp;".$thickness."<br />+<br />";
            }
            
            echo empty($this->edition['requirement2']) ? '' : "<div class='font-italic' style='width: 300px;'>".$this->edition['requirement2']."</div>";
            
            $lamination1_film_name = $this->edition['lamination1_film_name'];
            $lamination1_thickness = $this->edition['lamination1_thickness'];
        
            if(empty($lamination1_film_name)) {
                $lamination1_film_name = $this->edition['lamination1_individual_film_name'];
                $lamination1_thickness = $this->edition['lamination1_individual_thickness'];
            }
            
            echo $lamination1_film_name."&nbsp;&nbsp;&nbsp;".$lamination1_thickness;
        }
        elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2) {
            if($this->edition['work_type_id'] == WORK_TYPE_NOPRINT) {
                echo "1 прогон<br />+<br />";
            }
            
            echo empty($this->edition['requirement3']) ? '' : "<div class='font-italic' style='width: 300px;'>".$this->edition['requirement3']."</div>";
            
            $lamination2_film_name = $this->edition['lamination2_film_name'];
            $lamination2_thickness = $this->edition['lamination2_thickness'];
        
            if(empty($lamination2_film_name)) {
                $lamination2_film_name = $this->edition['lamination2_individual_film_name'];
                $lamination2_thickness = $this->edition['lamination2_individual_thickness'];
            }
            
            echo $lamination2_film_name."&nbsp;&nbsp;&nbsp;".$lamination2_thickness;
        }
        ?>
    </td>
    <td class="<?=$top." ".$this->plan_shift->shift ?> not_storekeeper_hidden cutting_hidden">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_1']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_2']), 0) ?></div>
        <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_3']), 0) ?></div>
        <?php endif; ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> text-nowrap">
        <?= ($this->edition['type'] == PLAN_TYPE_EVENT || $this->edition['has_continuation']) ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?>">
        <?php
        if(!empty($this->edition['status_id']) && !$this->edition['has_continuation']) {
            $this->ShowOrderStatusPrint($this->edition['status_id'], $this->edition['length_cut'], $this->edition['weight_cut'], $this->edition['quantity_sum'], $this->edition['quantity'], $this->edition['unit'], $this->edition['raport'], $this->edition['length'], $this->edition['gap_raport'], $this->edition['cut_remove_cause']);
        }
        ?>
    </td>
    <td class="<?=$top.' '.$this->plan_shift->shift ?> colorist_hidden">
        <?=$this->edition['comment'] ?>
    </td>
</tr>