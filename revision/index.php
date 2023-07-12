<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

if(null !== filter_input(INPUT_POST, 'find-submit')) {
    $id = trim(filter_input(INPUT_POST, 'id'));
    
    // Если первый символ р или Р, ищем среди рулонов
    if((mb_substr($id, 0, 1) == "р" || mb_substr($id, 0, 1) == "Р") && is_numeric(mb_substr($id, 1))) {
        $roll_id = mb_substr($id, 1);
        $sql = "select id from roll where id = '$roll_id' limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/revision/roll_edit.php?id='.$row[0]);
        }
        else {
            $error_message = "Не найдено";
        }
    }
    // Если первый символ п или П
    elseif((mb_substr($id, 0, 1) == "п" || mb_substr ($id, 0, 1) == "П") && is_numeric(mb_substr($id, 1))) {
        $pallet_id = mb_substr($id, 1);
        $sql = "select id from pallet where id = '$pallet_id' limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            header('Location: '.APPLICATION.'/revision/pallet_edit.php?id='.$row[0]);
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
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-between">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="utilize.php">Сработать всё</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown no-dropdown-arrow-after">
                        <a class="nav-link mr-0" id="logout-submit" href="../user_mobile.php?link=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="fa fa-user-alt" aria-hidden="true"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
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