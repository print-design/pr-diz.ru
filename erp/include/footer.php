<div style="height: 50px;"></div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/popper.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
<script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>

<script>
    // Всплывающая подсказка
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    });
    
    // Фильтрация ввода
    $('.int-only').keypress(function(e) {
        if(/\D/.test(String.fromCharCode(e.charCode))) {
            return false;
        }
    });
    
    $('.int-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(/[^\d]/g, '');
        $(this).val(val);
    });
    
    $('.float-only').keypress(function(e) {
        if(!/[\.\,\d]/.test(String.fromCharCode(e.charCode))) {
            return false;
        }
        
        if(/[\.\,]/.test(String.fromCharCode(e.charCode)) && ($(e.target).val().includes('.') || $(e.target).val().includes(','))) {
            return false;
        }
    });
    
    $('.float-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(',', '.');
        val = val.replace(/[^\.\d]/g, '');
        $(this).val(val);
    });
    
    $('.no-latin').keypress(function(e) {
        if(/[a-zA-Z]/.test(String.fromCharCode(e.charCode))) {
            $(this).next('.invalid-feedback').text('Переключите раскладку');
            $(this).next('.invalid-feedback').show();
            return false;
        }
        else {
            $(this).next('.invalid-feedback').hide();
        }
    });
    
    $('.no-latin').change(function() {
        var val = $(this).val();
        val = val.replace('[a-zA-Z]', '');
        $(this).val(val);
    });
    
    $('input[type="text"]').prop('autocomplete', 'off');
    
    $('form').prop('autocomplete', 'off');
    
    $('textarea').css('resize', 'none');
    
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
    
    $('select').change(function(){
        $(this).removeClass('is-invalid');
    });
    
    $.mask.definitions['~'] = "[+-]";
    $("#phone").mask("+7 (999) 999-99-99");
    
    // Подтверждение удаления
    $('button.confirmable').click(function() {
        return confirm('Действительно удалить?');
    });
    
    // Отмена нажатия неактивной кнопки
    $('button.disabled').click(function() {
        return false;
    });
    
    // Поиск
    $('input#find').focusin(function() {
        $('#find-group').addClass('w-100', { duration: 300 });
        $('#find-form').addClass('w-100', { duration: 300 });
    });
    
    $("#find-append").click(function() {
        $('#find-form').removeClass('d-none');
        $("#find-append").addClass('d-none');
        $('input#find').focus();
    });
    
    $('input#find').focusout(function() {
        var getstring = window.location.search;
        var getparams = getstring.substring(1).split('&');
        
        var hasfind = false;
        
        for(i=0; i<getparams.length; i++) {
            var keyvalues = getparams[i].split('=');
            
            if(keyvalues[0] == 'find') {
                hasfind = true;
            }
        }
        
        if(!hasfind && $('input#find').val() == '') {
            $('#find-group').removeClass('w-100');
            $('#find-form').removeClass('w-100');
            $('#find-append').removeClass('d-none');
            $('#find-form').addClass('d-none');
        }
    });
    
    //********************************************************
    // ГРАФИК
    
    // Работа с буфером обмена
    function CopyEdition(edition, button) {
        var alert = button.children('.clipboard_alert');
        
        $.ajax("../ajax/clipboard.php?edition=" + edition)
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
    
    // Печать
    $('input[type=date]#from').change(function(){
        $('input[type=hidden].print_from').val($(this).val());
    });
    
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
    
    // Автоматическое сохранение значений полей
    function EditOrganization(field) {
        var organization = field.val();
        var id = field.attr('data-id');
        field.val('000');
        $.ajax({ url: "../ajax/edition.php?organization=" + organization + "&id=" + id, context: field })
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
        $.ajax({ url: "../ajax/edition.php?edition=" + edition + "&id=" + id, context: field })
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
        $.ajax({ url: "../ajax/edition.php?length=" + length + "&id=" + id, context: field })
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
        $.ajax({ url: "../ajax/edition.php?coloring=" + coloring + "&id=" + id, context: field })
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
        $.ajax({ url: "../ajax/edition.php?comment=" + encodeURI(comment) + "&id=" + id, context: area })
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
            $('#waiting').html("<img src='../images/waiting.gif' />");
            var user1_id = select.val();
            var id = select.attr('data-id');
            var date = select.attr('data-date');
            var shift = select.attr('data-shift');
            $.ajax({ url: "../ajax/edit_user1.php?user1_id=" + user1_id + "&id=" + id + "&date=" + date + "&shift=" + shift + "&machine_id=" + select.attr('data-machine') })
                    .done(function() {
                        $.ajax({ url: "../ajax/draw.php?machine_id=" + select.attr('data-machine') + "&from=" + select.attr('data-from') + "&to=" + select.attr('data-to'), context: select })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
            $('#waiting').html("<img src='../images/waiting.gif' />");
            var user2_id = select.val();
            var id = select.attr('data-id');
            var date = select.attr('data-date');
            var shift = select.attr('data-shift');
            $.ajax({ url: "../ajax/edit_user2.php?user2_id=" + user2_id + "&id=" + id + "&date=" + date + "&shift=" + shift + "&machine_id=" + select.attr('data-machine') })
                    .done(function() {
                        $.ajax({ url: "../ajax/draw.php?machine_id=" + select.attr('data-machine') + "&from=" + select.attr('data-from') + "&to=" + select.attr('data-to'), context: select })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
        $('#waiting').html("<img src='../images/waiting.gif' />");
        var user1 = button.parent().prev().val();
        var id = button.attr('data-id');
        var role_id = button.attr('role_id');
        var date = button.attr('data-date');
        var shift = button.attr('data-shift');
        var machine_id = button.attr('data-machine');
        $.ajax({ url: "../ajax/create_user1.php?user1=" + user1 + "&id=" + id + "&role_id=" + role_id + "&date=" + date + "&shift=" + shift + "&machine_id=" + machine_id, context: button })
                .done(function() {
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
        $('#waiting').html("<img src='../images/waiting.gif' />");
        var user2 = button.parent().prev().val();
        var id = button.attr('data-id');
        var role_id = button.attr('role_id');
        var date = button.attr('data-date');
        var shift = button.attr('data-shift');
        var machine_id = button.attr('data-machine');
        $.ajax({ url: "../ajax/create_user2.php?user2=" + user2 + "&id=" + id + "&role_id=" + role_id + "&date=" + date + "&shift=" + shift + "&machine_id=" + machine_id, context: button })
                .done(function() {
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
        $.ajax({ url: "../ajax/edition.php?status_id=" + status_id + "&id=" + id, context: select })
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
        $.ajax({ url: "../ajax/edition.php?roller_id=" + roller_id + "&id=" + id, context: select })
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
        $.ajax({ url: "../ajax/edition.php?lamination_id=" + lamination_id + "&id=" + id, context: select })
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
        $.ajax({ url: "../ajax/edition.php?manager_id=" + manager_id + "&id=" + id, context: select })
                .done(function(data){
                    select.val(data);
        })
                .fail(function(){
                    alert('Ошибка при смене менеджера');
        });
    }
    
    // Создание тиража
    function CreateEdition(button) {
        $('#waiting').html("<img src='../images/waiting.gif' />");
        $.ajax({ url: "../ajax/create_edition.php?workshift_id=" + button.attr('data-workshift') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to') + "&position=" + button.attr('data-position') + "&direction=" + button.attr('data-direction'), context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
    
    // Вставка тиража
    function PasteEdition(button) {
        $('#waiting').html("<img src='../images/waiting.gif' />");
        $.ajax({ url: "../ajax/clipboard_paste.php?clipboard=" + button.attr('data-clipboard') + "&machine_id=" + button.attr('data-machine') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&workshift_id=" + button.attr('data-workshift') + "&direction=" + button.attr('data-direction') + "&position=" + button.attr('data-position'), context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
    
    // Удаление тиража
    function DeleteEdition(button) {
        $('#waiting').html("<img src='../images/waiting.gif' />");
        $.ajax({ url: "../ajax/delete_edition.php?id=" + button.attr('data-id'), context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
        $('#waiting').html("<img src='../images/waiting.gif' />");
        $.ajax({ url: "../ajax/delete_shift.php?id=" + button.attr('data-id'), context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
        $('#waiting').html("<img src='../images/waiting.gif' />");
        var machine_id = $('#move_shifts_machine_id').val();
        var from = $('#move_shifts_date_from').val();
        var shift_from = $('#move_shifts_shift_from').val();
        var to = $('#move_shifts_date_to').val();
        var shift_to = $('#move_shifts_shift_to').val();
        var days = $('#move_shifts_days').val();
        var half = $('#move_shifts_half').is(':checked');
        
        $.ajax({ url: "../ajax/move_shifts_up.php?machine_id=" + machine_id + "&from=" + from + "&shift_from=" + shift_from + "&to=" + to + "&shift_to=" + shift_to + "&days=" + days + "&half=" + half, context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
    
    function MoveShiftsDown(button) {
        $('#waiting').html("<img src='../images/waiting.gif' />");
        var machine_id = $('#move_shifts_machine_id').val();
        var from = $('#move_shifts_date_from').val();
        var shift_from = $('#move_shifts_shift_from').val();
        var to = $('#move_shifts_date_to').val();
        var shift_to = $('#move_shifts_shift_to').val();
        var days = $('#move_shifts_days').val();
        var half = $('#move_shifts_half').is(':checked');
        
        $.ajax({ url: "../ajax/move_shifts_down.php?machine_id=" + machine_id + "&from=" + from + "&shift_from=" + shift_from + "&to=" + to + "&shift_to=" + shift_to + "&days=" + days + "&half=" + half, context: button })
                .done(function(){
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#waiting').html('');
                                $('#maincontent').html(data);
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
    
    //********************************************************
    
    // Автологаут резчика
    
    <?php if(IsInRole('cutter')): ?>
        function AutoLogout(end) {
            var beforeLogout = end - (new Date());
            
            if(beforeLogout < 0) {
                $('#logout_submit').click();
            } else {
                var beforeLogoutSec = Math.floor(beforeLogout / 1000);
                var beforeLogoutMin = Math.floor(beforeLogoutSec / 60);
                var beforeLogoutLastSec = beforeLogoutSec - (beforeLogoutMin * 60);
                $('#autologout').html(String(beforeLogoutMin).padStart(2, '0') + ':' + String(beforeLogoutLastSec).padStart(2, '0'));
            }
        }
        
        // Автологаут через 20 минут
        let unix_timestamp = <?= filter_input(INPUT_COOKIE, LOGIN_TIME) ?>;
        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        var begin_date = new Date(unix_timestamp * 1000);
        var end_date = new Date(unix_timestamp * 1000 + (<?=AUTOLOGOUT_MIN ?> * 60 * 1000));
        
        var beforeLogout = end_date - (new Date());       
        var beforeLogoutSec = Math.floor(beforeLogout / 1000);
        var beforeLogoutMin = Math.floor(beforeLogoutSec / 60);
        var beforeLogoutLastSec = beforeLogoutSec - (beforeLogoutMin * 60);
        AutoLogout(end_date);
        
        setInterval(AutoLogout, 1000, end_date);
    <?php endif; ?>
        
    // Прокрутка на прежнее место после отправки формы
    $(window).on("scroll", function(){
        $('input[name="scroll"]').val($(window).scrollTop());
        
        // Прокрута заголовков формы в графике
        if($('thead#grafik-thead').length > 0 && $('tbody#grafik-tbody').length > 0) {
            var windowTop = $(window).scrollTop();
            var headHeight = $('thead#grafik-thead').height();
            var bodyTop = $('tbody#grafik-tbody').offset().top;
            var bodyPosition = $('tbody#grafik-tbody').offset().top - windowTop;
            
            if(bodyPosition < headHeight) {
                $('thead#grafik-thead').css('transform', 'translate3d(0, ' + (windowTop - bodyTop + headHeight) + 'px, 100px)');
            }
            else {
                $('thead#grafik-thead').css('transform', 'none');
            }
        }
    });
    
    <?php if(!empty($_REQUEST['scroll'])): ?>
        window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
    <?php endif; ?>
</script>