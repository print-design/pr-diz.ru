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
    </head>
    <body>
        <div id="workspace"></div>
        <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
        <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script src="<?=APPLICATION ?>/js/popper.min.js"></script>
        <script src="<?=APPLICATION ?>/js/jquery.maskedinput.js"></script>
        <script>
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
            
            function AssignHandlers() {
                // Переходы между страницами
                $('.goto_index').click(function() {
                    OpenAjaxPage("_index.php");
                });
                
                $('.goto_material').click(function() {
                    OpenAjaxPage("_material.php");
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
            
                // В поле "Ширина" ограничиваем значения: целые числа от 1 до 1600
                //$('#width').keyup(function() {
                //    KeyUpLimitIntValue($(this), 1600);
                //});
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