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
        
        <?php $data = "00000040290004241220251216854227153148"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854227153149"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854234153161"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854234153162"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854240153177"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854240153179"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854252153201"; ?>
        <!-- p><? = $ data ?></p-->
        <?php $qrcode = (new QRCode)->render($data); ?>
        <img src="<?=$qrcode ?>" alt="<?=$data ?>" width="400" height="400" />
        
        <?php $data = "00000040290004241220251216854252153202"; ?>
        <!-- p><? = $ data ?></p-->
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