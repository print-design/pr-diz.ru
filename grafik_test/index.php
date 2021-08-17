<?php
include 'include/test_grafik.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h1>График</h1>
        <?php
        // Создаём новый график
        $grafik = new Test_Grafik();
        
        // Показываем график
        $grafik->ShowRange(7, 17);
        ?>
    </body>
</html>
