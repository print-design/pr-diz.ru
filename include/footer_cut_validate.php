<script>
    function CutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#take_stream_old_weight').val();
        old_length = $('#take_stream_old_length').val();
        
        result = 0;
        
        if(old_weight > 0) {
            result = weight * (old_length / old_weight);
        }
        
        input.parent().parent().next().val(result);
        input.parent().parent().next().next().find('input[type=text]').val(result.toFixed(2));
    }
    
    function NTCutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#not_take_stream_old_weight').val();
        old_length = $('#not_take_stream_old_length').val();
        
        result = 0;
        
        if(old_weight > 0) {
            result = weight * (old_length / old_weight);
        }
        
        input.parent().parent().next().val(result);
        input.parent().parent().next().next().find('input[type=text]').val(result.toFixed(2));
    }
    
    function ANTCutCalculate(input) {
        if(input.val() !== '' && input.val() !== 0 && $('select#calculation_stream_id').val() !== '' && $('select#calculation_stream_id').val() !== 0) {
            weight = input.val().replace(',', '.');
            sum_weight = $('#add_not_take_stream_sum_weight').val();
            sum_length = $('#add_not_take_stream_sum_length').val();
            
            if(sum_weight === 0 || sum_length === 0) {
                input.parent().parent().next().val(0);
                input.parent().parent().next().next().find('input[type=text]').val(0);
            }
            else {
                input.parent().parent().next().val(weight * (sum_length / sum_weight));
                input.parent().parent().next().next().find('input[type=text]').val((weight * (sum_length / sum_weight)).toFixed(2));
            }
        }
    }
    
    function ANTStreamSelect(select) {
        $('#add_not_take_stream_sum_weight').val($('#sum_weight_stream_' + select.val()).val());
        $('#add_not_take_stream_sum_length').val($('#sum_length_stream_' + select.val()).val());
        $('#add_not_take_stream_alert').addClass('d-none');
        ANTCutCalculate($('#add_not_take_stream_weight'));
    }
    
    function IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3) {
        result = false;
        
        // Если брак, то ставим нулевые значения (двойной == правильно!).
        if(length == 0 && radius == 0) {
            return true;
        }
        
        if(spool === 76) {
            if(0.85 * (0.15 * radius * radius + 11.3961 * radius - 176.4427) * (20 / (thickness_1 + thickness_2 + thickness_3)) < length && length < 1.15 * (0.15 * radius * radius + 11.3961 * radius - 176.4427) * (20 / (thickness_1 + thickness_2 + thickness_3))) {
                result = true;
            }
        }
        else if(spool === 152) {
            if(0.85 * (0.1524 * radius * radius + 23.1245 * radius - 228.5017) * (20 / (thickness_1 + thickness_2 + thickness_3)) < length && length < 1.15 * (0.1524 * radius * radius + 23.1245 * radius - 228.5017) * (20 / (thickness_1 + thickness_2 + thickness_3))) {
                result = true;
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
    
    function ANTCutValidate() {
        radius = $('#add_not_take_stream_radius').val().replace(',', '.');
        length = $('#add_not_take_stream_length').val();
        spool = <?=$calculation_result->spool ?>;
        thickness_1 = <?=$calculation->thickness_1 ?>;
        thickness_2 = <?=$calculation->thickness_2 ?>;
        thickness_3 = <?=$calculation->thickness_3 ?>;
        
        result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
        
        if(!result) {
            $('#add_not_take_stream_alert').removeClass('d-none');
        }
        
        return result;
    }
</script>