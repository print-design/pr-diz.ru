<script>
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
    
    $(document).ready(function() {
        SetFindClearVisibility($('input#id'));
    });
    
    $(document).on("decode", function(e) {
        if(e.detail.type == 'ZBAR_QRCODE') {
            substrings = e.detail.value.split("?id=");
            
            if(substrings.length != 2 && isNaN(substrings[1])) {
                $('input#id').val("Неправильный код");
                $('input#id').change();
                $('#close_video').click();
            }
            else if(e.detail.value.includes('pallet/pallet.php?id=')) {
                $('input#id').val("П" + substrings[1]);
                $('input#id').change();
                $('input#id').focusin();
                $('#close_video').click();
                // Автоматичеески нажимаем "Найти"
                $('#find-submit').click();
            }
            else if(e.detail.value.includes('roll/roll.php?id=')) {
                $('input#id').val("Р" + substrings[1]);
                $('input#id').change();
                $('input#id').focusin();
                $('#close_video').click();
                // Автоматичеески нажимаем "Найти"
                $('#find-submit').click();
            }
            else if(e.detail.value.includes('pallet/roll.php?id=')) {
                $.ajax({ url: "../ajax/roll_id_to_number.php?id=" + substrings[1] })
                    .done(function(data) {
                        $('input#id').val(data);
                        $('input#id').change();
                        $('input#id').focusin();
                        $('#close_video').click();
                        // Автоматичеески нажимаем "Найти"
                        $('#find-submit').click();
                    })
                    .fail(function() {
                        $('input#id').val("Ошибка");
                        $('input#id').change();
                        $('#close_video').click();
                    });
            }
            else {
                $('input#id').val("Неправильный код");
                $('input#id').change();
                $('#close_video').click();
            }
        }
        else {
            $('input#id').val(e.detail.value);
            $('input#id').change();
            $('input#id').focusin();
            $('#close_video').click();
            // Автоматичеески нажимаем "Найти"
            $('#find-submit').click();
        }
    });
    
    $(document).on("play", function() {
        $('#waiting2').addClass('d-none');
        
        $('#close_video').click(function() {
            document.dispatchEvent(new Event('stop'));
        });
    });
    
    $('input#id').focusin(function (){
        $('#find-submit').removeClass('d-none');
    });
    
    $('#codeReaderWrapper').on('shown.bs.modal', function() {
        document.dispatchEvent(new Event('scan'));
    });
    
    $('#codeReaderWrapper').on('hidden.bs.modal', function() {
        $('#waiting2').removeClass('d-none');
    });
    
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
<script>!function(e){function r(r){for(var n,l,f=r[0],i=r[1],a=r[2],p=0,s=[];p<f.length;p++)l=f[p],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in i)Object.prototype.hasOwnProperty.call(i,n)&&(e[n]=i[n]);for(c&&c(r);s.length;)s.shift()();return u.push.apply(u,a||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,f=1;f<t.length;f++){var i=t[f];0!==o[i]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="<?=APPLICATION ?>/zbar/";var f=this.webpackJsonpsrc=this.webpackJsonpsrc||[],i=f.push.bind(f);f.push=r,f=f.slice();for(var a=0;a<f.length;a++)r(f[a]);var c=i;t()}([])</script>
<script src="<?=APPLICATION ?>/zbar/js/2.8358c4d7.chunk.js"></script>
<script src="<?=APPLICATION ?>/zbar/js/main.73d75875.chunk.js"></script>