<script src="<?=APPLICATION ?>/js/zxing-js.umd.min.js"></script>
<script>
    // Create instance of the object. The only argument is the "id" of HTML element created above.
    const codeReader = new ZXing.BrowserBarcodeReader();
    let selectedDeviceId = null;
            
    $(document).ready(function() {
        // This method will trigger user permissions
        codeReader.getVideoInputDevices()
                .then((videoInputDevices) => {
                    if (videoInputDevices.length > 0) {
                        videoInputDevices.forEach((element) => {
                            if(element.label.indexOf('back') != -1) {
                                selectedDeviceId = element.id;
                            }
                        });

                        if(selectedDeviceId == null && videoInputDevices.length > 1) {
                            selectedDeviceId = videoInputDevices[1].id;
                        }
                        else if(selectedDeviceId == null) {
                            selectedDeviceId = videoInputDevices[0].id;
                        }
                        
                        $('#close_video').click(function() {
                            $('#video').removeClass('detected');
                            codeReader.reset();
                        });
                    }
                })
                .catch((err) => {
                    console.error(err);
                });
                    
        SetFindClearVisibility($('input#id'));
    });
    
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
            $('#codeReaderWrapper').modal('show');
            
            codeReader.decodeOnceFromVideoDevice(selectedDeviceId, 'video')
                    .then((result) => {
                        $('input#id').val(result.text);
                        $('input#id').change();
                        codeReader.reset();
                        $('#codeReaderWrapper').modal('hide');
                    })
                    .catch((err) => {
                        console.error(err);
                    });
        });
    }

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