<tr data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" data-id="<?=$this->edition['calculation_id'] ?>" data-position="<?=$this->edition['position'] ?>">
    <?php if($this->plan_shift->shift == 'day' && $this->edition_key == 0): ?>
    <td class="border-right" rowspan="<?=$this->plan_shift->date_editions_count ?>">
        <?=$GLOBALS['weekdays'][$this->plan_shift->date->format('w')] ?>
        <div style="font-size: 18px; font-weight: bold;"><?= ltrim($this->plan_shift->date->format('d.m'), '0') ?></div>
    </td>
    <?php endif; ?>
    <?php if($this->edition_key == 0): ?>
    <td class="<?=$this->plan_shift->shift ?>" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php if(!$this->plan_shift->includes_continuation): ?>
        <div class="foredit">
            <a href="javascript: void(0);" onclick="javascript: MoveUp(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/up_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
        <div style="display: block; white-space: nowrap;">
            <?=($this->plan_shift->shift == 'day' ? 'День' : 'Ночь') ?><div class="font-italic" style="display: block;"><?= DisplayNumber($this->plan_shift->shift_worktime, 2) ?> ч.</div>
        </div>
        <?php if(!$this->plan_shift->includes_continuation): ?>
        <div class="foredit" style="margin-top: 6px;">
            <a href="javascript: void(0);" onclick="javascript: MoveDown(event);" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>">
                <img src="../images/icons/down_arrow.png" data-date="<?=$this->plan_shift->date->format('Y-m-d') ?>" data-shift="<?=$this->plan_shift->shift ?>" />
            </a>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> border-right" rowspan="<?=$this->plan_shift->shift_editions_count ?>">
        <?php
        $key = $this->plan_shift->timetable->work_id.'_'.$this->plan_shift->timetable->machine_id.'_'.$this->plan_shift->date->format('Y-m-d').'_'.$this->plan_shift->shift;
        
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))):
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
        
        if($this->plan_shift->timetable->work_id == WORK_PRINTING && $this->plan_shift->timetable->machine_id == PRINTER_COMIFLEX):
        if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))):
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
    
    if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))) {
        $drop = " ondrop='DropTimetable(event);' ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'";
        
        if($this->edition['type'] == PLAN_TYPE_CONTINUATION || $this->edition['type'] == PLAN_TYPE_PART_CONTINUATION) {
            $drop = "";
        }
    }
    ?>
    <td class="<?=$this->plan_shift->shift ?> showdropline fordrag"<?=$drop ?>>
        <?php if($this->edition['type'] == PLAN_TYPE_EDITION && !$this->edition['has_continuation'] && $this->edition['status_id'] != ORDER_STATUS_CUT_PRILADKA): ?>
        <div draggable="true" ondragstart="DragTimetableEdition(event);" data-id="<?=$this->edition['calculation_id'] ?>" data-lamination="<?=$this->edition['lamination'] ?>" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'>
            <img src="../images/icons/double-vertical-dots.svg" draggable="false" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);' />
        </div>
        <?php endif; ?>
        <?php if($this->edition['type'] == PLAN_TYPE_PART && !$this->edition['has_continuation'] && $this->edition['status_id'] != ORDER_STATUS_CUT_PRILADKA): ?>
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
                    <label class="btn btn-light btn-edition-continue foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->plan_shift->shift_worktime > 12 && $this->plan_shift->is_last && $this->edition['type'] == PLAN_TYPE_PART && !$this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_EDITION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <i class="fas fa-chevron-down notforedit"></i>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_PART && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemovePartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <i class="fas fa-chevron-down notforedit"></i>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_CONTINUATION && !$this->edition['has_continuation'] && $this->edition['worktime'] > 12): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_PART_CONTINUATION && !$this->edition['has_continuation'] && $this->edition['worktime'] > 12): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="AddChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <i class="fas fa-chevron-down notforedit"></i>
                </div>
                <?php endif; ?>
                <?php if($this->edition['type'] == PLAN_TYPE_PART_CONTINUATION && $this->edition['has_continuation']): ?>
                <div class="btn-group-toggle ml-1" data-toggle="buttons">
                    <label class="btn btn-light btn-edition-continue active foredit">
                        <input type="checkbox" style="height: 10px; width: 10px;" checked autocomplete="off" onchange="RemoveChildPartContinuation(<?=$this->edition['id'] ?>)"><i class="fas fa-chevron-down"></i>
                    </label>
                    <i class="fas fa-chevron-down notforedit"></i>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap cutting_hidden lamination_hidden storekeeper_hidden"<?=$drop ?>>
        <?= $this->edition['samples_count'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : rtrim(rtrim(DisplayNumber(floatval($this->edition['raport']), 3), "0"), ",") ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['laminations'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden"<?=$drop ?>>
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['ink_number'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline storekeeper_hidden"<?=$drop ?>>
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
        <div class="text-nowrap">
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
        </div>
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
        <?= $this->edition['type'] == PLAN_TYPE_EVENT ? "" : $this->edition['manager'] ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline text-nowrap"<?=$drop ?>>
        <?php
        if(!empty($this->edition['status_id'])):
        ?>
        <i class="fas fa-circle" style="color: <?=ORDER_STATUS_COLORS[$this->edition['status_id']] ?>;"></i>&nbsp;&nbsp;<?=ORDER_STATUS_NAMES[$this->edition['status_id']] ?>
        <?php endif; ?>
    </td>
    <td class="<?=$this->plan_shift->shift ?> showdropline comment_cell comment_invisible"<?=$drop ?>>
        <div class="d-flex justify-content-start">
            <div class="pr-2 comment_pen foredit">
                <a href="javascript: void(0);" onclick="EditComment(event);">
                    <image src="../images/icons/edit1.svg" title="Редактировать" />
                </a>
            </div>
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
        <?php if($this->edition['type'] == PLAN_TYPE_EVENT && IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))): ?>
        <a class="black timetable_menu_trigger" href="javascript: void(0);"><img src="../images/icons/vertical-dots1.svg"<?=$drop ?> /></a>
        <div class="timetable_menu text-left">
            <div class="command">
                <button type="button" class="btn btn-link h-25" style="font-size: 14px;" onclick="javascript: DeleteEvent(<?=$this->edition['calculation_id'] ?>);"><div style="display: inline; padding-right: 10px;"><img src="../images/icons/trash2.svg" /></div>Удалить</button>
            </div>
        </div>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && IsInRole(array(ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_STOREKEEPER]))): ?>
        <a href="../calculation/print_tm.php?id=<?=$this->edition['calculation_id'] ?>" target="_blank"<?=$drop ?>>
            <img src="../images/icons/vertical-dots1.svg"<?=$drop ?> />
        </a>
        <?php elseif($this->edition['type'] != PLAN_TYPE_EVENT && (IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR])) || (IsInRole(ROLE_NAMES[ROLE_MANAGER]) && $this->edition['manager_id'] == GetUserId()))): ?>
        <a href="../calculation/techmap.php?id=<?=$this->edition['calculation_id'] ?>"<?=$drop ?>>
            <img src="../images/icons/vertical-dots1.svg"<?=$drop ?> />
        </a>
        <?php endif; ?>
    </td>
</tr>