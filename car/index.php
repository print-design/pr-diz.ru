<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
function FindByCell($id) {
    $sql = "select (select count(p.id) "
            . "from pallet p "
            . "where p.cell='$id' "
            . "and p.id in (select pr1.pallet_id from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")")
            . ")) + "
            . "(select count(r.id) "
            . "from roll r "
            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
            . "where r.cell='$id'"
            . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.")")
            . ")";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            header('Location: '.APPLICATION.'/car/by_cell.php?cell='.$id);
        }
        else {
            $error_message = "Объект не найден";
        }
    }
    else {
        $error_message = "Объект не найден";
    }
    
    return $error_message;
}

if(null !== filter_input(INPUT_POST, 'find-submit')) {
    $id = trim(filter_input(INPUT_POST, 'id') ?? '');
    
    // Если первый символ р или Р, ищем среди рулонов
    if((mb_substr($id, 0, 1) == "р" || mb_substr($id, 0, 1) == "Р") && is_numeric(mb_substr($id, 1))) {
        $roll_id = mb_substr($id, 1);
        $sql = "select r.id "
                . "from roll r "
                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                . "where r.id='$roll_id' "
                . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : "and (rsh.status_id is null or rsh.status_id = ".ROLL_STATUS_FREE.") ")
                . "limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/car/roll.php?id='.$row[0]);
        }
        else {
            $error_message = "Не найдено";
        }
    }
    // Если первый символ п или П
    elseif((mb_substr($id, 0, 1) == "п" || mb_substr ($id, 0, 1) == "П") && is_numeric(mb_substr($id, 1))) {
        $pallet_id = mb_substr($id, 1);
        
        $sql = "select p.id "
                . "from pallet p "
                . "where p.id=$pallet_id "
                . "and p.id in (select pr1.pallet_id from pallet_roll pr1 left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh1 on prsh1.pallet_roll_id = pr1.id where pr1.pallet_id = p.id"
                . (IsInRole(ROLE_NAMES[ROLE_AUDITOR]) ? '' : " and (prsh1.status_id is null or prsh1.status_id = ".ROLL_STATUS_FREE.")")
                . ")";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/car/pallet.php?id='.$row[0]);
        }
        else {
            $error_message = "Не найдено";
        }
    }
    else {
        $error_message = "Не найдено";
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
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/find_mobile.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            // Устанавливаем фокус
            $('input#id').focus();
        </script>
    </body>
</html>