<hr />
<div class="container-fluid">
    &COPY;&nbsp;Принт-дизайн
</div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.js'></script>
<script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>

<script>
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
    $('.clipboard_copy').click(function(){
        var alert = $(this).children('.clipboard_alert');
        var edition = $(this).attr('data');
        $.ajax("../ajax/clipboard.php?edition=" + edition)
                .done(function(data){
                    if(data == '') {
                        alert('Ошибка при копировании тиража в буфер обмена');
                    }
                    else {
                        $('.clipboard').val(data);
                        $('.clipboard_paste').prop("disabled", false);
                        alert.slideDown(300, function(){
                            $(this).slideUp(1000);
                        });
                    }
                })
                .fail(function(){
                    alert('Ошибка при копировании тиража в буфер обмена.');
        });
    });
    
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
    $('input#edition.editable').focusout(function(){
        var edition = $(this).val();
        var id = $(this).parent().prev('#id').val();
        $(this).val('000');
        $.ajax({ url: "../ajax/edition.php?edition=" + edition + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
            editions.push(data);
            editions = [...new Set(editions)].sort();
        })
                .fail(function() {
                    $(this).val('70773');
        });
    });
        
    $('input#organization.editable').focusout(function(){
        var organization = $(this).val();
        var id = $(this).parent().prev('#id').val();
        $(this).val('000');
        $.ajax({ url: "../ajax/edition.php?organization=" + organization + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
            organizations.push(data);
            organizations = [...new Set(organizations)].sort();
        })
                .fail(function() {
                    $(this).val('70773');
        });
    });
        
    $('input#length.editable').focusout(function(){
        var length = $(this).val();
        var id = $(this).parent().prev('#id').val();
        $(this).val('000');
        $.ajax({ url: "../ajax/edition.php?length=" + length + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
        })
                .fail(function() {
                    $(this).val('70773');
        });
    });
        
    $('input#coloring.editable').focusout(function(){
        var coloring = $(this).val();
        var id = $(this).parent().prev('#id').val();
        $(this).val('000');
        $.ajax({ url: "../ajax/edition.php?coloring=" + coloring + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
        })
                .fail(function() {
                    $(this).val('70773');
        });
    });
        
    $('textarea#comment.editable').focusout(function(){
        var comment = $(this).val();
        var id = $(this).parent().prev('#id').val();
        $(this).val('000');
        $.ajax({ url: "../ajax/edition.php?comment=" + encodeURI(comment) + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
        })
                .fail(function() {
                    $(this).val('70773');
        });
    });
        
    $('select[id=user1_id],select[id=user2_id]').change(function(){
        if(this.value == '+') {
            $(this).parent().next().removeClass('d-none');
            $(this).parent().addClass('d-none');
            return;
        }
        this.form.submit();
    });
        
    $('select[id=status_id]').focusout(function(){
        var status_id = $(this).val();
        var id = $(this).prev('#id').val();
        $(this).val('');
        $.ajax({ url: "../ajax/edition.php?status_id=" + status_id + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
        })
                .fail(function() {
                    alert('Ошибка при смене статуса');
        });
    });
        
    $('select[id=roller_id]').focusout(function(){
        var roller_id = $(this).val();
        var id = $(this).prev('#id').val();
        $(this).val('');
        $.ajax({ url: "../ajax/edition.php?roller_id=" + roller_id + "&id=" + id, context: $(this) })
                .done(function(data) {
                    $(this).val(data);
        })
                .fail(function() {
                    alert('Ошибка при смене вала');
        });
    });
        
    $('select[id=lamination_id]').focusout(function(){
        var lamination_id = $(this).val();
        var id = $(this).prev('#id').val();
        $(this).val('');
        $.ajax({ url: "../ajax/edition.php?lamination_id=" + lamination_id + "&id=" + id, context: $(this) })
                .done(function(data){
                    $(this).val(data);
        })
                .fail(function(){
                    alert('Ошибка при смене ламинации');
        });
    });
        
    $('select[id=manager_id]').focusout(function(){
        var manager_id = $(this).val();
        var id = $(this).prev('#id').val();
        $(this).val('');
        $.ajax({ url: "../ajax/edition.php?manager_id=" + manager_id + "&id=" + id, context: $(this) })
                .done(function(data){
                    $(this).val(data);
        })
                .fail(function(){
                    alert('Ошибка при смене менеджера');
        });
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