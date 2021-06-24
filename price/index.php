<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-start">
                <div class="p-1">
                    <?php
                    include '../include/subheader_admin.php';
                    ?>
                </div>
            </div>
            <hr />
            <?php
            $sql = "select distinct fb.name film_brand, fbv.thickness, fp.price, fp.currency "
                    . "from film_brand_variation fbv "
                    . "inner join film_brand fb on fbv.film_brand_id = fb.id left join film_price fp on fp.brand_name = fb.name and fp.thickness = fbv.thickness "
                    . "order by fb.name, fbv.thickness";
            $result = (new Grabber($sql))->result;
            $film_brand_variations = array();
            
            foreach ($result as $item) {
                if(!array_key_exists($item['film_brand'], $film_brand_variations)) {
                    $film_brand_variations[$item['film_brand']] = array();
                }
                
                array_push($film_brand_variations[$item['film_brand']], array( 'thickness' => $item['thickness'], 'price' => $item['price'], 'currency' => $item['currency'] ));
            }
            
            foreach(array_keys($film_brand_variations) as $key):
            ?>
            <h2 style="font-size: 18px; line-height: 24px; font-weight: 600;"><?=$key ?></h2>
            <table class="table" style="width: auto; border-bottom: 0; margin-bottom: 30px;">
                <tr>
                    <th>Толщина</th>
                    <?php foreach ($film_brand_variations[$key] as $value): ?>
                    <td><?=$value['thickness'] ?> мкм</td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th>Цена</th>
                    <?php foreach ($film_brand_variations[$key] as $value): ?>
                    <td>
                        <div class="input-group">
                            <input type="text" 
                                   name="price" 
                                   class="form-control float-only film-price" 
                                   placeholder="Цена" style="width: 80px;" 
                                   value="<?=$value['price'] ?>" 
                                   data-brand-name="<?=$key ?>" 
                                   data-thickness="<?=$value['thickness'] ?>"
                                   onmousedown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onmouseup="javascript: $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" 
                                   onkeydown="javascript: $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                   onkeyup="javascript: $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" 
                                   onfocusout="javascript: $(this).attr('name', 'price'); $(this).attr('placeholder', 'Цена');" />
                            <div class="input-group-append">
                                <select name="currency" class="film-currency">
                                    <option value="" hidden="">...</option>
                                    <option value="rub"<?=$value['currency'] == "rub" ? " selected='selected'" : "" ?>>Руб</option>
                                    <option value="usd"<?=$value['currency'] == "usd" ? " selected='selected'" : "" ?>>USD</option>
                                    <option value="euro"<?=$value['currency'] == "euro" ? " selected='selected'" : "" ?>>EUR</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <?php endforeach; ?>
                </tr>
            </table>
            <?php endforeach; ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('.film-price').focusout(function() {
                currency_select = $(this).next('.input-group-append').children('select');
                if(currency_select.val() != '') {
                    SaveFilmPrice($(this), currency_select.val());
                }
            });
            
            $('.film-currency').change(function() {
                price_input = $(this).parent().prev('input');
                if(price_input.val() != '') {
                    SaveFilmPrice(price_input, $(this).val());
                }
            })
            
            function SaveFilmPrice(price_input, currency) {
                var price = price_input.val();
                if(price === '' || isNaN(price)) return false;
                
                var brand_name = price_input.attr('data-brand-name');
                var thickness = price_input.attr('data-thickness');
                price_input.val('000');
                $.ajax({ url: "../ajax/film_price.php?brand_name=" + brand_name + "&thickness=" + thickness + "&price=" + price + "&currency=" + currency, context: price_input })
                        .done(function(data) {
                            price_input.val(data);
                })
                        .fail(function() {
                            alert('Ошибка при сохранении цены плёнки');
                });
            }
        </script>
    </body>
</html>