// Показ и скрытие таблиц рулонов на страницах резки (подключается из include/footer_cut.php и order/cut.php)
// Требует jQuery

function ShowTakeTable(id) {
    $('a.show_table[data-id=' + id + ']').addClass('d-none');
    $('a.hide_table[data-id=' + id + ']').removeClass('d-none');
    $('table.take_table[data-id=' + id + ']').removeClass('d-none');
}

function HideTakeTable(id) {
    $('a.hide_table[data-id=' + id + ']').addClass('d-none');
    $('a.show_table[data-id=' + id + ']').removeClass('d-none');
    $('table.take_table[data-id=' + id + ']').addClass('d-none');
}

function ShowNotTakeTable() {
    $('a.show_not_take_table').addClass('d-none');
    $('a.hide_not_take_table').removeClass('d-none');
    $('table.not_take_table').removeClass('d-none');
}
        
function HideNotTakeTable() {
    $('a.hide_not_take_table').addClass('d-none');
    $('a.show_not_take_table').removeClass('d-none');
    $('table.not_take_table').addClass('d-none');
}
