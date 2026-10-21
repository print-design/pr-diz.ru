// Модальные окна страниц резки (подключается из include/footer_cut.php)
// Требует jQuery и Bootstrap

$('#cut_remove').on('shown.bs.modal', function() {
    $('input:text:visible:first').focus();
});
        
$('#cut_remove').on('hidden.bs.modal', function() {
    $('input#status_comment').val('');
});

$('#edit_take_stream').on('shown.bs.modal', function() {
    $('input#take_stream_weight').focus();
});
        
$('#edit_take_stream').on('hidden.bs.modal', function() {
    $('input#take_stream_weight').val('');
    $('input#take_stream_length').val('');
    $('input#take_stream_radius').val('');
    $('#edit_take_stream_alert').addClass('d-none');
});
        
$('#add_not_take_stream').on('shown.bs.modal', function() {
    $('select#calculation_stream_id').focus();
});
        
$('#add_not_take_stream').on('hidden.bs.modal', function() {
    $('select#calculation_stream_id').val('');
    $('input#add_not_take_stream_weight').val('');
    $('input#add_not_take_stream_length').val('');
    $('input#add_not_take_stream_radius').val('');
    $('#add_not_take_stream_alert').addClass('d-none');
});

$('#edit_not_take_stream').on('shown.bs.modal', function() {
    $('input#not_take_stream_weight').focus();
});
        
$('#edit_not_take_stream').on('hidden.bs.modal', function() {
    $('input#not_take_stream_weight').val('');
    $('input#not_take_stream_length').val('');
    $('input#not_take_stream_radius').val('');
    $('#edit_not_take_stream_alert').addClass('d-none');
});
