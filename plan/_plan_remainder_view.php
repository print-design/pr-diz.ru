<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="" data-position="">
    <?php if($this->timetable->editable): ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php endif; ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline storekeeper_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php if(IsInRole(ROLE_NAMES[ROLE_LAM_HEAD]) && $this->timetable->work_id == WORK_LAMINATION): ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover="DragOverTimetable(event);" ondragleave="DragLeaveTimetable(event);"></td>
    <?php endif; ?>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden planner_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline cutting_hidden lamination_hidden storekeeper_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline not_colorist_hidden"></td>
    <td class="<?=$this->shift ?> showdropline storekeeper_hidden colorist_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline not_storekeeper_hidden"></td>
    <td class="<?=$this->shift ?> showdropline not_storekeeper_hidden"></td>
    <td class="<?=$this->shift ?> showdropline not_storekeeper_hidden cutting_hidden"></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php
    $comment_invisible_class = "";
    if($this->timetable->editable) {
        $comment_invisible_class = " comment_invisible";
    }
    ?>
    <td class="<?=$this->shift ?> showdropline comment_cell<?=$comment_invisible_class ?> colorist_hidden" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
</tr>