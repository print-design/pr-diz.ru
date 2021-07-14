<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение cut_id, возвращаемся на первую страницу
$cut_id = $_REQUEST['cut_id'];
if(empty($cut_id)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// СТАТУС "СВОБОДНЫЙ"
$free_status_id = 1;

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$radius_valid = '';

if(null !== filter_input(INPUT_POST, 'close-submit')) {
    if(null == filter_input(INPUT_POST, 'remains')) {
        header('Location: '.APPLICATION.'/cutter/finish.php');
    }
    else {
        $radius = filter_input(INPUT_POST, 'radius');
        if(empty($radius)) {
            $radius_valid = ISINVALID;
            $form_valid = false;
        }
            
        if($form_valid) {
            print_r($_POST);
        }
    }
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
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start"></nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Закрытие заявки</h1>
            <form method="post">
                <?php
                $checked = " checked='checked'";
                if(filter_input(INPUT_POST, 'close-submit') !== null && filter_input(INPUT_POST, 'remains') == null) {
                    $checked = "";
                }
                ?>
                <div class="form-group">
                    <input type="checkbox" id="remains" name="remains"<?=$checked ?> />
                    <label class="form-check-label" for="remains">Остался исходный ролик</label>
                </div>
                <?php
                $remainder_class = " d-none";
                $remainder_required = "";
                
                if(filter_input(INPUT_POST, 'close-submit') === null || filter_input(INPUT_POST, 'remains') == 'on') {
                    $remainder_class = "";
                    $remainder_required = " required='required'";
                }
                ?>
                <div class="form-group remainder-group<?=$remainder_class ?>">
                    <label for="radius">Введите радиус от вала исходного роля</label>
                    <div class="input-group">
                        <input type="text" class="form-control int-only" id="radius" name="radius" value="<?= filter_input(INPUT_POST, 'radius') ?>"<?=$remainder_required ?> />
                        <div class="input-group-append"><span class="input-group-text">мм</span></div>
                        <div class="invalid-feedback">Радиус от вала обязательно</div>
                    </div>
                </div>
                <div class="form-group remainder-group<?=$remainder_class ?>">
                    <label for="spool">Диаметр шпули</label>
                    <?php
                    $d76_checked = (filter_input(INPUT_POST, 'spool') == null || filter_input(INPUT_POST, 'spool') == 76) ? " checked='checked'" : "";
                    $d152_checked = filter_input(INPUT_POST, 'spool') == 152 ? " checked='checked'" : "";
                    ?>
                    <div class="d-block">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="spool" value="76"<?=$d76_checked ?> />76 мм
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="spool" value="152"<?=$d152_checked ?> />152 мм
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control" style="height: 5rem;" id="close-submit" name="close-submit">Распечатать исходный роль<br /> и закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            $('#remains').change(function() {
                if($(this).is(':checked')) {
                    $('.remainder-group').removeClass('d-none');
                    $('input#radius').attr('required', 'required');
                }
                else {
                    $('.remainder-group').addClass('d-none');
                    $('input#radius').removeAttr('required');
                }
            });
        </script>
    </body>
</html>