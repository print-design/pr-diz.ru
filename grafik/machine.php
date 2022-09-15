<?php
include '../include/topscripts.php';
include '../include/GrafikTimetable.php';

// Авторизация
if(!LoggedIn()) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан параметр id, переводим на начальную страницу
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION);
}

$date_from = null;
$date_to = null;
GetDateFromDateTo(filter_input(INPUT_GET, 'from'), filter_input(INPUT_GET, 'to'), $date_from, $date_to);

$timetable = new GrafikTimetable($date_from, $date_to, filter_input(INPUT_GET, 'id'));
$error_message = $timetable->error_message;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>График - <?=$timetable->name ?></title>
        <?php
        include '../include/head.php';
        ?>
        <style>
            body {
                padding-left: 0;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_grafik.php';
        ?>
        <div style="position: fixed; top: 0; left: 0; z-index: 1000;" id="waiting"></div>
        <div class="container-fluid" id="maincontent">
            <?php
            if(isset($error_message) && $error_message != '') {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            $timetable->Show();
            ?>
        </div>
        <?php
        include '../include/footer_grafik.php';
        ?>
        <script>
            // Подтверждение удаления
            $('button.confirmable').click(function(){
                return confirm('Действительно удалить?');
            });
            
            // Печать
            $('input[type=date]#from').change(function(){
                $('input[type=hidden].print_from').val($(this).val());
            });
                        
            // Копирование в буфер обмена (старая версия)
            function CopyEdition(edition, button) {
                var alert = button.children('.clipboard_alert');
        
                $.ajax("ajax/clipboard.php?edition=" + edition)
                        .done(function(data){
                            if(data == '') {
                                alert('Ошибка при копировании тиража в буфер обмена');
                            }
                            else {
                                $('.btn_clipboard_paste').attr('data-clipboard', data);
                                $('.btn_clipboard_paste').prop("disabled", false);
                        
                                alert.slideDown(300, function(){
                                    $(this).slideUp(1000);
                                });
                            }
                        })
                        .fail(function(){
                            alert('Ошибка при копировании тиража в буфер обмена.');
                });
            }
    
            // Копирование в буфер обмена (новая версия)
            function CopyEditionDb(edition, button) {
                var alert = button.children('.clipboard_alert');
        
                $.ajax("ajax/clipboard_db.php?edition=" + edition)
                        .done(function(){
                            $('.btn_clipboard_paste').prop("disabled", false);
            
                            alert.slideDown(300, function(){
                                $(this).slideUp(1000);
                            });
                        })
                        .fail(function(){
                            alert('Ошибка при копировании тиража в буфер обмена.');
                });
            }
            
            // Автозаполнение
            function Autocomplete() {
                // Автозаполнение текстового поля "Заказчик"
                var organizations = [
                    <?php
                    $orgs = array();
                    $fetcher = new Fetcher("select distinct organization from edition order by organization");
                    while ($row = $fetcher->Fetch()) {
                        if (count_chars($row['organization']) > 0) {
                            array_push($orgs, '"'.addslashes($row['organization']).'"');
                        }
                    }
        
                    echo implode(",", $orgs);
                    ?>
                ];
                $(".organizations").autocomplete({
                    source: organizations
                });
        
                // Автозаполнение текстового поля "Наименование тиража"
                var editions = [
                    <?php
                    $eds = array();
                    $fetcher = new Fetcher("select distinct name from edition order by name");
                    while ($row = $fetcher->Fetch()) {
                        if(count_chars($row['name']) > 0) {
                            array_push($eds, '"'.addslashes($row['name']).'"');
                        }
                    }
            
                    echo implode(",", $eds);
                    ?>
                ];
                $(".editions").autocomplete({
                    source: editions
                });
            }
            
            // Активация автозаполнения
            Autocomplete();
    
            // Автоматическое сохранение значений полей
            function EditOrganization(field) {
                var organization = field.val();
                var id = field.attr('data-id');
                field.val('000');
                $.ajax({ url: "ajax/edition.php?organization=" + organization + "&id=" + id, context: field })
                        .done(function(data) {
                            field.val(data);
                            organizations.push(data);
                            organizations = [...new Set(organizations)].sort();
                        })
                        .fail(function() {
                            field.val('70773');
                });
            }
    
            function EditEdition(field) {
                var edition = field.val();
                var id = field.attr('data-id');
                field.val('000');
                $.ajax({ url: "ajax/edition.php?edition=" + edition + "&id=" + id, context: field })
                        .done(function(data) {
                            field.val(data);
                            editions.push(data);
                            editions = [...new Set(editions)].sort();
                        })
                        .fail(function() {
                            field.val('70773');
                });
            }
    
            function EditLength(field) {
                var length = field.val();
                var id = field.attr('data-id');
                field.val('000');
                $.ajax({ url: "ajax/edition.php?length=" + length + "&id=" + id, context: field })
                        .done(function(data) {
                            field.val(data);
                        })
                        .fail(function() {
                            field.val('70773');
                });
            }
        
            function EditColoring(field) {
                var coloring = field.val();
                var id = field.attr('data-id');
                field.val('000');
                $.ajax({ url: "ajax/edition.php?coloring=" + coloring + "&id=" + id, context: field })
                        .done(function(data) {
                            field.val(data);
                        })
                        .fail(function() {
                            field.val('70773');
                });
            }
    
            function EditComment(area) {
                var comment = area.val();
                var id = area.attr('data-id');
                area.val('000');
                $.ajax({ url: "ajax/edition.php?comment=" + encodeURI(comment) + "&id=" + id, context: area })
                        .done(function(data) {
                            area.val(data);
                        })
                        .fail(function() {
                            area.val('70773');
                });
            }
    
            function CancelCreateUser1(button) {
                button.parent().parent().addClass('d-none');
                button.parent().parent().prev().removeClass('d-none');
                button.parent().parent().prev().val(button.attr('data-user1'));
                button.parent().prev().prev().val('');
            }
    
            function CancelCreateUser2(button) {
                button.parent().parent().addClass('d-none');
                button.parent().parent().prev().removeClass('d-none');
                button.parent().parent().prev().val(button.attr('data-user2'));
                button.parent().prev().prev().val('');
            }
    
            function EditUser1(select) {
                if(select.val() == '+') {
                    select.next().removeClass('d-none');
                    select.next().find('input').focus();
                    select.addClass('d-none');
                }
                else {
                    $('#waiting').html("<img src='images/waiting2.gif' />");
                    var user1_id = select.val();
                    var id = select.attr('data-id');
                    var date = select.attr('data-date');
                    var shift = select.attr('data-shift');
                    $.ajax({ url: "ajax/edit_user1.php?user1_id=" + user1_id + "&id=" + id + "&date=" + date + "&shift=" + shift + "&machine_id=" + select.attr('data-machine') })
                            .done(function() {
                                $.ajax({ url: "ajax/draw.php?machine_id=" + select.attr('data-machine') + "&from=" + select.attr('data-from') + "&to=" + select.attr('data-to'), context: select })
                                        .done(function(data){
                                            $('#waiting').html('');
                                            $('#maincontent').html(data);
                                            Autocomplete();
                                        })
                                        .fail(function(){
                                            $('#waiting').html('');
                                    alert('Ошибка при перерисовке страницы');
                                });
                            })
                            .fail(function() {
                                $('#waiting').html('');
                                alert('Ошибка при выборе работника 1');
                            });
                }
            }
    
            function EditUser2(select) {
                if(select.val() == '+') {
                    select.next().removeClass('d-none');
                    select.next().find('input').focus();
                    select.addClass('d-none');
                }
                else {
                    $('#waiting').html("<img src='images/waiting2.gif' />");
                    var user2_id = select.val();
                    var id = select.attr('data-id');
                    var date = select.attr('data-date');
                    var shift = select.attr('data-shift');
                    $.ajax({ url: "ajax/edit_user2.php?user2_id=" + user2_id + "&id=" + id + "&date=" + date + "&shift=" + shift + "&machine_id=" + select.attr('data-machine') })
                            .done(function() {
                                $.ajax({ url: "ajax/draw.php?machine_id=" + select.attr('data-machine') + "&from=" + select.attr('data-from') + "&to=" + select.attr('data-to'), context: select })
                                        .done(function(data){
                                            $('#waiting').html('');
                                            $('#maincontent').html(data);
                                            Autocomplete();
                                        })
                                        .fail(function(){
                                            $('#waiting').html('');
                                            alert('Ошибка при перерисовке страницы');
                                        });
                            })
                            .fail(function() {
                                $('#waiting').html('');
                                alert('Ошибка при выборе работника 1');
                            });
                }
            }
    
            function CreateUser1(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                var user1 = button.parent().prev().val();
                var id = button.attr('data-id');
                var role_id = button.attr('role_id');
                var date = button.attr('data-date');
                var shift = button.attr('data-shift');
                var machine_id = button.attr('data-machine');
                $.ajax({ url: "ajax/create_user1.php?user1=" + user1 + "&id=" + id + "&role_id=" + role_id + "&date=" + date + "&shift=" + shift + "&machine_id=" + machine_id, context: button })
                        .done(function() {
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function() {
                            $('#waiting').html('');
                            alert("Ошибка при создании пользователя");
                        });
            }
    
            function CreateUser2(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                var user2 = button.parent().prev().val();
                var id = button.attr('data-id');
                var role_id = button.attr('role_id');
                var date = button.attr('data-date');
                var shift = button.attr('data-shift');
                var machine_id = button.attr('data-machine');
                $.ajax({ url: "ajax/create_user2.php?user2=" + user2 + "&id=" + id + "&role_id=" + role_id + "&date=" + date + "&shift=" + shift + "&machine_id=" + machine_id, context: button })
                        .done(function() {
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function() {
                            $('#waiting').html('');
                            alert("Ошибка при создании пользователя");
                        });
            }
    
            function EditStatus(select) {
                var status_id = select.val();
                var id = select.attr('data-id');
                select.val('');
                $.ajax({ url: "ajax/edition.php?status_id=" + status_id + "&id=" + id, context: select })
                        .done(function(data) {
                            select.val(data);
                        })
                        .fail(function() {
                            alert('Ошибка при смене статуса');
                });
            }
        
            function EditRoller(select) {
                var roller_id = select.val();
                var id = select.attr('data-id');
                select.val('');
                $.ajax({ url: "ajax/edition.php?roller_id=" + roller_id + "&id=" + id, context: select })
                        .done(function(data) {
                            select.val(data);
                        })
                        .fail(function() {
                            alert('Ошибка при смене вала');
                });
            }
        
            function EditLamination(select) {
                var lamination_id = select.val();
                var id = select.attr('data-id');
                select.val('');
                $.ajax({ url: "ajax/edition.php?lamination_id=" + lamination_id + "&id=" + id, context: select })
                        .done(function(data){
                            select.val(data);
                        })
                        .fail(function(){
                            alert('Ошибка при смене ламинации');
                });
            }
    
            function EditManager(select) {
                var manager_id = select.val();
                var id = select.attr('data-id');
                $(this).val('');
                $.ajax({ url: "ajax/edition.php?manager_id=" + manager_id + "&id=" + id, context: select })
                        .done(function(data){
                            select.val(data);
                        })
                        .fail(function(){
                            alert('Ошибка при смене менеджера');
                });
            }
    
            // Создание тиража
            function CreateEdition(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                $.ajax({ url: "ajax/create_edition.php?workshift_id=" + button.attr('data-workshift') + "&machine_id=" + button.attr('data-machine') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to') + "&position=" + button.attr('data-position') + "&direction=" + button.attr('data-direction'), context: button })
                        .done(function(){
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert('Ошибка при совершении операции');
                        });
            }
    
            // Вставка тиража (старая версия)
            function PasteEdition(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                $.ajax({ url: "ajax/clipboard_paste.php?clipboard=" + button.attr('data-clipboard') + "&machine_id=" + button.attr('data-machine') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&workshift_id=" + button.attr('data-workshift') + "&direction=" + button.attr('data-direction') + "&position=" + button.attr('data-position'), context: button })
                        .done(function(){
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при совершении операции");
                        });
            }
    
            // Вставка тиража (новая версия)
            function PasteEditionDb(button) {
                source_id = 0;
        
                $('#waiting').html("<img src='images/waiting2.gif' />");
                $.ajax({ url: "ajax/clipboard_paste_db.php?machine_id=" + button.attr('data-machine') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&workshift_id=" + button.attr('data-workshift') + "&direction=" + button.attr('data-direction') + "&position=" + button.attr('data-position'), context: button})
                        .done(function(data){
                            result = JSON.parse(data);
                            source_id = result.id;
                            source_name = result.name;
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                            
                                        setTimeout(function(){
                                            if(confirm('Удалить исходный тираж "' + source_name + '"?')){
                                                $.ajax({ url: "ajax/delete_edition.php?id=" + source_id + "&keep_workshift=1", context: button })
                                                        .done(function(){
                                                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                                                    .done(function(data){
                                                                        $('#waiting').html('');
                                                                        $('#maincontent').html(data);
                                                                        Autocomplete();
                                                                    })
                                                                    .fail(function(){
                                                                        $('#waiting').html('');
                                                                        alert('Ошибка при перерисовке');
                                                                    });
                                                        })
                                                        .fail(function(){
                                                            $('#waiting').html('');
                                                            alert('Ошибка операции удаления');
                                                        });
                                            }
                                        }, 500);
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при перерисовке страницы");
                        });
            }
    
            // Удаление тиража
            function DeleteEdition(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                $.ajax({ url: "ajax/delete_edition.php?id=" + button.attr('data-id'), context: button })
                        .done(function(){
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при совершении операции");
                        });
            }
    
            function DeleteShift(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                $.ajax({ url: "ajax/delete_shift.php?id=" + button.attr('data-id'), context: button })
                        .done(function(){
                            $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                    .done(function(data){
                                        $('#waiting').html('');
                                        $('#maincontent').html(data);
                                        Autocomplete();
                                    })
                                    .fail(function(){
                                        $('#waiting').html('');
                                        alert('Ошибка при перерисовке страницы');
                                    });
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при совершении операции");
                        });
            }
    
            // Сдвиг нескольких смен
            function ShowMoveForm(button) {
                var local_date = (new Date(button.attr('data-date'))).toLocaleDateString('ru');
                var local_shift = 'день';
                if(button.attr('data-shift') == 'night') local_shift = 'ночь';
                $('#move_shifts_title').text('Начиная со смены: ' + local_date + ', ' + local_shift + '.');
                
                $('#move_shifts_date_from').val(button.attr('data-date'));
                $('#move_shifts_shift_from').val(button.attr('data-shift'));
                $('#move_shifts_machine_id').val(button.attr('data-machine'));
        
                $('#move-shift-up-button').attr('data-machine', button.attr('data-machine'));
                $('#move-shift-up-button').attr('data-from', button.attr('data-from'));
                $('#move-shift-up-button').attr('data-to', button.attr('data-to'));
        
                $('#move-shift-down-button').attr('data-machine', button.attr('data-machine'));
                $('#move-shift-down-button').attr('data-from', button.attr('data-from'));
                $('#move-shift-down-button').attr('data-to', button.attr('data-to'));
        
                $('#move_shifts_form').modal('show');
            }
    
            function MoveShiftsUp(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                var machine_id = $('#move_shifts_machine_id').val();
                var from = $('#move_shifts_date_from').val();
                var shift_from = $('#move_shifts_shift_from').val();
                var to = $('#move_shifts_date_to').val();
                var shift_to = $('#move_shifts_shift_to').val();
                var count = $('#move_shifts_count').val();
        
                $.ajax({ url: "ajax/move_editions_up.php?machine_id=" + machine_id + "&from=" + from + "&shift_from=" + shift_from + "&to=" + to + "&shift_to=" + shift_to + "&count=" + count })
                        .done(function(data){
                            if(data != '') {
                                $('#waiting').html('');
                                alert(data);
                            }
                            else {
                                $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                        .done(function(data){
                                            $('#waiting').html('');
                                            $('#maincontent').html(data);
                                            Autocomplete();
                                        })
                                        .fail(function(){
                                            $('#waiting').html('');
                                            alert('Ошибка при перерисовке страницы');
                                        });
                            }
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при совершении операции");
                        });
            }
    
            function MoveShiftsDown(button) {
                $('#waiting').html("<img src='images/waiting2.gif' />");
                var machine_id = $('#move_shifts_machine_id').val();
                var from = $('#move_shifts_date_from').val();
                var shift_from = $('#move_shifts_shift_from').val();
                var to = $('#move_shifts_date_to').val();
                var shift_to = $('#move_shifts_shift_to').val();
                var count = $('#move_shifts_count').val();
        
                $.ajax({ url: "ajax/move_editions_down.php?machine_id=" + machine_id + "&from=" + from + "&shift_from=" + shift_from + "&to=" + to + "&shift_to=" + shift_to + "&count=" + count })
                        .done(function(data){
                            if(data != '') {
                                $('#waiting').html('');
                                alert(data);
                            }
                            else {
                                $.ajax({ url: "ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                                        .done(function(data){
                                            $('#waiting').html('');
                                            $('#maincontent').html(data);
                                            Autocomplete();
                                        })
                                        .fail(function(){
                                            $('#waiting').html('');
                                            alert('Ошибка при перерисовке страницы');
                                        });
                            }
                        })
                        .fail(function(){
                            $('#waiting').html('');
                            alert("Ошибка при совершении операции");
                        });
            }
        </script>
    </body>
</html>