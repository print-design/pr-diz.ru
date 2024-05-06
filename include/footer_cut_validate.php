<script>
    function CutCalculate(input) {
        weight = input.val().replace(',', '.');
        old_weight = $('#take_stream_old_weight').val();
        old_length = $('#take_stream_old_length').val();
        input.parent().parent().next().val(weight * (old_length / old_weight));
        input.parent().parent().next().next().find('input[type=text]').val((weight * (old_length / old_weight)).toFixed(2));
    }
    
    function CutValidate() {
        radius = $('#take_stream_radius').val().replace(',', '.');
        length = $('#take_stream_length').val();
        spool = <?=$calculation_result->spool ?>;
        thickness_1 = <?=$calculation->thickness_1 ?>;
        thickness_2 = <?=$calculation->thickness_2 ?>;
        thickness_3 = <?=$calculation->thickness_3 ?>;
        alert(length);
        /*
         * 
         * Если 76 шпуля
0,85* (0,15*R*R+11,3961*R-176,4427)*(20/(толщина пленка 1 + толщина пленка2 + толщина пленка 3))
<Метраж катушки<1,15* (0,15*R*R+11,3961*R-176,4427) *(20/(толщина пленка 1 + толщина пленка2 + толщина пленка 3))
Если 152 шпуля
1,15* (0,1524*R*R+23,1245*R-228,5017) *(20/(толщина пленка 1 + толщина пленка2 + толщина пленка 3))<Метраж<1,15* (0,1524*R*R+23,1245*R-228,5017)*(20/(толщина пленка 1 + толщина пленка2 + толщина пленка 3))


         * 
         */
        
        $('#edit_take_stream_alert').removeClass('d-none');
        return false;
    }
</script>