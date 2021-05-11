<script>
    // Список с  поиском
    $('#customer_id').select2({
        placeholder: "Заказчик...",
        maximumSelectionLength: 2,
        language: "ru"
    });
    
    // Обработка выбора типа плёнки основной плёнки: перерисовка списка толщин
    $('#brand_name').change(function(){
        if($(this).val() == "") {
            $('#thickness').html("<option id=''>Толщина...</option>");
        }
        else {
            $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                    .done(function(data) {
                        $('#thickness').html(data);
            })
                    .fail(function() {
                        alert('Ошибка при выборе марки пленки');
            });
        }
    });
            
    // Обработка выбора типа плёнки ламинации1: перерисовка списка толщин
    $('#lamination1_brand_name').change(function(){
        if($(this).val() == "") {
            $('#lamination1_thickness').html("<option id=''>Толщина...</option>");
        }
        else {
            $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                    .done(function(data) {
                        $('#lamination1_thickness').html(data);
            })
                    .fail(function() {
                        alert('Ошибка при выборе марки пленки');
            });
        }
    });
            
    // Обработка выбора типа плёнки ламинации2: перерисовка списка толщин
    $('#lamination2_brand_name').change(function(){
        if($(this).val() == "") {
            $('#lamination2_thickness').html("<option id=''>Толщина...</option>");
        }
        else {
            $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                    .done(function(data) {
                        $('#lamination2_thickness').html(data);
            })
                    .fail(function() {
                        alert('Ошибка при выборе марки пленки');
            });
        }
    });
            
    // Показ марки плёнки и толщины для ламинации 1
    function ShowLamination1() {
        $('#form_lamination_1').removeClass('d-none');
        $('#show_lamination_1').addClass('d-none');
        $('#main_film_title').removeClass('d-none');
        $('#lamination1_brand_name').attr('required', 'required');
        $('#lamination1_thickness').attr('required', 'required');
    }
            
    // Скрытие марки плёнки и толщины для ламинации 1
    function HideLamination1() {
        $('#lamination1_brand_name').val('');
        $('#lamination1_brand_name').change();
        
        $('#form_lamination_1').addClass('d-none');
        $('#show_lamination_1').removeClass('d-none');
        $('#main_film_title').addClass('d-none');
        $('#lamination1_brand_name').removeAttr('required');
        $('#lamination1_thickness').removeAttr('required');
        HideLamination2();
    }
            
    // Показ марки плёнки и толщины для ламинации 2
    function ShowLamination2() {
        $('#form_lamination_2').removeClass('d-none');
        $('#show_lamination_2').addClass('d-none');
        $('#hide_lamination_1').addClass('d-none');
        $('#lamination2_brand_name').attr('required', 'required');
        $('#lamination2_thickness').attr('required', 'required');
    }
            
    // Скрытие марки плёнки и толщины для ламинации 2
    function HideLamination2() {
        $('#lamination2_brand_name').val('');
        $('#lamination2_brand_name').change();
        
        $('#form_lamination_2').addClass('d-none');
        $('#show_lamination_2').removeClass('d-none');
        $('#hide_lamination_1').removeClass('d-none');
        $('#lamination2_brand_name').removeAttr('required');
        $('#lamination2_thickness').removeAttr('required');
    }
</script>