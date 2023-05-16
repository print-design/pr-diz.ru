<tr data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-id="" data-position="">
    <td class="<?=$this->shift ?> showdropline border-left fordrag" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php if($this->timetable->work_id == WORK_PRINTING): ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php endif; ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php if($this->timetable->work_id == WORK_PRINTING): ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <?php endif; ?>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
    <td class="<?=$this->shift ?> showdropline text-right" ondrop="DropTimetable(event);" ondragover='DragOverTimetable(event);' ondragleave='DragLeaveTimetable(event);'></td>
</tr>