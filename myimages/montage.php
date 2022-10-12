<?php
include '../include/topscripts.php';
$psfile = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/temp/ps/10274_SPAR_ZEFIR_BEL_ROZ.ps";
$tifffile = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/temp/tiff/111022_zefir_belo-rozovii.tif";
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a href="./" class="btn btn-outline-dark">На главную</a>
            <h1>Монтаж</h1>
            <?php
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
    </body>
</html>