<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// СТАТУС "СВОБОДНЫЙ" ДЛЯ РУЛОНА
const  FREE_STATUS_ID = 1;

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'find-submit')) {
    $id = trim(filter_input(INPUT_POST, 'id'));
    
    // Если первый символ р или Р, ищем среди рулонов
    if(mb_substr($id, 0, 1) == "р" || mb_substr($id, 0, 1) == "Р") {
        $roll_id = mb_substr($id, 1);
        $sql = "select r.id "
                . "from roll r "
                . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                . "where r.id=$roll_id and (rsh.status_id is null or rsh.status_id = ".FREE_STATUS_ID.") limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/cut/roll.php?id='.$row[0]);
        }
        else {
            $error_message = "Объект не найден";
        }
    }
    // Если первый символ п или П, ищем среди рулонов, которые в паллетах
    elseif(mb_substr($id, 0, 1) == "п" || mb_substr ($id, 0, 1) == "П") {
        $pallet_trim = mb_substr($id, 1);
        $substrings = mb_split("\D", $pallet_trim);
        
        // Если внутри имеется буква, ищем среди рулонов, которые в паллетах
        if(count($substrings) == 2) {
            $pallet_id = $substrings[0];
            $ordinal = $substrings[1];
            $sql = "select pr.id "
                    . "from pallet_roll pr "
                    . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                    . "where pr.pallet_id=$pallet_id and pr.ordinal=$ordinal "
                    . "and (prsh.status_id is null or prsh.status_id = ".FREE_STATUS_ID.")";
            
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                header('Location: '.APPLICATION.'/cut/pallet_roll.php?id='.$row[0]);
            }
            else {
                $error_message = "Объект не найден";
            }
        }
        else {
            $error_message = "Объект не найден";
        }
    }
    else {
        $error_message = "Объект не найден";
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
    </body>
</html>