<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на Главную
$id = filter_input(INPUT_GET, 'id');
$pallet = filter_input(INPUT_GET, 'pallet');
$link = filter_input(INPUT_GET, 'link');
if(empty($id) || empty($pallet) || empty($link)) {
    header('Location: '.APPLICATION.'/cut/');
}

// СТАТУС "СВОБОДНЫЙ" ДЛЯ РУЛОНА
const  FREE_STATUS_ID = 1;

// Валидация ID рулона
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$manual_id_valid = '';

if(null !== filter_input(INPUT_POST, 'next-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $pallet = filter_input(INPUT_POST, 'pallet');
    $link = filter_input(INPUT_POST, 'link');
    $manual_id = filter_input(INPUT_POST, 'manual_id');
    
    // Если это из паллета, то проверяем, чтобы ID имел вид ПxxxРx
    // Если не из паллета, то проверяем, чтобы ID имел вид Рxxx
    if($pallet == 1) {
        if(mb_substr($manual_id, 0, 1) == "п" || mb_substr ($manual_id, 0, 1) == "П") {
            $manual_id_trim = mb_substr($manual_id, 1);
            $substrings = mb_split("[Рр]", $manual_id_trim);
            
            if(count($substrings) == 2) {
                $pallet_id = $substrings[0];
                $ordinal = $substrings[1];
                
                if(is_numeric($pallet_id) && is_numeric($ordinal)) {
                    $sql = "select pr.id "
                            . "from pallet_roll pr "
                            . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "                    . "where pr.pallet_id=$pallet_id and pr.ordinal=$ordinal "
                            . "and (prsh.status_id is null or prsh.status_id = ".FREE_STATUS_ID.")";
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        if($row['id'] == $id) {
                            // Всё OK
                        }
                        else {
                            $manual_id_valid = ISINVALID;
                            $form_valid = false;
                        }
                    }
                    else {
                        $manual_id_valid = ISINVALID;
                        $form_valid = false;
                    }
                }
                else {
                    $manual_id_valid = ISINVALID;
                    $form_valid = false;
                }
            }
            else {
                $manual_id_valid = ISINVALID;
                $form_valid = false;
            }
        }
        else {
            $manual_id_valid = ISINVALID;
            $form_valid = false;
        }
    }
    else {
        if(mb_substr($manual_id, 0, 1) == "р" || mb_substr($manual_id, 0, 1) == "Р") {
            $roll_id = mb_substr($manual_id, 1);
            
            if(is_numeric($roll_id)) {
                $sql = "select r.id "
                        . "from roll r "
                        . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                        . "where r.id=$roll_id and (rsh.status_id is null or rsh.status_id = ".FREE_STATUS_ID.") limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    if($row['id'] == $id) {
                        // Всё OK
                    }
                    else {
                        $manual_id_valid = ISINVALID;
                        $form_valid = false;
                    }
                }
                else {
                    $manual_id_valid = ISINVALID;
                    $form_valid = false;
                }
            }
            else {
                $manual_id_valid = ISINVALID;
                $form_valid = false;
            }
        }
        else {
            $manual_id_valid = ISINVALID;
            $form_valid = false;
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
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= urldecode(filter_input(INPUT_GET, 'link')) ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="object-card">
                        <h1>Нарезка по заявке №1234</h1>
                        <p class="mt-4 mb-5" style="font-size: 1.1rem;">Введите информацию об исходном ролике</p>
                        <form method="post">
                            <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                            <input type="hidden" id="pallet" name="pallet" value="<?=$pallet ?>" />
                            <input type="hidden" id="link" name="link" value="<?=$link ?>" />
                            <div class="form-group">
                                <label for="id">ID исходного ролика</label>
                                <input type="text" 
                                       class="form-control no-latin<?=$manual_id_valid ?>" 
                                       id="manual_id" 
                                       name="manual_id" 
                                       required="required" 
                                       value="<?= filter_input(INPUT_POST, 'manual_id') ?>" />
                                <div class="invalid-feedback">Неверный рулон</div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark form-control" id="next-submit" name="next-submit" style="margin-top: 250px;">Далее</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
    </body>
</html>