<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
        <style>
            table.print tr td {
                font-size: 42px;
                line-height: 48px;
                vertical-align: top;
                white-space: pre-wrap;
                padding: 0;
                padding-right: 10px;
            }
        </style>
        <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
        <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/popper.min.js"></script>
        <script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>
        <script src="<?=APPLICATION ?>/js/calculation.js?version=1"></script>
    </head>
    <body>
        <div id="workspace"></div>
        <script>
            // Открытие страницы через AJAX
            function OpenAjaxPage(link) {
                $.ajax({ url: link, context: $('#workspace') })
                        .done(function(data) {
                            $(this).html(data);
                            AssignHandlers();
                        })
                        .fail(function() {
                            alert('Ошибка при переходе на страницу.');
                        });
            }
            
            // Ограничение значений
            function KeyUpLimitIntValue(textbox, max) {
                val = textbox.val().replace(/[^\d]/g, '');
        
                if(val != null && val != '' && !isNaN(val) && parseInt(val) > max) {
                    textbox.addClass('is-invalid');
                }
                else {
                    textbox.removeClass('is-invalid');
                }
            }
            
            // Регистрация обработчиков событий
            function AssignHandlers() {
                // Валидация
                $('input').keypress(function(){
                    $(this).removeClass('is-invalid');
                });
    
                $('select').change(function(){
                    $(this).removeClass('is-invalid');
                });
    
                // Фильтрация ввода
                $('.int-only').keypress(function(e) {
                    if(/\D/.test(e.key)) {
                        return false;
                    }
                });
    
                $('.int-only').keyup(function() {
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
                
                $('.int-only').change(function(e) {
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
                
                // Переходы между страницами
                $('.goto_index').click(function() {
                    OpenAjaxPage("_index.php");
                });
                
                $('.goto_material').click(function() {
                    OpenAjaxPage("_material.php?supplier_id=" + $(this).attr('data-supplier_id') + "&film_brand_id=" + $(this).attr('data-film_brand_id') + "&thickness=" + $(this).attr('data-thickness') + "&width=" + $(this).attr('data-width'));
                });
                
                $('.goto_cut').click(function() {
                    link = "_cut.php?supplier_id=" + $(this).attr('data-supplier_id') + "&film_brand_id=" + $(this).attr('data-film_brand_id') + "&thickness=" + $(this).attr('data-thickness') + "&width=" + $(this).attr('data-width') + "&streams-count=" + $(this).attr('data-streams-count');
                    for(i=1; i<=19; i++) {
                        if(!isNaN($(this).attr('data-stream' + i))) {
                            link += '&stream_' + i + "=" + $(this).attr('data-stream' + i);
                        }
                    }
                    OpenAjaxPage(link);
                });
                
                $('.goto_next').click(function() {
                    OpenAjaxPage("_next.php?cut_id=" + $(this).attr('data-cut-id'));
                });
                
                $('.goto_finish').click(function() {
                    OpenAjaxPage("_finish.php?cut_id=" + $(this).attr('data-cut-id'));
                });
                
                // Загрузка списка марок пленки
                $('#supplier_id').change(function(){
                    if($(this).val() == "") {
                        $('#film_brand_id').html("<option value=''>Выберите марку</option>");
                    }
                    else {
                        $.ajax({ url: "../ajax/film_brand.php?supplier_id=" + $(this).val() })
                                .done(function(data) {
                                    $('#film_brand_id').html(data);
                                    $('#film_brand_id').change();
                                })
                                .fail(function() {
                                    alert('Ошибка при выборе поставщика');
                                });
                            }
                });
            
                // Загрузка списка толщин
                $('#film_brand_id').change(function(){
                    if($(this).val() == "") {
                        $('#thickness').html("<option value=''>Выберите толщину</option>");
                    }
                    else {
                        $.ajax({ url: "../ajax/thickness.php?film_brand_id=" + $(this).val() })
                                .done(function(data) {
                                    $('#thickness').html(data);
                                })
                                .fail(function() {
                                    alert('Ошибка при выборе марки пленки');
                                });
                    }
                });
            
                // Ограничения значений
                $('.int-only').keyup(function() {
                    KeyUpLimitIntValue($(this), $(this).attr('data-max'));
                });
            }
            
            <?php
            $sql = "select request_uri from user where id=".GetUserId();
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
                if(empty($row[0])):
                    ?>
                        OpenAjaxPage("_index.php");
                    <?php
                else:
                    ?>
                        OpenAjaxPage("<?=$row[0] ?>");
                <?php
                endif;
            endif;
            ?>
        </script>
    </body>
</html>