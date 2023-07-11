<hr />
<div class="container-fluid">
    &COPY;&nbsp;Принт-дизайн
</div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.js'></script>

<script>
    $(document).ready(function(){
        $('.int-only').keypress(function(e) {
            if(/\D/.test(String.fromCharCode(e.charCode))) {
                return false;
            }
        });
        
        $('.float-only').keypress(function(e) {
            if(!/[\.\d]/.test(String.fromCharCode(e.charCode))) {
                return false;
            }
        });
        
        $('input').keypress(function(){
            $(this).removeClass('is-invalid');
        });
        
        $('select').change(function(){
            $(this).removeClass('is-invalid');
        });
    });
</script>