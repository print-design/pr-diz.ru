<script>
    $('input#id').focusin(function (){
        $('#car-submit').removeClass('d-none');
    });
    
    $('#find-clear').click(function() {
        $('input#id').val('');
        $('input#id').focus();
    });
</script>