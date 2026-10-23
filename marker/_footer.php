<script src="<?=APPLICATION ?>/js/input_filters.js?v=<?=filemtime(__DIR__.'/../js/input_filters.js') ?>"></script>
<script>
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
    
    $('select').change(function(){
        $(this).removeClass('is-invalid');
    });
    
    
    // Ограничение значений
    function KeyUpLimitIntValue(textbox, max) {
        val = textbox.val().replace(/[^\d]/g, '');
        
        if(val !== null && val !== '' && !isNaN(val) && parseInt(val) > max) {
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