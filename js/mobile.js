// Кнопка очистки поля id на мобильных страницах (подключается из include/footer_mobile.php)
// Требует jQuery

function AddClearListener() {
    $('button#clear').click(function() {
        $('input#id').val('');
        $('input#id').change();
        $('input#id').focus();
    });
}

function SetClearVisibility(obj) {
    if(obj.val() === '') {
        $('button#clear').addClass('d-none');
    }
    else {
        $('button#clear').removeClass('d-none');
    }
}

$(document).ready(function() {
    SetClearVisibility($('input#id'));
    AddClearListener();
});

$('input#id').on('keyup keypress change', function(e) {
    SetClearVisibility($(e.target));
});
