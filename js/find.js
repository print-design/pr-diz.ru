// Поле поиска в шапке раздела (подключается из include/footer_find.php)
// Требует jQuery

// Показываем поле поиска
$('input#find').focusin(function() {
    $('#find-group').addClass('w-100');
    $('#find-form').addClass('w-100');
});

// Нажимаем на кнопку с камерой для показа поля поиска
$("#find-append").click(function() {
    $('#find-form').removeClass('d-none');
    $("#find-append").addClass('d-none');
    $('input#find').focus();
});

// Скрываем поле поиска
$('input#find').focusout(function() {
    var hasfind = new URLSearchParams(window.location.search).has('find');
    
    if(!hasfind && $('input#find').val() === '') {
        $('#find-group').removeClass('w-100');
        $('#find-form').removeClass('w-100');
        $('#find-append').removeClass('d-none');
        $('#find-form').addClass('d-none');
    }
});
