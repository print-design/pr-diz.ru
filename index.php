<?php
include 'include/topscripts.php';

// Карщика и ревизора перенаправляем в раздел car
if(IsInRole(array(ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/car/');
}

// Резчика по раскрою перенаправляем в раздел cut
if(IsInRole(ROLE_NAMES[ROLE_CUTTER])) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Маркиратора перенаправляем в раздел marker
if(IsInRole(ROLE_NAMES[ROLE_MARKER])) {
    header('Location: '.APPLICATION.'/marker/');
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <?php
        include 'include/head.php';
        ?>
        <style>
            #topmost {
                height: 85px;
            }
        </style>
    </head>
    <body>
        <?php
        include 'include/header.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger mt-3'>$error_message</div>";
            }
            ?>
            <h1>Принт-Дизайн</h1>
            <h2>Управление ресурсами предприятия</h2>
            <?php if(!LoggedIn()): ?>
            <a href="mobile.php" title="Мобильная версия" class="btn btn-dark d-none" style="height: 12rem; padding: 2rem; font-size: 8rem;"><i class="fas fa-mobile-alt"></i></a>
            <?php endif; ?>
            
            <?php
            /*echo "DELETE";
            require 'vendor/autoload.php';
            use chillerlan\QRCode\QRCode;
            $data   = 'otpauth://totp/test?secret=B3JX4VCVJDVNXNZ5&issuer=chillerlan.net';
            $qrcode = (new QRCode)->render($data);
            printf('<img src="%s" alt="QR Code" />', $qrcode);*/
            ?>
            
        </div>
        <?php
        include 'include/footer.php';
        include 'include/footer_cut.php';
        ?>
    </body>
</html>