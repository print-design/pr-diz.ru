<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
function FindByCell($id) {
    $sql = "select (select count(id) from pallet where cell='$id') + (select count(id) from roll where cell='$id')";
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

if(null !== filter_input(INPUT_POST, 'car-submit')) {
    $id = trim(filter_input(INPUT_POST, 'id'));
    
    // Если первый символ р или Р, ищем среди рулонов
    if(mb_substr($id, 0, 1) == "р" || mb_substr($id, 0, 1) == "Р") {
        $roll_id = mb_substr($id, 1);
        $sql = "select id from roll where id=$roll_id limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/car/roll.php?id='.$row[0]);
        }
        else {
            $error_message = FindByCell($id);
        }
    }
    // Если первый символ п или П
    elseif(mb_substr($id, 0, 1) == "п" || mb_substr ($id, 0, 1) == "П") {
        $pallet_trim = mb_substr($id, 1);
        $substrings = mb_split("\D", $pallet_trim);
        
        // Если внутри имеется буква, ищем среди рулонов, которые в паллетах
        if(count($substrings) == 2) {
            $pallet_id = $substrings[0];
            $ordinal = $substrings[1];
            $sql = "select id from pallet_roll where pallet_id=$pallet_id and ordinal=$ordinal limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                header('Location: '.APPLICATION.'/car/pallet_roll.php?id='.$row[0]);
            }
            else {
                $error_message = FindByCell($id);
            }
        }
        elseif(count($substrings) == 1) {
            $pallet_id = $substrings[0];
            $sql = "select id from pallet where id=$pallet_id limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                header('Location: '.APPLICATION.'/car/pallet.php?id='.$row[0]);
            }
            else {
                $error_message = FindByCell($id);
            }
        }
        else {
            $error_message = FindByCell($id);
        }
    }
    else {
        $error_message = FindByCell($id);
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
        include '_style.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_mobile.php';
        ?>
        <div class="container-fluid">
            <?php
            include '_find.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '_footer.php';
        ?>
    </body>
</html>