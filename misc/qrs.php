<?php
require '../vendor/autoload.php';
use chillerlan\QRCode\QRCode;
?>
<!DOCTYPE html>
<html>
    <body>
        <h1>Заказ 50684 (676-31)</h1>
        <p><strong>Пример:</strong> Артикул (ТУ-2245-003-70398464-2016) + Дата резки (22.12.2025) + Номер партии плёнки (1241237) + ID ролика (24615)</p>
        <hr />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524615"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524614"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524613"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524612"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524611"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "ТУ-2245-003-70398464-201622.12.202524610"; ?>
        <p><?=$data ?></p>
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />

        <?php
        /*$data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";
        
        $data = "1234567890";
        $qrcode = (new QRCode)->render($data);
        echo "<img src='$qrcode' alt='QR Code' />";*/
        ?>
    </body>
</html>