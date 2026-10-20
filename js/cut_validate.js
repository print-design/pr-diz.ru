// Расчёт и проверка рулонов при резке (подключается из include/footer_cut_validate.php)
// Требует jQuery и объект CUT_PARAMS (шпуля и толщины), который задаётся в footer_cut_validate.php

function CutCalculate(input) {
    var weight = input.val().replace(',', '.');
    var old_weight = $('#take_stream_old_weight').val();
    var old_length = $('#take_stream_old_length').val();
    
    var result = 0;
    
    if(old_weight > 0) {
        result = weight * (old_length / old_weight);
    }
    
    input.parent().parent().next().val(result);
    input.parent().parent().next().next().find('input[type=text]').val(result.toFixed(2));
}

function NTCutCalculate(input) {
    var weight = input.val().replace(',', '.');
    var old_weight = $('#not_take_stream_old_weight').val();
    var old_length = $('#not_take_stream_old_length').val();
    
    var result = 0;
    
    if(old_weight > 0) {
        result = weight * (old_length / old_weight);
    }
    
    input.parent().parent().next().val(result);
    input.parent().parent().next().next().find('input[type=text]').val(result.toFixed(2));
}

function ANTCutCalculate(input) {
    if(input.val() !== '' && input.val() !== 0 && $('select#calculation_stream_id').val() !== '' && $('select#calculation_stream_id').val() !== 0) {
        var weight = input.val().replace(',', '.');
        var sum_weight = $('#add_not_take_stream_sum_weight').val();
        var sum_length = $('#add_not_take_stream_sum_length').val();
        
        if(!(parseFloat(sum_weight) > 0) || !(parseFloat(sum_length) > 0)) {
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
    var result = false;
    
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
    var radius = $('#take_stream_radius').val().replace(',', '.');
    var length = $('#take_stream_length').val();
    var spool = CUT_PARAMS.spool;
    var thickness_1 = CUT_PARAMS.thickness_1;
    var thickness_2 = CUT_PARAMS.thickness_2;
    var thickness_3 = CUT_PARAMS.thickness_3;
    
    var result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
    
    if(!result) {
        $('#edit_take_stream_alert').removeClass('d-none');
    }
    
    return result;
}

function NTCutValidate() {
    var radius = $('#not_take_stream_radius').val().replace(',', '.');
    var length = $('#not_take_stream_length').val();
    var spool = CUT_PARAMS.spool;
    var thickness_1 = CUT_PARAMS.thickness_1;
    var thickness_2 = CUT_PARAMS.thickness_2;
    var thickness_3 = CUT_PARAMS.thickness_3;
    
    var result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
    
    if(!result) {
        $('#edit_not_take_stream_alert').removeClass('d-none');
    }
    
    return result;
}

function ANTCutValidate() {
    var radius = $('#add_not_take_stream_radius').val().replace(',', '.');
    var length = $('#add_not_take_stream_length').val();
    var spool = CUT_PARAMS.spool;
    var thickness_1 = CUT_PARAMS.thickness_1;
    var thickness_2 = CUT_PARAMS.thickness_2;
    var thickness_3 = CUT_PARAMS.thickness_3;
    
    var result = IsValid(spool, radius, length, thickness_1, thickness_2, thickness_3);
    
    if(!result) {
        $('#add_not_take_stream_alert').removeClass('d-none');
    }
    
    return result;
}
