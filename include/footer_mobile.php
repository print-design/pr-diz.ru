<script>
    $('input#id').focusin(function (){
        $('#car-submit').removeClass('d-none');
    });
    
    $('button#find-clear').click(function(e) {
        $(e.target).parent().parent().remove('.input-group-append');
        $(e.target).parent().remove('.input-group-append');
        $('input#id').val('');
        $('input#id').focus();
    });

    function SetFindClearVisibility(obj) {
        if(obj.val() == '') {
            if(obj.parent().children('.input-group-append').length > 0) {
                obj.parent().children().remove('.input-group-append');
            }
        }
        else {
             if(obj.parent().children('.input-group-append').length == 0) {
                var app = $("<div class='input-group-append'></div>");
                var btn = $("<button type='button' class='btn' id='find-clear'><i class='fas fa-times'></i></button>");
                btn.click(function(e) {
                    $(e.target).parent().parent().remove('.input-group-append');
                    $(e.target).parent().remove('.input-group-append');
                    $('input#id').val('');
                    $('input#id').focus();
                });
                app.append(btn);
                obj.parent().append(app);
            }
        }
    }
    
    $('input#id').keyup(function(e) {
        SetFindClearVisibility($(e.target));
    });
    
    $('input#id').keypress(function(e) {
        SetFindClearVisibility($(e.target));
    });
    
    $('input#id').change(function(e) {
        SetFindClearVisibility($(e.target));
    });
</script>