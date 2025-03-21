<tr data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-id="<?=$this->edition['calculation_id'] ?>" data-position="<?=$this->edition['position'] ?>">
    <?php if($this->plan_shift->shift == 'day' && $this->edition_key == 0): ?>
    <td class="border-right" rowspan="<?=$this->plan_shift->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->plan_shift->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->plan_shift->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php if($this->plan_shift->timetable->editable && !$this->plan_shift->includes_continuation): ?>
        <a href="javascript: void(0);" onclick="javascript: MoveUp(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
            <img src="../images/icons/up_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
        </a>
        <?php endif; ?>
        <div style="display: block; white-space: nowrap;">
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><div class="font-italic mb-2" style="display: block;"><?= DisplayNumber($this->plan_shift->shift_worktime, 2) ?> ч.</div>
        </div>
        <?php if($this->plan_shift->timetable->editable && !$this->plan_shift->includes_continuation): ?>
        <a href="javascript: void(0);" onclick="javascript: MoveDown(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
            <img src="../images/icons/down_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
        </a>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> border-right text-nowrap" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php
        $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
        
        if($this->plan_shift->timetable->editable):
        ?>
        <select onchange="javascript: ChangeEmployee1($(this));" class="form-control" style="min-width: 100px;" data-work-id="<?=$this->plan_shift->timetable->work_id ?>" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->plan_shift->timetable->dateTo->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            foreach($this->plan_shift->timetable->employees as $emp_key => $employee):
                $selected = '';
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts1) && $emp_key == $this->plan_shift->timetable->workshifts1[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == WORK_PLAN_ROLES[$this->plan_shift->timetable->work_id] && ($employee['active'] == 1 || $emp_key == $this->plan_shift->timetable->workshifts1[$key])):
            ?>
            <option value="<?=$emp_key ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php
        elseif(array_key_exists($key, $this->plan_shift->timetable->workshifts1)):
            $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts1[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        endif;
        
        if($this->plan_shift->timetable->work_id == WORK_PRINTING && ($this->plan_shift->timetable->machine_id == PRINTER_COMIFLEX || $this->plan_shift->timetable->machine_id == PRINTER_SOMA_OPTIMA)):
        if($this->plan_shift->timetable->editable):
        ?>
        <select onchange="javascript: ChangeEmployee2($(this));" class="form-control mt-2" style="min-width: 100px;" data-work-id="<?=$this->plan_shift->timetable->work_id ?>" data-machine-id="<?=$this->plan_shift->timetable->machine_id ?>" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-from="<?=$this->plan_shift->timetable->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->plan_shift->timetable->dateTo->format('Y-m-d') ?>">
            <option value="">...</option>
            <?php
            $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
            foreach($this->plan_shift->timetable->employees as $emp_key => $employee):
                $selected = '';
            if(array_key_exists($key, $this->plan_shift->timetable->workshifts2) && $emp_key == $this->plan_shift->timetable->workshifts2[$key]) {
                $selected = " selected='selected'";
            }
            if($employee['role_id'] == PLAN_ROLE_ASSISTANT && ($employee['active'] == 1 || $emp_key == $this->plan_shift->timetable->workshifts2[$key])):
            ?>
            <option value="<?=$emp_key ?>"<?=$selected ?>><?=$employee['last_name'].' '.$employee['first_name'] ?></option>
            <?php
            endif;
            endforeach;
            ?>
        </select>
        <?php
        elseif(array_key_exists($key, $this->plan_shift->timetable->workshifts2)):
            echo '<br />';
            $employee = $this->plan_shift->timetable->employees[$this->plan_shift->timetable->workshifts2[$key]];
            echo $employee['last_name'].' '.$employee['first_name'];
        endif;
        endif;
        ?>
    </td>
    <?php endif; ?>
    <?php
    $drop = "";
    
    if($this->plan_shift->timetable->editable) {
        $drop = " ondrop='DropTimetable(event);' ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'";
        
        if($this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION) {
            $drop = "";
        }
    }
    
    if($this->plan_shift->timetable->editable):
    ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?php if($this->edition['type'] == PLAN_TYPE_EDITION && !$this->edition['has_continuation'] && !in_array($this->edition['status_id'], ORDER_STATUSES_IN_CUT)): ?>
        <div draggable="true" ondragstart="DragTimetableEdition(event);" data-id="<?=$this->edition['calculation_id'] ?>" data-lamination="<?=$this->edition['lamination'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
        <?php if($this->edition['type'] == PLAN_TYPE_PART && !$this->edition['has_continuation'] && !in_array($this->edition['status_id'], ORDER_STATUSES_IN_CUT)): ?>
        <div draggable="true" ondragstart="DragTimetablePart(event);" data-id="<?=$this->edition['id'] ?>" data-lamination="<?=$this->edition['lamination'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT): ?>
        <div draggable="true" ondragstart="DragTimetableEvent(event);" data-id="<?=$this->edition['id'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?php
        if(!empty($this->edition['customer_id']) && !empty($this->edition['num_for_customer'])) {
            echo $this->edition['customer_id'].'-'.$this->edition['num_for_customer'];
        }
        ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT): ?>
        <?= $this->edition['calculation'] ?>
        <?php else: ?>
        <div style="font-weight: bold; display: inline;"<?=$drop ?>><?= $this->edition['calculation'] ?></div><br /><?= $this->edition['customer'] ?>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap storekeeper_hidden"<?=$drop ?>>
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT): ?>
        <div class="d-flex justify-content-between">
            <div>
                <?php if($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING): ?>
                <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_pure_1']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
                <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_pure_2']), 0) ?></div>
                <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
                <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_pure_3']), 0) ?></div>
                <?php endif; ?>
                <?= $this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION ? ' '.WORK_CONTINUATIONS[$this->plan_shift->timetable->work_id] : '' ?>
            </div>
            <div>
                <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && $this->edition['type'] == PLAN_TYPE_EDITION && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && $this->edition['type'] == PLAN_TYPE_PART && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_EDITION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php else: ?>
                    <i class="fas fa-chevron-down"></i>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_PART && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemovePartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php else: ?>
                    <i class="fas fa-chevron-down"></i>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['worktime'] > 12 && $this->edition['type'] == PLAN_TYPE_CONTINUATION && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['worktime'] > 12 && $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php else: ?>
                    <i class="fas fa-chevron-down"></i>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_PART_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <?php if($this->plan_shift->timetable->editable): ?>
                    <label class="btn btn-light btn-edition-continue active">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <?php else: ?>
                    <i class="fas fa-chevron-down"></i>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </td>
    <?php if(IsInRole(ROLE_NAMES[ROLE_LAM_HEAD]) && $this->plan_shift->timetable->work_id == WORK_LAMINATION): ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>>
        <?php
        if($this->edition['type'] != PLAN_TYPE_EVENT):
        $films_strings = GetFilmsString($this->edition['lamination'], $this->edition['film_name'], $this->edition['thickness'], $this->edition['individual_film_name'], $this->edition['individual_thickness'], $this->edition['width_1'], 
                $this->edition['lamination1_film_name'], $this->edition['lamination1_thickness'], $this->edition['lamination1_individual_film_name'], $this->edition['lamination1_individual_thickness'], $this->edition['width_2'], 
                $this->edition['lamination2_film_name'], $this->edition['lamination2_thickness'], $this->edition['lamination2_individual_film_name'], $this->edition['lamination2_individual_thickness'], $this->edition['width_3']);
        ?>
        <span class="text-nowrap"><?=$films_strings[0] ?></span> <span class="text-nowrap"><?=$films_strings[1] ?></span> <span class="text-nowrap"><?=$films_strings[2] ?></span>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline"<?=$drop ?>><?=$this->edition['lamination_roller_width'] ?></td>
    <?php endif; ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden"<?=$drop ?>>
        <?= $this->edition['samples_count'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden colorist_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : rtrim(rtrim(DisplayNumber(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline not_colorist_hidden"<?=$drop ?>>
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
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Cyan - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_MAGENDA:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_MAGENDA];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Magenda - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_YELLOW:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_YELLOW];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Yellow - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case CMYK_KONTUR:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[CMYK_KONTUR];
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Kontur - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                        }
                        break;
                    case INK_PANTON:
                        $ink_expense = $this->plan_shift->timetable->ink_expenses[INK_PANTON];
                        $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                        $ink_class = " ".$this->plan_shift->shift;
                        $ink_style = "";
                        $color = GetColorByPanton($this->edition['color_'.$i]);
                        if($color) {
                            $ink_class = '';
                            $ink_style = " style='color: $color;'";
                        }
                        array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square$ink_class'$ink_style></i> P".$this->edition['color_'.$i]." - ".DisplayNumber($color_weight, 2)." кг</span>");
                        break;
                    case INK_WHITE:
                        $ink_expense = $this->plan_shift->timetable->ink_expenses[INK_WHITE];
                        $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                        array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Белая - ".DisplayNumber($color_weight, 2)." кг</span>");
                        break;
                    case INK_LACQUER:
                        switch($this->edition['lacquer_'.$i]) {
                            case LACQUER_GLOSSY:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[LACQUER_GLOSSY];
                                if($this->edition['work_type_id'] == WORK_TYPE_SELF_ADHESIVE) {
                                    $ink_expense = $this->plan_shift->timetable->ink_expenses[WORK_TYPE_SELF_ADHESIVE];
                                }
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Лак глянцевый - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                            case LACQUER_MATTE:
                                $ink_expense = $this->plan_shift->timetable->ink_expenses[LACQUER_MATTE];
                                if($this->edition['work_type_id'] == WORK_TYPE_SELF_ADHESIVE) {
                                    $ink_expense = $this->plan_shift->timetable->ink_expenses[WORK_TYPE_SELF_ADHESIVE];
                                }
                                $color_weight = $print_area * $ink_expense * $this->edition['percent_'.$i] / 1000 / 100;
                                array_push($color_lines, "<span class='text-nowrap'><i class='fas fa-square ".$this->plan_shift->shift."'></i> Лак матовый - ".DisplayNumber($color_weight, 2)." кг</span>");
                                break;
                        }
                        break;
                }
            }
        }
        
        echo implode('<br />', $color_lines);
        ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline storekeeper_hidden colorist_hidden"<?=$drop ?>>
        <?= DisplayNumber(floatval($this->edition['worktime']), 2) ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> not_storekeeper_hidden">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_dirty_1']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_dirty_2']), 0) ?></div>
        <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['length_dirty_3']), 0) ?></div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> not_storekeeper_hidden">
        <?php
        if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)) {
            echo empty($this->edition['requirement1']) ? '' : "<div class='font-italic'>".$this->edition['requirement1']."</div>";
            
            $film_id = $this->edition['film_id'];
            $film_name = $this->edition['film_name'];
            $thickness = $this->edition['thickness'];
            $width = intval($this->edition['width_1']);
            $filter = "<a href='".APPLICATION."/roll/?film_id=$film_id&thickness=$thickness&width_from=$width&width_to=$width' target='_blank' title='Склад'><i class='fas fa-filter'></i></a>";
        
            if(empty($film_name)) {
                $film_name = $this->edition['individual_film_name'];
                $thickness = $this->edition['individual_thickness'];
                $filter = "";
            }
            
            echo $film_name."&nbsp;&nbsp;&nbsp;".$thickness."&nbsp;&nbsp;&nbsp;".$filter;
        }
        elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1) {
            if($this->edition['work_type_id'] == WORK_TYPE_NOPRINT) {
                echo empty($this->edition['requirement1']) ? '' : "<div class='font-italic'>".$this->edition['requirement1']."</div>";
            
                $film_id = $this->edition['film_id'];
                $film_name = $this->edition['film_name'];
                $thickness = $this->edition['thickness'];
                $width = intval($this->edition['width_1']);
                $filter = "<a href='".APPLICATION."/roll/?film_id=$film_id&thickness=$thickness&width_from=$width&width_to=$width' target='_blank' title='Склад'><i class='fas fa-filter'></i></a>";
        
                if(empty($film_name)) {
                    $film_name = $this->edition['individual_film_name'];
                    $thickness = $this->edition['individual_thickness'];
                    $filter = "";
                }
            
                echo $film_name."&nbsp;&nbsp;&nbsp;".$thickness."&nbsp;&nbsp;&nbsp;".$filter."<br />+<br />";
            }
            
            echo empty($this->edition['requirement2']) ? '' : "<div class='font-italic'>".$this->edition['requirement2']."</div>";
            
            $lamination1_film_id = $this->edition['lamination1_film_id'];
            $lamination1_film_name = $this->edition['lamination1_film_name'];
            $lamination1_thickness = $this->edition['lamination1_thickness'];
            $width = intval($this->edition['width_2']);
            $filter = "<a href='".APPLICATION."/roll/?film_id=$lamination1_film_id&thickness=$lamination1_thickness&width_from=$width&width_to=$width' target='_blank' title='Склад'><i class='fas fa-filter'></i></a>";
        
            if(empty($lamination1_film_name)) {
                $lamination1_film_name = $this->edition['lamination1_individual_film_name'];
                $lamination1_thickness = $this->edition['lamination1_individual_thickness'];
                $filter = "";
            }
            
            echo $lamination1_film_name."&nbsp;&nbsp;&nbsp;".$lamination1_thickness."&nbsp;&nbsp;&nbsp;".$filter;
        }
        elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2) {
            if($this->edition['work_type_id'] == WORK_TYPE_NOPRINT) {
                echo "1 прогон<br />+<br />";
            }
            
            echo empty($this->edition['requirement3']) ? '' : "<div class='font-italic'>".$this->edition['requirement3']."</div>";
            
            $lamination2_film_id = $this->edition['lamination2_film_id'];
            $lamination2_film_name = $this->edition['lamination2_film_name'];
            $lamination2_thickness = $this->edition['lamination2_thickness'];
            $width = intval($this->edition['width_3']);
            $filter = "<a href='".APPLICATION."/roll/?film_id=$lamination2_film_id&thickness=$lamination2_thickness&width_from=$width&width_to=$width' target='_blank' title='Склад'><i class='fas fa-filter'></i></a>";
        
            if(empty($lamination2_film_name)) {
                $lamination2_film_name = $this->edition['lamination2_individual_film_name'];
                $lamination2_thickness = $this->edition['lamination2_individual_thickness'];
                $filter = "";
            }
            
            echo $lamination2_film_name."&nbsp;&nbsp;&nbsp;".$lamination2_thickness.(empty($filter) ? "" : "&nbsp;&nbsp;&nbsp;".$filter);
        }
        ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> not_storekeeper_hidden cutting_hidden">
        <?php if($this->edition['type'] != PLAN_TYPE_EVENT && ($this->plan_shift->timetable->work_id == WORK_PRINTING || $this->plan_shift->timetable->work_id == WORK_CUTTING)): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_1']), 0) ?></div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && $this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 1): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_2']), 0) ?></div>
        <?php elseif($this->plan_shift->timetable->work_id == WORK_LAMINATION && $this->edition['lamination'] == 2): ?>
        <div class='text-nowrap'><?= DisplayNumber(floatval($this->edition['width_3']), 0) ?></div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?= ($this->edition['type'] == PLAN_TYPE_EVENT || $this->edition['has_continuation']) ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?php
        if(!empty($this->edition['status_id']) && !$this->edition['has_continuation']) {
            $this->ShowOrderStatus($this->edition['status_id'], $this->edition['length_cut'], $this->edition['weight_cut'], $this->edition['quantity_sum'], $this->edition['quantity'], $this->edition['unit'], $this->edition['raport'], $this->edition['length'], $this->edition['gap_raport'], $this->edition['cut_remove_cause']);
        }
        ?>
    </td>
    <?php
    $comment_invisible_class = "";
    if($this->plan_shift->timetable->editable) {
        $comment_invisible_class = " comment_invisible";
    }
    ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline comment_cell<?=$comment_invisible_class ?> colorist_hidden"<?=$drop ?>>
        <div class="d-flex justify-content-start">
            <?php if($this->plan_shift->timetable->editable): ?>
            <div class="pr-2 comment_pen">
                <a href="javascript: void(0);" onclick="EditComment(event);">
                    <image src="../images/icons/edit1.svg" title="Редактировать" />
                </a>
            </div>
            <?php endif; ?>
            <div class="comment_text"><?=$this->edition['comment'] ?></div>
        </div>
        <div class="d-none comment_input">
            <input type="text" 
                   class="form-control comment_cell_<?=$this->edition['type'] ?>" 
                   value="<?=$this->edition['comment'] ?>" 
                   onkeydown="if(event.key == 'Enter') { SaveComment(event, <?=$this->edition['type'] ?>, <?=$this->edition['id'] ?>); }" 
                   onfocusout="SaveComment(event, <?=$this->edition['type'] ?>, <?=$this->edition['id'] ?>);" />
        </div>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-right" style="position:relative;"<?=$drop ?>>
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT && $this->plan_shift->timetable->editable): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg"<?=$drop ?> /></a>
        <div class="timetable_menu text-left">
            <div class="command">
                <button type="button" class="btn btn-link h-25" style="font-size: 14px;" onclick="javascript: event.preventDefault(); DeleteEvent(<?=$this->edition['calculation_id'] ?>);"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/trash2.svg" /></div>Удалить</button>
            </div>
        </div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && !$this->edition['has_continuation'] && ($this->plan_shift->timetable->editable || IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_STOREKEEPER])))): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"<?=$drop ?>><img src="../images/icons/vertical-dots1.svg" /></a>
        <div class="timetable_menu text-left">
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/print_tm.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Распечатать тех. карту</div></a></div>
            <?php if(in_array($this->edition['status_id'], ORDER_STATUSES_IN_CUT)): ?>
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/cut.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Результаты</div></a></div>
            <?php endif; ?>
        </div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && !$this->edition['has_continuation'] && IsInRole(ROLE_NAMES[ROLE_PACKER])): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"<?=$drop ?>><img src="../images/icons/vertical-dots1.svg" /></a>
        <div class="timetable_menu text-left">
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/print_tm.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Распечатать тех. карту</div></a></div>
            <?php if(in_array($this->edition['status_id'], ORDER_STATUSES_IN_CUT)): ?>
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../pack/details.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Результаты</div></a></div>
            <?php endif; ?>
        </div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && !$this->edition['has_continuation'] && (IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR])) || (IsInRole(ROLE_NAMES[ROLE_MANAGER]) && $this->edition['manager_id'] == GetUserId()))): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"<?=$drop ?>><img src="../images/icons/vertical-dots1.svg" /></a>
        <div class="timetable_menu text-left">
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Тех. карта</div></a></div>
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/print_tm.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Распечатать тех. карту</div></a></div>
            <?php if(in_array($this->edition['status_id'], ORDER_STATUSES_IN_CUT)): ?>
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/cut.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Результаты</div></a></div>
            <?php endif; ?>
        </div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && !$this->edition['has_continuation'] && IsInRole(ROLE_NAMES[ROLE_COLORIST])): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"<?=$drop ?>><img src="../images/icons/vertical-dots1.svg" /></a>
        <div class="timetable_menu text-left">
            <div><a class="btn btn-link h-25 w-100 text-left" style="font-size: 14px;" href="../calculation/print_tm.php?id=<?=$this->edition['calculation_id'] ?>"><div class="command">Распечатать тех. карту</div></a></div>
        </div>
        <?php endif; ?>
    </td>
</tr>