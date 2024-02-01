<?php
include '../include/topscripts.php';
include '../calculation/calculation.php';
include './_queue.php';
include './_plan_timetable.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_PACKER]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

$work_id = filter_input(INPUT_GET, 'work_id');
$machine_id = filter_input(INPUT_GET, 'machine_id');
$from = filter_input(INPUT_GET, 'from');
$to = filter_input(INPUT_GET, 'to');

// Если не указаны work_id или machine_id, перенаправляем на печать/Comiflex
if(empty($work_id) || empty($machine_id)) {
    if(empty($work_id)) {
        $work_id = WORK_PRINTING;
    }
    
    if(empty($machine_id)) {
        switch ($work_id) {
            case WORK_PRINTING:
                $machine_id = PRINTER_COMIFLEX;
                break;
            case WORK_LAMINATION:
                $machine_id = LAMINATOR_SOLVENT;
                break;
            case WORK_CUTTING:
                $machine_id = CUTTER_1;
                break;
        }
    }
    
    header("Location: ?work_id=$work_id&machine_id=$machine_id".(empty($from) ? "" : "&from=$from").(empty($to) ? "" : "&to=$to"));
}

// Добавление события
if(null !== filter_input(INPUT_POST, 'add_event_submit')) {
    $work_id = filter_input(INPUT_POST, 'work_id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $text = addslashes(filter_input(INPUT_POST, 'text'));
    $worktime = filter_input(INPUT_POST, 'worktime');
    $in_plan = filter_input(INPUT_POST, 'in_plan');
    
    $sql = "insert into plan_event (work_id, machine_id, text, worktime, in_plan) values ($work_id, $machine_id, '$text', $worktime, $in_plan)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Удаление события
if(null !== filter_input(INPUT_POST, 'delete_event_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "delete from plan_event where id = $id";
    $executer = new Executer($sql);
    $error_message = $executer->error;
}

// Разделение заказа
if(null !== filter_input(INPUT_POST, 'divide_submit')) {
    $calculation_id = filter_input(INPUT_POST, 'calculation_id');
    $work_id = filter_input(INPUT_POST, 'work_id');
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $lamination = filter_input(INPUT_POST, 'lamination');
    $length1 = filter_input(INPUT_POST, 'length1');
    
    $length_total = 0;
    $worktime_total = 0;
    
    // Проверяем, что этот заказ ещё не разделён
    $sql = "select count(id) from plan_part where calculation_id = $calculation_id and work_id = $work_id and lamination = $lamination";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            $error_message = "Этот заказ уже разделён";
        }
    }
    
    // Получаем общие метраж и время по нужному расчёту
    if(empty($error_message)) {
        $sql = "select cr.length_dirty_1, cr.work_time_1, cr.length_dirty_2, cr.work_time_2, cr.length_dirty_3, cr.work_time_3 "
                . "from calculation_result cr "
                . "inner join calculation c on cr.calculation_id = c.id "
                . "where c.id = $calculation_id";
        $fetcher = new Fetcher($sql);
        $error_message = $fetcher->error;
        if($row = $fetcher->Fetch()) {
            if($work_id == WORK_PRINTING) {
                $length_total = $row['length_dirty_1'];
                $worktime_total = $row['work_time_1'];
            }
            elseif($work_id == WORK_CUTTING) {
                $length_total = $row['length_dirty_1'];
                $worktime_total = $length_total / CUTTER_SPEEDS[$machine_id] / 60;
            }
            elseif($work_id == WORK_LAMINATION && $lamination == 1) {
                $length_total = $row['length_dirty_2'];
                $worktime_total = $row['work_time_2'];
            }
            elseif($work_id == WORK_LAMINATION && $lamination == 2) {
                $length_total = $row['length_dirty_3'];
                $worktime_total = $row['work_time_3'];
            }
        }
    
        if($length_total < $length1) {
            $error_message = "Остаток тиража должен быть больше нуля";
        }
    }
    
    // Вычисляем размеры половинок после разделения
    $length2 = $length_total - $length1;
    
    // Помещаем данные в базу
    if(empty($error_message)) {
        $worktime1 = $worktime_total * $length1 / $length_total;
        
        $sql = "insert into plan_part (calculation_id, lamination, length, in_plan, work_id, worktime) "
                . "values ($calculation_id, $lamination, $length1, 0, $work_id, $worktime1)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        $worktime2 = $worktime_total * $length2 / $length_total;
        
        $sql = "insert into plan_part (calculation_id, lamination, length, in_plan, work_id, worktime) "
                . "values ($calculation_id, $lamination, $length2, 0, $work_id, $worktime2)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Отмена разделения заказа
if(null !== filter_input(INPUT_POST, 'undivide_submit')) {
    $calculation_id = filter_input(INPUT_POST, 'calculation_id');
    $work_id = filter_input(INPUT_POST, 'work_id');
    $lamination = filter_input(INPUT_POST, 'lamination');
    
    $sql = "delete from plan_part_continuation where plan_part_id in (select id from plan_part where calculation_id = $calculation_id and work_id = $work_id and lamination = $lamination)";
    $executer = new Executer($sql);
    $error_message = $executer->error;
    
    if(empty($error_message)) {
        $sql = "delete from plan_part where calculation_id = $calculation_id and work_id = $work_id and lamination = $lamination";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .wrapper {
                display: flex;
                width: 100%;
                align-items: stretch;
            }
            
            #sidebar {
                position: relative;
                min-width: 397px;
                max-width: 397px;
                padding-right: 15px;
                transition: all 0.3s;
            }
            
            #sidebar.active {
                margin-left: -397px;
            }
            
            #sidebar_toggle_button {
                position: absolute;
                top: 0px;
                right: 3px;
            }
            
            .modal-content {
                border-radius: 20px;
            }
            
            @media (max-width: 768px) {
                #sidebar {
                    margin-left: -397px;
                }
                #sidebar.active {
                    margin-left: 0;
                }
                #sidebarCollapse span {
                    display: none;
                }
            }
            
            .queue_item {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                margin: 5px 5px 8px 5px;
            }
            
            #queue.droppable {
                border: solid 3px lightgray;
            }
            
            /* Таблица */
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }

            table.typography tr th {
                color: #68676C;
                border-top: 0;
            }
            
            table.typography tr:has(td.target) td.showdropline {
                border-top: solid 3px darkgray;
            }

            table.typography tr td {
                background-color: white;
            }

            table.typography tr td.night {
                background-color: #F0F1FA;
            }

            thead#grafik-thead {
                background-color: lightcyan;
            }
            
            table.typography tr th.fordrag, table.typography tr td.fordrag {
                padding-left: 3px;
                padding-right: 3px;
            }
            
            /* Выпадающее меню в таблице */
            .timetable_menu, .queue_menu {
                position: absolute;
                top: 80%;
                right: 0;
                border: solid 1px #A1A4B1;
                padding-left: 13px;
                padding-right: 13px;
                padding-top: 10px;
                padding-bottom: 10px;
                background-color: white;
                z-index: 2;
                display: none;
                border-radius: 15px;
            }
            
            .film_menu .command {
                padding-top: 3px;
                padding-bottom: 3px;
            }
            
            /* Кнопка "Продолжать" */
            .btn.btn-edition-continue {
                height: 25px;
                width: 25px;
                padding: 0;
                color: #212529;
                border-radius: 0;
                background-color: rgba(0,0,0,0);
            }
            
            <?php if($work_id == WORK_CUTTING): ?>
            .cutting_hidden {
                display: none;
            }
            <?php elseif($work_id == WORK_LAMINATION): ?>
            .lamination_hidden {
                display: none;
            }
            <?php endif; ?>
            
            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))): ?>
            .comment_invisible {
                display: none;
            }
            .notforedit {
                display: none;
            }
            <?php else: ?>
            th.fordrag, td.fordrag {
                display: none;
            }
            .foredit {
                display: none;
            }
            <?php endif; ?>
            
            <?php if(IsInRole(ROLE_NAMES[ROLE_STOREKEEPER])): ?>
            .storekeeper_hidden {
                display: none;
            }
            <?php else: ?>
            .not_storekeeper_hidden {
                display: none;
            }
            <?php endif; ?>
            
            <?php if(IsInRole(ROLE_NAMES[ROLE_SCHEDULER])): ?>
            .planner_hidden {
                display: none;
            }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <div id="add_event" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" name="work_id" value="<?= filter_input(INPUT_GET, 'work_id') ?>" />
                        <input type="hidden" name="machine_id" value="<?= filter_input(INPUT_GET, 'machine_id') ?>" />
                        <input type="hidden" name="in_plan" value="0" />
                        <input type="hidden" name="scroll" />
                        <div class="modal-header">
                            <div class="font-weight-bold" style="font-size: x-large;">Добавить событие</div>
                            <button type="button" class="close add_event_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-nowrap w-25"><label>Кол-во часов</label></div>
                                <div class="w-75">
                                    <input type="text" 
                                           name="worktime" 
                                           class="form-control float-only" 
                                           required="required" 
                                           autocomplete="off"
                                           onmousedown="javascript: $(this).removeAttr('name');" 
                                           onfocus="javascript: $(this).removeAttr('name');" 
                                           onmouseup="javascript: $(this).attr('name', 'worktime');" 
                                           onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('name'); }" 
                                           onkeyup="javascript: $(this).attr('name', 'worktime');" 
                                           onfocusout="javascript: $(this).attr('name', 'worktime');" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text">Событие</label>
                                <textarea name="text" class="form-control" required="required"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" name="add_event_submit">Добавить</button>
                            <button type="button" class="btn btn-light add_event_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="divide" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <div class="font-weight-bold" style="font-size: x-large;">Разделение заказа</div>
                            <button type="button" class="close add_event_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A"></i></button>
                        </div>
                        <div class="modal-body" id="divide_modal_form">
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" name="divide_submit" onclick="javascript: if(form.elements.divide_total.value - form.elements.length1.value <= 0) { alert('Остаток тиража должен быть больше нуля'); return false; }">Разделить</button>
                            <button type="button" class="btn btn-light add_event_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/header_plan.php';
        ?>
        <div class="container-fluid">
            <?php
            $header = '';
            ?>
            <?php if($work_id == WORK_PRINTING): ?>
            <div class="text-nowrap nav2">
                <?php
                $printer_id = filter_input(INPUT_GET, 'machine_id');
                $header = PRINTER_NAMES[$printer_id];
    
                foreach(PRINTERS as $printer):
                    $printer_class = $printer_id == $printer ? ' active' : '';
                ?>
                <a href="<?= BuildQuery('machine_id', $printer) ?>" class="mr-4<?=$printer_class ?>"><?=PRINTER_NAMES[$printer] ?></a>
                <?php endforeach; ?>
            </div>
            <?php elseif ($work_id == WORK_LAMINATION): ?>
            <div class="text-nowrap nav2">
                <?php
                $laminator_id = filter_input(INPUT_GET, 'machine_id');
                $header = LAMINATOR_NAMES[$laminator_id];
    
                foreach(LAMINATORS as $laminator):
                    $laminator_class = $laminator_id == $laminator ? ' active' : '';
                ?>
                <a href="<?= BuildQuery('machine_id', $laminator) ?>" class="mr-4<?=$laminator_class ?>"><?=LAMINATOR_NAMES[$laminator] ?></a>
                <?php endforeach; ?>
            </div>
            <?php elseif($work_id == WORK_CUTTING): ?>
            <div class="text-nowrap nav2">
            <?php
                $cutter_id = filter_input(INPUT_GET, 'machine_id');
                $header = CUTTER_NAMES[$cutter_id];
    
                foreach (CUTTERS as $cutter):
                    $cutter_class = $cutter_id == $cutter ? ' active' : '';
                ?>
                <a href="<?= BuildQuery('machine_id', $cutter) ?>" class="mr-4<?=$cutter_class ?>"><?=CUTTER_NAMES[$cutter] ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <hr />
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="wrapper" style="position: absolute; top: 100px; bottom: 0; left: 0; right: 0; padding-left: 75px;">
                <nav id="sidebar" class="foredit">
                    <div id="sidebar_toggle_button">
                        <button type="button" id="sidebarCollapse" class="btn btn-link"><img src="../images/icons/collapse.png" style="margin-right: 8px;" />Скрыть</button>
                    </div>
                    <h2>Очередь</h2>
                    <div id="queue" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 15px;" ondragover="DragOverQueue(event);" ondragleave="DragLeaveQueue(event);" ondrop="DropQueue(event);">
                        <?php
                        $queue = new Queue($work_id, $machine_id);
                        $queue->Show();
                        ?>
                    </div>
                </nav>
                <div id="content" style="width: 100%; position: relative;">
                    <div class="d-flex justify-content-between pr-3">
                        <div class="d-flex justify-content-start">
                            <button type="button" id="sidebarExpand" class="btn btn-link" style="display: none; padding-left: 0;">
                                <img src="../images/icons/expand.png" style="margin-right: 8px;" />
                            </button>
                            <h2>План&nbsp;&nbsp;<?=$header ?></h2>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form class="form-inline" method="get">
                                <input type="hidden" name="work_id" value="<?=$work_id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <input type="hidden" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" />
                                <label for="from" style="font-size: larger;">от&nbsp;</label>
                                <input type="date" 
                                       id="from" 
                                       name="from" 
                                       class="form-control mr-2" 
                                       value="<?= filter_input(INPUT_GET, 'from') ?>" 
                                       style="border: 0; width: 8.5rem;" 
                                       onchange="javascript: this.form.submit();" />
                                
                            </form>
                            <form class="form-inline" method="get">
                                <input type="hidden" name="work_id" value="<?=$work_id ?>" />
                                <input type="hidden" name="machine_id" value="<?=$machine_id ?>" />
                                <input type="hidden" name="from" value="<?= filter_input(INPUT_GET, 'from') ?>" />
                                <label for="to" style="font-size: larger;">до&nbsp;</label>
                                <input type="date" 
                                       id="to" 
                                       name="to" 
                                       class="form-control mr-2" 
                                       value="<?= filter_input(INPUT_GET, 'to') ?>" 
                                       style="border: 0; width: 8.5rem;" 
                                       onchange="javascript: this.form.submit();" />
                            </form>
                            <?php if(!empty(filter_input(INPUT_GET, 'from')) || !empty(filter_input(INPUT_GET, 'to'))): ?>
                            <a href="?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>" class="btn btn-light">Сбросить</a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light foredit ml-2" data-toggle="modal" data-target="#add_event"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить событие</button>
                            <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER_SENIOR]))): ?>
                            <?php if($work_id == WORK_PRINTING && $machine_id != PRINTER_ATLAS): ?>
                            <a class="btn btn-light ml-2" href="excel_print.php?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>&to=<?= filter_input(INPUT_GET, 'to') ?>">Выгрузка&nbsp;&nbsp;&nbsp;<i class="fas fa-file-download"></i></a>
                            <?php elseif($work_id == WORK_LAMINATION): ?>
                            <a class="btn btn-light ml-2" href="excel_laminate.php?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>&to=<?= filter_input(INPUT_GET, 'to') ?>">Выгрузка&nbsp;&nbsp;&nbsp;<i class="fas fa-file-download"></i></a>
                            <?php endif; ?>
                            <?php endif; ?>
                            <a class="btn btn-light ml-2" href="print.php?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>&to=<?= filter_input(INPUT_GET, 'to') ?>" target="_blank">Печать&nbsp;&nbsp;&nbsp;<i class="fas fa-print"></i></a>
                        </div>
                    </div>
                    <div id="timetable" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 0; padding: 5px;">
                        <?php
                        $date_from = null;
                        $date_to = null;
                        GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);
                        
                        $timetable = new PlanTimetable($work_id, $machine_id, $date_from, $date_to);
                        $timetable->Show();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_SCHEDULER]))): ?>
        <script>
            // Скрытие/показ левой панели.
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').addClass('active');
                $('#sidebarExpand').show();
                $('.comment_cell').removeClass('comment_invisible');
            });
                
            $('#sidebarExpand').on('click', function() {
                $('#sidebar').removeClass('active');
                $('#sidebarExpand').hide();
                $('.comment_cell').addClass('comment_invisible');
            });
                
            // При показе формы добавления события,
            // устанавливаем фокус на текстовом поле.
            $('#add_event').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            function EditComment(ev) {
                $(ev.target).parents('td').children('.d-flex').children('.comment_pen').addClass('d-none');
                $(ev.target).parents('td').children('.d-flex').children('.comment_text').addClass('d-none');
                $(ev.target).parents('td').children('.comment_input').removeClass('d-none');
                $(ev.target).parents('td').children('.comment_input').children('input').focus();
                
                input = $(ev.target).parents('td').children('.comment_input').children('input');
                input.prop("selectionStart", input.val().length);
                input.prop("selectionEnd", input.val().length);
            }
            
            function SaveComment(ev, plan_type, id) {
                text = $(ev.target).val();
                $(ev.target).val('');
                $.ajax({ url: "_add_comment.php?plan_type=" + plan_type + "&id=" + id + "&text=" + text })
                        .done(function(data) {
                            $(ev.target).val(data);
                            $(ev.target).parents('.comment_input').addClass('d-none');
                            $(ev.target).parents('td').children('.d-flex').children('.comment_text').html(data);
                            $(ev.target).parents('td').children('.d-flex').children('.comment_pen').removeClass('d-none');
                            $(ev.target).parents('td').children('.d-flex').children('.comment_text').removeClass('d-none');
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении комментария');
                        });
            }
            
            function DeleteEvent(event_id) {
                if(confirm("Действительно удалить?")) {
                    $.ajax({ dataType: 'JSON', url: "_delete_event.php?event_id=" + event_id })
                            .done(function(data) {
                                if(data.error == '') {
                                    DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                                }
                                else {
                                    alert(data.error);
                                }
                            })
                            .fail(function() {
                                alert("Ошибка при удалении события");
                            });
                }
                
                $('.timetable_menu').slideUp();
            }
            
            function CountDividedSize(divide_first) {
                if(divide_first == '') {
                    $('#divide_rest').text('');
                }
                else {
                    divide_total = $('#divide_total').val();
                    divide_rest = divide_total - divide_first;
                    $('#divide_rest').text(Intl.NumberFormat('ru-RU').format(Math.round(divide_rest)) + ' м');
                   
                    if(divide_rest > 0) {
                        $('#divide_rest').removeClass('text-danger');
                    }
                    else {
                        $('#divide_rest').addClass('text-danger');
                    }
                }
            }
            
            function EnableMenu() {
                $('.timetable_menu_trigger').click(function() {
                    var menu = $(this).next('.timetable_menu');
                    $('.timetable_menu').not(menu).hide();
                    menu.slideToggle();
                });
                
                $('.queue_menu_trigger').click(function() {
                    var menu = $(this).next('.queue_menu');
                    $('.queue_menu').not(menu).hide();
                    menu.slideToggle();
                });
                
                $(document).click(function(e) {
                    if($(e.target).closest($('.timetable_menu')).length || $(e.target).closest($('.timetable_menu_trigger')).length) return;
                    $('.timetable_menu').slideUp();
                    
                    if($(e.target).closest($('.queue_menu')).length || $(e.target).closest($('.queue_menu_trigger')).length) return;
                    $('.queue_menu').slideUp();
                });
                
                // Открытие формы разделения заказа
                $('.btn_divide').click(function() {
                    $('.queue_menu').slideUp();
                    var id = $(this).attr('data-id');
                    var lamination = $(this).attr('data-lamination');
                
                    $.ajax({ url: "_divide_form.php?id=" + id + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&lamination=" + lamination })
                            .done(function(data) {
                                $('#divide_modal_form').html(data);
                                $('#divide').modal('show');
                                $('input[name="scroll"]').val($('#timetable').scrollTop());
                            })
                            .fail(function() {
                                alert('Ошибка при открытии формы разделения заказа');
                            });
                });
            
                // При показе формы разделения заказа,
                // устанавливаем фокус на текстовом поле.
                // Допускается ввод только чисел.
                $('#divide').on('shown.bs.modal', function() {
                    $('input:text:visible:first').focus();
                    
                    $('input:text:visible:first').keypress(function(e) {
                        if(/\D/.test(e.key)) {
                            return false;
                        }
                    });
                
                    $('input:text:visible:first').keyup(function() {
                        var val = $(this).val();
                        val = val.replaceAll(/\D/g, '');
                    
                        if(val === '') {
                            $(this).val('');
                        }
                        else {
                            val = parseInt(val);
                        
                            if($(this).hasClass('int-format')) {
                                val = Intl.NumberFormat('ru-RU').format(val);
                            }
                        
                            $(this).val(val);
                        }
                        
                        CountDividedSize($(this).val());
                    });
                
                    $('input:text:visible:first').change(function(e) {
                        var val = $(this).val();
                        val = val.replace(/[^\d]/g, '');
                    
                        if(val === '') {
                            $(this).val('');
                        }
                        else {
                            val = parseInt(val);
                        
                            if($(this).hasClass('int-format')) {
                                val = Intl.NumberFormat('ru-RU').format(val);
                            }
                        
                            $(this).val(val);
                        }
                        
                        CountDividedSize($(this).val());
                    });
                });
            }
            
            EnableMenu();
            
            function DrawTimetable(work_id, machine_id, from, to) {
                $('#timetable').load("_draw_timetable.php?work_id=" + work_id + "&machine_id=" + machine_id + "&from=" + from + "&to=" + to, function() {
                    $('#queue').load("_draw_queue.php?work_id=" + work_id + "&machine_id=" + machine_id, function() {
                        EnableMenu();
                    });
                });
            }
            
            function ChangeEmployee1(select) {
                var id = select.val();
                var work_id = select.attr('data-work-id');
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee1.php?id=" + id + "&work_id=" + work_id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            DrawTimetable(select.attr('data-work-id'), select.attr('data-machine-id'), select.attr('data-from'), select.attr('data-to'));
                        })
                        .fail(function() {
                            alert('Ошибка при смене работника');
                        });
            }
            
            function ChangeEmployee2(select) {
                var id = select.val();
                var work_id = select.attr('data-work-id');
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee2.php?id=" + id + "&work_id=" + work_id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            DrawTimetable(select.attr('data-work-id'), select.attr('data-machine-id'), select.attr('data-from'), select.attr('data-to'));
                        })
                        .fail(function() {
                            alert('Ошибка при смене помощника');
                        });
            }
            
            function MoveDown(ev) {
                var date = $(ev.target).attr('data-date');
                var shift = $(ev.target).attr('data-shift');
                $.ajax({ dataType: 'JSON', url: "_move_down.php?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при смещении смен');
                        });
            }
            
            function MoveUp(ev) {
                var date = $(ev.target).attr('data-date');
                var shift = $(ev.target).attr('data-shift');
                $.ajax({ dataType: 'JSON', url: "_move_up.php?work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при смещении смен');
                        });
            }
            
            function AddContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_add_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при создании допечатки');
                        });
            }
            
            function RemoveContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_remove_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при удалении допечатки');
                        });
            }
            
            function AddChildContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_add_child_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при создании допечатки');
                        });
            }
            
            function RemoveChildContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_remove_child_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при удалении допечатки');
                        });
            }
            
            function AddPartContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_add_part_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при создании допечатки разделённого тиража');
                        });
            }
            
            function RemovePartContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_remove_part_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при удалении допечатки разделённого тиража');
                        });
            }
            
            function AddChildPartContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_add_child_part_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при создании допечатки разделённого тиража');
                        });
            }
            
            function RemoveChildPartContinuation(id) {
                $.ajax({ dataType: 'JSON', url: "_remove_child_part_continuation.php?id=" + id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при удалении допечатки разделённого тиража');
                        });
            }
            
            var dragqueue = false;
            
            function DragEdition(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("lamination", $(ev.target).attr("data-lamination"));
                ev.dataTransfer.setData("type", "edition");
            }
            
            function DragEvent(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("event_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "event");
            }
            
            function DragPart(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("part_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("lamination", $(ev.target).attr("data-lamination"));
                ev.dataTransfer.setData("type", "part");
            }
            
            function DragTimetableEdition(ev) {
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("lamination", $(ev.target).attr("data-lamination"));
                ev.dataTransfer.setData("type", "timetableedition");
            }
            
            function DragTimetableEvent(ev) {
                ev.dataTransfer.setData("event_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "timetableevent");
            }
            
            function DragTimetablePart(ev) {
                ev.dataTransfer.setData("part_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("lamination", $(ev.target).attr("data-lamination"));
                ev.dataTransfer.setData("type", "timetablepart");
            }
            
            function DragOverQueue(ev) {
                if(!dragqueue) {
                    ev.preventDefault(); 
                    $('#queue').addClass('droppable');
                }
            }
            
            function DragOverTimetable(ev) {
                ev.preventDefault();
                $(ev.target).addClass('target');
                
                if($(ev.target).parents('td').length > 0) { 
                    td = $(ev.target).parents('td')[0];
                    $(td).addClass('target');
                }
            }
            
            function DragLeaveQueue(ev) {
                if($(ev.target).parents('#queue').length == 0) {
                    ev.preventDefault();
                    dragqueue = false;
                    $('#queue').removeClass('droppable');
                }
            }
            
            function DragLeaveTimetable(ev) {
                ev.preventDefault();
                $(ev.target).removeClass('target');
            }
            
            function DropQueue(ev) {
                ev.preventDefault();
                dragqueue = false;
                var type = ev.dataTransfer.getData('type');
                
                if(type == 'timetableedition') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    var lamination = ev.dataTransfer.getData('lamination');
                    
                    $.ajax({ dataType: 'JSON', url: "_remove_edition.php?calculation_id=" + calculation_id + "&lamination=" + lamination + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>" })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                                }
                                else {
                                    alert(remove_data.error);
                                    $('td').removeClass('target');
                                    $('#queue').removeClass('droppable');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при удалении тиража из плана');
                            });
                }
                else if(type == 'timetableevent') {
                    var event_id = ev.dataTransfer.getData('event_id');
                
                    $.ajax({ dataType: 'JSON', url: "_remove_event.php?event_id=" + event_id })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                                }
                                else {
                                    alert(remove_data.error);
                                    $('td').removeClass('target');
                                    $('#queue').removeClass('droppable');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при удалении события из плана');
                            });
                }
                else if(type == 'timetablepart') {
                    var part_id = ev.dataTransfer.getData('part_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_remove_part.php?part_id=" + part_id + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>" })
                        .done(function(remove_data) {
                            if(remove_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(remove_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при удалении разделённого тиража из плана');
                        });
                }
        
                $('#queue').removeClass('droppable');
            }
            
            function DropTimetable(ev) {
                ev.preventDefault();
                var type = ev.dataTransfer.getData('type');
                tr = $(ev.target).parents('tr')[0];
                var date = $(tr).attr('data-date');
                var shift = $(tr).attr('data-shift');
                var before = $(tr).attr('data-position');
                
                if(type == 'edition') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    var lamination = ev.dataTransfer.getData('lamination');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_edition.php?calculation_id=" + calculation_id + "&lamination=" + lamination + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении тиража в план' + error);
                        });
                }
                else if(type == 'event') {
                    var event_id = ev.dataTransfer.getData('event_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_event.php?event_id=" + event_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении события в план' + error);
                        });
                }
                else if(type == 'part') {
                    var part_id = ev.dataTransfer.getData('part_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_part.php?part_id=" + part_id + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении разделённого тиража в план');
                        });
                }
                else if(type == 'timetableedition') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    var lamination = ev.dataTransfer.getData('lamination');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_edition.php?calculation_id=" + calculation_id + "&lamination=" + lamination + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении тиража в план');
                        });
                }
                else if(type == 'timetableevent') {
                    var event_id = ev.dataTransfer.getData('event_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_event.php?event_id=" + event_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении события в план');
                        });
                }
                else if(type == 'timetablepart') {
                    var part_id = ev.dataTransfer.getData('part_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_part.php?part_id=" + part_id + "&work_id=<?= filter_input(INPUT_GET, 'work_id') ?>&machine_id=<?= filter_input(INPUT_GET, 'machine_id') ?>&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'work_id') ?>', '<?= filter_input(INPUT_GET, 'machine_id') ?>', '<?= filter_input(INPUT_GET, 'from') ?>', '<?= filter_input(INPUT_GET, 'to') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert("Ошибка при добавлении разделённого тиража в план");
                        });
                }
                else {
                    return;
                }
            }
            
            $('#timetable').on('scroll', function() {
                $('input[name="scroll"]').val($(this).scrollTop());
            });
            
            <?php
            $scroll = filter_input(INPUT_POST, 'scroll');
            if(!empty($scroll)):
            ?>
            $('#timetable').animate({ scrollTop: <?=$scroll ?> }, 0);
            <?php endif; ?>
        </script>
        <?php endif; ?>
    </body>
</html>