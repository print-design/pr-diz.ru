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
            
    // Загрузка списка марок пленки
    $('#supplier_id').change(function(){
        if($(this).val() == "") {
            $('#film_id').html("<option value=''>Выберите марку</option>");
        }
        else {
            $.ajax({ url: "../ajax/film.php?supplier_id=" + $(this).val() })
                    .done(function(data) {
                        $('#film_id').html(data);
                        $('#film_id').change();
                    })
                    .fail(function() {
                        alert('Ошибка при выборе поставщика');
                    });
        }
    });
    
    // Загрузка списка толщин
    $('#film_id').change(function(){
        if($(this).val() == "") {
            $('#film_variation_id').html("<option value=''>Выберите толщину</option>");
        }
        else {
            $.ajax({ url: "../ajax/thickness.php?film_id=" + $(this).val() + "&supplier_id=" + $('#supplier_id').val() })
                    .done(function(data) {
                        $('#film_variation_id').html(data);
                    })
                    .fail(function() {
                        alert('Ошибка при выборе марки пленки');
                    });
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