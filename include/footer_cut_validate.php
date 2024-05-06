<script>
    function CutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#take_stream_old_weight').val();
        old_length = $('#take_stream_old_length').val();
        input.parent().parent().next().val(weight * (old_length / old_weight));
        input.parent().parent().next().next().find('input[type=text]').val((weight * (old_length / old_weight)).toFixed(2));
    }
    
    function NTCutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#not_take_stream_old_weight').val();
        old_length = $('#not_take_stream_old_length').val();
        input.parent().parent().next().val(weight * (old_length / old_weight));
        input.parent().parent().next().next().find('input[type=text]').val((weight * (old_length / old_weight)).toFixed(2));
    }
    
    function IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3) {
        result = false;
        
        if(spool == 76) {
            if(0.85 * (0.15 * radius * radius + 11.3961 * radius - 176.4427) * (20 / (thickness_1 + thickness_2 + thickness_3)) < length && length < 1.15 * (0.15 * radius * radius + 11.3961 * radius - 176.4427) * (20 / (thickness_1 + thickness_2 + thickness_3))) {
                result = true;
            }
        }
        else if(spool == 152) {
            if(1.15 * (0.1524 * radius * radius + 23.1245 * radius - 228.5017) * (20 / (thickness_1 + thickness_2 + thickness_3)) < length && length < 1.15 * (0.1524 * radius * radius + 23.1245 * radius - 228.5017) * (20 / (thickness_1 + thickness_2 + thickness_3))) {
                $validation2 = true;
            }
        }
        else {
            if(length > 0 && radius > 0) {
                result = true;
            }
        }
        
        return result;
    }
    
    function CutValidate() {
        radius = $('#take_stream_radius').val().replace(',', '.');
        length = $('#take_stream_length').val();
        spool = <?=$calculation_result->spool ?>;
        thickness_1 = <?=$calculation->thickness_1 ?>;
        thickness_2 = <?=$calculation->thickness_2 ?>;
        thickness_3 = <?=$calculation->thickness_3 ?>;
        
        result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
        
        if(!result) {
            $('#edit_take_stream_alert').removeClass('d-none');
        }
        
        return result;
    }
    
    function NTCutValidate() {
        radius = $('#not_take_stream_radius').val().replace(',', '.');
        length = $('#not_take_stream_length').val();
        spool = <?=$calculation_result->spool ?>;
        thickness_1 = <?=$calculation->thickness_1 ?>;
        thickness_2 = <?=$calculation->thickness_2 ?>;
        thickness_3 = <?=$calculation->thickness_3 ?>;
        
        result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
        
        if(!result) {
            $('#edit_not_take_stream_alert').removeClass('d-none');
        }
        
        return result;
    }
</script>