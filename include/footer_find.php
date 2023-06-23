<script>
    var mouseOverCamera = false;
    var source_input_id = '';
    
    // Показываем поле поиска
    $('input#find').focusin(function() {
        $('#find-group').addClass('w-100', { duration: 300 });
        $('#find-form').addClass('w-100', { duration: 300 });
    });
    
    // Нажимаем на кнопку с камерой для показа поля поиска
    $("#find-append").click(function() {
        $('#find-form').removeClass('d-none');
        $("#find-append").addClass('d-none');
        $('input#find').focus();
    });
    
    // Двигаем мышью над кнопкой с камерой, чтобы можно было её нажать
    $('#find-camera').mouseenter(function() {
        mouseOverCamera = true;
    });
    
    $('#find-camera').mouseleave(function() {
        mouseOverCamera = false;
    });
    
    // Скрываем поле поиска
    $('input#find').focusout(function() {
        var getstring = window.location.search;
        var getparams = getstring.substring(1).split('&');
        
        var hasfind = false;
        
        for(i=0; i<getparams.length; i++) {
            var keyvalues = getparams[i].split('=');
            
            if(keyvalues[0] == 'find') {
                hasfind = true;
            }
        }
        
        if(!hasfind && $('input#find').val() == '' && !mouseOverCamera) {
            $('#find-group').removeClass('w-100');
            $('#find-form').removeClass('w-100');
            $('#find-append').removeClass('d-none');
            $('#find-form').addClass('d-none');
        }
    });
    
    // Открываем форму чтения кода по нажатию кнопки с камерой
    $('#find-camera').click(function() {
        source_input_id = 'find';
        $('#findCameraWrapper').modal('show');
    });
    
    // При показе формы чтения кода посылаем сигнал "Сканируй"
    $('#findCameraWrapper').on('shown.bs.modal', function() {
        document.dispatchEvent(new Event('scan'));
    });
    
    // При скрытии формы чтения кода делаем видимыми песочные часы (чтобы при следующем открытии они были видны)
    // и посылаем сигнал "Останови поток видео"
    // и устанавливаем фокус на кнопке с поиском
    $('#findCameraWrapper').on('hidden.bs.modal', function() {
        $('#waiting2').removeClass('d-none');
        document.dispatchEvent(new Event('stop'));
        $('input#find').focus();
    });
    
    // При открытии экрана сканирования делаем невидимыми песочные часы
    $(document).on("play", function() {
        $('#waiting2').addClass('d-none');
    });
    
    // Отображаем прочитанный код
    $(document).on("decode", function(e) {
        if(e.detail.type == 'ZBAR_QRCODE') {
            substrings = e.detail.value.split("?id=");
            
            if(substrings.length != 2 && isNaN(substrings[1])) {
                $('input#' + source_input_id).val("Неправильный код");
                $('input#' + source_input_id).change();
                $('#close_video').click();
            }
            else if(e.detail.value.includes('pallet/pallet.php?id=')) {
                $('input#' + source_input_id).val("П" + substrings[1]);
                $('input#' + source_input_id).change();
                $('#close_video').click();
            }
            else if(e.detail.value.includes('roll/roll.php?id=')) {
                $('input#' + source_input_id).val("Р" + substrings[1]);
                $('input#' + source_input_id).change();
                $('#close_video').click();
            }
            else if(e.detail.value.includes('pallet/roll.php?id=')) {
                $.ajax({ url: "../ajax/roll_id_to_number.php?id=" + substrings[1] })
                        .done(function(data) {
                            $('input#' + source_input_id).val(data);
                            $('input#' + source_input_id).change();
                            $('#close_video').click();
                        })
                        .fail(function() {
                            $('input#' + source_input_id).val("Ошибка");
                            $('input#' + source_input_id).change();
                            $('#close_video').click();
                        });
            }
            else {
                $('input#' + source_input_id).val("Неправильный код");
                $('input#' + source_input_id).change();
                $('#close_video').click();
            }
        }
        else {
            $('input#' + source_input_id).val(e.detail.value);
            $('input#' + source_input_id).change();
            $('#close_video').click();
        }
    });
</script>
<script>!function(e){function r(r){for(var n,l,f=r[0],i=r[1],a=r[2],p=0,s=[];p<f.length;p++)l=f[p],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in i)Object.prototype.hasOwnProperty.call(i,n)&&(e[n]=i[n]);for(c&&c(r);s.length;)s.shift()();return u.push.apply(u,a||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,f=1;f<t.length;f++){var i=t[f];0!==o[i]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="<?=APPLICATION ?>/zbar/";var f=this.webpackJsonpsrc=this.webpackJsonpsrc||[],i=f.push.bind(f);f.push=r,f=f.slice();for(var a=0;a<f.length;a++)r(f[a]);var c=i;t()}([])</script>
<script src="<?=APPLICATION ?>/zbar/js/2.8358c4d7.chunk.js"></script>
<script src="<?=APPLICATION ?>/zbar/js/main.73d75875.chunk.js"></script>