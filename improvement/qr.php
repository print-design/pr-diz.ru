<?php
include '../include/topscripts.php';
require '../vendor/autoload.php';
use chillerlan\QRCode\QRCode;
?>
<!DOCTYPE html>
<html>
    <body>
        <?php
        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION."/improvement/";
        $qrcode = (new QRCode)->render($data);
        ?>
        <img src="<?=$qrcode ?>" alt="QR Code" />
    </body>
</html>