<script>
    // Если начали печатать хоть один символ в любое из пяти текстовых полей --
    // сбрасываем раскрывающийся список и возвращаем required всем пяти полям
    $(document).on('input', '.pallet-required-field', function() {
        if($(this).val().length > 0) {
            $('.pallet-shared-with-select').val('');
            $('.pallet-required-field').attr('required', 'required');
        }
    });
    
    // Если выбрали заказ в раскрывающемся списке -- стираем все пять полей и убираем required.
    // Если вернули пустое значение -- required возвращается.
    $(document).on('change', '.pallet-shared-with-select', function() {
        if($(this).val() !== '') {
            $('.pallet-required-field').val('');
            $('.pallet-required-field').removeAttr('required');
        }
        else {
            $('.pallet-required-field').attr('required', 'required');
        }
    });
</script>
