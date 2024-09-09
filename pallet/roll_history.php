<?php
include '../include/topscripts.php';

// Пекренаправление на страницу карщика или резчика при чтении QR-кода
if(IsInRole(ROLE_NAMES[ROLE_ELECTROCARIST])) {
    header('Location: '.APPLICATION.'/car/pallet_roll_edit.php?id='. filter_input(INPUT_GET, 'id'));
}

// Авторизация
elseif(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Получение данных
$pallet_id = null;
$ordinal = null;
$status_id = null;

$sql = "select pr.pallet_id, pr.ordinal, prsh.status_id status_id "
        . "from pallet_roll pr "
        . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
        . "where pr.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $pallet_id = $row['pallet_id'];
    $ordinal = $row['ordinal'];
    $status_id = $row['status_id'];
}
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
        include '../include/header_sklad.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-light backlink" href="roll.php<?= BuildQuery('id', $id) ?>">Назад</a>
            <h1 style="font-size: 24px; font-weight: 600;">История рулона из паллета № <?="П".$pallet_id."Р".$ordinal ?></h1>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>