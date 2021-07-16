<div style="height: 50px;"></div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
<script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
<script src="<?=APPLICATION ?>/js/popper.min.js"></script>
<script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>

<script>
    // Отправка формы по нажатию Enter
    $('input').keypress(function(e) {
        if(e.which == 10 || e.which == 13) {
            $(e.target).focusout();
         
               submit_btn = $(e.target.form).find("button[type='submit']");
                
                if(submit_btn == null) {
                    this.form.submit();
                }
                else {
                    submit_btn.click();
                }
            }
    });
    
    // Фильтрация ввода
    $('.int-only').keypress(function(e) {
        if(/\D/.test(e.key)) {
            return false;
        }
    });
    
    $('.int-only').keyup(function() {
        var val = $(this).val();
        val = val.replaceAll(/\D/g, '');
        
        if(val === '') {
            $(this).val('');
        }
        else {
            val = parseInt(val);
            
            if($(this).hasClass('int-format')) {
                val = Intl.NumberFormat('ru-RU').format(replv);
            }
            
            $(this).val(val);
        }
    });
    
    $('.int-only').change(function(e) {
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
    });
    
    $('.float-only').keypress(function(e) {
        if(!/[\.\,\d]/.test(e.key)) {
            return false;
        }
        
        if(/[\.\,]/.test(e.key) && ($(e.target).val().includes('.') || $(e.target).val().includes(','))) {
            return false;
        }
    });
    
    $('.float-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(',', '.');
        val = val.replace(/[^\.\d]/g, '');
        
        if(val === '' || isNaN(val)) {
            $(this).val('');
        }
        else {
            val = parseFloat(val);
            $(this).val(val);
        }
    });
    
    $('.no-latin').keypress(function(e) {
        if(e.which != 10 && e.which != 13) {
            if(/[a-zA-Z]/.test(e.key)) {
                $(this).next('.invalid-feedback').text('Переключите раскладку');
                $(this).next('.invalid-feedback').show();
                return false;
            }
            else {
                $(this).next('.invalid-feedback').hide();
            }
        }
    });
    
    $('.no-latin').change(function() {
        var val = $(this).val();
        val = val.replace('[a-zA-Z]', '');
        $(this).val(val);
    });
    
    $('.no-latin').keyup(function() {
        var val = $(this).val();
        val = val.replace(/[a-zA-Z]/g, '');
        $(this).val(val);
    });
    
    // Ограничение значений для полей с целочисленными значениями (проценты и т. д.)
    // Обработка изменения нажатия клавиш
    function KeyDownLimitIntValue(textbox, e, max) {
        if(e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39) {
            if(/\D/.test(e.key)) {
                return false;
            }
            
            var text = textbox.val();
            var selStart = textbox.prop('selectionStart');
            var selEnd = textbox.prop('selectionEnd');
            var textStart = text.substring(0, selStart);
            var textEnd = text.substring(selEnd);
            var newvalue = textStart + e.key + textEnd;
            newvalue = newvalue.replace(/\D/g, ''); // целое число может быть разбито на разряды
            var iNewValue = parseInt(newvalue);
            
            if(iNewValue == null || iNewValue < 1 || iNewValue > max) {
                return false;
            }
        }
        
        return true;
    }
    
    // Ограничение значений для полей с целочисленными значениями (проценты и т. д.) 
    // Обработка отпускания клавиши
    function KeyUpLimitIntValue(textbox, max) {
        val = textbox.val().replace(/[^\d]/g, '');
        
        if(val != null && val != '' && !isNaN(val) && parseInt(val) > max) {
            textbox.addClass('is-invalid');
        }
        else {
            textbox.removeClass('is-invalid');
        }
    }
    
    // Ограничение значений для полей с целочисленными значениями (проценты и т. д.)
    // Обработка изменения текста
    function ChangeLimitIntValue(textbox, max) {
        val = textbox.val().replace(/[^\d]/g, '');
        textbox.val(val);
        
        if(val === null || val === '' || isNaN(val)) {
            alert('Только целое значение от 1 до ' + max);
            textbox.val('');
            textbox.focus();
        }
        else {
            iVal = parseInt(val);
            
            if(iVal < 1 || iVal > max) {
                alert('Только целое значение от 1 до ' + max);
                textbox.val('');
                textbox.focus();
            }
            else {
                textbox.val(iVal);
            }
        }
    }
    
    // Ограничение значений для полей с числовыми значениями (проценты и т. д.)
    // Обработка изменения нажатия клавиш
    function KeyDownLimitFloatValue(textbox, e, max) {
        if(e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39) {
            if(!/[\.\,\d]/.test(e.key)) {
                return false;
            }
            
            if(/[\.\,]/.test(e.key) && (textbox.val().includes('.') || textbox.val().includes(',') || parseFloat(textbox.val()) >= max)) {
                return false;
            }
            
            var text = textbox.val();
            var selStart = textbox.prop('selectionStart');
            var selEnd = textbox.prop('selectionEnd');
            var textStart = text.substring(0, selStart);
            var textEnd = text.substring(selEnd);
            var newvalue = textStart + e.key + textEnd;
            var fNewValue = parseFloat(newvalue);
            
            if(fNewValue == null || fNewValue < 1 || fNewValue > max) {
                return false;
            }
        }
        
        return true;
    }
    
    // Ограничение значений для полей с числовыми значениями (проценты и т. д.)
    // Обработка изменения текста
    function ChangeLimitFloatValue(textbox, max) {
        var val = textbox.val();
        val = val.replace(',', '.');
        val = val.replace(/[^\.\d]/g, '');
        textbox.val(val);
        
        if(val === null || val === '' || isNaN(val)) {
            alert('Только целое значение от 0 до ' + max);
            textbox.val('');
            textbox.focus();
        }
        else {
            fVal = parseFloat(val);
            
            if(fVal < 0 || fVal > max) {
                alert('Только числовое значение от 0 до ' + max);
                textbox.val('');
                textbox.focus();
            }
            else {
                textbox.val(fVal);
            }
        }
    }
    
    // Форматирование целочисленного поля для отображения разрядов
    function IntFormat(textbox) {
        oldv = textbox.val();
        replv = oldv.replaceAll(/\D/g, '');
        
        if(replv === '') textbox.val('');
        else {
            val = Intl.NumberFormat('ru-RU').format(replv);
            textbox.val(val);
        }
    }
    
    // Запрет на изменение размеров всех многострочных текстовых полей вручную
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
    
    // При щелчке в поле телефона, устанавливаем курсор в самое начало ввода телефонного номера.
    $("#phone").click(function(){
        var maskposition = $(this).val().indexOf("_");
        if(Number.isInteger(maskposition)) {
            $(this).prop("selectionStart", maskposition);
            $(this).prop("selectionEnd", maskposition);
        }
    });
    
    // Подтверждение удаления
    $('button.confirmable').click(function() {
        return confirm("Действительно удалить?");
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
    
    // Редактирование наценки
    function EditExtracharge(field) {
        var extracharge = field.val();
        if(extracharge === '' || isNaN(extracharge)) return false;
        
        var id = field.attr('data-id');
        field.val('000');
        $.ajax({ url: "../ajax/calculation.php?extracharge=" + extracharge + "&id=" + id, context: field })
                .done(function(data) {
                    field.val(data);
                    $('.extracharge').val(data);
        })
                .fail(function() {
                    alert('Ошибка при редактировании наценки.');
        });
    }
    
    // Всплывающая подсказка
    $(function() {
        $("a.left_bar_item").tooltip({
            position: {
                my: "left center",
                at: "right+10 center"
            }
        });
    });
    
    // Защита от двойного нажатия
    var submit_clicked = false;
    
    $('button[type=submit]').click(function () {
        if(submit_clicked) {
            return false;
        }
        else {
            submit_clicked = true;
        }
    });
    
    $(document).keydown(function () {
        submit_clicked = false;
    });
    
    $('select').change(function () {
        submit_clicked = false;
    });
    
    $('input').keydown(function() {
        submit_clicked = false;
    });
    
    $('input').change(function() {
        submit_clicked = false;
    });
    
    // Автологаут резчика
    <?php if(IsInRole('cutterOLD')): ?>
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
        
    // Отображение полностью блока с фиксированной позицией, не умещающегося полностью в окне
    function AdjustFixedBlock(fixed_block) {
        windowHeight = $(window).height();
        blockTop = fixed_block.offset().top;
        blockHeight = fixed_block.outerHeight();
        blockMarginTop = parseInt(fixed_block.css('margin-top').replace('px', ''));
        
        if(blockHeight + blockMarginTop < windowHeight) {
            fixed_block.css('position', 'fixed');
            fixed_block.css('top', 0);
            fixed_block.css('bottom', 'auto');
        }
        else {
            if(blockHeight + blockMarginTop < $(window).scrollTop() + windowHeight) {
                fixed_block.css('position', 'fixed');
                fixed_block.css('bottom', 0);
                fixed_block.css('top', 'auto');
            }
            else {
                fixed_block.css('position', 'absolute');
                fixed_block.css('top', 0);
                fixed_block.css('bottom', 'auto');
            }
        }
    };
        
    // Прокрутка на прежнее место после отправки формы
    $(window).on("scroll", function(){
        $('input[name="scroll"]').val($(window).scrollTop());
    });
    
    <?php if(!empty($_REQUEST['scroll'])): ?>
        window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
    <?php endif; ?>
</script>