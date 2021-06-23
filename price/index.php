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
            $sql = "select distinct fb.name film_brand, fbv.thickness, null price "
                    . "from film_brand_variation fbv "
                    . "inner join film_brand fb on fbv.film_brand_id = fb.id "
                    . "order by fb.name, fbv.thickness";
            $result = (new Grabber($sql))->result;
            $film_brand_variations = array();
            
            foreach ($result as $item) {
                if(!array_key_exists($item['film_brand'], $film_brand_variations)) {
                    $film_brand_variations[$item['film_brand']] = array();
                }
                
                array_push($film_brand_variations[$item['film_brand']], array( 'thickness' => $item['thickness'], 'price' => $item['price'] ));
            }
            
            foreach(array_keys($film_brand_variations) as $key):
            ?>
            <h2 style="font-size: 18px; line-height: 24px; font-weight: 600;"><?=$key ?></h2>
            <table class="table" style="width: auto; border-bottom: 0; margin-bottom: 30px;">
                <tr>
                    <th>Толщина</th>
                    <?php foreach ($film_brand_variations[$key] as $value): ?>
                    <td><?=$value['thickness'] ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th>Цена</th>
                    <?php foreach ($film_brand_variations[$key] as $value): ?>
                    <td><?=$value['price'] ?></td>
                    <?php endforeach; ?>
                </tr>
            </table>
            <?php endforeach; ?>
        </div>
    </body>
</html>