<script>
    function SetSubmitVisibility(input, e) {
        val = input.val();
        if(val == '') {
            $('#car-submit').addClass('d-none');
        }
        else {
            $('#car-submit').removeClass('d-none');
        }
        
        /*if(/[^0-9а-яА-Я]/.test(e.key) && val != '' && e.which != 8 && e.which != 46 && e.which != 37 && e.which != 39) {
            input.addClass('is-invalid');
            $('#id-valid').removeClass('d-none');
            return false;
        }
        else {
            input.removeClass('is-invalid');
            $('#id-valid').addClass('d-none');
        }*/
        
        return true;
    }
    
    $('input#id').keydown(function (e){ return SetSubmitVisibility($(this), e) });
    $('input#id').keyup(function (e){ return SetSubmitVisibility($(this), e) });
    $('input#id').change(function (e){ return SetSubmitVisibility($(this), e) });
</script>