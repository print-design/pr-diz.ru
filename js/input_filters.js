// Фильтры ввода для полей с классами int-only, float-only и no-latin
// (подключается из include/footer.php, cutter/_footer.php и marker/_footer.php)
// Требует jQuery

// Форматирование дробного поля (например, денежной суммы) для отображения разрядов целой части,
// с сохранением дробной части как есть (без округления при вводе)
function FloatFormat(rawVal) {
    var parts = rawVal.split('.');
    var intPart = Intl.NumberFormat('ru-RU').format(parts[0] === '' ? 0 : parts[0]);
    return parts.length > 1 ? (intPart + ',' + parts[1]) : intPart;
}

// Навешивает обработчики на поля внутри root.
// Для полей, созданных после загрузки страницы, нужно вызвать после их добавления:
// BindInputFilters($('#контейнер'));
function BindInputFilters(root) {
    // Фильтрация ввода
    root.find('.int-only').keypress(function(e) {
        if(/\D/.test(e.key)) {
            return false;
        }
    });

    root.find('.int-only').keyup(function() {
        var val = $(this).val();
        val = val.replaceAll(/\D/g, '');
    
        if(val === '') {
            $(this).val('');
        }
        else {
            val = parseInt(val);
        
            if($(this).hasClass('int-format')) {
                val = Intl.NumberFormat('ru-RU').format(val);
            }
        
            $(this).val(val);
        }
    });

    root.find('.int-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(/[^\d]/g, '');
    
        if(val === '') {
            $(this).val('');
        }
        else {
            val = parseInt(val);
        
            if($(this).hasClass('int-format')) {
                val = Intl.NumberFormat('ru-RU').format(val);
            }
        
            $(this).val(val);
        }
    });

    root.find('.float-only').keypress(function(e) {
        if(!/[\.\,\d]/.test(e.key)) {
            return false;
        }
    
        if(/[\.\,]/.test(e.key) && ($(e.target).val().includes('.') || $(e.target).val().includes(','))) {
            return false;
        }
    });

    root.find('.float-only').keyup(function(e) {
        if($(this).hasClass('float-format')) {
            var val = $(this).val();
            val = val.replace(',', '.');
            val = val.replace(/[^\.\d]/g, '');
        
            if(val === '' || val === '.') {
                $(this).val(val);
            }
            else {
                $(this).val(FloatFormat(val));
            }
        }
    });

    root.find('.float-only').change(function(e) {
        var val = $(this).val();
        val = val.replace(',', '.');
        val = val.replace(/[^\.\d]/g, '');
    
        if(val === '' || isNaN(val)) {
            $(this).val('');
        }
        else {
            val = parseFloat(val);
        
            if($(this).hasClass('float-format')) {
                $(this).val(FloatFormat(val.toString()));
            }
            else {
                $(this).val(val);
            }
        }
    });

    root.find('.no-latin').keypress(function(e) {
        if(e.which !== 10 && e.which !== 13) {
            if(/[a-zA-Z]/.test(e.key)) {
                $(this).next('.invalid-feedback').text('Переключите раскладку');
                $(this).next('.invalid-feedback').show();
                return false;
            }
            else {
                $(this).next('.invalid-feedback').hide();
            }
        }
    });

    root.find('.no-latin').change(function() {
        var val = $(this).val();
        val = val.replace(/[a-zA-Z]/g, '');
        $(this).val(val);
    });

    root.find('.no-latin').keyup(function() {
        var val = $(this).val();
        val = val.replace(/[a-zA-Z]/g, '');
        $(this).val(val);
    });
}

BindInputFilters($(document));
