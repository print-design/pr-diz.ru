<script>
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
    
    $('select').change(function(){
        $(this).removeClass('is-invalid');
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
                val = Intl.NumberFormat('ru-RU').format(val);
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
    
    // Ограничение значений
    function KeyUpLimitIntValue(textbox, max) {
        val = textbox.val().replace(/[^\d]/g, '');
        
        if(val != null && val != '' && !isNaN(val) && parseInt(val) > max) {
            textbox.addClass('is-invalid');
        }
        else {
            textbox.removeClass('is-invalid');
        }
    }
    
    // Ограничения значений
    $('.int-only').keyup(function() {
        KeyUpLimitIntValue($(this), $(this).attr('data-max'));
    });
</script>