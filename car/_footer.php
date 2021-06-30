<script>
    function SetSubmitVisibility(input, e) {
        val = input.val();
        if(val == '') {
            $('#car-submit').addClass('d-none');
        }
        else {
            $('#car-submit').removeClass('d-none');
        }
        
        return true;
        
        input.val(val.replace(/[^0-9Р°-СЏРђ-РЇ]/g, ''));
        return true;
    }
    
    $('input#id').keydown(function (e){ return SetSubmitVisibility($(this), e) });
    $('input#id').keyup(function (e){ return SetSubmitVisibility($(this), e) });
    $('input#id').change(function (e){ return SetSubmitVisibility($(this), e) });
</script>