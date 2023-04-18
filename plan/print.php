<?php
include '../include/topscripts.php';
include './_queue.php';
include './_plan_timetable.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
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
            
            table.typography tr:has(td.target) {
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
        </style>
    </head>
    <body>
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
                            <button type="button" class="btn btn-light"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить событие</button>
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
            
            function ChangeEmployee1(select) {
                //$('#waiting').html("<img src='../images/waiting2.gif' />");
                var id = select.val();
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee1.php?id=" + id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            $.ajax({ url: "_draw_timetable.php?machine_id=" + select.attr('data-machine-id') + "&from=" + select.attr('data-from') })
                                .done(function(data) {
                                    //$('#waiting').html('');
                                    $('#timetable').html(data);
                                    
                                    if($('#sidebar').hasClass('active')) {
                                        $('th.assistant').show();
                                        $('td.assistant').show();
                                    }
                                })
                                .fail(function() {
                                    //$('#waiting').html('');
                                    alert('Ошибка при перерисовке страницы');
                                });
                        })
                        .fail(function() {
                            //$('#waiting').html('');
                            alert('Ошибка при смене работника');
                        });
            }
            
            function ChangeEmployee2(select) {
                //$('#waiting').html("<img src='../images/waiting2.gif' />");
                var id = select.val();
                var machine_id = select.attr('data-machine-id');
                var date = select.attr('data-date');
                var shift = select.attr('data-shift');
                $.ajax({ url: "_set_employee2.php?id=" + id + "&machine_id=" + machine_id + "&date=" + date + "&shift=" + shift })
                        .done(function() {
                            $.ajax({ url: "_draw_timetable.php?machine_id=" + select.attr('data-machine-id') + "&from=" + select.attr('data-from') })
                                .done(function(data) {
                                    //$('#waiting').html('');
                                    $('#timetable').html(data);
                                    
                                    if($('#sidebar').hasClass('active')) {
                                        $('th.assistant').show();
                                        $('td.assistant').show();
                                    }
                                })
                                .fail(function() {
                                    //$('#waiting').html('');
                                    alert('Ошибка при перерисовке страницы');
                                });
                        })
                        .fail(function() {
                            //$('#waiting').html('');
                            alert('Ошибка при смене работника');
                        });
            }
            
            var dragqueue = false;
            
            function DragQueue(ev) {
                dragqueue = true;
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "queue");
            }
            
            function DragTimetable(ev) {
                ev.dataTransfer.setData("calculation_id", $(ev.target).attr("data-id"));
                ev.dataTransfer.setData("type", "timetable");
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
                var calculation_id = ev.dataTransfer.getData('calculation_id');
                var type = ev.dataTransfer.getData('type');
                
                if(type == 'timetable') {
                    //$('#waiting').html("<img src='../images/waiting2.gif' />");
                    $.ajax({ dataType: 'JSON', url: "_remove_from_plan.php?calculation_id=" + calculation_id + "&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    $.ajax({ url: "_draw_timetable.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                                    .done(function(timetable_data) {
                                        $('#timetable').html(timetable_data);
                                
                                        if($('#sidebar').hasClass('active')) {
                                            $('th.assistant').show();
                                            $('td.assistant').show();
                                        }
                                        $.ajax({ url: "_draw_queue.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&machine=<?=$machine ?>" })
                                                .done(function(queue_data) {
                                                    //$('#waiting').html('');
                                                    $('#queue').html(queue_data);
                                                })
                                                .fail(function() {
                                                    //$('#waiting').html('');
                                                    alert('Ошибка при перерисовке очереди');
                                                });
                                    })
                                    .fail(function() {
                                        //$('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                                }
                                else {
                                    //$('#waiting').html('');
                                    $('#timetable').html(add_data.error);
                                }
                            })
                            .fail(function() {
                                //$('#waiting').html('');
                                alert('Ошибка при удалении из плана');
                            });
                }
        
                $('#queue').removeClass('droppable');
            }
            
            function DropTimetable(ev) {
                ev.preventDefault();
                var calculation_id = ev.dataTransfer.getData('calculation_id');
                var type = ev.dataTransfer.getData('type');
                var date = $(ev.target).parent('tr').attr('data-date');
                var shift = $(ev.target).parent('tr').attr('data-shift');
                var before = $(ev.target).parent('tr').attr('data-id');
                
                if(type == 'queue') {
                    //$('#waiting').html("<img src='../images/waiting2.gif' />");
                    $.ajax({ dataType: 'JSON', url: "_add_to_plan.php?calculation_id=" + calculation_id + "&date=" + date + "&shift=" + shift + "&before=" + before + "&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                        .done(function(add_data) {
                            if(add_data.error == '') {
                                $.ajax({ url: "_draw_timetable.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                                    .done(function(timetable_data) {
                                        $('#timetable').html(timetable_data);
                                
                                        if($('#sidebar').hasClass('active')) {
                                            $('th.assistant').show();
                                            $('td.assistant').show();
                                        }
                                        $.ajax({ url: "_draw_queue.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&machine=<?=$machine ?>" })
                                                .done(function(queue_data) {
                                                    //$('#waiting').html('');
                                                    $('#queue').html(queue_data);
                                                })
                                                .fail(function() {
                                                    //$('#waiting').html('');
                                                    alert('Ошибка при перерисовке очереди');
                                                });
                                    })
                                    .fail(function() {
                                        //$('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                            }
                            else {
                                //$('#waiting').html('');
                                $('#timetable').html(add_data.error);
                            }
                        })
                        .fail(function() {
                            //$('#waiting').html('');
                            alert('Ошибка при добавлении в план' + error);
                        });
                }
                else if(type == 'timetable') {
                    //$('#waiting').html("<img src='../images/waiting2.gif' />");
                    $.ajax({ dataType: 'JSON', url: "_remove_from_plan.php?calculation_id=" + calculation_id + "&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                            .done(function(remove_data) {
                                if(remove_data.error == '') {
                                    $.ajax({ dataType: 'JSON', url: "_add_to_plan.php?calculation_id=" + calculation_id + "&date=" + date + "&shift=" + shift + "&before=" + before + "&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                                        .done(function(add_data) {
                                            if(add_data.error == '') {
                                                $.ajax({ url: "_draw_timetable.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&from=<?= filter_input(INPUT_GET, 'from') ?>" })
                                                    .done(function(timetable_data) {
                                                        $('#timetable').html(timetable_data);
                                                
                                                        if($('#sidebar').hasClass('active')) {
                                                            $('th.assistant').show();
                                                            $('td.assistant').show();
                                                        }
                                                        $.ajax({ url: "_draw_queue.php?machine_id=<?= filter_input(INPUT_GET, 'id') ?>&machine=<?=$machine ?>" })
                                                            .done(function(queue_data) {
                                                                //$('#waiting').html('');
                                                                $('#queue').html(queue_data);
                                                            })
                                                            .fail(function() {
                                                                //$('#waiting').html('');
                                                                alert('Ошибка при перерисовке очереди');
                                                            });
                                                    })
                                                    .fail(function() {
                                                        //$('#waiting').html('');
                                                        alert('Ошибка при перерисовке страницы');
                                                    });
                                            }
                                            else {
                                                //$('#waiting').html('');
                                                $('#timetable').html(add_data.error);
                                            }
                                        })
                                        .fail(function() {
                                            //$('#waiting').html('');
                                            alert('Ошибка при добавлении в план');
                                        });
                                }
                                else {
                                    //$('#waiting').html('');
                                    $('#timetable').html(add_data.error);
                                }
                            })
                            .fail(function() {
                                //$('#waiting').html('');
                                alert('Ошибка при удалении из плана');
                            });
                }
                else {
                    //$('#waiting').html('');
                    return;
                }
            }
        </script>
    </body>
</html>