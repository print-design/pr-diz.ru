<hr />
<div class="container-fluid">
    &COPY;&nbsp;Принт-дизайн
</div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.js'></script>
<script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
<script src="<?=APPLICATION ?>/js/popper.min.js"></script>

<script>
    // Всплывающие подсказки
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    });
    
    // Фильтрация ввода
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
    
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
        
    $('select').change(function(){
        $(this).removeClass('is-invalid');
    });
    
    // Прокрутка на прежнее место после отправки формы
    $(window).on("scroll", function(){
        $('input[name="scroll"]').val($(window).scrollTop());
        
        if($('thead#grafik-thead').length > 0 && $('tbody#grafik-tbody').length > 0) {
            var windowTop = $(window).scrollTop();
            var headHeight = $('thead#grafik-thead').height();
            var bodyTop = $('tbody#grafik-tbody').offset().top;
            var bodyPosition = $('tbody#grafik-tbody').offset().top - windowTop;
            
            if(bodyPosition < headHeight) {
                $('thead#grafik-thead').css('transform', 'translate3d(0, ' + (windowTop - bodyTop + headHeight) + 'px, 100px)');
            }
            else {
                $('thead#grafik-thead').css('transform', 'none');
            }
        }
    });
    
    <?php if (!empty($_REQUEST['scroll'])): ?>
    window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
    <?php endif; ?>
</script>