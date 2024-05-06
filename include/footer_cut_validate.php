<script>
    function CutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#take_stream_old_weight').val();
        old_length = $('#take_stream_old_length').val();
        input.parent().parent().next().val(weight);
        input.parent().parent().next().next().find('input[type=text]').val((weight * (old_length / old_weight)).toFixed(2));
    }
    
    function CutValidate() {
        $('#edit_take_stream_alert').removeClass('d-none');
        return false;
    }
</script>