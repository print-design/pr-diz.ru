<?php
include '../include/topscripts.php';
include '../qr/qrlib.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Значение марки плёнки "другая"
const INDIVIDUAL = "individual";

// Текущее время
$current_date_time = date("dmYHis");

// Получение объекта
$id = filter_input(INPUT_POST, 'id');
if(empty($id)) {
    $id = filter_input(INPUT_GET, 'id');
}

$sql = "select c.date, c.name, "
        . "cus.name customer "
        . "from request_calc c "
        . "inner join customer cus on c.customer_id=cus.id "
        . "where c.id=$id";
$row = (new Fetcher($sql))->Fetch();

$date = $row['date'];
$name = $row['name'];
$customer = $row['customer'];
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
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="request_calc.php?id=<?= $id ?>">Назад</a>
            <div id="title_zone">
                <?php
                $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/request_calc/request_calc.php?id='.$id;
                $filename = "../temp/techmap".$id."_".$current_date_time.".png";
                
                do {
                    QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                } while (!file_exists($filename));
                ?>
                <img src='<?=$filename ?>' style='height: 800px; width: 800px;' />
                <p><?=$customer ?></p>
                <p><?=$name ?></p>
                <p>№<?=$id ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y') ?></p>
            </div>
        </div>
        <?php
        // Удаление всех файлов, кроме текущих (чтобы диск не переполнился).
        $files = scandir("../temp/");
        foreach ($files as $file) {
            $created = filemtime("../temp/".$file);
            $now = time();
            $diff = $now - $created;
            
            if($diff > 20 &&
                    $file != "techmap".$id."_".$current_date_time.".png" &&
                    !is_dir($file)) {
                unlink("../temp/$file");
            }
        }
        
        include '../include/footer.php';
        ?>
    </body>
</html>