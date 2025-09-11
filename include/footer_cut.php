<script>
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
    
    function ShowImage(stream_id) {
        $.ajax({ url: "../include/big_image_show.php?stream_id=" + stream_id,
            dataType: "json",
            success: function(response) {
                if(response.error.length > 0) {
                    alert(response.error);
                }
                else {
                    $('#big_image_header').text(response.name);
                    $('#big_image_img').attr('src', '../content/stream/' + response.filename + '?' + Date.now());
                    document.forms.download_image_form.object.value = 'stream';
                    document.forms.download_image_form.id.value = stream_id;
                    document.forms.download_image_form.image.value = response.image;
                    ShowImageButtons(stream_id, response.image);
                }
            }
        });
    }
    
    function ShowImageButtons(stream_id, image) {
        $.ajax({ url: "../include/big_image_buttons.php?stream_id=" + stream_id + "&image=" + image,
            success: function(response) {
                $('#big_image_buttons').html(response);
            },
            error: function() {
                alert('Ошибка при создании кнопок макета.');
            }
        });
    }
    
    $('#cut_remove').on('shown.bs.modal', function() {
        $('input:text:visible:first').focus();
    });
            
    $('#cut_remove').on('hidden.bs.modal', function() {
        $('input#cut_remove_cause').val('');
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
    
    <?php if(IsInRole(CUTTER_USERS)): ?>
    function ShowCutterName() {
        $('span#top_user_name').load("<?=APPLICATION ?>/cut/_get_user.php");
    }
    
    ShowCutterName();
    setInterval(ShowCutterName, 60000);
    <?php endif; ?>
</script>