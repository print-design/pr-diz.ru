<hr />
<div class="container-fluid">
    &COPY;&nbsp;Принт-дизайн
</div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.js'></script>
<script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
<script src="<?=APPLICATION ?>/js/popper.min.js"></script>

<script>
    // Всплывающие подсказки
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    });
    
    // Фильтрация ввода
    $('.int-only').keypress(function(e) {
        if(/\D/.test(String.fromCharCode(e.charCode))) {
            return false;
        }
    });
        
    $('.float-only').keypress(function(e) {
        if(!/[\.\d]/.test(String.fromCharCode(e.charCode))) {
            return false;
        }
    });
    
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
        
    $('select').change(function(){
        $(this).removeClass('is-invalid');
    });
    
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
    
    // Подтверждение удаления
    $('button.confirmable').click(function(){
        return confirm('Действительно удалить?');
    });
    
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
        
    $('select[id=user1_id],select[id=user2_id]').change(function(){
        if(this.value == '+') {
            $(this).parent().next().removeClass('d-none');
            $(this).parent().addClass('d-none');
            return;
        }
        this.form.submit();
    });
        
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
        $.ajax({ url: "../ajax/create_edition.php?workshift_id=" + button.attr('data-workshift') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to') + "&position=" + button.attr('data-position') + "&direction=" + button.attr('data-direction'), context: button })
                .done(function(){
                    $('#waiting').html("<img src='../images/waiting.gif' />");
            
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#maincontent').html(data);
                                $('#waiting').html('');
                            })
                            .fail(function(){
                                alert('Ошибка при перерисовки страницы');
                            });
                })
                .fail(function(){
                    alert('Ошибка при совершении операции');
                });
    }
    
    // Вставка тиража
    function PasteEdition(button) {
        $.ajax({ url: "../ajax/clipboard_paste.php?clipboard=" + button.attr('data-clipboard') + "&machine_id=" + button.attr('data-machine') + "&date=" + button.attr('data-date') + "&shift=" + button.attr('data-shift') + "&workshift_id=" + button.attr('data-workshift') + "&direction=" + button.attr('data-direction') + "&position=" + button.attr('data-position'), context: button })
                .done(function(){
                    $('#waiting').html("<img src='../images/waiting.gif' />");
            
                    $.ajax({ url: "../ajax/draw.php?machine_id=" + button.attr('data-machine') + "&from=" + button.attr('data-from') + "&to=" + button.attr('data-to'), context: button })
                            .done(function(data){
                                $('#maincontent').html(data);
                                $('#waiting').html('');
                            })
                            .fail(function(){
                                alert('Ошибка при перерисовки страницы');
                            });
                })
                .fail(function(){
                    alert("Ошибка при совершении операции");
                });
    }
    
    // Сдвиг нескольких смен
    $('.show_move_form').click(function(){
        $('#move_shifts_from').val($(this).attr('data-date'));
        $('#move_shifts_shift').val($(this).attr('data-shift'));
        $('#move_shifts_machine_id').val($(this).attr('data-machine_id'));
        $('#move_shifts_form').modal('show');
    });
    
    // Прокрутка на прежнее место после отправки формы
    $(window).on("scroll", function(){
        $('input[name="scroll"]').val($(window).scrollTop());
        
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
    
    <?php if (!empty($_REQUEST['scroll'])): ?>
    window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
    <?php endif; ?>
</script>