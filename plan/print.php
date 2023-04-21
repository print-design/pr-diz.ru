<?php
include '../include/topscripts.php';
include './_queue.php';
include './_plan_timetable.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Добавление события
if(null !== filter_input(INPUT_POST, 'add_event_submit')) {
    $machine_id = filter_input(INPUT_POST, 'machine_id');
    $text = addslashes(filter_input(INPUT_POST, 'text'));
    $date = filter_input(INPUT_POST, 'date');
    $shift = filter_input(INPUT_POST, 'shift');
    $worktime = filter_input(INPUT_POST, 'worktime');
    $in_plan = filter_input(INPUT_POST, 'in_plan');
    
    $max_edition = 0;
    $max_event = 0;
    
    $sql = "select max(e.position) "
            . "from plan_edition e "
            . "inner join calculation c on e.calculation_id = c.id "
            . "where c.machine_id = $machine_id and e.date = '$date' and e.shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции тиража";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_edition = $row[0];
    
    $sql = "select max(position) "
            . "from plan_event "
            . "where in_plan = 1 and machine_id = $machine_id and date = '$date' and shift = '$shift'";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    if(!$row) {
        $error = "Ошибка при определении позиции события";
        echo json_encode(array('error' => $error));
        exit();
    }
    $max_event = $row[0];
    
    $position = max($max_edition, $max_event) + 1;
    
    $sql = "insert into plan_event (machine_id, text, date, shift, position, worktime, in_plan) values ($machine_id, '$text', '$date', '$shift', $position, $worktime, $in_plan)";
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
            .timetable_menu {
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
        </style>
    </head>
    <body>
        <div id="add_event" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <input type="hidden" name="machine_id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                        <input type="hidden" name="in_plan" value="1" />
                        <input type="hidden" name="scroll" />
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Добавить событие</p>
                            <button type="button" class="close add_event_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="date">Дата</label>
                                        <input type="date" name="date" class="form-control" required="required" />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="worktime">Кол-во часов</label>
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
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="shift">Начало события</label>
                                        <select name="shift" class="form-control" required="required">
                                            <option hidden="hidden" value="">...</option>
                                            <option value="day">День</option>
                                            <option value="night">Ночь</option>
                                        </select>
                                    </div>
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
        <?php
        include '../include/header_plan.php';
        ?>
        <div style="position: fixed; top: 0; left: 0; z-index: 1000;" id="waiting"></div>
        <div class="container-fluid">
            <?php
            include '../include/subheader_print.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1><?=$print_header ?></h1>
            <div class="wrapper" style="position: absolute; top: 170px; bottom: 0; left: 0; right: 0; padding-left: 75px;">
                <nav id="sidebar">
                    <div id="sidebar_toggle_button">
                        <button type="button" id="sidebarCollapse" class="btn btn-link"><img src="../images/icons/collapse.png" style="margin-right: 8px;" />Скрыть</button>
                    </div>
                    <h2>Очередь</h2>
                    <div id="queue" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 15px;" ondragover="DragOverQueue(event);" ondragleave="DragLeaveQueue(event);" ondrop="DropQueue(event);">
                        <?php
                        $queue = new Queue(filter_input(INPUT_GET, 'id'), $machine);
                        $queue->Show();
                        ?>
                    </div>
                </nav>
                <div id="content" style="width: 100%; position: relative;">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex justify-content-start">
                            <button type="button" id="sidebarExpand" class="btn btn-link" style="display: none; padding-left: 0;">
                                <img src="../images/icons/expand.png" style="margin-right: 8px;" />
                            </button>
                            <h2>План</h2>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form class="form-inline" method="get">
                                <label for="from" style="font-size: larger;">от&nbsp;</label>
                                <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                                <input type="date" 
                                       id="from" name="from" 
                                       class="form-control mr-2" 
                                       value="<?= filter_input(INPUT_GET, 'from') ?>" 
                                       style="border: 0; width: 8.5rem;" 
                                       onchange="javascript: this.form.submit();" />
                                
                            </form>
                            <?php if(!empty(filter_input(INPUT_GET, 'from'))): ?>
                            <a href="<?= BuildQueryRemove('from') ?>" class="btn btn-light">Сбросить</a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#add_event"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить событие</button>
                        </div>
                    </div>
                    <div id="timetable" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 0; padding: 5px;">
                        <?php
                        $date_from = null;
                        $date_to = null;
                        GetDateFromDateTo(filter_input(INPUT_GET, 'from'), null, $date_from, $date_to);
                        
                        $timetable = new PlanTimetable(filter_input(INPUT_GET, 'id'), $date_from, $date_to);
                        $timetable->Show();
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').addClass('active');
                $('#sidebarExpand').show();
                $('th.assistant').show();
                $('td.assistant').show();
            });
                
            $('#sidebarExpand').on('click', function() {
                $('#sidebar').removeClass('active');
                $('#sidebarExpand').hide();
                $('th.assistant').hide();
                $('td.assistant').hide();
            });
            
            // При показе формы добавления плёнки,
            // устанавливаем фокус на текстовом поле.
            $('#add_event').on('shown.bs.modal', function() {
                $('textarea:visible:first').focus();
            });
            
            function DeleteEvent(event_id) {
                $.ajax({ dataType: 'JSON', url: "_delete_event.php?event_id=" + event_id })
                        .done(function(data) {
                            if(data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                            }
                            else {
                                alert(data.error);
                            }
                        })
                        .fail(function() {
                            alert("Ошибка при удалении события");
                        });
            }
            
            function EnableMenu() {
                $('.timetable_menu_trigger').click(function() {
                    var menu = $(this).next('.timetable_menu');
                    $('.timetable_menu').not(menu).hide();
                    menu.slideToggle();
                });
                
                $(document).click(function(e) {
                    if($(e.target).closest($('.timetable_menu')).length || $(e.target).closest($('.timetable_menu_trigger')).length) return;
                    $('.timetable_menu').slideUp();
                });
            }
            
            EnableMenu();
            
            function DrawQueue(machine_id, machine) {
                $.ajax({ url: "_draw_queue.php?machine_id=" + machine_id + "&machine=" + machine })
                        .done(function(queue_data) {
                            $('#queue').html(queue_data);
                        })
                        .fail(function() {
                            alert('Ошибка при перерисовке очереди');
                        });
            }
            
            function DrawTimetable(machine_id, machine, from) {
                $.ajax({ url: "_draw_timetable.php?machine_id=" + machine_id + "&from=" + from })
                        .done(function(data) {
                            $('#timetable').html(data);
                    
                            if($('#sidebar').hasClass('active')) {
                                $('th.assistant').show();
                                $('td.assistant').show();
                            }
                            
                            DrawQueue(machine_id, machine);
                            EnableMenu();
                        })
                        .fail(function() {
                            alert('Ошибка при перерисовке страницы');
                        });
            }
            
            function ChangeEmployee1(select) {
                var id = select.val();
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee1.php?id=" + id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            DrawTimetable(select.attr('data-machine-id'), '<?=$machine ?>', select.attr('data-from'));
                        })
                        .fail(function() {
                            alert('Ошибка при смене работника');
                        });
            }
            
            function ChangeEmployee2(select) {
                var id = select.val();
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee2.php?id=" + id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            DrawTimetable(select.attr('data-machine-id'), '<?=$machine ?>', select.attr('data-from'));
                        })
                        .fail(function() {
                            alert('Ошибка при смене помощника');
                        });
            }
            
            var dragqueue = false;
            
            function DragQueue(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "queue");
            }
            
            function DragEvent(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("event_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "event");
            }
            
            function DragTimetable(ev) {
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "timetable");
            }
            
            function DragTimetableEvent(ev) {
                ev.dataTransfer.setData("event_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "timetableevent");
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
                
                if(type == 'timetable') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_remove_from_plan.php?calculation_id=" + calculation_id })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                                }
                                else {
                                    alert(add_data.error);
                                    $('td').removeClass('target');
                                    $('#queue').removeClass('droppable');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при удалении из плана');
                            });
                }
                else if(type == 'timetableevent') {
                    var event_id = ev.dataTransfer.getData('event_id');
                
                    $.ajax({ dataType: 'JSON', url: "_remove_event.php?event_id=" + event_id })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                                }
                                else {
                                    alert(add_data.error);
                                    $('td').removeClass('target');
                                    $('#queue').removeClass('droppable');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при удалении события из плана');
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
                
                if(type == 'queue') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_to_plan.php?calculation_id=" + calculation_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении в план' + error);
                        });
                }
                else if(type == 'event') {
                    var event_id = ev.dataTransfer.getData('event_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_event.php?event_id=" + event_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                            }
                            else {
                                alert(add_data.error);
                                $('td').removeClass('target');
                                $('#queue').removeClass('droppable');
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при добавлении в план' + error);
                        });
                }
                else if(type == 'timetable') {
                    var calculation_id = ev.dataTransfer.getData('calculation_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_remove_from_plan.php?calculation_id=" + calculation_id })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    $.ajax({ dataType: 'JSON', url: "_add_to_plan.php?calculation_id=" + calculation_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                                        .done(function(add_data) {
                                            if(add_data.error == '') {
                                                DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
                                            }
                                            else {
                                                alert(add_data.error);
                                                $('td').removeClass('target');
                                                $('#queue').removeClass('droppable');
                                            }
                                        })
                                        .fail(function() {
                                            alert('Ошибка при добавлении в план');
                                        });
                                }
                                else {
                                    alert(remove_data.error);
                                    $('td').removeClass('target');
                                    $('#queue').removeClass('droppable');
                                }
                            })
                            .fail(function() {
                                alert('Ошибка при удалении из плана');
                            });
                }
                else if(type == 'timetableevent') {
                    var event_id = ev.dataTransfer.getData('event_id');
                    
                    $.ajax({ dataType: 'JSON', url: "_add_event.php?event_id=" + event_id + "&date=" + date + "&shift=" + shift + "&before=" + before })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                DrawTimetable('<?= filter_input(INPUT_GET, 'id') ?>', '<?=$machine ?>', '<?= filter_input(INPUT_GET, 'from') ?>');
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
                else {
                    return;
                }
            }
            
            $('#timetable').on('scroll', function() {
                $('input[name="scroll"]').val($('#timetable').scrollTop());
            });
            
            <?php if(!empty($_REQUEST['scroll'])): ?>
                $('#timetable').scrollTop(<?= intval($_REQUEST['scroll']) ?>);
            <?php endif; ?>
        </script>
    </body>
</html>