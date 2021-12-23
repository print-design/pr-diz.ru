<script>
    $('input#id').focusin(function (){
        $('#find-submit').removeClass('d-none');
    });
    
    function AddFindClearListener() {
        $('button#find-clear').click(function() {
            $('input#id').val('');
            $('input#id').change();
            $('input#id').focus();
        });
    }
    
    function AddFindCameraListener() {
        $('button#find-camera').click(function() {
            alert('MOTOR');
        });
    }
    
    SetFindClearVisibility($('input#id'));

    function SetFindClearVisibility(obj) {
        if(obj.val() == '' && obj.parent().children('.input-group-append').children('#find-camera').length == 0) {
            obj.parent().children('.input-group-append').html('');
            var btn = $("<button type='button' class='btn' id='find-camera'><i class='fas fa-camera'></i></button>");
            obj.parent().children('.input-group-append').append(btn);
            AddFindCameraListener();
        }
        else if(obj.val() != '' && obj.parent().children('.input-group-append').children('#find-clear').length == 0) {
            obj.parent().children('.input-group-append').html('');
            var btn = $("<button type='button' class='btn' id='find-clear'><i class='fas fa-times'></i></button>");
            obj.parent().children('.input-group-append').append(btn);
            AddFindClearListener();
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