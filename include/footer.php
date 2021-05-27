<div style="height: 50px;"></div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
<script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
<script src="<?=APPLICATION ?>/js/popper.min.js"></script>
<script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>

<script>
    // Фильтрация ввода
    $('.int-only').keypress(function(e) {
        if(/\D/.test(e.key)) {
            return false;
        }
    });
    
    $('.int-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(/[^\d]/g, '');
        $(this).val(val);
    });
    
    $('.float-only').keypress(function(e) {
        if(!/[\.\,\d]/.test(e.key)) {
            return false;
        }
        
        if(/[\.\,]/.test(e.key) && ($(e.target).val().includes('.') || $(e.target).val().includes(','))) {
            return false;
        }
    });
    
    $('.float-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(',', '.');
        val = val.replace(/[^\.\d]/g, '');
        $(this).val(val);
    });
    
    $('.no-latin').keypress(function(e) {
        if(/[a-zA-Z]/.test(e.key)) {
            $(this).next('.invalid-feedback').text('Переключите раскладку');
            $(this).next('.invalid-feedback').show();
            return false;
        }
        else {
            $(this).next('.invalid-feedback').hide();
        }
    });
    
    $('.no-latin').change(function() {
        var val = $(this).val();
        val = val.replace('[a-zA-Z]', '');
        $(this).val(val);
    });
    
    $('textarea').css('resize', 'none');
    
    // Валидация
    $('input').keypress(function(){
        $(this).removeClass('is-invalid');
    });
    
    $('select').change(function(){
        $(this).removeClass('is-invalid');
    });
    
    $.mask.definitions['~'] = "[+-]";
    $("#phone").mask("+7 (999) 999-99-99");
    
    // При щелчке в поле телефона, устанавливаем курсор в самое начало ввода телефонного номера.
    $("#phone").click(function(){
        var maskposition = $(this).val().indexOf("_");
        if(Number.isInteger(maskposition)) {
            $(this).prop("selectionStart", maskposition);
            $(this).prop("selectionEnd", maskposition);
        }
    });
    
    // При изменении значения в поле формы, показываем заголовок поля
    $('input').change(function(e) {
        if($(e.target).prev().is('label.hideable')) {
            if($(e.target).val() == '') {
                $(e.target).prev().hide();
            }
            else {
                $(e.target).prev().show();
            }
        }
        
        if($(e.target).parent().is('.input-group') && $(e.target).parent().prev().is('label.hideable')) {
            if($(e.target).val() == '') {
                $(e.target).parent().prev().hide();
            }
            else {
                $(e.target).parent().prev().show();
            }
        }
    });
    
    $('select').change(function(e) {
        if($(e.target).prev().is('label.hideable')) {
            if($(e.target).val() == '') {
                $(e.target).prev().hide();
            }
            else {
                $(e.target).prev().show();
            }
        }
        
        if($(e.target).parent().is('.input-group') && $(e.target).parent().prev().is('label.hideable')) {
            if($(e.target).val() == '') {
                $(e.target).parent().prev().hide();
            }
            else {
                $(e.target).parent().prev().show();
            }
        }
    });
    
    // Делаем видимыми все заголовки непустых полей
    $('label.hideable').each(function() {
        if($(this).next().val() != '') {
            $(this).show();
        }
        
        if($(this).next().is('.input-group') && $(this).next().children().first().val() != '') {
            $(this).show();
        }
    });
    
    // Подтверждение удаления
    $('button.confirmable').click(function() {
        return confirm("Действительно удалить?");
    });
    
    // Отмена нажатия неактивной кнопки
    $('button.disabled').click(function() {
        return false;
    });
    
    // Поиск
    $('input#find').focusin(function() {
        $('#find-group').addClass('w-100', { duration: 300 });
        $('#find-form').addClass('w-100', { duration: 300 });
    });
    
    $("#find-append").click(function() {
        $('#find-form').removeClass('d-none');
        $("#find-append").addClass('d-none');
        $('input#find').focus();
    });
    
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
        
        if(!hasfind && $('input#find').val() == '') {
            $('#find-group').removeClass('w-100');
            $('#find-form').removeClass('w-100');
            $('#find-append').removeClass('d-none');
            $('#find-form').addClass('d-none');
        }
    });
    
    // Всплывающая подсказка
    $(function() {
        $("a.left_bar_item").tooltip({
            position: {
                my: "left center",
                at: "right+10 center"
            }
        });
    });
    
    // Защита от двойного нажатия
    var submit_clicked = false;
    
    $('button[type=submit]').click(function () {
        if(submit_clicked) {
            return false;
        }
        else {
            submit_clicked = true;
        }
    });
    
    $(document).keydown(function () {
        submit_clicked = false;
    });
    
    $('select').change(function () {
        submit_clicked = false;
    });
    
    $('input').keydown(function() {
        submit_clicked = false;
    });
    
    $('input').change(function() {
        submit_clicked = false;
    });
    
    // Автологаут резчика
    <?php if(IsInRole('cutter')): ?>
        function AutoLogout(end) {
            var beforeLogout = end - (new Date());
            
            if(beforeLogout < 0) {
                $('#logout_submit').click();
            } else {
                var beforeLogoutSec = Math.floor(beforeLogout / 1000);
                var beforeLogoutMin = Math.floor(beforeLogoutSec / 60);
                var beforeLogoutLastSec = beforeLogoutSec - (beforeLogoutMin * 60);
                $('#autologout').html(String(beforeLogoutMin).padStart(2, '0') + ':' + String(beforeLogoutLastSec).padStart(2, '0'));
            }
        }
        
        // Автологаут через 20 минут
        let unix_timestamp = <?= filter_input(INPUT_COOKIE, LOGIN_TIME) ?>;
        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        var begin_date = new Date(unix_timestamp * 1000);
        var end_date = new Date(unix_timestamp * 1000 + (<?=AUTOLOGOUT_MIN ?> * 60 * 1000));
        
        var beforeLogout = end_date - (new Date());       
        var beforeLogoutSec = Math.floor(beforeLogout / 1000);
        var beforeLogoutMin = Math.floor(beforeLogoutSec / 60);
        var beforeLogoutLastSec = beforeLogoutSec - (beforeLogoutMin * 60);
        AutoLogout(end_date);
        
        setInterval(AutoLogout, 1000, end_date);
    <?php endif; ?>
        
    // Прокрутка на прежнее место после отправки формы
    $(window).on("scroll", function(){
        $('input[name="scroll"]').val($(window).scrollTop());
    });
    
    <?php if(!empty($_REQUEST['scroll'])): ?>
        window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
    <?php endif; ?>
</script>